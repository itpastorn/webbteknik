<?php
/**
 * Startsidan på webbplatsen
 * 
 * @author <gunther@keryx.se>
 * 
 * @version "Under construction 2"
 */

session_start();
require_once '../includes/loadfiles.php';

user::setSessionData();

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

$stattypes = array('Videos', 'Länkar', 'Uppgifter', 'Flashcards');

$sql = <<<SQL
    SELECT count(videoname) AS num FROM videos
    UNION
    SELECT count(linkID) AS num FROM links
    UNION
    SELECT count(joblistID) AS num FROM joblist
    UNION
    SELECT count(flashcardID) AS num FROM flashcards
SQL;
$status = "<ul>\n";
$i = 0;
$stmt = $dbh->prepare($sql);
$stmt->execute();
$numbers = $stmt->fetchAll();
foreach ( $numbers as $num ) {
    $status .= "<li>{$stattypes[$i++]}: {$num['num']}</li>\n";
}
$status .= "</ul>\n";

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>webbteknik.nu</title>
  <link rel="stylesheet" href="css/webbteknik-nu.css" />
  <link href='http://fonts.googleapis.com/css?family=Handlee' rel='stylesheet' type='text/css'>
</head>
<body>
  <h1>webbteknik.nu</h1>
  <?php require "../includes/snippets/mainmenu.php"; ?>
  <p>
    Hej. Roligt att du är intresserad av
    <a href="http://www.skolportalen.se/laromedel/produkt/J200%204500/Webbutveckling%201%20-%20L%C3%A4robok/">vårt
    läromedel</a>.
    Just nu arbetar jag (Lars Gunther) på att göra den här webbplatsen användbar.
  </p>
  <p>
    <strong>Om du <a href="http://webbteknik.nu/sign-in.php">registrerar dig</a>, så kommer du
    få meddelande när det sker uppdateringar.</strong> Du kan då också se fler videos.
  </p>
  <p>
    Här finns <a href="laxhjalpen-demowebb/">Läxhjälpen &ndash; bokens demowebbplats</a>.
  </p>
  <h2>Uppdateringsstatus</h2>
  <?php echo $status; ?>
  <h2>Smakprov på en video</h2>
  <p class="centered">
    <iframe src="http://www.youtube.com/embed/JdJA9w-vyJY"></iframe>
  </p>
  <h2>Smakprov på Flashcards</h2>
  <p>
    <a href="flashcards.php?set=wu1-1">Flashcards med ord från kapitel 1</a>. Det kommer finnas
    en eller ett par såda uppsättningar flashcards per kapitel. Dessutom en version anpassad för mobiler.
  </p>
  <h2>Information</h2>
  <p>Elevresurser som vi planerar för denna webbplats:</p>
  <ul>
    <li>Videos</li>
    <li>Demokod</li>
    <li>Länkar till mer information</li>
    <li>Interaktiva demos</li>
    <li>Flashcards</li>
  </ul>
  <p>Lärarresurser som vi planerar för denna webbplats:</p>
  <ul>
    <li>Slides (tänk PowerPoint fast online)</li>
    <li>Utskriftsversioner av material för rollspel</li>
  </ul>
  <p class="centered sign">Lars Gunther</p>
  <p class="centered">
    <a href="http://www.skolportalen.se/"><img src="img/skolportalen-logo-408-111.png" 
      width="408" height="111" alt="Thelin Läromedel, Skolportalen" /></a>
  </p>
</body>
</html>
