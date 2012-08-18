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
class data_courses extends items implements data
{

    /**
     * The course url
     * 
     * May be null, if no URL exists
     */
    protected $courseUrl = null;
    
    private function __construct($id, $name, $courseUrl)
    {
        $this->id   = $id;
        $this->name = $name;
        $this->courseUrl  = $courseUrl;
    }
    
    /**
     * Loads an instance from DB
     * 
     * @param string $id  The course ID, matches DB primary key
     * @param object $dbh       Instance of PDO
     */
    public static function loadOne($id, PDO $dbh) {
        $sql  = <<<SQL
            SELECT courseID AS id, course_name AS name, course_url AS courseUrl
            FROM courses WHERE id = :id
SQL;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, __CLASS__, array('id', 'name', 'courseUrl')); 
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
            SELECT courseID AS id, course_name AS name, course_url AS courseUrl FROM courses
            ORDER BY name
SQL;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, __CLASS__, array('id', 'name', 'courseUrl'));
        return $stmt->fetchAll();
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


