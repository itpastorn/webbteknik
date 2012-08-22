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
    
    /**
     * The start of SQL commands to fetch data for this object
     * 
     * Can be used to help build outside queries for 2nd param of loadAll method
     */
    const SELECT_SQL = "SELECT `courses`.courseID AS id, course_name AS name, course_url AS courseUrl FROM courses ";


    private function __construct($id, $name, $courseUrl)
    {
        $this->id   = $id;
        $this->name = $name;
        $this->courseUrl  = $courseUrl;
    }
    
    public static function loadOne($id, PDO $dbh)
    {
        $sql  = self::SELECT_SQL . "FROM courses WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, __CLASS__, array('id', 'name', 'courseUrl')); 
        return $stmt->fetch();
    }
    
    public static function loadAll(PDO $dbh, $sql=false, $params=array())
    {
        $sql  = self::SELECT_SQL . "ORDER BY name";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute($params);
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
    
    public static function isExistingId($id, PDO $dbh=null)
    {
    	if ( empty($dbh) ) {
            $dbx = config::get('dbx');
            $dbh = keryxDB2_cx::get($dbx);
    	}
    	// TODO Validate single prop, before invoking DB
        $sql  = "SELECT count(*) FROM courses where courseID = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    

}


