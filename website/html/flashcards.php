<?php
/**
 * System to show flashcards
 */

session_start();
require_once '../includes/loadfiles.php';

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

user::setSessionData();

user::requires(user::TEXTBOOK);

/*
 * WHERE set = :set
 * 
 * I flera set....?
 * 
 */

$sql  = <<<SQL
    SELECT fc.*, fs.*, books.booktitle FROM flashcards AS fc
    NATURAL JOIN flashcardsets AS fs
    LEFT JOIN books ON ( fs.bookID = books.bookID )
    WHERE fc.setID = :set
SQL;
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
$i    = 0;
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

if ( 0 == $i ) {
    // empty set
    $setinfo = "Tomt set = inga sådana flashcards finns.";
    $list    = '';
} else {
	if (  empty($row['booktitle']) ) {
	    $btitle = '';
	} else {
	    $btitle = " för boken {$row['booktitle']}";
	}
    $setinfo = "{$row['setname']}{$btitle}";
}

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
  <title lang="en">Flashcards</title>
  <?php require "../includes/snippets/dochead.php"; ?>
  <link rel="stylesheet" href="css/flashcards.css" />
</head>
<body>
  <h1>Flashcards hjälper dig öva in termer</h1>
  <?php require "../includes/snippets/mainmenu.php"; ?>
  <h2><?php echo $setinfo; ?></h2>
  <div class="usertip" data-tipname="explainFlashcards">
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
  <p>
    <a href="resources/flashcards/">Alla flashcards</a>
  </p>
  <?php require "../includes/snippets/footer.php"; ?>
  <script src="./script/flashcards.js"></script>
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
