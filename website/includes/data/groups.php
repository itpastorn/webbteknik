<?php
/**
 * Class definition for groups
 * 
 * @author <gunther@keryx.se>
 * @version "Under construction 1"
 * @license http://www.mozilla.org/MPL/
 * @package webbteknik.nu
 * 
 */

/**
 * group
 *
 * Name and description of groups offered at the national level or by the institute
 * 
 * @todo interface and/or abstract class for all data types
 */
class data_groups extends items implements data
{
    
    // All properties that are not i abstract class are written as groupProp
    
    // Inherit
    // + id   <=> groupID in DB
    // + name <=> group_nickname in DB
    
    // "Foreign keys" must map to pre-existing record in DB
    // Sanitization rules are imported, validation used via DB

    /**
     * At what school does this course exist
     * 
     * Foreign key
     */
    protected $schoolID;

    /**
     * What course is it that the group studies
     * 
     * Foreign key
     */
    protected $courseID;

    /**
     * The web page of the group
     * 
     * May be null, if no URL exists
     */
    protected $groupUrl = null;

    /**
     * The maximum number of students in the group, not including teachers
     */
    protected $groupMaxSize = 0;

    
    /**
     * Start date the group
     * 
     * Formatted YYYY-MM-DD
     */
    protected $groupStartDate = null;
    
    /**
     * The list of students that belong to the group (their email)
     */
    protected $students = array();

    /**
     * The total number of students that belong to the group (their email)
     */
    protected $numStudents = 0;

    

    protected static $filterSanitizeRules = array(
        'id' => array(
            'filter' => FILTER_SANITIZE_STRIPPED,
            'flags'  => FILTER_FLAG_STRIP_LOW
        ),
        'schoolID' => array(
            'filter' => FILTER_SANITIZE_STRIPPED,
            'flags'  => FILTER_FLAG_STRIP_LOW
        ),
        'courseID' => array(
            'filter' => FILTER_SANITIZE_STRIPPED,
            'flags'  => FILTER_FLAG_STRIP_LOW
        ),
        'name' => array(
            'filter' => FILTER_SANITIZE_STRIPPED,
            'flags'  => 68
        ),
        'groupMaxSize' => array(
            'filter'   => FILTER_SANITIZE_NUMBER_INT
        ),
        'groupStartDate' => array(
            'filter' => FILTER_SANITIZE_STRIPPED,
            'flags'  => 68
        ),
        'groupUrl' => array(
            'filter' => FILTER_SANITIZE_URL
        )
    );
    // 68 == FILTER_FLAG_STRIP_LOW|FILTER_FLAG_ENCODE_AMP    

    protected static $filterValidateRules = array(
        'id' => array(
            'filter'  => FILTER_VALIDATE_REGEXP,
            'options' => array( 'regexp' => "/^[a-z0-9]{5}$/u" )
        ),
        'schoolID' => array(
            'filter'  => FILTER_CALLBACK,
            'options' => 'data_schools::isExistingId'
        ),
        'courseID' => array(
            'filter'  => FILTER_CALLBACK,
            'options' => 'data_courses::isExistingId'
        ),
        'name' => array(
            'filter'  => FILTER_VALIDATE_REGEXP,
            'options' => array( 'regexp' => "/^\\p{L}[\\p{L}\\p{Nd}\\p{Pd}&#38;]{2,20}$/u" )
        ),
        'groupMaxSize' => array(
            'filter'   => FILTER_VALIDATE_INT,
            'flags'    => FILTER_REQUIRE_SCALAR,
            'options'  => array('min_range' => 1, 'max_range' => 500)
        ),
        'groupStartDate' => array(
            'filter'  => FILTER_VALIDATE_REGEXP,
            'options' => array( 'regexp' => "/^20[1-3][0-9]-[01][0-9]-[0-3][0-9]$/u" )
        ),
        'groupUrl' => array(
            'filter'  => FILTER_VALIDATE_URL,
            'flags'   => FILTER_FLAG_SCHEME_REQUIRED
        )
    );
    
    
    /**
     * Rules for filter_input_array/filter_var_array, validation step
     */
    protected $errorStrings = array(
        'id'             => "Fel format, inte enligt /^[a-z0-9]{5}$/u",
        'schoolId'       => "Matchar inte något existerande värde.",
        'courseId'       => "Matchar inte något existerande värde.",
        'name'           => "Fel format, inte enligt /^\\p{L}[\\p{L}\\p{Nd}\\p{Pd}&#38;]{2,20}$/u",
        'groupMaxSize'   => "Inte ett heltal mellan 1 och 500",
        'groupStartDate' => "Inte ett datum (YYYY-MM-DD)",
        'groupUrl'       => "Inte en URL. (Den måste inkludera schema.)"
    );
/*
        'id'             => "Måste vara fem tecken, a-z eller siffror",
        'name'           => "För kort (min 2), för långt (max 20), eller otillåtna tecken",
*/

    protected $gettable =  array('schoolID', 'courseID', 'groupUrl', 'groupMaxSize', 'groupStartDate', 'students', 'numStudents');

    /**
     * The start of SQL commands to fetch data for this object
     * 
     * Can be used to help build outside queries for 2nd param of loadAll method
     */
    const SELECT_SQL = "
              SELECT `groups`.groupID AS id, schoolID, courseID, group_nickname AS name, group_max_size AS groupMaxSize, 
                     group_start_date AS groupStartDate, group_url AS groupUrl
              FROM groups";

    /**
     * Constructor for group objects
     * 
     * @param string $id
     * @param string $schoolID
     * @param string $courseID
     * @param string $name The nickname
     * @param int    $groupMaxSize
     * @param string $groupStartDate
     * @param string $gropuUrl
     */
    private function __construct($id, $schoolID, $courseID, $name, $groupMaxSize, $groupStartDate, $groupUrl=null)
    {
        $this->id             = $id;
        $this->schoolID       = $schoolID;
        $this->courseID       = $courseID;
        $this->name           = $name;
        $this->groupMaxSize   = $groupMaxSize;
        $this->groupStartDate = $groupStartDate;
        $this->groupUrl       = $groupUrl;
        $this->numStudents    = 0; // Will be updated from DB
    }
    
    /**
     * Set the fetchmode for all PDOStatements for this class
     * 
     * @param PDOStatement $stmt passed by reference 
     */
    private static function fetchMode(PDOStatement &$stmt)
    {
        $stmt->setFetchMode(
            PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE,
            __CLASS__,
            array('id', 'schoolID', 'courseID', 'name', 'groupMaxSize', 'groupStartDate', 'groupUrl')
        );
    }
    
    public static function loadOne($id, PDO $dbh) {
        $sql  = self::SELECT_SQL . <<<SQL
            WHERE  groupID = :id
SQL;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        self::fetchMode($stmt);
        $group = $stmt->fetch();
        $group->getUsers($dbh);
    }
    
    public static function loadAll(PDO $dbh, $sql=false, $params=array())
    {
        if ( !$sql ) {
        $sql  = self::SELECT_SQL . <<<SQL
            ORDER BY groupStartDate DESC, schoolID ASC, name
SQL;
        }
        $stmt = $dbh->prepare($sql);
        $stmt->execute($params);
        self::fetchMode($stmt);
        $groups = $stmt->fetchAll();
        $i = 0;
        foreach ( $groups as $group ) {
            $group->getUsers($dbh);
        }
        return $groups;
    }
    
    /**
     * Fet all users that belong to the group
     */
    private function getUsers (PDO $dbh)
    {
    	static $gustmt;
    	if ( empty($gustmt) ) {
            // Only make a statement the first time to get better performance    	    
            $sql = <<<SQL
                SELECT email FROM belonging_groups WHERE groupID = :gid
SQL;
            $gustmt = $dbh->prepare($sql);
            $GLOBALS['FIREPHP']->log("gustmt prepared");
    	}
        $gustmt->bindParam(':gid', $id); // Must be repeted for each method invocation
    	$id = $this->getId();
       	$gustmt->execute();
       	$this->students    = $gustmt->fetchAll(PDO::FETCH_COLUMN);
       	$this->numStudents = count($this->students);
        
    }

    /**
     * Make a new object from user data
     * 
     * @param Array $arr
     */
    public static function fromArray($arr)
    {
        if ( 
            !isset($arr['id'])           ||
            !isset($arr['schoolID'])     ||
            !isset($arr['courseID'])     ||
            !isset($arr['name'])         ||
            !isset($arr['groupMaxSize']) ||
            !isset($arr['groupStartDate']) 
           ) {
            trigger_error("Trying to create group object with too little data", E_USER_NOTICE);
            return false;
        }
        $groupUrl  = ( empty($arr['groupUrl']) ) ? '' : $arr['groupUrl'];

        $obj = new data_groups(
            $arr['id'], $arr['schoolID'], $arr['courseID'], $arr['name'],
            $arr['groupMaxSize'], $arr['groupStartDate'], $groupUrl
        );
        if ( empty($arr['id']) ) {
            $obj->generateId();
        }
        $obj->validate();
        // Allow empty URL
        if ( empty($groupUrl) ) {
            unset($obj->propertyErrors['groupUrl']); 
        }
        return $obj;
        
    }    
    
    /**
     * Generate an id
     */
    private function generateId()
    {
        // Debug with FirePHP;
        $fphp = $GLOBALS['FIREPHP'];
        
        $sql = "SELECT count(*) from groups WHERE groupID = :id";

        // @improve Tight coupling here
        $dbx      = config::get('dbx');
        $dbh      = keryxDB2_cx::get($dbx);

        $stmt     = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id);

        // Endless loop?
        // There are 23 068 672 possible ids
        // the probability of hittong a duplicate twice is miniscule beyond reason
        do {
            $id = self::generateIdHelp();
            $fphp->log("The generated groupID pre-DB check is: " . $id);
    
            $stmt->execute();
            $id_is_used = $stmt->fetchColumn();

        } while ( $id_is_used );

        $fphp->log("The final generated groupID is: " . $id);
        $this->id = $id;
        return true;
    } 
    /**
     * Helper function to generateId
     */
    private static function generateIdHelp()
    {
        // Not every char since some are harder to see or speak the difference between
        $chars = "0123456789abcdefghiklmnopqrstuxyz"; // last index = 32
        // Always start with a letter
        $id = $chars[rand(10, 32)];
        for ( $i = 0; $i < 4; $i++ ) {
            $id .= $chars[rand(0, 32)];
        }
        return $id;
    }

    /**
     * A mock/example object
     * 
     * Must not be savable
     * Same arguments as the constructor
     */
    public static function fake(
        $id='', $schoolID='', $courseID='', $name='', $groupMaxSize='', $groupStartDate='', $groupUrl=''
    )
    {
        $groupStartDate = ( $groupStartDate ) ?: date('Y-m-d');
        $fakeobj = new data_groups($id, $schoolID, $courseID, $name, $groupMaxSize, $groupStartDate, $groupUrl);
        $fakeobj->isFake = true;
        return $fakeobj;
    }
    
    public function save(PDO $dbh)
    {
        $safe = parent::preSaveChecks();
        if ( !$safe ) {
            return false;
        }
        // TODO Add support for UPDATE
        $sql = <<<SQL
            INSERT INTO groups (groupID, schoolID, courseID, group_nickname, group_max_size, group_start_date, group_url)
            VALUES (:groupID, :schoolID, :courseID, :group_nickname, :group_max_size, :group_start_date, :group_url)
SQL;

        // TODO Tomorrow roll this into a transaction

        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':groupID', $this->id);
        $stmt->bindParam(':schoolID', $this->schoolID);
        $stmt->bindParam(':courseID', $this->courseID);
        $stmt->bindParam(':group_nickname', $this->name);
        $stmt->bindParam(':group_max_size', $this->groupMaxSize);
        $stmt->bindParam(':group_start_date', $this->groupStartDate);
        $stmt->bindParam(':group_url', $this->groupUrl);
        $group = $stmt->execute();
        
        // When a group is created it must also have a teacher
        // TODO Investigate pattern if group is created by admin
        
        // Only add teacher on group creation....
        $this->addTeacher($_SESSION['user'], $dbh);
        
        // End transaction
        return $group;
    }
    
    /**
     * Get the full name (includes place)
     * 
     * @return string
     */
    public function getFullName()
    {
        return "{$this->name}, {$this->groupPlace}";
    }

    /**
     * Get the url
     * 
     * @return string
     */
    public function getUrl()
    {
        return $this->groupUrl;
    }
    
    /**
     * Validate or extract groupid within parenthesis from end of string
     * 
     * Used to extract valid id when working with relationship-data
     * @param string $id Test string
     * @param bool   $extract Set to true to extract substring
     * @return mixed The valid/extracted id or false
     */
    public static function checkgroupId($id, $extract = false)
    {
        $regexp = self::$filterValidateRules['id']['options']['regexp'];
        if ( !$extract ) {
            $test = preg_match($regexp, $id);
            return ( $test ) ? $id : false; 
        }
        // Modify regexp a bit
        // Remove "start token" (^)
        // Add literal parenthesis and capturing parenthesis
        $regexp = str_replace('^', '\((', $regexp);
        // Move "end token" ($) and repeat
        $regexp = str_replace('$', ')\)$', $regexp);
        $test   = preg_match($regexp, $id, $matches);
        return ( $test ) ? $matches[1] : false; 
    }
    
    
    public static function isExistingId($id, PDO $dbh=null)
    {
        if ( empty($dbh) ) {
            $dbx = config::get('dbx');
            $dbh = keryxDB2_cx::get($dbx);
        }
        // TODO Validate single prop, before invoking DB
        $sql  = "SELECT count(*) FROM groups where groupID = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    /**
     * Check if a user is a member of this group
     * 
     * @param string User id (email)
     * @param PDO    database connection
     * @return bool
     */
    public function isMember($user, PDO $dbh)
    {
        $sql  = "SELECT count(*) FROM belonging_groups WHERE email = :user AND groupID = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(":user", $user);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    /**
     * Check if a user is a teacher for this group
     * 
     * @param string User id (email)
     * @param PDO    database connection
     * @return bool
     */
    public function isTeacher($user, PDO $dbh)
    {
        $sql  = "SELECT count(*) FROM teaching_groups WHERE email = :user AND groupID = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(":user", $user);
        $stmt->bindParam(":id", $this->id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    
    /**
     * Add member to group (join)
     * 
     * @todo Error handling
     * 
     * @param string User id (email)
     * @param PDO    database connection
     * @return bool
     */
    public function addMember($user, PDO $dbh)
    {
        $sql  = <<<SQL
            INSERT INTO belonging_groups ( email, groupID, since ) 
            VALUES (  :user, :groupID, NOW() )
SQL;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(":user", $user);
        $stmt->bindParam(":groupID", $this->id);
        $stmt->execute();
        return $stmt->rowCount();
    }
    
    /**
     * Add member to group (join)
     * 
     * @todo Error handling
     * @todo Safety checks: Is teacher
     * @todo Is member? Remove as member first
     * 
     * @param string User id (email)
     * @param PDO    database connection
     * @return bool
     */
    public function addTeacher($user, PDO $dbh)
    {
        $sql  = <<<SQL
            INSERT INTO teaching_groups ( email, groupID, since ) 
            VALUES (  :user, :groupID, NOW() )
SQL;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(":user", $user);
        $stmt->bindParam(":groupID", $this->id);
        $stmt->execute();
        return $stmt->rowCount();
    }
           
    // TODO Remove user from group
    // TODO Remove teacher from group
}
/*
// The students
$sql = <<<SQL
    SELECT users.*, groups.group_nickname, schools.school_name FROM `users` 
    INNER JOIN belonging_groups USING (email)
    INNER JOIN groups USING (groupID)
    INNER JOIN schools USING(schoolID)
    WHERE group.groupID = :gid
    ORDER BY schools.schoolID, groups.group_nickname, users.lastname ASC, users.firstname DESC
SQL;
// stats
// All groups stats
$sql = <<<SQL
    SELECT schools.school_name, groups.group_nickname, COUNT(*) AS numStudents FROM `users` 
    INNER JOIN belonging_groups USING (email)
    INNER JOIN groups USING (groupID)
    INNER JOIN schools USING(schoolID)
    GROUP BY groups.groupID
    ORDER BY numStudents DESC
SQL;

$stmt = $dbh->prepare($sql);
$stmt->bindParam('gid', $tempgid);
foreach ( $cur_groups as $tempgroup ) {
    $tempgid = $tempgroup->getId();
    $stmt->execute();
    $tempgroup->numStudents = $stmt->fetchColumn();
    // TODO I should not be able to dynamically assign a member variable like this...
}

*/