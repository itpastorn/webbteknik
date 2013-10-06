<?php
/**
 * Startsidan på webbplatsen
 * 
 * @author <gunther@keryx.se>
 * 
 * @version "Under construction 2"
 */

session_start();
$_SESSION['not_empty'] = 1;

require_once '../includes/loadfiles.php';

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

user::setSessionData();

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
  <p class="newsflash">
    Nu kan du hämta <a href="webbserverprogrammering-provkapitel.pdf">provkapitel för
    boken Webbserverprogrammering 1</a> som ska komma ut i Augusti. I provkapitlen ingår
    en installationsguide för PHP och ett antal exempel som hjälper dig komma igång att
    testa webbserverprogrammering. Dessutom ser du vad som är planerat att ingå i boken
    i övrigt.
  </p>
  <h2>Errata</h2>
  <p>
    Nu finns det
    <a href="j200-4500-utg-1-errata.pdf">errata till första upplagan av Läroboken Webbutveckling 1</a>.
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
