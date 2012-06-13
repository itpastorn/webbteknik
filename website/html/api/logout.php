<?php
/*
 * Logout, to be used with Ajax calls (maybe a bad idea)
 *
 * @author <gunther@keryx.se>
 */

session_start();

/**
 * Fire PHP
 */
require_once('FirePHPCore/FirePHP.class.php');
$firephp = FirePHP::getInstance(true);

$_SESSION = array();
session_regenerate_id();
echo "logged out";

// Note that when logging out from restricted pages, location must be changed