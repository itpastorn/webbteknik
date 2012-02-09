<?php
/*
 * Skriv ut koder
 *
 * @author Lars Gunther <gunther@keryx.se>
 */

error_reporting(E_ALL);
ini_set("display_errors", "on");

// Inloggad?

/**
 * Databasanslutning
 */
require_once("dbcx.php");
$dbh = dbcx();

// Borde det finnas en "vad är min kod" funktion? JA - via mejl!

$stmt = $dbh->prepare("SELECT fornamn, efternamn, klass, kod FROM elever ORDER BY klass ASC efternamn ASC");

