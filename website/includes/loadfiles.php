<?php
/**
 * Load files for later usage and instantiate common objects/classes
 * 
 * This file will also set Timezone, etc, in lieu of a settings file
 * 
 * @author     Lars Gunther <gunther@keryx.se>
 * @copyright  Lars Gunther <gunther@keryx.se>
 * @license    Creative Commons Attribution-Noncommercial-Share Alike 3.0 http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package    webbteknik.nu
 * @filesource
 */

// Common config settings
ini_set('default_charset', 'UTF-8'); // Move to .htaccess ?

// Include path
$BELOWROOT = dirname(dirname(__FILE__));
set_include_path(
    get_include_path() . PATH_SEPARATOR . $BELOWROOT . DIRECTORY_SEPARATOR . 'includes' .
    PATH_SEPARATOR . $BELOWROOT . DIRECTORY_SEPARATOR . 'keryxIncludes.ep'
);

/**
 * Fire PHP, loads from PEAR
 */
require_once('FirePHPCore/FirePHP.class.php');

/**
 * Configuration
 */
require_once 'config.php';

/**
 * Database
 */
require_once 'keryxDB2/cx.php';

/**
 * Users
 */
require_once 'user.php';

date_default_timezone_set("Europe/Stockholm");
$CURRENT_LOCALE = setlocale(LC_ALL, "sv_SE", "Swedish", "sve");
$TODAY          = date('Y-m-d');
