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


$sql  = "INSERT INTO elever (personnummer, fornamn, efternamn, klass, kod, year) ";
$sql .= "VALUES (:personnummer, :fornamn, :efternamn, :klass, :kod, :year)";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(":personnummer", $personnummer);
$stmt->bindParam(":fornamn", $fornamn);
$stmt->bindParam(":efternamn", $efternamn);
$stmt->bindParam(":klass", $klass);
$stmt->bindParam(":kod", $kod);
$stmt->bindParam(":year", $year);

date_default_timezone_set("Europe/Stockholm");
$year = date("Y");

foreach ( $elever as $e ) {
    // skapa kod
    
    // skapa variabler me list()
    
}
