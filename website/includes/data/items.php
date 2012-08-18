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
    protected $validationRules;

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
        // $obj->validate();
        return $obj;
        
    }
    
    /**
     * A mock/example object
     * 
     * Must not be savable
     */
    public static function fake($id, $name, $schoolUrl="")
    {
        // TODO: Use fromArray
        $fakeobj = new data_schools($id, $name, $schoolUrl);
        $fakeobj->isFake = true;
        return $fakeobj;
    }
    
    /**
     * Saving an object
     * 
     * Should only happen if it has been validated and is error free
     */
    public function save()
    {
        if ( $this->isFake() ) {
            trigger_error(E_USER_WARNING, "Trying to save a fake object");
            return false;
        }
        if ( $this->propertyErrors['tested'] == false ) {
            trigger_error(E_USER_NOTICE, "Can not save an object that is untested");
            return false;
        }
        // TODO Write SQL to save object, etc
        trigger_error(E_USER_WARNING, "Not implemented yet");
    }
    
    /**
     * Get error message for a property
     */
    public function errorMessage($propName)
    {
        return isset($this->propertyErrors[$propName]) ? $this->propertyErrors[$propName] : '';
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
     * @return bool If check was passed or not
     */
    public function validate()
    {
        $prop_rules = json_decode($this->validationRules);
        if ( !is_object($prop_rules) ) {
            echo "<pre>";
            var_dump($this->validationRules);
            var_dump($prop_rules);
            throw new Exception("Empty contents for validationRules in " . __CLASS__);
        }
        foreach ( $prop_rules as $propName => $sp_rules ) {
            foreach ( $sp_rules as $rule_name => $rule_rules ) {
                if ( $rule_name == "required" && $rule_rules ) {
                    if ( empty($this->$propName) ) {
                        // TODO: Move strings to config
                        $this->propertyErrors[$propName] = "Värde krävs";
                        // No need to check any more rules
                        break;
                    }
                }
                $valid = validator::$rule_name($this->$propName, $rule_rules, true);
                if ( !$valid ) {
                    $this->propertyErrors[$propName] = $valid;
                }
                 
                
            }
        }
        $this->propertyErrors['tested'] = true;
        
    }
    
    // TODO Tomorrow: Export rules to JavaScript
    
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
    /*
    PHP filters:
    Simple types
    FILTER_VALIDATE_BOOLEAN "boolean"         FILTER_NULL_ON_FAILURE              
    FILTER_VALIDATE_FLOAT   "float"           FILTER_FLAG_ALLOW_THOUSAND          FILTER_SANITIZE_NUMBER_FLOAT
    FILTER_VALIDATE_INT 	"int"                                                 FILTER_SANITIZE_NUMBER_INT

    FILTER_SANITIZE_STRIPPED "stripped"       FILTER_FLAG_STRIP_LOW  My own option: Allow newline, strip additional problem chars

    Content types
    FILTER_VALIDATE_EMAIL   "validate_email"                                      FILTER_SANITIZE_EMAIL
    FILTER_VALIDATE_URL     "validate_url"                                        FILTER_SANITIZE_URL
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
	
    /**
     * 
     */
}