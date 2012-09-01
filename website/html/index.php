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

$stattypes = array('Videos', 'Länkar', 'Uppgifter', 'Flashcards', 'Användare', 'Grupper');

$sql = <<<SQL
    SELECT count(videoname) AS num FROM videos
    UNION
    SELECT count(linkID) AS num FROM links
    UNION
    SELECT count(joblistID) AS num FROM joblist
    UNION
    SELECT count(flashcardID) AS num FROM flashcards
    UNION 
    SELECT count(*) AS num FROM users
    UNION 
    SELECT count(*) AS num FROM groups
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
  <?php require "../includes/snippets/dochead.php"; ?>
</head>
<body>
  <h1>webbteknik.nu</h1>
  <?php require "../includes/snippets/mainmenu.php"; ?>
  <p>
    Hej. Roligt att du är intresserad av
    <a href="http://www.skolportalen.se/laromedel/produkt/J200%204500/Webbutveckling%201%20-%20L%C3%A4robok/">vårt
    läromedel</a>.
    Just nu arbetar jag (Lars Gunther) på att göra den här webbplatsen användbar (sedan ska den bli snyggare).
  </p>
  <p>
    <strong>Om du <a href="http://webbteknik.nu/sign-in.php">registrerar dig</a>, så kommer du
    få meddelande när det sker uppdateringar.</strong> Du kan då också se fler videos.
  </p>
  <h2>Uppdateringsstatus</h2>
  <p>2012-08-30: Övningsfiler finns nu på sin egen sida.</p>
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
  <h2>Webbserverprogrammering</h2>
  <p>
    Under läsåret kommer jag ta fram ett läromedel i Webbserverprogrammering.
    Vill du ta del av delar av det innan det är klart, så hör av dig till
    gunther {at} keryx punkt se. Här kommer ett smakprov:
  </p>
  <p class="centered">
    <iframe src="http://www.youtube.com/embed/M7BXMfYbFwg"></iframe>
  </p>
  <!--video class="halfsize" controls tabindex="0">
    <source type="video/webm" src="media/intro-xampp.webm"></source>
    <source type="video/mp4" src="media/intro-xampp.mp4"></source>
  </video -->
  <p class="centered sign">Lars Gunther</p>
  <p class="centered">
    <a href="http://www.skolportalen.se/"><img src="img/skolportalen-logo-408-111.png" 
      width="408" height="111" alt="Thelin Läromedel, Skolportalen" /></a>
  </p>
</body>
</html>
