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
 * 
 * @todo Per chapter/ show only not-completed/ show and hide bonus/ link to bonus material
 * 
 * @todo First step: 3 tables: Fast track, slow track, bonus jobs = how to show it to fast track students
 * 
 * @todo Make it possible to provide link to Github/JSBin/etc in status for quick access
 */

session_start();
require_once '../includes/loadfiles.php';

user::setSessionData();

user::requires(user::TEXTBOOK);

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

// All jobs, but fast-track first
$sql = <<<SQL
    SELECT jl.* , v.*, up.percentage_complete, up.status
    FROM `joblist` AS jl
    LEFT JOIN videos AS v ON v.videoname = jl.where_to_do_it
    LEFT JOIN userprogress AS up ON up.joblistID = jl.joblistID
    ORDER BY chapter ASC, track ASC, joborder ASC
SQL;
/*
    WHERE jl.fast_track_order IS NOT NULL
    UNION
    SELECT jl.* , v.*, up.percentage_complete, up.status
    FROM `joblist` AS jl
    LEFT JOIN videos AS v ON v.videoname = jl.where_to_do_it
    LEFT JOIN userprogress AS up ON up.joblistID = jl.joblistID
    WHERE jl.fast_track_order IS NULL

 */

$stmt = $dbh->prepare($sql);
// $stmt->bindParam(':email', $_SESSION['user']);
$stmt->execute();
$jobs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <title>Arbetsplanering - webbteknik.nu</title>
  <link rel="stylesheet" href="css/webbteknik-nu.css" />
  <link href='http://fonts.googleapis.com/css?family=Handlee' rel='stylesheet' type='text/css'>
</head>
<body class="wide">
  <h1>webbteknik.nu &ndash; Arbetsplanering</h1>
  <?php require "../includes/snippets/mainmenu.php"; ?>
  <p>
    På den här sidan så får du ett <em>förslag</em> på hur du kan lägga upp ditt jobb.
    Du kan rapportera hur långt du kommit, och få uppgifter godkända av din lärare.
  </p>
  <p>
    <em>Preliminär info: Här kommer alla uppgifter och resurser att listas, så att elever kan checka av vad de gjort
    och lärare få en rapport. Videotittande rapporteras av sig självt när man tittar.</em>
  </p>
  <p id="showhidebuttons">&nbsp;</p><!-- placeholder for show/hide finished jobs -->
  <table class="jobreport blackborder zebra">
    <tr>
      <th>Uppgift</th>
      <th>Rapporterad status</th>
      <th>Godkänd</th>
    </tr>
  <?php
    foreach ( $jobs as $curjob ) {
    	if ( empty($curjob['status']) ) {
    	    $curjob['status'] = "0";
    	}
    	if ( $curjob['status'] == 'finished' ) {
    	    // Job is done, low key
    	    echo '<tr class="finished">';
    	} elseif ( $curjob['status'] == 'skipped' ) {
    	    // Job is done, low key
    	    echo '<tr class="skipped">';
    	} else {
    	    echo '<tr>';
    	    
    	}
    	echo "<td>";
        if ( $curjob['what_to_do'] == 'video' ) {
            echo 'Video: <a href="userpage.php?video=' . $curjob['where_to_do_it'] . '">';
            echo $curjob['title'] . '</a>';
        } else {
            // Self contained info about task in DB
            echo $curjob['what_to_do']. " i " . $curjob['where_to_do_it'];
        }
        echo "</td>\n";
        // Set class for CSS and JS to know if it is a video or not
        // Perhaps move to TR?
        if ( $curjob['what_to_do'] == 'video' ) {
            echo "<td data-jobid=\"{$curjob['joblistID']}\" class=\"job_is_video\">\n";
        } else {
            echo "<td data-jobid=\"{$curjob['joblistID']}\">\n";
        }
        if ( $curjob['what_to_do'] == 'video' ) {
            // Automatically reported when watching
            // PHP 5.3 shorthand below for default zero
            echo "Sett " . ($curjob['percentage_complete'] ?: 0) . " %";
            if ( $curjob['status'] == 'skipped' ) {
                echo " (överhoppad)";
            }
        } else {
            if ( $curjob['status'] == 'skipped' ) {
                echo " (överhoppad)";
            } elseif ( $curjob['status'] == 'finished' ) {
                echo " (Färdig)";
            } else {
                echo 'Ej klar';
            }
        }
        echo "</td>\n";
        echo "<td>Sätts av lärare";
        echo "</td>\n";
    }
    echo "</tr>\n";
  
  ?>
  </table>
  <?php require "../includes/snippets/footer.php"; ?>
  <script src="script/progressreport.js"></script>
</body>
</html>
