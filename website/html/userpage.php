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
        SELECT v.*, jl.joblistID, bs.section FROM videos AS v
        INNER JOIN booksections AS bs USING (booksectionID)
        LEFT JOIN joblist AS jl
        ON (v.videoname = jl.where_to_do_it)
        WHERE v.videoname = :video
SQL;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':video', $_GET['video']);
    $stmt->execute();
    $curvid = $stmt->fetch();
    //var_dump($curvid); exit;
    if ( $curvid ) {
        // Video exists, lets find user progress data
        $sql = <<<SQL
            SELECT progressdata, percentage_complete, status
            FROM   userprogress
            WHERE  joblistID = :joblistID AND email = :email
SQL;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':joblistID', $curvid['joblistID']);
        $stmt->bindParam(':email', $_SESSION['user']);
        $stmt->execute();
        $userdata = $stmt->fetch();
        $curvid = array_merge($curvid, (array)$userdata);
    }
    
} elseif (isset($_GET['vidnum']) ) {
    // TODO: Change to use job number, video table should not have any suggested order
    // Problem with that is that we can not do+/- 1 to get previous and next
    $vidnum = (int)$_GET['vidnum'];
    $sql = <<<SQL
        SELECT v.*, jl.joblistID, bs.section FROM videos AS v
        INNER JOIN booksections AS bs USING (booksectionID)
        LEFT JOIN joblist AS jl
        ON (v.videoname = jl.where_to_do_it)
        WHERE v.order = :vidnum
SQL;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':vidnum', $_GET['vidnum']);
    $stmt->execute();
    $curvid = $stmt->fetch();
    //var_dump($curvid); exit;
    if ( $curvid ) {
        // Video exists, lets find user progress data
        $sql = <<<SQL
            SELECT progressdata, percentage_complete, status
            FROM   userprogress
            WHERE  joblistID = :joblistID AND email = :email
SQL;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':joblistID', $curvid['joblistID']);
        $stmt->bindParam(':email', $_SESSION['user']);
        $stmt->execute();
        $userdata = $stmt->fetch();
        $curvid = array_merge($curvid, (array)$userdata);
    }
} else {
    // Default
    // Find next unseen video for user
    $sql = <<<SQL
        SELECT v.*, jl.joblistID, bs.section, up.progressdata, up.percentage_complete, up.status
        FROM videos AS v 
        INNER JOIN booksections AS bs USING (booksectionID)
        LEFT JOIN joblist AS jl
        ON (jl.where_to_do_it = v.videoname)
        LEFT JOIN userprogress AS up
        ON (jl.joblistID = up.joblistID)
        WHERE up.email = :email AND jl.what_to_do = 'video' AND up.status = 'begun' 
              OR up.email IS NULL
        ORDER BY jl.chapter ASC, jl.joborder ASC
SQL;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':email', $_SESSION['user']);
    $stmt->execute();
    $curvid = $stmt->fetch();
}

// Last video
$sql  = "SELECT MAX(`order`) AS `last` FROM videos";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$last = $stmt->fetchColumn(0);

if ( $curvid ) {
    
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
    
    // Find related links
    // TODO Find a better way to handle lots of links
    $sql = <<<SQL
       SELECT * FROM links WHERE booksectionID = :booksectionID LIMIT 0,5
SQL;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':booksectionID', $curvid['booksectionID']);
    $stmt->execute();
    $linkhtml = "<ul class=\"tightparagraph\">";
    $linktypes = array(
        'book' => "Boklänk",
        'ref'  => "Referens/läs mer",
        'note' => "Fotnot/faktakälla",
        'tip'  => "Tips",
        'deep' => "Fördjupning"
    );
    while ( $linkrow = $stmt->fetch() ) {
    	$type = $linkrow['linktype'];
        $linkhtml .= <<<LINKHTML
            <li>
              <a href="{$linkrow['linkurl']}" class="{$type}link"> 
                <span class="linktype" title="{$linktypes[$type]}">[{$type}]</span> 
                     {$linkrow['linktext']}</a>
            </li>
LINKHTML;
    }
    $linkhtml .= "</ul>\n";
    /*
        } else {
            $linkhtml .= "Inga matchande länkar till denna video.";
        }
    */
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
    ORDER BY jl.joborder ASC
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
  <title>Användarsida - webbteknik.nu</title>
  <base href="<?php echo $baseref; ?>" />
  <link rel="stylesheet" href="css/webbteknik-nu.css" />
  <link href='http://fonts.googleapis.com/css?family=Handlee' rel='stylesheet' type='text/css'>
</head>
<body class="wide">
  <h1>webbteknik.nu &ndash; Användarsida</h1>
  <?php require "../includes/snippets/mainmenu.php"; ?>
  <h3>Video: <?php echo "{$curvid['title']} (Bokavsnitt {$curvid['section']})"; ?></h3>
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
        <li>med förslag&hellip;</li><!-- TODO -->
      </ul>
    </div>
  <?php endif; ?>
  </div>
  <div id="videobuttons">
    <button id="skipvid" disabled>Markera videon <br /> som <b>sedd</b></button>
    <button id="unskipvid" disabled>Markera videon <br /> som <b>osedd</b></button>
    <button id="nextunseen" disabled"><b>Första osedda</b> video</button>
    <button class="prevnextvideo" disabled data-vidnum="<?php echo $curvid['next']; ?>"><b>Nästa</b> video</button>
    <button class="prevnextvideo" disabled data-vidnum="<?php echo $curvid['prev']; ?>"><b>Föregående</b> video</button>
  </div>
  <p id="vidprogress" class="unobtrusive">Status för denna video: </p>
  <p class="unobtrusive">
     Om sidan strular, vänligen tala om vilken webbläsare du använder (namn + version) samt vad du ser i
     konsollen<br /> (CTRL + SHIFT + K i Firefox, CTRL + SHIFT + J i Chrome) till gunther@keryx.se
  </p>
  <!-- TODO Clickable progress bar with timeranges converted to graphics -->
  <section id="resource_suggestions">
    <div>
      <h3 class="boxedheader">Länkar</h3>
      <?php echo $linkhtml; ?>
    </div>
    <div>
      <h3 class="boxedheader">Resurser 1</h3>
      <p class="tightparagraph">JSBin testa själv (TODO)</p>
    </div>
    <div>
      <h3 class="boxedheader">Resurser 2</h3>
      <p class="tightparagraph">TODO (Flashcards, interactive, etc)</p>
    </div>
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
     var wtglobal_joblistID        = <?php echo isset($curvid['joblistID']) ? $curvid['joblistID'] : "null"; ?>;
  </script>
  <script src="script/videoreport.js"></script>
</body>
</html>
