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

if ( empty($_POST['level']) && empty($_POST['answer'])) {
    // error - Bad call
    var_dump($_POST);
    exit("error"); // TODO http-heads, etc
}

// Send question
if ( empty($_POST['answer']) ) {
    $levelrequest = filter_input(INPUT_POST, 'level', FILTER_SANITIZE_NUMBER_INT);
    $stmt = $dbh->prepare(
        "SELECT pqID, question FROM privilege_questions WHERE privileges = :levelrequest ORDER BY RAND() LIMIT 0,1"
    );
    $stmt->bindParam(':levelrequest', $levelrequest);
    $stmt->execute();
    $question         = $stmt->fetch();
    if ( empty($question) ) {
        echo '{"error": "unavailable"}';
        exit;
    }
    $_SESSION['levelrequest'] = $levelrequest;
    $_SESSION['pqID']         = $question['pqID']; // Use when testing answer to avoid manipulation
    echo json_encode($question);
    exit;
}

// Check answer
// TODO: Check for downgrades
$answer = filter_input(INPUT_POST, 'answer', FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
$stmt = $dbh->prepare("SELECT answer FROM privilege_questions WHERE pqID = :pqID AND answer = :answer");
$stmt->bindParam(':pqID', $_SESSION['pqID']);
$stmt->bindParam(':answer', $answer);
$stmt->execute();
unset($_SESSION['pqID']);
$isCorrect = array( 'istrue' => (bool)$stmt->fetch());
if ( $isCorrect['istrue'] ) {
    // Update DB
    try {
    	$sql = "UPDATE users SET privileges = :privileges, privlevel_since = NOW() WHERE email = :email";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':privileges', $_SESSION['levelrequest']);
        $stmt->bindParam(':email', $_SESSION['user']);
        $stmt->execute();
        $_SESSION['userdata']->privileges = (int)$_SESSION['levelrequest'];
        unset($_SESSION['levelrequest']);
    }
    catch (Exception $e) {
        // TODO Better error handling UPDATE users SET privlevel_since
        $firephp->log("DB failure setting privilege level.");
        exit($e->getMessage());
    }
}

echo json_encode($isCorrect);
exit;

