<?php
/**
 * Returnerar bara basuppgifter för en elev som JSON
 */

//
error_reporting(E_ALL);
ini_set("display_errors", "on");

/**
 * Databasanslutning
 */
require_once("dbcx.php");
$dbh = dbcx();

$funnen = false;
$elev = "Ingen kod angiven";
if ( !empty($_GET['kod']) ) {
    // Rätt mönster för koder
    $ok_verified_pkod = "/^[a-z]{4}/";
    if ( !preg_match($ok_verified_pkod, $_GET['kod']) ) {
        $elev = "Ogiltigt kodmönster";
    } else {
        // Kolla att koden matchar en användare som faktiskt finns
        $stmt = $dbh->prepare("SELECT fornamn, efternamn, klass FROM elever WHERE kod = :kod");
        $stmt->bindParam(":kod", $_GET['kod']);
        $stmt->execute();
        $userdata = $stmt->fetch(PDO::FETCH_ASSOC);
        if ( empty($userdata) ) {
            $elev = "Felaktig kod, matchar ingen person i databasen";
        } else {
            $elev = "{$userdata['fornamn']} {$userdata['efternamn']}, {$userdata['klass']}";
            $funnen = true;
        }
    }
}
if ( !$funnen ) {
    header("HTTP/1.0 404 Not Found");
}
echo $elev;
 