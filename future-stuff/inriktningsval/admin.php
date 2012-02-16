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
    SELECT e.personnummer, e.fornamn, e.efternamn, e.klass, e.kod, e.inriktning, e.confirmed, ip.name AS inr_name FROM elever AS e
    LEFT JOIN inriktning_paket AS ip ON e.inriktning = ip.inr_pak_id
    WHERE e.year = :year
    ORDER BY e.klass ASC, e.efternamn ASC, e.fornamn ASC
SQL;
// personnummer, fornamn, efternamn, klass, kod, inriktning, paket1, paket2, kommentar, email, confirmed, year, inr_pak_id, name, kinfo


$stmt = $dbh->prepare($sql);
$stmt->bindParam(":year", $year);
$stmt->execute();

// TODO: På lång sikt ska denna lista hämtas ur DB också - eller?
// TODO: Statistik per klass
$statistik = array(
    "design"     => 0,
    "it"         => 0,
    "produktion" => 0,
    "samhall"    => 0,
    "inget"      => 0
);
$html = $cur_class = "";
$first_run = true;
$disabled = "";
if ( $_SESSION['privilegier'] == "read" ) {
    $disabled = 'disabled title="Kräver privilegier"';
}
while ( $dbrow = $stmt->fetch() ) {
    if ( empty($_GET['testing']) ) {
        if ( $dbrow['klass'] == "Te0F" ) {
            continue;
        }
    }
    if ( $dbrow['klass'] !== $cur_class ) {
        if ( !$first_run ) {
            $html .= "</table>\n";
        }
        $klasser[] = $dbrow['klass'];
        $first_run = false;
        $html .= "<h2 id='{$dbrow['klass']}'>{$dbrow['klass']}</h2>\n";
        $html .= "<table>\n";
        $html .= <<<HTML
     <tr>
       <th>Namn</th>
       <th>Personnummmer</th>
       <th>Inriktning</th>
       <th>Skriv ut</th>
       <th>Ångra val</th>
       <th>Bekräfta</th><!-- kan ej längre ångra -->
     </tr>

HTML;
    }
    $cur_class = $dbrow['klass'];
    if ( $dbrow['inriktning'] && $dbrow['confirmed'] ) {
        $confirm = "<span title=\"Bekräftad\">&#x2713;</span>"; // TODO Unicode
    } elseif ( $dbrow['inriktning'] ) {
        $confirm = "<input type='checkbox' name='confirm_{$dbrow['kod']}' value='{$dbrow['kod']}' $disabled />";
    } else {
        $confirm = "";
    }
    if ( !$dbrow['inriktning'] ) {
        $regret = "";
    } else {
        $regret = "<input type=\"checkbox\" name=\"regret_{$dbrow['kod']}\" value=\"{$dbrow['kod']}\" $disabled />";
    }
    $html .= <<<HTML
     <tr>
       <td>{$dbrow['fornamn']} {$dbrow['efternamn']}</th>
       <td>{$dbrow['personnummer']}</td>
       <td>{$dbrow['inr_name']}</th>
       <td><a href="val.php?kod={$dbrow['kod']}">{$dbrow['kod']}</a></td>
       <td>{$regret}</td>
       <td>{$confirm}</td>
     </tr>
HTML;
    if ( $dbrow['inriktning'] ) {
        $statistik[$dbrow['inriktning']]++;
    } else {
        $statistik['inget']++;
    }
}
$html .= "</table>\n";

$nav = "";
foreach ( $klasser as $k ) {
    $nav .= "<li><a href='#{$k}'>{$k}</a></li>\n";
}
$nav .= "<li><a href='#stat'>Statistik</a></li>\n";

// Skapa underlag för SVG-skapande JavaScript

$antal_som_inte_valt = array_pop($statistik);
$jsclasses = array_keys($statistik);
// Lambda nytt i PHP 5.3 - felaktigt error i Eclipse
// TODO: Kolla om jag kan använda inbyggda JSON-funktioner i PHP?
$jsclasses = array_map(function($s) {return '"' . $s . '"'; }, $jsclasses);
$jsclasses = "var classes = [" . join(",", $jsclasses) . "];\n";
$jsantalin = "var antalin = [" . join(",", $statistik) . "];\n";

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
   <ul class="sidnav">
     <?php echo $nav; ?>
   </ul>
   <form action="" method="post">
<?php echo $html; ?>
   </form>

   <h2 id="stat">Statistik</h2>
   <svg id="diagram1" viewbox="-150 -150 300 300" width="500px" height="500px">
   </svg>
   <p>Antal som ännu inte valt: <?php echo $antal_som_inte_valt; ?></p>
   <script src="piecharts.js"></script>
   <script>
   <?php
     echo $jsantalin;
     echo $jsclasses;
   ?>
   drawPieChart("diagram1", antalin, classes);
   </script>
   <?php if ( $_SESSION['privilegier'] != "read" ): ?>
   <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
   <script src="admin.js"></script>
   <?php endif; ?>
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
