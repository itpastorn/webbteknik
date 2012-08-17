<?php
/**
 * Class definition for courses
 * 
 * @author <gunther@keryx.se>
 * @version "Under construction 1"
 * @license http://www.mozilla.org/MPL/
 * @package webbteknik.nu
 * 
 */

/**
 * Courses
 *
 * Name and description of courses offered at the national level or by the institute
 * 
 * @todo interface and/or abstract class for all data types
 */
class data_courses implements data
{
    /**
     * The course ID, matches DB-record
     */
    protected $courseID;

    /**
     * The course spoken name, matches DB-record
     */
    protected $courseName;

    /**
     * The course url
     * 
     * May be null, if no URL exists
     */
    protected $courseUrl = null;
    
    private function __construct($courseID, $courseName, $courseUrl)
    {
        $this->courseID   = $courseID;
        $this->courseName = $courseName;
        $this->courseUrl  = $courseUrl;
    }
    
    /**
     * Loads an instance from DB
     * 
     * @param string $courseID  The course ID, matches DB primary key
     * @param object $dbh       Instance of PDO
     */
    public static function loadOne($courseID, PDO $dbh) {
        $sql  = <<<SQL
            SELECT courseID, course_name AS courseName, course_url AS courseUrl
            FROM courses WHERE courseID = :courseID
SQL;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':courseID', $courseID);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, __CLASS__, array('courseID', 'courseName', 'courseUrl')); 
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
            SELECT courseID, course_name AS courseName, course_url AS courseUrl FROM courses
            ORDER BY courseName
SQL;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':courseID', $courseID);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, __CLASS__, array('courseID', 'courseName', 'courseUrl'));
        return $stmt->fetchAll();
    }
    
    /**
     * Get the id
     * 
     * @return string
     */
    public function getId()
    {
        return $this->courseID;
    }

    /**
     * Get the name
     * 
     * @return string
     */
    public function getName()
    {
        return $this->courseName;
    }

    /**
     * Get the url
     * 
     * @return string
     */
    public function getUrl()
    {
        return $this->courseUrl;
    }
    

}


