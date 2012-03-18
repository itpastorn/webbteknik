<?php
/*
 * Administrera inriktnings- och paketval
 * 
 * Ska presentera 4 tabeller:
 * * Grupperad info per paketval oavsett inriktning
 * * Grupperad info per inriktning - sorteringsbar
 * * Total per kurs oavsett paket/inriktning
 * * Total per kurs per paket/inriktning
 * 
 * @author Lars Gunther <gunther@keryx.se>
 */

error_reporting(E_ALL);
ini_set("display_errors", "on");

// Inloggad?
session_start();

// "read" = kan se sidan
// "write" = kan bekräfta och ta bort elevers val
// "admin" = kan skapa och ändra privilegier

if ( empty($_SESSION['privilegier']) ) {
    header("Location: admin-loginform.php");
    exit;
}

// Datumfunktioner
date_default_timezone_set("Europe/Stockholm");

/**
 * Databasanslutning
 */
require_once("dbcx.php");
$dbh = dbcx();

if ( empty($_GET['year']) ) {
    $year = date("Y");
} else {
    if ( preg_match("/^2[0-9]{3}$/", $_GET['year']) ) {
        $year = (int)$_GET['year'];
    } else {
        // TODO: Nice error message
        exit("<h1>Ogiltigt årtal</h1>");
    }
}

// Vilka kurser ingår i paketen?
$sql = <<<SQL
    SELECT  bk.inr_pak_ID, k.* FROM `kurser` AS k
    INNER JOIN block_kurser AS bk
    USING (kurskod)
    ORDER BY bk.inr_pak_ID
SQL;
$index  = "";
$kurser = array();
$stmt   = $dbh->query($sql);
foreach ( $stmt as $row ) {
    if ( $row['inr_pak_ID'] != $index ) {
        $index = $row['inr_pak_ID'];
    }
    $kurser[$index][] = array($row['kurskod'], $row['kursnamn'], $row['poang']);
}

// Namn på inriktningarna
$inr_names = array();
$stmt = $dbh->query("SELECT inr_pak_ID, name FROM inriktning_paket WHERE name IS NOT NULL");
while ( $dbrow = $stmt->fetch() ) {
    $inr_names[$dbrow['inr_pak_ID']] = $dbrow['name'];
}


$t1  = "";
$sql = <<<SQL
    SELECT paket1 AS paket, COUNT(*) AS antal FROM elever
    WHERE `klass` <> 'Te0F' AND paket1 IS NOT NULL GROUP BY paket1
    UNION
    SELECT paket2 AS paket, COUNT(*) AS antal FROM elever
    WHERE `klass` <> 'Te0F' AND paket2 IS NOT NULL GROUP BY paket2
SQL;
$stmt = $dbh->query($sql);
foreach ( $stmt as $row ) {
    // Antal kurser som ingår i paketet
    $kant = count($kurser[$row['paket']]);
    $firstrun = true;
    foreach ( $kurser[$row['paket']] as $kurs ) {
        $t1 .=  "<tr>\n";
        if ( $firstrun ) {
            $t1 .= '<td rowspan="' . $kant . '">' . $row['paket'] . "</td>\n";
        }
        $t1 .= "<td>{$kurs[1]}</td><td>{$kurs[2]}</td>\n";
        if ( $firstrun ) {
            $t1 .= <<<TR2
            <td rowspan="{$kant}">{$row['antal']}</td>
TR2;
            $firstrun = false;
        }
        $t1 .= "</tr>\n";
    }
    
}
// rowspan x 2



$t2  = "";
$sql = <<<SQL
    SELECT inriktning, paket1 AS paket, COUNT(*) AS antal FROM elever
    WHERE `klass` <> 'Te0F' AND paket1 IS NOT NULL GROUP BY inriktning, paket1
    UNION
    SELECT inriktning, paket2 AS paket, COUNT(*) AS antal FROM elever
    WHERE `klass` <> 'Te0F' AND paket2 IS NOT NULL GROUP BY inriktning, paket2
    ORDER BY paket
SQL;
$stmt = $dbh->query($sql);
foreach ( $stmt as $row ) {
    $t2 .= <<<TR
      <tr>
        <td>{$inr_names[$row['inriktning']]}</td>
        <td>{$row['paket']}</td>
        <td>{$row['antal']}</td>
      </tr>
TR;
}
$sql = <<<SQL
    SELECT k.*, COUNT(*) AS antal FROM elever AS e
    INNER JOIN block_kurser AS bk
    ON (e.inriktning = bk.inr_pak_ID OR e.paket1  = bk.inr_pak_ID OR e.paket2  = bk.inr_pak_ID)
    INNER JOIN kurser AS k
    ON (k.kurskod = bk.kurskod)
    WHERE e.klass <> 'Te0F'
    GROUP BY k.kurskod ASC
    ORDER BY `antal` DESC
SQL;
$t3 = "";
$stmt = $dbh->query($sql);
foreach ( $stmt as $row ) {
    $t3 .= <<<TR
      <tr>
        <td>{$row['kurskod']}</td>
        <td>{$row['kursnamn']}</td>
        <td>{$row['poang']}</td>
        <td>{$row['antal']}</td>
      </tr>
TR;
}

$sql = <<<SQL
    SELECT bk.inr_pak_ID, k.*, COUNT(*) AS antal FROM elever AS e
    INNER JOIN block_kurser AS bk
    ON (e.inriktning = bk.inr_pak_ID OR e.paket1  = bk.inr_pak_ID OR e.paket2  = bk.inr_pak_ID)
    INNER JOIN kurser AS k
    ON (k.kurskod = bk.kurskod)
    WHERE e.klass <> 'Te0F'
    GROUP BY bk.inr_pak_ID, k.kurskod ASC
    ORDER BY kurskod ASC, inr_pak_ID ASC
SQL;
$t4 = "";
$stmt = $dbh->query($sql);
foreach ( $stmt as $row ) {
    $t4 .= <<<TR
      <tr>
        <td>{$row['inr_pak_ID']}</td>
        <td>{$row['kurskod']}</td>
        <td>{$row['kursnamn']}</td>
        <td>{$row['poang']}</td>
        <td>{$row['antal']}</td>
      </tr>
TR;
}


/*
echo "<pre>";
var_dump($stat);
exit;
 */
// Sidmall
?>
<!DOCTYPE html>
<html lang="sv">
 <head>
  <meta charset="utf-8" />
  <title>Statistik inriktnings- och fördjupningskursval på Teknikprogrammet, NE, <?php echo $year; ?></title>
  <link href="inr-val.css" rel="stylesheet" />
 </head>
 <body class="admin">
   <h1>Statistik inriktnings- och fördjupningskursval på Teknikprogrammet, NE, <?php echo $year; ?></h1>
   <table>
     <caption>Grupperad info per paketval oavsett inriktning</caption>
     <tr>
       <th>Paket</th>
       <th>Kurs</th>
       <th>Poäng</th>
       <th>Antal</th>
     </tr>
     <?php echo $t1; ?>
   </table>
   <table>
     <caption>Grupperad info per inriktning</caption>
     <tr>
       <th>Inriktning</th>
       <th>Paket</th>
       <th>Antal</th>
     </tr>
     <?php echo $t2; ?>
   </table>
   <table>
     <caption>Elever per kurs</caption>
     <tr>
       <th>Kurskod</th>
       <th>kursnamn</th>
       <th>Poang</th>
       <th>Antal</th>
     </tr>
     <?php echo $t3; ?>
   </table>
   <table>
     <caption>Elever per inriktning/paket och kurs</caption>
     <tr>
       <th>Val</th>
       <th>Kurskod</th>
       <th>kursnamn</th>
       <th>Poang</th>
       <th>Antal</th>
     </tr>
     <?php echo $t4; ?>
   </table>
  </body>
</html>

<!-- 
Hur göra tabell 4? Nästan som 3

SELECT bk.inr_pak_ID, k.*, COUNT(*) AS antal FROM elever AS e
INNER JOIN block_kurser AS bk
ON (e.inriktning = bk.inr_pak_ID OR e.paket1  = bk.inr_pak_ID OR e.paket2  = bk.inr_pak_ID)
INNER JOIN kurser AS k
ON (k.kurskod = bk.kurskod)
WHERE e.klass <> 'Te0F'
GROUP BY bk.inr_pak_ID, k.kurskod ASC
ORDER BY `kurskod` DESC

-->
