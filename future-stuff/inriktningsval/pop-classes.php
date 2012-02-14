<?php
/**
 * Lägg in elever i DB
 */

error_reporting(E_ALL);
ini_set("display_errors", "on");

/**
 * Databasanslutning
 */
require_once("dbcx.php");
$dbh = dbcx();

$elever = file("te1b-2012.cvs");
$elever = array_merge(file("te1a-2012.cvs"), $elever);

// echo "<pre>";
// var_dump($elever);
// klass,efternamn, fornamn,personnummer


$sql  = "INSERT IGNORE INTO elever (personnummer, fornamn, efternamn, klass, kod, year) ";
$sql .= "VALUES (:personnummer, :fornamn, :efternamn, :klass, :kod, :year)";
// $sql .= "ON DUPLICATE KEY UPDATE year = :year";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(":personnummer", $personnummer);
$stmt->bindParam(":fornamn", $fornamn);
$stmt->bindParam(":efternamn", $efternamn);
$stmt->bindParam(":klass", $klass);
$stmt->bindParam(":kod", $kod);
$stmt->bindParam(":year", $year);

date_default_timezone_set("Europe/Stockholm");
$year = date("Y");

// Skapa slumpmässig kod
function gkod() {
    // Ineffektivt med upprepning varje gång med eftersom max 100 elever läggs in gör det inget
    // 2012 sa alla koder börja med a, 2013 med b, o.s.v.
    $varv     = date("Y") - 2012;
    $kodstart = "a";
    for ($i = 0; $i < $varv; $i++) {
        $kodstart++;
    }
    $chars   = "abcdefghijkmnpqrstvxyz"; // Urval gjort med användbarhet i åtanke (nolla förväxlas med o, etc)
    $charlen = strlen($chars) - 1; // Bara ASCII så strlen OK
    $kod = $kodstart . $chars[rand(0, $charlen)] . $chars[rand(0, $charlen)] . $chars[rand(0, $charlen)];
    // Mycket liten chans för dubletter...
    static $alla_koder;
    if ( in_array($kod, (array)$alla_koder) ) {
        $kod = gkod();
    }
    $alla_koder[] = $kod;
    return $kod;
}

foreach ( $elever as $e ) {
    $kod = gkod();
    // skapa variabler med list()
    list($klass, $efternamn, $fornamn, $personnummer) = explode(",", $e);
    $klass        = trim($klass);
    $efternamn    = trim($personnummer);
    $fornamn      = trim($personnummer);
    $personnummer = trim($personnummer);
    $stmt->execute();
    // UPDATE elever SET fornamn=TRIM(fornamn) WHERE fornamn <> TRIM(fornamn)
}

echo "Uppgifter lagrade!";
