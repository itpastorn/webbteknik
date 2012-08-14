<?php
/**
 * User start page, requires log in
 * 
 * @author <gunther@keryx.se>
 * @version "Under construction 1"
 * @license http://www.mozilla.org/MPL/
 * @package webbteknik.nu
 * 
 * @todo ACL to videos based on privileges and what book it is related to
 * @todo Make generic resource page
 * 
 * @todo Remove order from videos table in DB and use joblist instead
 * 
 * @todo Sort should know if on fast-track
 */

session_start();
require_once '../includes/loadfiles.php';

user::setSessionData();

user::requires(user::TEXTBOOK);

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

if ( isset($_GET['video']) ) {
    // TODO filter, but prepared statements should catch any SQL-injection attempt
    $sql = <<<SQL
        SELECT v.*, jl.joblistID, up.progressdata, up.percentage_complete, up.status
        FROM videos AS v
        LEFT JOIN joblist AS jl
        ON (jl.where_to_do_it = v.videoname)
        LEFT JOIN userprogress AS up
        ON (jl.joblistID = up.joblistID)
        WHERE v.videoname = :video AND ( up.email = :email or up.email IS NULL )
SQL;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':video', $_GET['video']);
} elseif (isset($_GET['vidnum']) ) {
    // TODO: Change to use job number, video table should not have any suggested order
    // Problem with that is that we can not do+/- 1 to get previous and next
    $vidnum = (int)$_GET['vidnum'];
    $sql = <<<SQL
        SELECT v.*, jl.joblistID, up.progressdata, up.percentage_complete, up.status
        FROM videos AS v 
        LEFT JOIN joblist AS jl
        ON (jl.where_to_do_it = v.videoname)
        LEFT JOIN userprogress AS up
        ON (jl.joblistID = up.joblistID)
        WHERE v.order = :vidnum AND ( up.email = :email or up.email IS NULL )
SQL;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':vidnum', $vidnum);
} else {
    // Default
    // Find next unseen video for user
    $sql = <<<SQL
        SELECT v.*, jl.joblistID, up.progressdata, up.percentage_complete, up.status
        FROM videos AS v 
        LEFT JOIN joblist AS jl
        ON (jl.where_to_do_it = v.videoname)
        LEFT JOIN userprogress AS up
        ON (jl.joblistID = up.joblistID)
        WHERE up.email = :email AND jl.what_to_do = 'video' AND up.status = 'begun' 
              OR up.email IS NULL
        ORDER BY jl.chapter ASC, jl.slow_track_order ASC
SQL;
    $stmt = $dbh->prepare($sql);
}

$stmt->bindParam(':email', $_SESSION['user']);
$stmt->execute();

$videos = $stmt->fetchAll();
// TODO Do I really need to fetch more than on one?

// Last video
$sql  = "SELECT MAX(`order`) AS `last` FROM videos";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$last = $stmt->fetchColumn(0);

if ( $videos ) {
    $curvid = $videos[0];
    
    $progressdata = new stdClass();
    $progressdata->firstStop = 0;
    $progressdata->stops = array();
    $progressdata->viewTotal = 0;
    if ( !empty($curvid['status']) ) {
        $progressdata = json_decode($curvid['progressdata']);
    }
    // Is this the last video?
    if ( $curvid['order'] == $last ) {
        $curvid['next'] = "none";
    } else {
        $curvid['next'] = $curvid['order'] + 1;
    }
    // Is this the first video?
    if ( $curvid['order'] == 1 ) {
        $curvid['prev'] = "none";
    } else {
        $curvid['prev'] = $curvid['order'] - 1;
    }
} else {
    $curvid       = array('title' => 'Video ej funnen');
    $progressdata = "";
    // TODO NOT FOUND header
}
if ( !isset($curvid['status']) ) {
    $curvid['status'] = "unset";
}

// Flashcards

// Next job not done
$sql = <<<SQL
    SELECT jl.*, up.email, up.status
    FROM `joblist` AS jl
    LEFT JOIN userprogress AS up ON up.joblistID = jl.joblistID
    WHERE 
        ( up.status ='begun' OR up.status IS NULL )
    AND
        ( up.email = 'gunther@keryx.se' OR up.email IS NULL )
    AND
        jl.what_to_do !=  'video'
    ORDER BY jl.slow_track_order ASC
    LIMIT 0,1
SQL;
$stmt = $dbh->prepare($sql);
$stmt->bindParam('email', $_SESSION['user']);
$stmt->execute();
$nextjob = $stmt->fetch();
if ( $nextjob ) {
    $nextjobbdesc = $nextjob['what_to_do'] . "<br />" . $nextjob['where_to_do_it'];
} else {
    $nextjobbdesc = "Inga fler förslag.";
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
  <p class="usertip" data-tipname="videotip">
    <strong>Tips!</strong> Högerklicka på videon och välj visning i helskärm.
    Videons inbyggda upplösning är 1280 x 720 pixlar.
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
<!--    <?php echo "Jobb: " . $curvid['joblistID'] . "(debug)"; ?> -->
  <div id="videobuttons">
    <button id="skipvid" disabled>Markera videon <br /> som <b>sedd</b></button>
    <button id="unskipvid" disabled>Markera videon <br /> som <b>osedd</b></button>
    <button id="nextunseen" disabled"><b>Första osedda</b> video</button>
    <button class="prevnextvideo" disabled data-vidnum="<?php echo $curvid['next']; ?>"><b>Nästa</b> video</button>
    <button class="prevnextvideo" disabled data-vidnum="<?php echo $curvid['prev']; ?>"><b>Föregående</b> video</button>
  </div>
  <p id="vidprogress">Status för denna video: </p>
  <!-- TODO Clickable progress bar with timeranges converted to graphics -->
  <section id="resource_suggestions">
    <div>Flashcards (todo)</div>
    <div>Länkar (todo)</div>
    <div>Filer (todo)</div>
    <div>
      <h3 class="boxedheader">Nästa uppgift</h3>
      <p class="tightparagraph"><?php echo $nextjobbdesc; ?></p>
    </div>
  </section>
  <?php require "../includes/snippets/footer.php"; ?>
  <script>
     var wtglobal_start_video_at   = <?php
         // 5 seconds repeat to make it a bit easier to catch up
         if ( isset($progressdata->firstStop) ) { echo $progressdata->firstStop - 5; }
         else { echo 0; }; ?>;
     var wtglobal_old_progressdata = <?php
         if ( !empty($curvid['progressdata']) ) { echo $curvid['progressdata']; }
         else { echo 0; }; ?>;
     var wtglobal_old_status       = "<?php echo $curvid['status']; ?>";
     var wtglobal_joblistID        = <?php echo $curvid['joblistID']; ?>;
  </script>
  <script src="script/videoreport.js"></script>
</body>
</html>
