<?php
/*
 * Administrera inriktnings- och paketval - hantera Ajaxanrop
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
    // TODO: header
    echo "Ej inloggad";
    exit;
}
if ( $_SESSION['privilegier'] == "read" ) {
    // TODO: header
    echo "Saknar privilegier";
    exit;
}

// Datumfunktioner
date_default_timezone_set("Europe/Stockholm");

/**
 * Databasanslutning
 */
require_once("dbcx.php");
$dbh = dbcx();

$vad = explode('_', $_GET['vad']);

if ( $vad[0] == "regret" ) {
    // Eleven ångrar sitt val
    $sql = "UPDATE elever SET inriktning=NULL, paket1=NULL, paket2=NULL, confirmed=NULL WHERE kod=:kod";
} elseif  ( $vad[0] == "confirm" ) {
    // Dubbelkolla att val gjorts
    $sql = "SELECT inriktning FROM elever WHERE kod=:kod";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(":kod", $vad[1]);
    $stmt->execute();
    $ok = $stmt->fetchColumn();
    if ( empty($ok) ) {
        echo $vad[0] . ":" . $vad[1] . ":0";
        exit;
    }
    // Bekräfta elevens val
    $sql = "UPDATE elever SET confirmed=NOW() WHERE kod=:kod";
}

$stmt = $dbh->prepare($sql);
$stmt->bindParam(":kod", $vad[1]);
$ok = $stmt->execute();
if ( $ok ) {
    $ok = $stmt->rowCount();
}
$ok = (int)$ok;

// Vad har gjorts + för vem + status 
echo $vad[0] . ":" . $vad[1] . ":". $ok;
