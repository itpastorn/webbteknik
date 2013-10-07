<?php
/**
 * List of users, to used for sending email
 *
 */

session_start();
require_once '../../includes/loadfiles.php';

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

user::setSessionData();

user::requires(user::ADMIN);

header("Content-type: text/plain; charset=utf-8");

foreach ( $dbh->query("SELECT * FROM users ORDER BY user_since ASC") as $row) {
    echo "{$row['firstname']} {$row['lastname']} <{$row['email']}>\n";
}