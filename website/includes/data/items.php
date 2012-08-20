<?php
/**
 * Class definition for data items
 * 
 * @author <gunther@keryx.se>
 * @version "Under construction 1"
 * @license http://www.mozilla.org/MPL/
 * @package webbteknik.nu
 * 
 */

/**
 * The abstract class
 */
abstract class items
{
    /**
     * The ID, matches DB-record
     */
    protected $id;

    /**
     * The school spoken name, matches DB-record
     */
    protected $name;
    
    /**
     * Property validation rules
     */
    // protected $validationRules;

    /**
     * Rules for filter_input_array/filter_var_array, sanitization step
     * 
     * Abstract
     */
    protected static $filterSanitizeRules = null;
    
    /**
     * Rules for filter_input_array/filter_var_array, validation step
     * 
     * Abstract
     */
    protected static $filterValidateRules = null;

    /**
     * Error on properties
     * 
     * Empty = error free
     */
    protected $propertyErrors = array("tested" => false);
    
    /**
     * Fake objects should not be savable
     */
    protected $isFake = false;

    
    /**
     * Make a new object from user data
     * 
     * @param Array $arr
     */
    public static function fromArray($arr)
    {
    // Compare with bare minumum required in validation rules array
    
        if ( empty($arr['id']) ) {
            trigger_error("Trying to create an item with too little data", E_USER_NOTICE);
            return false;
        }
        $obj = new data_schools($arr['id'], $arr['name'], $arr['schoolUrl']);
        $obj->validate();
        return $obj;
        
    }

    /**
     * Common checks tu run before saving an object
     * 
     * Saving should only happen if it has been validated and is error free
     * @return bool Successfully passed checks or not
     */
    public function preSaveChecks()
    {
        if ( $this->isFake() ) {
            trigger_error(E_USER_WARNING, "Trying to save a fake object");
            return false;
        }
        if ( $this->propertyErrors['tested'] == false ) {
            trigger_error(E_USER_NOTICE, "Can not save an object that is untested");
            return false;
        }
        if ( !$this->isErrorFree() ) {
            trigger_error(E_USER_WARNING, "Can not save an object that has errors");
            return false;
        }
        return true;
    }
    /**
     * A mock/example object
     * 
     * Must not be savable
     * Example only since params may not be the same
     * TODO Move to interface and set array as para, for consistency?
     */
    /*
    public static function fake($id, $name, $schoolUrl="")
    {
        // TODO: Use fromArray
        $fakeobj = new data_schools($id, $name, $schoolUrl);
        $fakeobj->isFake = true;
        return $fakeobj;
    }
    */

    /**
     * Saving an object
     * 
     * Should only happen if it has been validated and is error free
     * @param object $dbh A PDO object
     * @return bool Successfully saved or not
     */
    public function save(PDO $dbh)
    {
        $safe = parent::preSaveChecks();
        if ( !$safe ) {
            return false;
        }
        // TODO Write SQL to save object, etc
        trigger_error(E_USER_ERROR, "Not implemented yet");
    }
    
    /**
     * Get error message for a property
     * 
     * @param string $propName Name of property that might have an error 
     * @param bool   $asHTML   Return HTML-escaped
     */
    public function errorMessage($propName, $asHTML)
    {
        $msg = isset($this->propertyErrors[$propName]) ? $this->propertyErrors[$propName] : '';
        if ( $asHTML ) {
            return htmlspecialchars($msg);
        }
        return $msg;
    }
    
    /**
     * Get error status for a property
     * 
     * @oaram bool $asHtmlClass Set to true will return 'class="error"'
     * @return mixed
     */
    public function isError($propName, $asHtmlClass = false)
    {
        $status = isset($this->propertyErrors[$propName]);
        if ( $asHtmlClass ) {
            return ( $status ) ? 'class="error"' : '';
        }
        return $status;
    }
    
    /**
     * Is it a fake object?
     */
    public function isFake()
    {
        return $this->isFake;
    }

    /**
     * Check that properties conform to validation rules
     * 
     * @todo Future, validate only one property
     * 
     * @return bool If check was passed or not
     */
    public function validate()
    {

        // Debug with FirePHP;
        $fphp = $GLOBALS['FIREPHP'];
        
        // Using late static binding, since we want to fetch rules from subclasses

        // What to test = Available rules and available properties (as array) intersection
        $test      = array_intersect_key(get_object_vars($this), static::$filterSanitizeRules);
        $sanitized = filter_var_array($test, static::$filterSanitizeRules);

        // Sanitized values should be put back into the object
        foreach ( $sanitized as $propName => $value ) {
            $this->$propName = $value;
        }
        
        // Note that we are keeping perhaps invalid properties
        // because we want to re-populate forms
            
        $test      = array_intersect_key($sanitized, static::$filterValidateRules);
        $validated = filter_var_array($test, static::$filterValidateRules);

        // Purge non true values
        $validated = array_filter($validated, function ($v) { return (bool)$v; });
        
        // Error messages to be set on array key diff
        $error_keys = array_keys(array_diff_key(static::$filterValidateRules, $validated));
        
        // Remove all previously set errors
        $this->propertyErrors = array();
        foreach ( $error_keys as $key ) {
            $this->propertyErrors[$key] = $this->errorStrings[$key];
        }
    
        $this->propertyErrors['tested'] = true;
        
    }
    
    /**
     * See if object is error free
     * 
     * @return bool
     */
    public function isErrorFree()
    {
        return (count($this->propertyErrors) < 2) && ($this->propertyErrors['tested'] == true);
    }
    
     /**
     * Get the id
     * 
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the full name, defaults in abstract class to alias of getName
     * 
     * @return string
     */
    public function getFullName()
    {
        return $this->name;
    }


}


/**
 * The validator class has a set of extra methods that PHP does not provide in its filter extension
 * 
 * Used for the callback filter
 * 
 * All methods must follow the convention
 * methodname($prop_to_check, $rules/value_to_use, $try_to_sanitize_first)
 * Simple rules must provide all parameters, but may chose to leave the unused
 * If possible, method names should equal names of PHP-filters
 */
class validator
{

    /**
     * Validate boolean values
     */
    public static function boolean($prop, $unused1, $unused2)
    {
        return filter_var(FILTER_VALIDATE_BOOLEAN );
    }
    
    /**
     * Sanitize input from textarea and similar fields, allow HTML and newlines
     */
    public static function safe_html()
    {
        trigger_error("safe_html() not implemented yet", E_USER_ERROR);
    }
    
}
/*
PHP filters:

FILTER_REQUIRE_SCALAR

Simple types
FILTER_VALIDATE_BOOLEAN "boolean"         FILTER_NULL_ON_FAILURE              
FILTER_VALIDATE_FLOAT   "float"           FILTER_FLAG_ALLOW_THOUSAND          FILTER_SANITIZE_NUMBER_FLOAT
FILTER_VALIDATE_INT     "int"                                                 FILTER_SANITIZE_NUMBER_INT

FILTER_SANITIZE_STRIPPED "stripped"  FILTER_FLAG_STRIP_LOW  My own option: Allow newline, strip additional problem chars

Content types
FILTER_VALIDATE_EMAIL   "validate_email"                                      FILTER_SANITIZE_EMAIL
FILTER_VALIDATE_URL     "validate_url"   FILTER_FLAG_SCHEME_REQUIRED          FILTER_SANITIZE_URL
FILTER_VALIDATE_IP      "validate_ip"
Pattern
FILTER_VALIDATE_REGEXP  "validate_regexp"    Note: Provide usable error message

FILTER_CALLBACK         "callback"

FILTER_SANITIZE_ENCODED

FILTER_UNSAFE_RAW   + FILTER_FLAG_STRIP_LOW   My own option: Allow newline, strip additional problem chars

My own filters/options

maxlength for strings
minlength for strings
is_json

*/
