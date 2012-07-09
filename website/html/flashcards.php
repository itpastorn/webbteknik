<?php
/**
 * System to show flashcards
 */

/*
 * WHERE set = :set
 * 
 * I flera set....?
 * 
 */
/*
$dbresult = array(
    array(
        'term'  => 'GPU',
        'short' => 'Graphics Processing Unit',
        'long'  => 'Grafikprocessor som gör att datorn kan rita komplexa 2D och 3D mönster, fonter (text) och genomskinliga effekter mycket snabbt.'
    ),
    array(
        'term'  => 'RAM',
        'short' => 'Random Access Memory',
        'long'  => 'Datorns arbetsminne/ primärminne. Mycket snabb. Flyktigt dvs när strömmen bryts tappas informationen. 1-16 GB på moderna datorer.'
    ),
    array(
        'term'  => 'CPU',
        'short' => 'Central Processing Unit',
        'long'  => 'Processorn. Utför beräkningar och kontrollerar dataflöden. Datorns <q>motor.</q>'
    ),
    array(
        'term'  => 'USB',
        'short' => 'Universal Serial Bus',
        'long'  => 'Gränssnitt (kontakt) för att ansluta kringutrustning som mus, tangentbord, lagringsenheter, skrivare och mobiltelefoner.'
    ),
    array(
        'term'  => 'SSD',
        'short' => 'Solid State Drive',
        'long'  => 'Flashminnesbaserad hårddisk, som har lägre latens (väntetid) än magnetiska diskar och drar mindre ström.'
    ),
    array(
        'term'  => 'GUI',
        'short' => 'Graphical User Interface',
        'long'  => 'Användarmiljö som bygger på muspekare, ikoner, fönster och menyer.',
    ),
    array(
        'term'  => 'CLI',
        'short' => 'Command Line Interface',
        'long'  => 'Användarmiljö som bygger på att kommandon skrivs i en texbaserad konsoll/terminal.'
    )
);
$sql  = "INSERT INTO flashcards VALUES (NULL, :term, :short, :long, 'test')";
$stmt = $dbh->prepare($sql);
$stmt->bindParam(":term", $term);
$stmt->bindParam(":short", $short);
$stmt->bindParam(":long", $long);

foreach ( $dbresult as $row ) {
    list($term, $short, $long) = array($row['term'], $row['short'], $row['long']);
    $stmt->execute();
}
exit;
*/
require '../keryxIncludes.ep/keryxDB/cx.php';
$GLOBALS['dsn']['phptype']  = 'mysql';
$GLOBALS['dsn']['hostspec'] = 'localhost';
$GLOBALS['dsn']['database'] = 'webbtek_webbtek';
$GLOBALS['dsn']['username'] = 'webbtek_webbtek';
$GLOBALS['dsn']['password'] = 'thule-is-valiant';
$GLOBALS['dsn']['charset']  = 'utf8';
$GLOBALS['dsn']['collate']  = 'utf8_swedish_ci';

$dbh  = keryxDB_cx::get();
// var_dump($_GET); exit;
$sql  = "SELECT * FROM flashcards WHERE setID = :set";
if ( empty($_GET['norand']) ) {
	$sql .= " ORDER BY RAND()";
} else {
    $sql .= " ORDER BY flashcardsID ASC";
}

$stmt = $dbh->prepare($sql);
$stmt->bindParam(":set", $set);

$set = isset($_GET['set']) ? $_GET['set'] : "wu1-1";
$stmt->execute();
$dbresult = $stmt->fetchAll(PDO::FETCH_ASSOC);

$list = '';
$i = 0;
foreach ( $dbresult as $row ) {
    if ( $i === 0 ) {
    	$class = ' class="activecard"';
    } elseif ( $i === 1 ) {
        $class = ' class="nextcard"';
    } else {
        $class = '';
    }
    $list .= "<dt{$class}>{$row['term']}</dt>\n";
    $list .= "<dd{$class}>{$row['short']}\n";
    if ( $row['long'] ) {
    	$list .= "<p>{$row['long']}</p>\n";
    }
    $list .= "</dd>\n";
    $i++;
}

?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <title lang="en">Flashcards</title>
  <link rel="stylesheet" href="css/webbteknik-nu.css" />
  <link href='http://fonts.googleapis.com/css?family=Handlee' rel='stylesheet' type='text/css'>
  <link rel="stylesheet" href="css/flashcards.css" />
</head>
<body>
  <h1>Flashcards hjälper dig öva in termer</h1>
  <?php require "../includes/snippets/mainmenu.php"; ?>
  <div id="explain">
    <p>Klicka på kortet för att vända det eller tryck på mellanslagstangenten.</p>
    <p>Kräver  Firefox 10 eller senare, Chrome eller Safari.</p>
    <p>Keyboard support:</p>
    <dl>
      <dt>Mellanslag</dt>
      <dd>Vänd kort</dd>
      <dt>Vänster pil</dt>
      <dd>Föregåede kort</dd>
      <dt>Höger pil</dt>
      <dd>Nästa kort</dd>
    </dl>
  </div>
  <dl class="flashcards" id="flashcards">
<?php
echo $list;
?>
  </dl>
  <p id="navigationbuttons">
    <button id="goto_prevcard" disabled>Föregående</button>
    <span id="curnum">1</span>/<span id="totnum"><?php echo count($dbresult); ?></span>
    <button id="goto_nextcard">Nästa</button>
  </p>
  <script src="flashcards.js"></script>
  <!-- 
  TODO major items:
  * Support MS and Opera
  * A nice design...
    - Have the dt text expand and fill the whole Flash-card - adapting the size dependong on length of content
    - Color 
    - Font
    - Images
  * Numbering items
    - Show number in lower right corner
    - Toggle URL hash (or push state)
    - Go to specific item depending on URL hash
  * Show full list and goto specific item
  * Mobile design:
    - Fullscreen
    - Gestures for forward and back
    - Offline storage
    - Portrait toggle = View header and full list (only DT) + kinetic scroll
    - Landscape toggle = View individual Flash card
  * Presentation mode for classrooms
  * PHP backend
  * Admin interface to add/edit items and complete lists
   -->
</body>
</html>
