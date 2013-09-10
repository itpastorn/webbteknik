<?php
/*
 * Logout, to be used with Ajax calls (maybe a bad idea)
 *
 * @author <gunther@keryx.se>
 */

session_start();


$_SESSION = array();
session_regenerate_id(true);

if ( "wt.book" == $_SERVER['SERVER_NAME']) {
    $location = "/website/html/";
} else {
    $location = "/";
}
header("Location: {$location}");
