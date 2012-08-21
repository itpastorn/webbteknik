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
    
    /**
     * Property validation rules, in addition to what PHP can provide
     */
     /*
    protected $validationRules = '
        {
            "id" : {
                "required" : true,
                "type"     : "string"
            },
            "name" : {
                "required" : true,
                "type"     : "string"
            },
            "schoolPlace" : {
                "required" : true,
                "type"     : "string"
            },
            "schoolUrl" : {
                "required" : false,
                "type"     : "url"
            }
        }';
    */

    /**
     * Rules for filter_input_array/filter_var_array, sanitization step
     */
    protected static $filterSanitizeRules = array(
        'id' => array(
            'filter' => FILTER_SANITIZE_STRIPPED,
            'flags'  => FILTER_FLAG_STRIP_LOW
        ),
        'name' => array(
            'filter' => FILTER_SANITIZE_STRIPPED,
            'flags'  => 68
        ),
        'schoolPlace' => array(
            'filter' => FILTER_SANITIZE_STRIPPED,
            'flags'  => 68
        ),
        'schoolUrl' => array(
            'filter' => FILTER_SANITIZE_URL
        )
    );
    // 68 == FILTER_FLAG_STRIP_LOW|FILTER_FLAG_ENCODE_AMP    

    /**
     * Rules for filter_input_array/filter_var_array, validation step
     */
    protected static $filterValidateRules = array(
        'id' => array(
            'filter'  => FILTER_VALIDATE_REGEXP,
            'options' => array( 'regexp' => "/^[a-z0-9]{5,6}$/u" )
        ),
        'name' => array(
            'filter'  => FILTER_VALIDATE_REGEXP,
            'options' => array( 'regexp' => "/^\\p{L}[\\p{L}\\x20\\p{Pd}&#38;]{2,100}$/u" )
        ),
        'schoolPlace' => array(
            'filter'  => FILTER_VALIDATE_REGEXP,
            'options' => array( 'regexp' => "/^\\p{L}[\\p{L}\\x20\\p{Pd}]{2,50}$/u" )
        ),
        'schoolUrl' => array(
            'filter'  => FILTER_VALIDATE_URL,
            'flags'   => FILTER_FLAG_SCHEME_REQUIRED
        )
    );
    // The u-flag also ensures UTF-8 validity
    // Pd = Punctuation, Dash
    // L  = Letter
    // &#38; is allowed, for encoded &
    
    // TODO Add more fields to map DB completetly
    
    /**
     * Rules for filter_input_array/filter_var_array, validation step
     */
    protected $errorStrings = array(
        'id' => "Fel format, inte enligt /^[a-z]{4,5}[a-z0-9]$/u",
        'name' => "Fel format, inte enligt /^\\p{L}[\\p{L}\\x20\\p{Pd}&#38;]{2,100}$/u",
        'schoolPlace' => "Fel format, inte enligt /^\\p{L}[\\p{L}\\x20\\p{Pd}]{2,50}$/u",
        'schoolUrl' => "Inte en URL. (Den måste inkludera schema.)"
    );
/*
        'id' => "För kort (min 5), för långt (max 6), eller otillåtna tecken",
        'name' => "För kort (min 2), för långt (max 100), eller otillåtna tecken",
        'schoolPlace' => "För kort (min 2), för långt (max 50), eller otillåtna tecken",
        'schoolUrl' => "Inte en URL. (Den måste inkludera schema.)"
*/

    private function __construct($id, $name, $schoolPlace, $schoolUrl)
    {
        $this->id          = $id;
        $this->name        = $name;
        $this->schoolPlace = $schoolPlace;
        $this->schoolUrl   = $schoolUrl;
    }
    
    public static function loadOne($id, PDO $dbh) {
        $sql  = <<<SQL
            SELECT schoolID AS id, school_name AS name, school_place AS schoolPlace, school_url AS schoolUrl
            FROM schools WHERE schoolID = :id
SQL;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $stmt->setFetchMode(
            PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, __CLASS__, array('id', 'name', 'schoolPlace', 'schoolUrl')
        );
        return $stmt->fetch();
    }
    
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
        $stmt->setFetchMode(
            PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, __CLASS__, array('id', 'name', 'schoolPlace', 'schoolUrl')
        );
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
            trigger_error("Trying to create school object with too little data", E_USER_NOTICE);
            return false;
        }
        $schoolUrl  = ( empty($arr['schoolUrl']) ) ? '' : $arr['schoolUrl'];
        $obj = new data_schools($arr['id'], $arr['name'], $arr['schoolPlace'], $schoolUrl);
        $obj->validate();
        if ( empty($arr['id']) ) {
            $obj->generateId();
            $obj->validate();
        }
        return $obj;
        
    }    
    
    /**
     * Generate an id from name and place
     */
    private function generateId()
    {
        // Debug with FirePHP;
        $fphp = $GLOBALS['FIREPHP'];
        
        // Name and place must be error free properties
        if ( $this->isError('name') || $this->isError('schoolPlace') || !$this->propertyErrors['tested'] ) {
            trigger_error("Trying to create id for school from untested or faulty data.", E_USER_NOTICE);
            return false;
        }
        // Temporarily undo any escaping och ampersand
        $name  = str_replace("&#38;", "&", $this->name);
        $place = str_replace("&#38;", "&", $this->schoolPlace);
        
        // Only allow [a-z], replace all else logically and convert all to lower case
        $name  = normalize_chars($name);
        $place = normalize_chars($place);
        
        // The words "skolan" and "gymnasiet" should be separate
        // E.g. "byskolan"" => "by|skolan"
        $name = str_replace("skolan", "|skolan", $name);
        $name = str_replace("gymnasiet", "|gymnasiet", $name);
        
        // Count number of words available to create id from
        // LOCALE should be swedish to (not) match åäö
        $n_words = preg_split("/\\W+/", $name);
        $p_words = preg_split("/\\W+/", $place);
        $nc = count($n_words) - 1; // Position in array of last word, hence - 1
        $pc = count($p_words) - 1;
        $tot_words = $nc + $pc + 2; // Total number of words
        switch ($tot_words) {
        case 2:
            $fphp->log("generating schoolID from 2 words total.");
            $id = substr($name, 0, 2) . substr($name, -1, 1) . substr($place, 1, 1) . substr($place, -1, 1); 
            break;
        case 3:
            $fphp->log("generating schoolID from 3 words total.");
            if ( 1 == $nc ) {
                // First letter in first name-word, first and last in 2nd name-word
                $id = substr($name, 0, 1) . substr($n_words[1], 0, 1) . substr($name, -1, 1) .
                      substr($place, 0, 1) . substr($place, -1, 1);
            } else {
                $id = substr($name, 0, 1) . substr($name, -1, 1) .
                      substr($place, 0, 1) . substr($p_words[1], -1, 1) .substr($place, -1, 1);
            }
            break;
        case 4:
            $fphp->log("generating schoolID from 4 words total.");
            if ( 2 == $nc ) {
                // First letter in first, 2nd and last name-word
                $id = substr($name, 0, 1) . substr($n_words[1], 0, 1) . substr($n_words[2], 0, 1) .
                      substr($place, 0, 1) . substr($place, -1, 1);
                
            } elseif ( 1 == $nc ) {
                $id = substr($name, 0, 1) . substr($n_words[1], 0, 1) . substr($name, -1, 1) .
                      substr($place, 0, 1) . substr($p_words[1], 0, 1);
            } else {
                // A 3 word place, is there such a place???
                $id = substr($name, 0, 1) . substr($name, -1, 1) .
                      substr($place, 0, 1) . substr($p_words[1], 0, 1) . substr($p_words[2], 0, 1);
            }
            break;
        default:
            $fphp->log("generating schoolID from 5 or more words total.");
            // 5 or more words
            // switch inside switch is hard to read, using if -else instead
            if ( 0 == $nc ) {
                // Always use 2 letters from name, use last place-word
                $id = substr($name, 0, 1) . substr($name, -1, 1) .
                      substr($place, 0, 1) . substr($p_words[1], 0, 1) . substr($p_words[$pc], 0, 1);
            } elseif ( 1 == $nc) {
                $id = substr($name, 0, 1) . substr($n_words[1], 0, 1) .
                      substr($place, 0, 1) . substr($p_words[1], 0, 1) . substr($p_words[$pc], 0, 1);
            } elseif ( 2 == $nc) {
                $id = substr($name, 0, 1) . substr($n_words[1], 0, 1) . substr($n_words[2], 0, 1) .
                      substr($place, 0, 1) . substr($p_words[$pc], 0, 1);
            } else {
                // $nc >= 3
                // Always use 2 letters from place
                $id = substr($name, 0, 1) . substr($n_words[1], 0, 1) .
                      substr($n_words[$nc], 0, 1) . substr($place, 0, 1);
                if ( $pc >= 1 ) {
                    $id .= substr($p_words[$pc], 0, 1);
                } else {
                    $id .= substr($place, -1, 1);
                }
            }
        }
        $fphp->log("The generated schoolID pre-DB is: " . $id);
        // Construction of pattern complete. Check availability
        // Find highest already in use
        // Geberated data is SQL-injection safe
        $sql = <<<SQL
                SELECT schoolID from schools
                WHERE schoolID lIKE '{$id}%'
                ORDER BY schoolID DESC
                LIMIT 0,1
SQL;
        $dbx      = config::get('dbx');
        $dbh      = keryxDB2_cx::get($dbx);
        $stmt     = $dbh->prepare($sql);
        $stmt->execute();
        $db_high_id = $stmt->fetchColumn();
        // If no other school has this letter-combination set this to 0 (zero)
        if ( empty($db_high_id) ) {
            $fphp->log("The generated schoolID was first with the letter combination.");
            $id .= "0";
        } else {
            // Increment as character to get sequence from 0-9 and then a-z
            $last = substr($db_high_id, -1, 1);
            if ( is_numeric($last) ) {
                if ( $last == 9 ) {
                    $last = "a";
                }
            }
            $last++;
            if ( ord($last) > 123 ) {
                // We are past "z"
                trigger_error("Can not generate unique schoolID.", E_USER_WARNING);
                return false;
            }
            $id .= $last;
        }
        $fphp->log("The final generated schoolID is: " . $id);
        $this->id = $id;
        return true;
    } 
    
    /**
     * A mock/example object
     * 
     * Must not be savable
     * Same arguments as the constructor
     */
    public static function fake($id='', $name='', $schoolPlace='', $schoolUrl='')
    {
        $fakeobj = new data_schools($id, $name, $schoolPlace, $schoolUrl);
        $fakeobj->isFake = true;
        return $fakeobj;
    }
    
    public function save(PDO $dbh)
    {
        $safe = parent::preSaveChecks();
        if ( !$safe ) {
            return false;
        }
        // TODO Add support for the num-fields
        $sql = <<<SQL
            INSERT INTO schools (schoolID, school_name, school_place, school_url)
            VALUES (:schoolID, :school_name, :school_place, :school_url)
SQL;
            /*
            ON DUPLICATE KEY UPDATE
            schoolID = :schoolID, school_name = :school_name, school_place = :school_place, school_url = :school_url
            */
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':schoolID', $this->id);
        $stmt->bindParam(':school_name', $this->name);
        $stmt->bindParam(':school_place', $this->schoolPlace);
        $stmt->bindParam(':school_url', $this->schoolUrl);
        return $stmt->execute();
        
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
    
    /**
     * Validate or extract schoolid within parenthesis from end of string
     * 
     * Used to extract valid id when working with relationship-data
     * @param string $id Test string
     * @param bool   $extract Set to true to extract substring
     * @return mixed The valid/extracted id or false
     */
    public static function checkSchoolId($id, $extract = false)
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
        $sql  = "SELECT count(*) FROM schools where schoolID = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}
