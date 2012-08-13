<?php
/**
 * Receives progress report through AJAX for manual reports
 *
 * Only 2 pieces of data may be sent through POST: jobid and status
 * status can only be one of 'begun', 'finished', 'skipped', 'reset'
 * Status reset = delete record from DB
 *
 * @author <gunther@keryx.se>
 * @version "Under construction 1"
 * @license http://www.mozilla.org/MPL/
 * @package webbteknik.nu
 * 
 */

session_start();
require_once '../../includes/loadfiles.php';

user::setSessionData();

user::requires(user::TEXTBOOK);

if ( empty($_POST['status']) || empty($_POST['jobid']) ) {
    exit("Bad request. Variables not set.");
}

// Filter input
$jobid = (int)$_POST['jobid'];

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

if ( $_POST['status'] == "reset" ) {
    $sql = <<<SQL
        DELETE FROM userprogress
        WHERE email = :email AND joblistID = :jobid
SQL;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':email', $_SESSION['user']);
    $stmt->bindParam(':jobid', $jobid);
} else {
    switch ( $_POST['status'] ) {
        case 'begun':
        case 'finished':
        case 'skipped':
            $status = $_POST['status'];
        break;
        default:
            die("Can not set that status");
    }
    $sql = <<<SQL
        INSERT INTO userprogress (email, joblistID, status)
        VALUES  (:email, :jobid, :status)
        ON DUPLICATE KEY UPDATE status = :status
SQL;
    $stmt = $dbh->prepare($sql);
    $stmt->bindParam(':email', $_SESSION['user']);
    $stmt->bindParam(':jobid', $jobid);
    $stmt->bindParam(':status', $status);
}
try {
	$stmt->execute();
}
catch ( Exception $e ) {
    header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    echo "Progressdata could not be loaded into database";
    var_dump($e);
    // TODO FirePHP for debug
    exit;
}

echo "Database updated";