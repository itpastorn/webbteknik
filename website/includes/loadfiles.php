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

// Base direcory for redirects
if ( "wt.book" == $_SERVER['SERVER_NAME']) {
    $PATHEXTRA = "/website/html/";
} else {
    $PATHEXTRA = "/";
}

/**
 * Fire PHP
 * 
 * Must come before handleErrors
 */
require_once('FirePHPCore/FirePHP.class.php');
$FIREPHP = FirePHP::getInstance(true);
// TODO Remove all includes of FirePHP from other files


/**
 * Error management
 */
require 'handleErrors.php';

// set_error_handler('handleErrors::handler');

/**
 * Configuration
 */
require 'config.php';

/**
 * Database
 */
require 'keryxDB2/cx.php';

/**
 * Users
 */
require 'user.php';

/**
 * Abstract items-class
 */
require "data/items.php";

/**
 * Data class interface
 */
require "data/data.php";

/**
 * Access control list
 */
require 'acl.php';

date_default_timezone_set("Europe/Stockholm");
$CURRENT_LOCALE = setlocale(LC_ALL, "sv_SE", "Swedish", "sve");
$TODAY          = date('Y-m-d');


/**
 * Normalize chars for url-usage
 * 
 * Replaces åäö with aao, etc.
 * @param string Characters to normalize
 * @return string
 */
function normalize_chars($string)
{
    // FRom http://ie2.php.net/manual/en/function.strtr.php#98669
    // Usage strtr($foo, $NORMALIZE_CHARS)
    $norm_chars = array(
        'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
        'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
        'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
        'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
        'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
        'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
        'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f'
    );
    return(strtolower(strtr($string, $norm_chars)));
}


