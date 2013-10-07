<?php
/*
 * For testing purposes unset user book choice
 */

session_start();
require_once '../includes/loadfiles.php';

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

user::setSessionData();
user::requires(user::LOGGEDIN);

unset($_SESSION['currentbook']);

header("Location: {$GLOBALS['PATHEXTRA']}edituser/");