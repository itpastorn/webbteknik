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

// Total -a/b klass - flickor/pojkar
$statistik = $stat['a'] = $stat['b'] = $stat['f'] = $stat['p'] = array(
    "design"     => 0,
    "it"         => 0,
    "produktion" => 0,
    "samhall"    => 0
);
$statistik["inget"] = 0;

$classtable = $cur_class = "";
$first_run  = true;
$disabled   = "";
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
            $classtable .= "</table>\n";
        }
        $klasser[] = $dbrow['klass'];
        $first_run = false;
        $classtable .= "<h2 id='{$dbrow['klass']}'>{$dbrow['klass']}</h2>\n";
        $classtable .= "<table class=\"classtab\">\n";
        $classtable .= <<<HTML
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
    $classtable .= <<<HTML
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
        $inr = $dbrow['inriktning'];
        $statistik[$inr]++;
        $ki = strtolower(substr($dbrow['klass'], -1));
        $stat[$ki][$inr]++;
        $gender = substr($dbrow['personnummer'], -2, 1) % 2 ? "p" : "f";
        $stat[$gender][$inr]++;
    } else {
        $statistik['inget']++;
    }
}
$classtable .= "</table>\n";
// var_dump($stat);
$nav = "";
foreach ( $klasser as $k ) {
    $nav .= "<li><a href='#{$k}'>{$k}</a></li>\n";
}
$nav .= "<li><a href='#stat'>Statistik</a></li>\n";

$stmt = $dbh->query("SELECT inr_pak_ID, name FROM inriktning_paket WHERE name IS NOT NULL");
while ( $dbrow = $stmt->fetch() ) {
    $inr_names[$dbrow['inr_pak_ID']] = $dbrow['name'];
}

$stattab = "";
$jsclasses = array_keys($statistik);
array_pop($jsclasses);
foreach ( $jsclasses as $c ) {
    $stattab .= "<tr>\n";
    $stattab .= "<th scope=\"row\">{$inr_names[$c]}</th>\n";
    foreach ( array("a", "b", "f", "p") as $k ) {
        $stattab .= "<td>{$stat[$k][$c]}</td>\n";
    }
    $stattab .= "<td>{$statistik[$c]}</td>\n";
    $stattab .= "</tr>\n";
}
/*
SELECT inriktning, paket1 AS paket, COUNT(*) AS antal FROM elever
WHERE `klass` <> 'Te0F' AND paket1 IS NOT NULL GROUP BY inriktning, paket1
UNION
SELECT inriktning, paket2 AS paket, COUNT(*) AS antal FROM elever
WHERE `klass` <> 'Te0F' AND paket2 IS NOT NULL GROUP BY inriktning, paket2

*/
// Skapa underlag för SVG-skapande JavaScript
$antal_som_inte_valt = array_pop($statistik);
// Lambda nytt i PHP 5.3 - felaktigt error i Eclipse
// TODO: Kolla om jag kan använda inbyggda JSON-funktioner i PHP?
$jsclasses    = array_map(function($s) {return '"' . $s . '"'; }, $jsclasses);
$jsclasses    = "var classes = [" . join(",", $jsclasses) . "];\n";
$jsantalin    = "var antalin = [" . join(",", $statistik) . "];\n";
$jsantal['a'] = "var antal_a = [" . join(",", $stat['a']) . "];\n";
$jsantal['b'] = "var antal_b = [" . join(",", $stat['b']) . "];\n";
$jsantal['f'] = "var antal_f = [" . join(",", $stat['f']) . "];\n";
$jsantal['p'] = "var antal_p = [" . join(",", $stat['p']) . "];\n";

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
<?php echo $classtable; ?>
   </form>

   <h2 id="stat">Statistik</h2>
   <svg id="Total"   viewbox="-150 -165 300 315"></svg>
   <svg id="Te3A"    viewbox="-150 -165 300 315"></svg>
   <svg id="Te3B"    viewbox="-150 -165 300 315"></svg>
   <svg id="Flickor" viewbox="-150 -165 300 315"></svg>
   <svg id="Pojkar"  viewbox="-150 -165 300 315"></svg>
   <p>Antal som ännu inte valt: <?php echo $antal_som_inte_valt; ?></p>
   <table class="statistik">
     <tr><th>Inriktning</th><th>Te3A</th><th>Te3B</th><th>Flickor</th><th>Pojkar</th><th>Total</th></tr>
     <?php echo $stattab; ?>
   </table>
   <script src="piecharts.js"></script>
   <script>
   <?php
     echo $jsantalin;
     echo $jsantal['a'];
     echo $jsantal['b'];
     echo $jsantal['f'];
     echo $jsantal['p'];
     echo $jsclasses;
   ?>
   drawPieChart("Total", antalin, classes);
   drawPieChart("Te3A", antal_a, classes);
   drawPieChart("Te3B", antal_b, classes);
   drawPieChart("Flickor", antal_f, classes);
   drawPieChart("Pojkar", antal_p, classes);
   </script>
   <?php if ( $_SESSION['privilegier'] != "read" ): ?>
   <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
   <script src="admin.js"></script>
   <?php endif; // var_dump($stat); ?>
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
