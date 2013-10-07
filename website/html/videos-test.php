<?php
/**
 * Videos testsida, kräver inloggning
 * 
 * @author <gunther@keryx.se>
 * @version "Under construction 1"
 */

session_start();
require_once '../includes/loadfiles.php';

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

user::setSessionData();

user::requires(user::TEXTBOOK);

?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <title>Provsida med fler videos - webbteknik.nu</title>
  <link rel="stylesheet" href="css/webbteknik-nu.css" />
  <link href='http://fonts.googleapis.com/css?family=Handlee' rel='stylesheet' type='text/css'>
</head>
<body class="wide">
  <h1>webbteknik.nu &ndash; provsida med fler videos</h1>
  <?php require "../includes/snippets/mainmenu.php"; ?>
  <p>
    <strong>Tips!</strong> Högerklicka på videon och välj visning i helskärm.
    Videons inbyggda upplösning är 1280 x 720 pixlar.
  </p> 
  <h2>Videos kapitel 1</h2>
  <h3>Avsnitt 1.0 och 1.1: Ett enkelt HTML-dokument, del 1</h3>
  <p>
    <video controls class="fullsize">
      <source src="media/kap-1-a-1.webm" type="video/webm" />
      <source src="media/kap-1-a-1.mp4" type="video/mp4" />
    </video>
  </p>
  <h3>Avsnitt 1.0 och 1.1: Ett enkelt HTML-dokument, del 2</h3>
  <p>
    <video controls class="fullsize">
      <source src="media/kap-1-a-2.webm" type="video/webm" />
      <source src="media/kap-1-a-2.mp4" type="video/mp4" />
    </video>
  </p>
  <h3>Avsnitt 1.0 och 1.1: Värdet av doctype samt inspektera element i Firefox</h3>
  <p>
    <video controls class="fullsize">
      <source src="media/kap-1-a-3.webm" type="video/webm" />
      <source src="media/kap-1-a-3.mp4" type="video/mp4" />
    </video>
  </p>
  <h3>Bonusvideo: Mozilla Thimble</h3>
  <p>
    <video controls class="fullsize">
      <source src="media/thimble.webm" type="video/webm" />
      <source src="media/thimble.mp4" type="video/mp4" />
    </video>
  </p>
  <h3>Avsnitt 1.3: Validering</h3>
  <p>
    <video controls class="fullsize">
      <source src="media/kap-1-a-4.webm" type="video/webm" />
      <source src="media/kap-1-a-4.mp4" type="video/mp4" />
    </video>
  </p>
  <p><a href="./">Startsidan</a></p>
</body>
</html>
