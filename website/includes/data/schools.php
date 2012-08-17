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
class data_schools implements data
{
    /**
     * The school ID, matches DB-record
     */
    protected $schoolID;

    /**
     * The school spoken name, matches DB-record
     */
    protected $schoolName;

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
    
    private function __construct($schoolID, $schoolName, $schoolUrl)
    {
        $this->schoolID   = $schoolID;
        $this->schoolName = $schoolName;
        $this->schoolUrl  = $schoolUrl;
    }
    
    /**
     * Loads an instance from DB
     * 
     * @param string $schoolID  The school ID, matches DB primary key
     * @param object $dbh       Instance of PDO
     */
    public static function loadOne($schoolID, PDO $dbh) {
        $sql  = <<<SQL
            SELECT schoolID, school_name AS schoolName, school_url AS schoolUrl, school_place AS schoolPlace
            FROM schools WHERE schoolID = :schoolID
SQL;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':schoolID', $schoolID);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, __CLASS__, array('schoolID', 'schoolName', 'schoolUrl')); 
        return $stmt->fetch();
    }
    
    /**
     * Return array of objects with all available records
     * 
     * @todo set limits, interval for pagination, etc
     * @todo 
     * 
     * @param object $dbh       Instance of PDO
     * @return array of instances of this class
     */
    public static function loadAll(PDO $dbh) {
        $sql  = <<<SQL
            SELECT schoolID, school_name AS schoolName, school_url AS schoolUrl, school_place AS schoolPlace
            FROM schools
            ORDER BY schoolName
SQL;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':schoolID', $schoolID);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, __CLASS__, array('schoolID', 'schoolName', 'schoolUrl'));
        return $stmt->fetchAll();
    }
    
    /**
     * Get the id
     * 
     * @return string
     */
    public function getId()
    {
        return $this->schoolID;
    }

    /**
     * Get the name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->schoolName;
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


