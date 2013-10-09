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
 * @todo Add support for other books than "wu1"
 * 
 * @todo Remove order from videos table in DB and use joblist instead
 * 
 * @todo Sort should know if on fast-track
 */

session_start();

require_once '../includes/loadfiles.php';

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

user::setSessionData();

user::requires(user::TEXTBOOK);

// Chose book to work with
$currentbook = acl::currentBookChoice($dbh);

// Name of video to show
$video = null;
// Associated links
$linkhtml = '';

// No chosen video
if ( empty($_GET['vidnum']) && empty($_GET['video']) ) {
    // Find next unseen video for user
    // First joblist-video not done
    // Inner query finds all watched videos
    // If 1,2,3 and 5 have been seen we want to find #4
    $sql = <<<SQL
        SELECT jl.where_to_do_it FROM joblist AS jl
        INNER JOIN userprogress AS up
        ON (jl.joblistID = up.joblistID)
        WHERE jl.what_to_do = 'video'
             AND jl.bookID = :bookID
             AND jl.joblistID NOT IN
              (
                SELECT injl.joblistID
                FROM joblist AS injl
                INNER JOIN userprogress AS inup
                ON (injl.joblistID = inup.joblistID)
                WHERE
                     (inup.status = 'finished' OR inup.status = 'skipped')
                  AND
                     inup.email = :email
                  AND
                     injl.what_to_do = 'video'
              )
        ORDER BY jl.joborder ASC
        LIMIT 0,1
SQL;
// Better SQL questions
// http://stackoverflow.com/questions/11998528/sql-optimization-finding-first-unwatched-video-using-no-subselect/11998669
/*
SELECT jl.where_to_do_it
FROM joblist jl INNER JOIN
     userprogress up
     ON (jl.joblistID = up.joblistID)
 WHERE jl.what_to_do = 'video' and
       not exists (
           (SELECT 1
            FROM joblist injl INNER JOIN
                 userprogress inup
                 ON (injl.joblistID = inup.joblistID)
            WHERE (inup.status = 'finished' OR inup.status = 'skipped') and
                  inup.email = 'info@keryx.se' and
                  injl.what_to_do = 'video' and
                  injl.joblistid = jl.joblistid
          )
)
ORDER BY jl.joborder ASC
LIMIT 0,1

SELECT jl.where_to_do_it, up.* FROM joblist AS jl
 INNER JOIN userprogress AS up
 ON (jl.joblistID = up.joblistID)
 LEFT JOIN (
    SELECT injl.joblistID
    FROM joblist AS injl
    INNER JOIN userprogress AS inup
    ON (injl.joblistID = inup.joblistID)
    WHERE
         (inup.status = 'finished' OR inup.status = 'skipped')
      AND
         inup.email = 'ingrid.sjoberg@kungsbacka.se'
      AND
         injl.what_to_do = 'video'
  ) SQ ON jl.joblistID = SQ.joblistID
 WHERE jl.what_to_do = 'video'
 AND SQ.joblistID IS NULL      
 ORDER BY jl.joborder ASC
*/
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':email', $_SESSION['user']);
    $stmt->bindParam(':bookID', $currentbook);
    $stmt->execute();
    $video = $stmt->fetchColumn();
    $FIREPHP->log("Next unseen video: {$video}");
}

if ( !$video && isset($_GET['video'])) {
    $video  = filter_input(INPUT_GET, 'video', FILTER_SANITIZE_URL);
}

$curvid = null;
if ( $video ) {
    // TODO filter, but prepared statements should catch any SQL-injection attempt
    $sql = <<<SQL
        SELECT v.*, jl.joblistID, bs.section FROM videos AS v
        LEFT JOIN booksections AS bs USING (booksectionID)
        LEFT JOIN joblist AS jl
        ON (v.videoname = jl.where_to_do_it)
        WHERE v.videoname = :video
SQL;
    $FIREPHP->log($sql);
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':video', $video);
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
    // Problem with that is that we can not do+/- 1 to get previous and next
    // TODO Add parameter for what book one has chosen
    $vidnum = (int)$_GET['vidnum'];
    $sql = <<<SQL
        SELECT v.*, jl.joblistID, bs.section FROM videos AS v
        LEFT JOIN booksections AS bs USING (booksectionID)
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
    if ( $curvid['order'] == $last || empty($curvid['order']) ) {
        $curvid['next'] = "none";
    } else {
        $curvid['next'] = $curvid['order'] + 1;
    }
    // Is this the first video?
    if ( $curvid['order'] == 1 || empty($curvid['order']) ) {
        $curvid['prev'] = "none";
    } else {
        $curvid['prev'] = $curvid['order'] - 1;
    }
    
    // Find related links
    // TODO Find a better way to handle lots of links
    $sql = <<<SQL
       SELECT * FROM links WHERE videoname = :videoname LIMIT 0,15
SQL;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':videoname', $curvid['videoname']);
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
    if ( $curvid['section'] ) {
        $curvid['title'] .= " (Bokavsnitt {$curvid['section']})";
    }
} else {
    $curvid       = array('title' => 'Video ej funnen');
    $progressdata = '';
    // TODO NOT FOUND header
    // TODO List of suggestions
}
if ( !isset($curvid['status']) ) {
    $curvid['status'] = "unset";
}

// Flashcards
// TODO Make object type and do not allow "empty sets"
$sql = <<<SQL
    SELECT setID, setname FROM flashcardsets
    INNER JOIN flashcards USING (setID)
    WHERE
      bookID = :bookID
    AND
      chapter = :chapter
    GROUP BY setID
    ORDER BY booksectionID ASC
    LIMIT 0,10
SQL;
$stmt = $dbh->prepare($sql);
$stmt->bindParam('bookID', $curvid['bookID']);
$stmt->bindParam('chapter', $curvid['chapter']);
$stmt->execute();
$matching_flashcard_sets = $stmt->fetchAll();
if ( empty($matching_flashcard_sets) ) {
    $flashcards = 'Inga flashcards finns till denna video (ännu)';
} else {
    $flashcards = '<ul class="tightparagraph">' . "\n";
    foreach ( $matching_flashcard_sets as $fset ) {
        $flashcards .= "<li><a href=\"flashcards/set/{$fset['setID']}/\">{$fset['setname']}</a></li>\n";
    }
    $flashcards .= "</ul>\n";
}



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
// Domain sharding of videos
// TODO Move to config
$sharding = 'media/';
if ( 'webbteknik.nu' == $_SERVER['SERVER_NAME']) {
    $sharding = 'http://bis.webbteknik.nu/media/';
}
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <title>Användarsida - webbteknik.nu</title>
  <?php require "../includes/snippets/dochead.php"; ?>
</head>
<body class="wide">
  <h1>webbteknik.nu &ndash; Användarsida</h1>
  <?php require "../includes/snippets/mainmenu.php"; ?>
  <h3>Video: <?php echo "{$curvid['title']}"; ?></h3>
  <p class="usertip" data-tipname="videotip">
    <strong>Tips!</strong> Högerklicka på videon och välj visning i helskärm.
    Videons inbyggda upplösning är 1280 x 720 pixlar.
  </p> 
  <div class="clearfix">
    <div id="videocontainer">
    <?php if ( isset($curvid['videoname']) ): ?>
      <video controls class="halfsize">
        <source src="<?php echo $sharding . $curvid['videoname']; ?>.webm" type="video/webm" />
        <source src="<?php echo $sharding . $curvid['videoname']; ?>.mp4" type="video/mp4" />
      </video>
      <p id="vidprogress" class="unobtrusive">Status för denna video: </p>
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
    <?php if ( isset($curvid['videoname']) ): ?>
      <button class="prevnextvideo" disabled data-bookID="<?php echo $curvid['bookID']; ?>" data-vidnum="<?php echo $curvid['next']; ?>"><b>Nästa</b> video</button>
      <button class="prevnextvideo" disabled data-bookID="<?php echo $curvid['bookID']; ?>" data-vidnum="<?php echo $curvid['prev']; ?>"><b>Föregående</b> video</button>
    <?php endif; ?>
    </div>
  </div>
  <p class="unobtrusive">
     Om sidan strular, vänligen tala om vilken webbläsare du använder (namn + version) samt vad du ser i
     konsollen<br /> (CTRL/CMD + SHIFT + K i Firefox, CTRL/CMD + SHIFT + J i Chrome) till gunther@keryx.se
  </p>
  <!-- TODO Clickable progress bar with timeranges converted to graphics -->
  <section id="resource_suggestions">
    <div>
      <h3 class="boxedheader">Länkar</h3>
      <?php echo $linkhtml; ?>
    </div>
    <div>
      <h3 class="boxedheader">Övningsfiler</h3>
      <p class="tightparagraph"><a href="assignments.php">Temporär sida med övningsfiler</a>.</p>
    </div>
    <div>
      <h3 class="boxedheader">Resurser 2</h3>
      <?php echo $flashcards; ?>
    </div>
    <div>
      <h3 class="boxedheader">Nästa uppgift</h3>
      <p class="tightparagraph"><?php echo $nextjobbdesc; ?></p>
      <!-- TODO knappar för att rapportera uppgiften och Ajax som då tar fram nästa -->
    </div>
  </section>
  <?php require "../includes/snippets/footer.php"; ?>
  <script>
     var wtglobal_start_video_at   = <?php
         // 5 seconds repeat to make it a bit easier to catch up
         if ( isset($progressdata->firstStop) ) { echo number_format($progressdata->firstStop, 0) - 5; }
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
