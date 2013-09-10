<?php
/*
 * Generate question to verify privilege request
 *
 * This file is part of the Ajax API
 * 
 * @todo Slow down when user is guessing wildly (last_guess)
 * @author <gunther@keryx.se>
 */

session_start();

/**
 * All needed files
 */
require_once '../../includes/loadfiles.php';

user::setSessionData();

user::requires(user::LOGGEDIN);


// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

if ( empty($_POST['bookID']) && empty($_POST['answer'])) {
    // error - Bad call
    exit('{"error": "bad call"}'); // TODO http-heads, etc
}

// Send question
if ( empty($_POST['answer']) ) {
    $bookrequest  = filter_input(INPUT_POST, 'bookID', FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);
    $stmt = $dbh->prepare(<<<SQL
        SELECT pqID, question
        FROM privilege_questions
        WHERE bookID = :bookID
        ORDER BY RAND() LIMIT 0,1
SQL
);
    $stmt->bindParam(':bookID', $bookrequest);
    $stmt->execute();
    $question = $stmt->fetch();
    if ( empty($question) ) {
        echo '{"error": "unavailable"}';
        exit;
    }
    $_SESSION['levelrequest'] = 7;
    $_SESSION['pqID']         = $question['pqID']; // Use when testing answer to avoid manipulation
    echo json_encode($question);
    exit;
}

// Check answer
// TODO: Check for downgrades
// TODO Use user class (make a method of this) - but not the is question correctly answered part

$answer = filter_input(INPUT_POST, 'answer', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
$stmt = $dbh->prepare("SELECT answer, bookID FROM privilege_questions WHERE pqID = :pqID AND answer = :answer");
$stmt->bindParam(':pqID', $_SESSION['pqID']);
$stmt->bindParam(':answer', $answer);
$stmt->execute();
unset($_SESSION['pqID']);
$question_data = $stmt->fetch();
$return_data = array( 'istrue' => (bool)$question_data);
if ( $return_data['istrue'] ) {
    // Update DB
    try {
        // Never downgrade
        $setlevel = $_SESSION['userdata']->privileges;
        if ( $_SESSION['userdata']->privileges < $_SESSION['levelrequest'] ) {
            $setlevel = user::setPrivilege($_SESSION['userdata'], $_SESSION['levelrequest'], $dbh);
            $return_data['newlevel'] = $setlevel;
        }
        if ( !$setlevel ) {
        	// Something has gone wrong - abort
        	throw new Exception("Privilege level could no be set.");
        }
        // Update ACL
        $acl_set = acl::set($_SESSION['user'], $question_data['bookID'], $dbh);
        $FIREPHP->log($acl_set);
    }
    catch (Exception $e) {
        $errorMsg = new StdClass();
        $errorMsg->eMsg  = $e->getMessage();
        $errorMsg->eCode = $e->getCode();
        echo json_encode($errorMsg);
        // Trace should not be sent to receiving script
        $errorMsg->eTrace = $e->getTraceAsString();
        $FIREPHP->log($errorMsg);
        exit;
    }
    $return_data['duplicate'] = false;
    if ( $acl_set === "duplicate" ) {
        $return_data['duplicate'] = true;
    }
}

echo json_encode($return_data);
exit;

