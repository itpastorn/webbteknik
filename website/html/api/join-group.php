<?php
/*
 * Joining a group
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
require '../../includes/loadfiles.php';

/**
 * All needed files
 */
require 'data/groups.php';

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

user::setSessionData();

user::requires(user::LOGGEDIN);


if ( !filter_has_var(INPUT_POST, 'group_id') ) {
    // TODO header bad request
    echo "Required data not sent";
}

// Verify that there is such a group by creating it
$gid = filter_input(INPUT_POST, 'group_id', FILTER_SANITIZE_STRIPPED, FILTER_FLAG_STRIP_LOW);

// TODO The group should not be too old to join....

$group = data_groups::loadOne($gid, $dbh);
$FIREPHP->log($group);


if ( $group ) {
    // Already a member?
    if ( $group->isMember($_SESSION['user'], $dbh) ) {
        // TODO Future: Less fragile AJAX communication
        echo '{ "result": "ismember" }';
        // Also verify that user has privileges at TEXTBOOK or better level
        // TODO - grouplevel shall be adjusted according to the group level
        exit;
    }
    $group->addMember($_SESSION['user'], $dbh);
    if ( !user::validate(user::TEXTBOOK) ) {
        user::setprivilege($_SESSION['userdata'], user::WORKBOOK, $dbh);
        $FIREPHP->log("set level ". user::WORKBOOK);
    }
    echo '{ "result": "joined" }';
} else {
    echo '{ "result": "nogroup" }';
}
