<?php
/**
 * User start page, requires log in
 * 
 * @author <gunther@keryx.se>
 * @version "Under construction 1"
 */

session_start();
require_once '../includes/loadfiles.php';

user::setSessionData();

user::requires(user::TEXTBOOK);

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

// TODO: Specific video can be set using get-param

if ( isset($_GET['video']) ) {
    // TODO filter, but prepared statements should catch any SQL-injection attempt
    $sql = <<<SQL
        SELECT v.*, up.progressdata, up.percentage_complete, up.status FROM videos AS v 
        LEFT JOIN userprogress AS up
        ON (up.resourceID = v.videoname)
        WHERE v.videoname = :video AND ( up.email = :email or up.email IS NULL )
        ORDER BY v.order ASC
SQL;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':video', $_GET['video']);
} else {
    // Find next unseen video for user
    $sql = <<<SQL
        SELECT v.*, up.progressdata, up.percentage_complete, up.status FROM videos AS v 
        LEFT JOIN userprogress AS up
        ON (up.resourceID = v.videoname)
        WHERE up.email = :email AND up.tablename = 'videos' AND up.status = 'begun' 
              OR up.email IS NULL
        ORDER BY v.order ASC
SQL;
    $stmt = $dbh->prepare($sql);
}

$stmt->bindParam(':email', $_SESSION['user']);
$stmt->execute();

$videos = $stmt->fetchAll();

if ( $videos ) {
    $curvid = $videos[0];
    
    $progressdata = new stdClass();
    $progressdata->firstStop = 0;
    $progressdata->stops = array();
    $progressdata->viewTotal = 0;
    if ( !empty($curvid['status']) ) {
        $progressdata = json_decode($curvid['progressdata']);
    }
} else {
    $curvid       = array('title' => 'Video ej funnen');
    $progressdata = "";
    // TODO NOT FOUND header
}
if ( !isset($curvid['status']) ) {
    $curvid['status'] = "unset";
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <title>Användarsida - webbteknik.nu</title>
  <link rel="stylesheet" href="css/webbteknik-nu.css" />
  <link href='http://fonts.googleapis.com/css?family=Handlee' rel='stylesheet' type='text/css'>
</head>
<body class="wide">
  <h1>webbteknik.nu &ndash; Användarsida</h1>
  <?php require "../includes/snippets/mainmenu.php"; ?>
  <h3>Video: <?php echo $curvid['title']; ?></h3>
  <p>
    <strong>Tips!</strong> Högerklicka på videon och välj visning i helskärm.
    Videons inbyggda upplösning är 1280 x 720 pixlar. (TODO: Dölj tips)
  </p> 
  <div id="videocontainer">
  <?php if ( isset($curvid['videoname']) ): ?>
    <video controls class="halfsize">
      <source src="media/<?php echo $curvid['videoname']; ?>.webm" type="video/webm" />
      <source src="media/<?php echo $curvid['videoname']; ?>.mp4" type="video/mp4" />
    </video>
  <?php else: ?>
    <div class="video_not_found halfsize">
      <p>Videon du sökte finns inte.</p>
      <ul>
        <li>Snart kommer</li>
        <li>en lista</li>
        <li>med förslag</li><!-- TODO -->
      </ul>
    </div>
  <?php endif; ?>
  </div>
  <div id="videobuttons">
    <button id="skipvid" disabled>Markera videon <br /> som <b>sedd</b></button>
    <button id="unskipvid" disabled>Markera videon <br /> som <b>osedd</b></button>
    <button id="nextvideo" disabled>Gå till <br /> <b>nästa</b> video</button>
    <button id="prevvideo" disabled>Gå till <br /> <b>föregående</b> video</button>
  </div>
  <p id="vidprogress">Status för denna video: </p><!-- TODO Clickable progress bar with timeranges converted to graphics -->
  <section id="resource_suggestions">
    <div>Flashcards (todo)</div>
    <div>Länkar (todo)</div>
    <div>Filer (todo)</div>
    <div>Förslag (todo)</div>
  </section>
  <script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
  <script>
     var wtglobal_start_video_at   = <?php
         if ( isset($progressdata->firstStop) ) { echo $progressdata->firstStop - 2; }
         else { echo 0; }; ?>;
     var wtglobal_old_progressdata = <?php
         if ( isset($curvid['progressdata']) ) { echo $curvid['progressdata']; }
         else { echo 0; }; ?>;
     var wtglobal_old_status       = "<?php echo $curvid['status']; ?>";
  </script>
  <script src="script/videoreport.js"></script>
</body>
</html>
