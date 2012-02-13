<?php
/*
 * Administrera inriktnings- och paketval
 *
 * @author Lars Gunther <gunther@keryx.se>
 */

error_reporting(E_ALL);
ini_set("display_errors", "on");

// Inloggad?

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

// Experiment SQL
$sql = <<<SQL
    SELECT e.*, ip.inr_pak_id, ip.name, GROUP_CONCAT(k.kursnamn, " (", k.kurskod, ")") AS kinfo FROM elever AS e
    INNER JOIN inriktning_paket AS ip ON e.inriktning = ip.inr_pak_id OR e.paket1 = ip.inr_pak_id OR e.paket2 = ip.inr_pak_id
    INNER JOIN block_kurser AS bk ON (ip.inr_pak_ID = bk.inr_pak_ID)
    INNER JOIN kurser AS k ON ( bk.kurskod = k.kurskod )
    WHERE e.year = :year
    GROUP BY e.personnummer, ip.inr_pak_id
    ORDER BY e.klass ASC, e.efternamn ASC, ip.name DESC, ip.inr_pak_ID ASC
SQL;
// personnummer, fornamn, efternamn, klass, kod, inriktning, paket1, paket2, kommentar, email, confirmed, year, inr_pak_id, name, kinfo

$sql = <<<SQL
    SELECT e.personnummer, e.fornamn, e.efternamn, e.klass, e.kod, e.inriktning, ip.name AS inr_name FROM elever AS e
    LEFT JOIN inriktning_paket AS ip ON e.inriktning = ip.inr_pak_id
    WHERE e.year = :year
    ORDER BY e.klass ASC, e.efternamn ASC, e.fornamn ASC
SQL;
// personnummer, fornamn, efternamn, klass, kod, inriktning, paket1, paket2, kommentar, email, confirmed, year, inr_pak_id, name, kinfo


$stmt = $dbh->prepare($sql);
$stmt->bindParam(":year", $year);
$stmt->execute();

// TODO: På lång sikt ska denna lista hämtas ur DB också - eller?
$statistik = array(
    "design"     => 0,
    "it"         => 0,
    "produktion" => 0,
    "samhall"    => 0,
    "null"       => 0
);
$html = $cur_class = "";
$first_run = true;
while ( $dbrow = $stmt->fetch() ) {
    if ( $dbrow['klass'] !== $cur_class ) {
        if ( !$first_run ) {
            $html .= "</table>\n";
        }
        $first_run = false;
        $html .= "<h2>{$dbrow['klass']}</h2>\n";
        $html .= "<table>\n";
        $html .= <<<HTML
     <tr>
       <th>Namn</th>
       <th>Personnummmer</th>
       <th>Inriktning</th>
       <th>Skriv ut</th>
       <th>Tag bort val</th>
       <th>Bekräfta</th><!-- kan ej längre ångra -->
     </tr>
HTML;
    }
    $cur_class = $dbrow['klass'];
    $html .= <<<HTML
     <tr>
       <td>{$dbrow['fornamn']} {$dbrow['efternamn']}</th>
       <td>{$dbrow['personnummer']}</td>
       <td>{$dbrow['inr_name']}</th>
       <td><a href="val.php?kod={$dbrow['kod']}">{$dbrow['kod']}</a></td>
       <td><input type="checkbox" name="regret_{$dbrow['kod']}" value="{$dbrow['kod']}" /></td>
       <td><input type="checkbox" name="confirm_{$dbrow['kod']}" value="{$dbrow['kod']}" /></td>
     </tr>
HTML;
    if ( $dbrow['inriktning'] ) {
        $statistik[$dbrow['inriktning']]++;
    } else {
        $statistik['null']++;
    }
}
$html .= "</table>\n";

// Sidmall
?>
<!DOCTYPE html>
<html lang="sv">
 <head>
  <meta charset="utf-8" />
  <title>Administrera inriktnings- och fördjupningskursval på Teknikprogrammet, NE, <?php echo $year; ?></title>
  <link href="inr-val.css" rel="stylesheet" />
 </head>
 <body class="admin">
   <h1>Administrera inriktnings- och fördjupningskursval på Teknikprogrammet, NE, <?php echo $year; ?></h1>
   <form action="" method="post">
<?php echo $html; ?>
   </form>

   <h2>Statistik</h2>
   <p>TODO</p>

  </body>
</html>

<!-- Se annat år -->
