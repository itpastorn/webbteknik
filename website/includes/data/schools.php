<?php
/**
 * Class definition for schools
 * 
 * @author <gunther@keryx.se>
 * @version "Under construction 1"
 * @license http://www.mozilla.org/MPL/
 * @package webbteknik.nu
 * 
 */

/**
 * School
 *
 * Name and description of schools offered at the national level or by the institute
 * 
 * @todo interface and/or abstract class for all data types
 */
class data_schools extends items implements data
{
	
	// All properties that are not i abstract class are written as schoolProp

    /**
     * Where the school is located, matches DB-record
     */
    protected $schoolPlace;

    /**
     * The web page of the school
     * 
     * May be null, if no URL exists
     */
    protected $schoolUrl = null;
    
    
    private function __construct($id, $name, $schoolPlace, $schoolUrl)
    {
        $this->id          = $id;
        $this->name        = $name;
        $this->schoolPlace = $schoolPlace;
        $this->schoolUrl   = $schoolUrl;
    }
    
    /**
     * Loads an instance from DB
     * 
     * @param string $id  The school ID, matches DB primary key
     * @param object $dbh Instance of PDO
     */
    public static function loadOne($id, PDO $dbh) {
        $sql  = <<<SQL
            SELECT schoolID AS id, school_name AS name, school_place AS schoolPlace, school_url AS schoolUrl
            FROM schools WHERE id = :id
SQL;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, __CLASS__, array('id', 'name', 'schoolPlace', 'schoolUrl'));
        return $stmt->fetch();
    }
    
    /**
     * Return array of objects with all available records
     * 
     * @todo set limits, interval for pagination, etc
     * @todo 
     * 
     * @param object $dbh Instance of PDO
     * @param string $dbh Custom SQL query
     * @return array of instances of this class
     */
    public static function loadAll(PDO $dbh, $sql = false) {
    	if ( !$sql ) {
        $sql  = <<<SQL
            SELECT schoolID AS id, school_name AS name, school_place AS schoolPlace, school_url AS schoolUrl
            FROM schools
            ORDER BY name
SQL;
    	}
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, __CLASS__, array('id', 'name', 'schoolPlace', 'schoolUrl'));
        return $stmt->fetchAll();
    }
    
    /**
     * Make a new object from user data
     * 
     * @param Array $arr
     */
    public static function fromArray($arr)
    {
        if ( !isset($arr['id']) || empty($arr['name']) || empty($arr['schoolPlace']) ) {
        	echo "<pre>";
        	trigger_error("Trying to create school object with too little data", E_USER_NOTICE);
            return false;
        }
        if ( empty($arr['id']) ) {
            // TODO: Generate new id
            $arr['id'] = "test";
        }
        $schoolUrl  = ( empty($arr['schoolUrl']) ) ? '' : $arr['schoolUrl'];
        $obj = new data_schools($arr['id'], $arr['name'], $arr['schoolPlace'], $schoolUrl);
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
     * Get the place
     * 
     * @return string
     */
    public function getPlace()
    {
        return $this->schoolPlace;
    }

    /**
     * Get the full name (includes place)
     * 
     * @return string
     */
    public function getFullName()
    {
        return "{$this->name}, {$this->schoolPlace}";
    }

    /**
     * Get the url
     * 
     * @return string
     */
    public function getUrl()
    {
        return $this->schoolUrl;
    }
    

}
