<?php
/**
 * Configuration settings
 * 
 * Created on 14 jun 2012
 *
 * @author     Lars Gunther <gunther@keryx.se>
 * @copyright  Lars Gunther <gunther@keryx.se>
 * @license    Creative Commons Attribution-Noncommercial-Share Alike 3.0 http://creativecommons.org/licenses/by-nc-sa/3.0/
 * @package    Webbteknik
 * @filesource
 */

/**
 * Configuration settings and accessor methods
 *
 */
class config
{
    /**
     * The configuration array
     */
    private static $config = array(
        'dbx' => array(
            'dbtype'   => 'mysql',
            'hostspec' => 'localhost',
            'database' => 'webbtek_webbtek',
            'username' => 'webbtek_webbtek',
            'password' => 'thule-is-valiant'
        )
    );
    
    /**
     * Make non instantiable
     */
     private final function __construct() {}
     private final function __clone() {}
    
    /**
     * Accessor
     */
    public static function get($setting)
    {
        if ( array_key_exists($setting, self::$config) ) {
            return self::$config[$setting];
        }
        // else
        trigger_error(E_USER_WARNING, 'Configuration setting does not exist.');
        return null;
    }
}
