<?php
/**
 * Assigments, temp page
 * 
 * @todo Buid from DB
 * @todo If a teacher has logged in, show links to finished solutions and "half way" solutions
 * 
 * @author <gunther@keryx.se>
 * @version "Under construction 1"
 * @license http://www.mozilla.org/MPL/
 * @package webbteknik.nu
 * 
 */

session_start();
require_once '../includes/loadfiles.php';

user::setSessionData();

user::requires(user::TEXTBOOK);

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);


// Preparing for mod_rewrite, set base-element
// TODO: Make this generic!
$baseref = dirname(htmlspecialchars($_SERVER['SCRIPT_NAME'])) . "/";
if ( "//" == $baseref ) {
    $baseref = "/";
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <title>Övningsfiler - webbteknik.nu</title>
  <base href="<?php echo $baseref; ?>" />
  <link rel="stylesheet" href="css/webbteknik-nu.css" />
  <link href='http://fonts.googleapis.com/css?family=Handlee' rel='stylesheet' type='text/css'>
</head>
<body class="wide">
  <h1>webbteknik.nu &ndash; Övningsfiler</h1>
  <?php require "../includes/snippets/mainmenu.php"; ?>
  <h2>Temporär sida med övningsfiler</h2>
  <p>
    En del uppgifter kan du göra på <b>JSBin</b> i stället för att ladd hem dem till din dator.
    Testa gärna både och för övningens skull!
  </p>
  <h3>Kapitel 4</h3>
  <ul class="filelist">
    <li>
      <a href="practice-files/kap-4-c.html">4C: kap-4-c.html</a>.<br />
      <a href="http://jsbin.com/utijuv/2/edit" target="_blank">4C på JSBin (öppnas i ny flik)</a>.
    </li>
    <li>
      <a href="practice-files/kap-4-e.html">4E kap-4-e.html</a> och 
      <a href="practice-files/kap-4-e.css">4E kap-4-e.css</a>. <br />
      <a href="http://jsbin.com/isuyar/3/edit" target="_blank">4E (HTML och CSS) på JSbin (öppnas i ny flik)</a>.
    </li>
  </ul>
  <h3>Kapitel 5</h3>
  <ul class="filelist">
    <li>
      <a href="practice-files/laxhjalpen/index.html">Start av index.html för Läxhjälpen</a>.
    </li>
    <li>
      <a href="practice-files/laxhjalpen/laxhjalpen.css">Start av laxhjalpen.css</a>.
    </li>
    <li>
      <a href="practice-files/laxhjalpen/images.zip">Alla bilder till läxhjälpen</a>.
    </li>
  </ul>
  <h3>Kapitel 9</h3>
  <ul class="filelist">
    <li>
      <a href="practice-files/kap-9-b.svg">9A SVG start</a>.<br />
      <a href="http://jsbin.com/imaquk/2/edit" target="_blank">9A på JSBin (öppnas i ny flik)</a>.
    </li>
  </ul>
  <h3>Kapitel 10</h3>
  <ul class="filelist">
    <li>
      <a href="practice-files/kap-10-a.html">kap-10-a.html</a> och
      <a href="practice-files/kap-10-a.css">kap-10-a.css</a>.<br />
      <a href="http://jsbin.com/ifikin/1/edit" target="_blank">10A på JSBin (öppnas i ny flik)</a>.
    </li>
    <li>
      <a href="practice-files/kap-10-b.html">kap-10-b.html</a> och
      <a href="practice-files/kap-10-b.css">kap-10-b.css</a>.<br />
      <a href="http://jsbin.com/itowem/6/edit" target="_blank">10B på JSBin (öppnas i ny flik)</a>.
    </li>
  </ul>
  <h3>Kapitel 11</h3>
  <ul class="filelist">
    <li>
      <a href="practice-files/laxhjalpen/om-oss.html">Start av om-oss.html för Läxhjälpen</a>.
    </li>
  </ul>
  
  <!-- TODO 
     13A Facit
     13B startfiler (kontaktformulär)
     
     14B Start av Nivo-slidern, med alla filer, inkl bilder

  <?php require "../includes/snippets/footer.php"; ?>
  <script src="script/videoreport.js"></script>
</body>
</html>
