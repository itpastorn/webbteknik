<?php
/**
 * Resources, page controler
 * 
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

// Type of resource
$r_types =  array(
    'flashcards' => 'flashcards',
    'links'      => 'länkar',
    'videos'      => 'videos'
);

$c_type = filter_input(INPUT_GET, 'what', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);

if ( empty($r_types[$c_type]) ) {
    // Resource type not available
    // TEMPORARY fix
    header("HTTP 1.1 403 Not Found");
    echo "<h1>Resurstypen finns inte</h1>";
    var_dump($_GET);
    exit;
   
}

// @improv - smarter paradign than switch
switch ( $c_type ) {
	case "flashcards":
        include "data/flashcards.php";
        $resources      = data_flashcards::loadAll($dbh, false, array('privileges' => $_SESSION['userdata']->privileges));
        $resource_table = data_flashcards::makeTable($resources);
        break;
	case "links":
        include "data/links.php";
        $resources      = data_links::loadAll($dbh, false, array('privileges' => $_SESSION['userdata']->privileges));
        $resource_table = data_links::makeTable($resources);
        break;
	case "videos":
        include "data/videos.php";
        $resources      = data_videos::loadAll($dbh, false, array('privileges' => $_SESSION['userdata']->privileges));
        $resource_table = data_videos::makeTable($resources);
        break;
    default:
        exit("You really never, ever should see this error...");
    
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
  <title><?php echo ucfirst($r_types[$c_type]); ?> - webbteknik.nu</title>
  <?php require "../includes/snippets/dochead.php"; ?>
</head>
<body class="wide">
  <h1>webbteknik.nu &ndash; <?php echo ucfirst($r_types[$c_type]); ?></h1>
  <?php require "../includes/snippets/mainmenu.php"; ?>

<?php
echo $resource_table;
?>

  <?php require "../includes/snippets/footer.php"; ?>
</body>
</html>
