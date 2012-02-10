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

$stmt = $dbh->prepare("SELECT fornamn, efternamn, klass, kod FROM elever ORDER BY klass ASC, efternamn ASC");
$stmt->execute();

$data = "";
while ( $e = $stmt->fetch() ) {
    $data .= "<tr><td>{$e['fornamn']}</td><td>{$e['efternamn']}</td>" .
            "<td>{$e['klass']}</td><td>{$e['kod']}</td><tr>";
}

// Sidmall
?>
<!DOCTYPE html>
<html lang="sv">
 <head>
  <meta charset="utf-8" />
  <title>För utskrift: Personliga koder</title>
  <style>
    body {
        font-family: Calibri, sans-serif;
        font-size: 13pt;
    }
    table, td {
        padding: 0.7cm 0.2cm;
        border-top: 1px solid silver;
        border-bottom: 1px solid silver;
        border-collapse: collapse;
    }
    td:last-of-type {
        padding-left: 2cm;
    }
  </style>
 </head>
 <body>
   <h1>För utskrift: Personliga koder</h1>
   <table>
     <?php echo $data; ?>
   </table>
   <p>Fattas det någon? Är det något fel?</p>