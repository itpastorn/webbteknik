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
    public static function save()
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
