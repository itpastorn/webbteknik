<?php
/**
 * Receives progress report through AJAX when viewing videos
 *
 * @author <gunther@keryx.se>
 * @version "Under construction 1"
 */

session_start();
require_once '../../includes/loadfiles.php';

user::setSessionData();

user::requires(user::TEXTBOOK);

$reportdata = filter_var($_POST['reportdata'], FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH);

$reportdata = json_decode($reportdata);

/*
$reportdata->src must be /[a-z0-9-]+/
$reportdata->firstStop must be numeric
$reportdata->viewTotal must be numeric
$reportdata->stops must be array (at least 1 in size)
$reportdata->percentage_complete must be integer 0 <= x <= 100

$reportdata->status - if present, must be enum("begun", "skipped", "finished")

clent side normalization of stops-array is taken for granted
*/

// Partial clone to insert into db column progressdata
$progressdata = new StdClass();
$progressdata->firstStop = $reportdata->firstStop;
$progressdata->viewTotal = $reportdata->viewTotal;
$progressdata->stops     = $reportdata->stops;

$progressdata = json_encode($progressdata);

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

// First view = create row

$sql = "SELECT * FROM userprogress WHERE email = :email AND tablename = 'videos' AND resourceID = :videoname";

$stmt = $dbh->prepare($sql);
$stmt->bindParam(':email', $_SESSION['user']);
$stmt->bindParam(':videoname', $reportdata->src);
$stmt->execute();

$curdata = $stmt->fetch();

if ( isset($curdata) && $curdata->status == 'finished' ) {
    echo "Viewing already complete (DB)";
    exit;
}
if ( $reportdata->status == 'finished' ) {
    // TODO what?
    // exit;
} elseif ( isset($curdata) && $curdata->status == 'skipped' ) {
    // echo "Marked as skipped in DB";
    // TODO: Undo?
    $reportdata->status = "skipped";
} elseif ( $reportdata->status == 'skipped' ) {
    // echo "Skipped";
    // exit;
} else {
    $reportdata->status = "begun";
}

if ( !$curdata ) {
    $sql = "INSERT INTO userprogress (email, tablename, resourceID, progressdata, percentage_complete, status) " .
           "VALUES (:email, 'videos', :videoname, :progressdata, :percentage_complete, :status)";
} else {
    $sql = "UPDATE userprogress " .
           "SET progressdata = :progressdata, percentage_complete = :percentage_complete, status = :status " .
           "WHERE email = :email AND tablename = 'videos' AND resourceID = :videoname";
}
try {
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':email', $_SESSION['user']);
    $stmt->bindParam(':videoname', $reportdata->src);
    $stmt->bindParam(':progressdata', $progressdata); // JSON-encoded
    $stmt->bindParam(':percentage_complete', $reportdata->percentage_complete);
    $stmt->bindParam(':status', $reportdata->status);
    $stmt->execute();
}
catch (PDOException $e) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    echo "Progressdata could not be loaded into database";
    // TODO FirePHP for debug
    exit;
}
echo "Progressdata saved";
