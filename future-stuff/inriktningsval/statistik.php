<?php
/*
 * Administrera inriktnings- och paketval
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
    $t1 .= <<<TR
      <tr>
        <td>{$row['paket']}</td>
        <td>{$row['antal']}</td>
      </tr>
TR;
}
// rowspan x 2



$t2  = "";
$sql = <<<SQL
SELECT inriktning, paket1 AS paket, COUNT(*) AS antal FROM elever
WHERE `klass` <> 'Te0F' AND paket1 IS NOT NULL GROUP BY inriktning, paket1
UNION
SELECT inriktning, paket2 AS paket, COUNT(*) AS antal FROM elever
WHERE `klass` <> 'Te0F' AND paket2 IS NOT NULL GROUP BY inriktning, paket2
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
  </body>
</html>

<!-- 
Idéer:
Bakgrund till texten med getBBBox samt koll att den inte sticker utanför SVG-elementet

Mer statistik:
Per klass
Per kön - Utnyttja näst sista siffran
Paket

+ Framtid
Se annat år 
Jämföra mellan år

+ Specialkul
Sortera tabellerna med JS

-->
