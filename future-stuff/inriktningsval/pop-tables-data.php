<?php
/*
 * Skapa tabeller och data för inriktnings- och paketval
 *
 * @author Lars Gunther <gunther@keryx.se>
 */

error_reporting(E_ALL);
ini_set("display_errors", "on");

/**
 * Databasanslutning
 */
require_once("dbcx.php");
$dbh = dbcx();

$kurser = '{
    "arkark0"  : "Arkitektur hus",
    "bilbil01a1" : "Bild och form 1a1",
    "bilbil01a2" : "Bild och form 1a2",
    "cadcad01" : "CAD 1",
    "cadcad02" : "CAD 2",
    "daodat01a" : "Datorteknik 1a",
    "dardat01" : "Datorstyrd produktion 1",
    "desdes01" : "Design 1",
    "fysfys02" : "Fysik 2",
    "grägrä0" : "Gränssnittsdesign",
    "hålhåb0" : "Hållbart samhällsbyggande",
    "hålmij0"  : "Miljö och energikunskap",
    "kotkos01" : "Konstruktion 1",
    "matmat04" : "Matematik 4",
    "mekmek01" : "Mekatronik 1",
    "prdpro01" : "Produktionskunskap 1",
    "pruprd01s" : "Produktionsutrustning 1",
    "pruprd02s" : "Produktionsutrustning 2",
    "prrprr01" : "Programmering 1",
    "prrprr02" : "Programmering 2",
    "webweu01" : "Webbutveckling 1",
    "webweu02" : "Webbutveckling 2",
    "webweb01" : "Webbserverprogrammering 1"
}';
/*
$kurser = (json_decode($kurser, true));
$sql  = "INSERT INTO kurser (kurskod, kursnamn) VALUES (:kurskod, :kursnamn)";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(":kurskod", $kurskod);
$stmt->bindParam(":kursnamn", $kursnamn);
foreach ( $kurser as $kurskod => $kursnamn ) {
    $stmt->execute();
}
*/
$inriktningar = '{
    "design" : {
        "name"    : "Design och produktutveckling",
        "blockar" : ["des1"],
        "passar4" : "prod1",
        "typ"     : "design",
        "kurser"  : ["bilbil01a1", "cadcad01", "desdes01", "kotkos01"]
    },
    "produktion" : {
        "name"    : "Produktionsteknik",
        "blockar" : ["prod1"],
        "passar4" : "des1",
        "typ"     : "produktion",
        "kurser"  : ["mekmek01", "prdpro01", "pruprd01s"]
    },
    "it" : {
        "name"    : "IT och media",
        "blockar" : [],
        "passar4" : "it1",
        "typ"     : "it",
        "kurser"  : ["webweu01", "prrprr01", "daodat01a"]
    },
    "samhall" : {
        "name" : "Samhällsbyggande och miljö",
        "blockar" : ["ark1", "sam2"],
        "passar4" : null,
        "typ" : "samhall",
        "kurser" : ["arkark0", "hålhåb0", "hålmij0"]
    }
}';
/*
echo "<pre>";
$inr = json_decode($inriktningar, true);
$sql  = "INSERT INTO inriktning_paket (inr_pak_ID, name, passar4, typ, json_modul)
         VALUES (:inr_pak_ID, :name, :passar4, :typ, :json_modul)";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(":inr_pak_ID", $inr_pak_ID);
$stmt->bindParam(":name", $name);
$stmt->bindParam(":passar4", $passar4);
$stmt->bindParam(":typ", $typ);
$stmt->bindParam(":json_modul", $json_modul);
foreach ( $inr as $inr_pak_ID => $inr_pak_data ) {
    $name       = $inr_pak_data['name'];
    $passar4    = $inr_pak_data['passar4'];
    $typ        = $inr_pak_data['typ'];
    $json_modul = 'inriktningar';
    $stmt->execute();
}
*/
$paket1 = '{
    "prod1"  : {
        "kurser" : ["prdpro01", "mekmek01"],
        "req"    : null,
        "typ"  : "produktion"
    },
    "it1"    : {
        "kurser" : ["grägrä0", "webweb01"],
        "req"    : "it",
        "typ"    : "it"
    },
    "ark1"   : {
        "kurser" : ["arkark0", "cadcad02", "bilbil01a2"],
        "req"    : "design",
        "typ"    : "design"
    },
    "des1"   : {
        "kurser" : ["kotkos01", "cadcad01", "bilbil01a1"],
        "req"    : null,
        "typ"    : "design"
    }
}';
/*
$inr = json_decode($paket1, true);
$sql  = "INSERT INTO inriktning_paket (inr_pak_ID, req, typ, json_modul)
         VALUES (:inr_pak_ID, :req, :typ, :json_modul)";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(":inr_pak_ID", $inr_pak_ID);
$stmt->bindParam(":req", $req);
$stmt->bindParam(":typ", $typ);
$stmt->bindParam(":json_modul", $json_modul);
foreach ( $inr as $inr_pak_ID => $inr_pak_data ) {
    $req        = $inr_pak_data['req'];
    $typ        = $inr_pak_data['typ'];
    $json_modul = 'paket1';
    $stmt->execute();
}
exit("ok");
*/
$paket2 = '{
        "civing" : {
            "kurser" : ["matmat04", "fysfys02"],
            "req"    : null,
            "typ"    : "civing"
        },
        "it2" : {
            "kurser" : ["prrprr02", "webweu02"],
            "req"    : "it",
            "typ"    : "it"
        },
        "prod2" : {
            "kurser" : ["pruprd02s", "dardat01"],
            "req"    : "produktion",
            "typ"    : "produktion"
        },
        "sam2" : {
            "kurser" : ["hålhåb0", "hålmij0"],
            "req"    : null,
            "typ"    : "samhall"
        }
}';
$inr = json_decode($paket2, true);
$sql  = "INSERT INTO inriktning_paket (inr_pak_ID, req, typ, json_modul)
         VALUES (:inr_pak_ID, :req, :typ, :json_modul)";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(":inr_pak_ID", $inr_pak_ID);
$stmt->bindParam(":req", $req);
$stmt->bindParam(":typ", $typ);
$stmt->bindParam(":json_modul", $json_modul);
foreach ( $inr as $inr_pak_ID => $inr_pak_data ) {
    $req        = $inr_pak_data['req'];
    $typ        = $inr_pak_data['typ'];
    $json_modul = 'paket2';
    $stmt->execute();
}
exit("ok");



?>
<h1>SQL för tabellerna</h1>

<pre><code>
CREATE TABLE IF NOT EXISTS `kurser` (
  `kurskod` varchar(10) COLLATE utf8_swedish_ci NOT NULL,
  `kursnamn` varchar(150) COLLATE utf8_swedish_ci NOT NULL,
  PRIMARY KEY (`kurskod`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_swedish_ci;


</code></pre>