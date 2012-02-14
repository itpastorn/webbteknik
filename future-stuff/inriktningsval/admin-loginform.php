<?php
/*
 * Lärarnas inloggning
 *
 * TODO: HTML5 options för lararkod?
 */

error_reporting(E_ALL);
ini_set("display_errors", "on");

// Inloggad?
session_start();

// Redan inloggad?
if ( !empty($_SESSION['privileges']) ) {
    header("Location: admin.php");
}

/**
 * Databasanslutning
 */
require_once("dbcx.php");
$dbh = dbcx();

// Datumfunktioner
// date_default_timezone_set("Europe/Stockholm");

$errormsg = "";
if ( !empty($_POST['lararkod']) ) {
    $sql = "SELECT lararkod, password, privilegier FROM admins WHERE lararkod = :lararkod";
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam("lararkod", $lk);
    // TODO: Indatafiltrering
    $lk = $_POST['lararkod'];
    $stmt->execute();
    $dbrow = $stmt->fetch();
    if (crypt($_POST['password'], $dbrow['password']) == $dbrow['password']) {
        $_SESSION['privilegier'] = $dbrow['privilegier'];
        $_SESSION['lararkod']   = $dbrow['lararkod'];
        header("Location: admin.php");
        exit;
    }
    $errormsg = "Felaktigt inloggningsnamn/lösenord";
}

?>
<!DOCTYPE html>
<html lang="sv">
 <head>
  <meta charset="utf-8" />
  <title>Login: Administrera inriktnings- och fördjupningskursval på Teknikprogrammet, NE</title>
  <link href="inr-val.css" rel="stylesheet" />
 </head>
 <body class="admin">
  <h1>Login: Administrera inriktnings- och fördjupningskursval på Teknikprogrammet, NE</h1>
  <form method="post">
    <p class="error"><?php echo $errormsg; ?></p>
    <p><label for="lararkod">Inloggningsnamn</label><input type="text" id="lararkod" name="lararkod" /></p>
    <p><label for="password">Lösenord</label><input type="password" id="password" name="password" /></p>
    <p><input type="submit" value="Logga in" /></p>
  </form>
 </body>
</html>
