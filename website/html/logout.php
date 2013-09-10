<?php
/*
 * Logout, to be used with Ajax calls
 *
 * @author <gunther@keryx.se>
 */

session_start();


$_SESSION = array();
session_regenerate_id(true);

echo "Successfully logged out";


/*
if ( "wt.book" == $_SERVER['SERVER_NAME']) {
    $location = "/website/html/";
} else {
    $location = "/";
}
header("Location: {$location}");
*/