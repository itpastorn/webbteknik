<?php
/**
 * User administration
 *
 * @todo Future: Make this a data interface, item-extending class - requires renaming of validate-method
 * @todo Future: Use when saving user data etc
 */

 
/**
 * User class
 */
class user
{
    // Constant used for bit based checks
    const LOGGEDIN = 1;
    /**
     * Constant to represent non anonymous users, but with no account
     */
    const WEBONLY  = 3;
    /**
     * Constant to represent users without a book, but with access to web materials
     */
    const TEXTBOOK = 7;
    /**
     * Constant to represent users with a textbook
     */
    const WORKBOOK = 15;
    /**
     * Constant to represent users with a workbook
     */
    const TEACHER  = 31;
    /**
     * Constant to represent teachers
     */
    const ADMIN    = 63;
    /**
     * Constant to represent admins (may add material)
     */
    const SUPER    = 127;
    /**
     * Constant to represent superusers (me and JE)
     */
    const HYPER    = 255;
    /**
     * Constant to represent impossible level of authority, for tests
     */
    
    /**
     * Extract session variables from JSON if they do not exist
     * 
     * TODO: Remove this and rely on PHP serialization
     * @return bool True if userdata already is an abject or has been turned into an object
     */
    public static function setSessionData()
    {
    	// URGENT message because of session breakage at Nexcess
    	if ( empty($_SESSION) && false) {
    		header("Status: 500 Server error");
    	    echo <<<MSG
<!DOCTYPE html>
<title>SERVER ERROR</title>
<h1>SERVER ERROR</h1>
<p><big><b>
Webbplatsen ligger för närvarande nere.
Serverfel gör att sessionsdata inte lagras på servern.
Jag kommunicerar med webbhotellet och kommer meddela alla så
fort detta åtgärdats. Tyvärr ligger felet utom min kontroll.
<br><br>
Lars Gunther
MSG;
    	}
    	
        if ( isset($_SESSION['userdata']->email) ) {
            return true;
        }
        if ( isset($_SESSION['userdata']) && is_string($_SESSION['userdata']) ) {
            $_SESSION['userdata'] = json_decode($_SESSION['userdata']);
            return true;
        }
        return false;
    }
    
    /**
     * Page access control
     * 
     * Put at top of page, after session_start()
     * @example user::requires(user::TEACHER);
     * 
     * @param int $req_level The level that is required for the page
     * @param int $userlevel The user's actual level
     */
    public static function requires($req_level, $userlevel = null) {
        if ( self::validate($req_level, $userlevel) ) {
            return true;
        }
        // Access violation
        if ( "wt.book" == $_SERVER['SERVER_NAME']) {
            $pathextra = "/website/html/";
        } else {
            $pathextra = "/";
        }
        header("Location: {$pathextra}sign-in.php?nopriv=1&ref=" . urlencode($_SERVER['REQUEST_URI']));
        exit;
        
    }
    
    /**
     * Ask if user has a certain access level
     * 
     * @example if ( user::validate(user::TEACHER) ) {}
     * 
     * @param int $req_level The level that the inquiry is about
     * @param int $userlevel The user's actual level, leave empty for currently logged in user
     */
    public static function validate($req_level, $userlevel = null) {
    	global $FIREPHP;
        if ( empty($userlevel) && isset($_SESSION['userdata']->privileges) ) {
            $userlevel = $_SESSION['userdata']->privileges;
        }
    	// var_dump($SESSION); exit;
        return ($req_level & $userlevel) >= $req_level;
    }
    
    /**
     * Creates a user instance
     * 
     * @todo factory method...?
     * 
     * @param string $email User identity
     */
     public function __construct($email)
     {
         if ( !filter_var(FILTER_VALIDATE_EMAIL, $email) ) {
             return null;
         }
         $this->email = $email;
     }
    
    /**
     * Sanitize rules for filter_input_array and filter_var on allowed names
     * 
     * @return array
     */
    public static function nameSanitizeRules()
    {
        return array(
            'firstname' => array(
                'filter' => FILTER_SANITIZE_STRING,
                'flags'  => FILTER_FLAG_STRIP_LOW
            ),
            'lastname' => array(
                'filter' => FILTER_SANITIZE_STRING,
                'flags'  => FILTER_FLAG_STRIP_LOW
            )
        );
    }
    /**
     * Strict rules for filter_input_array and filter_var on allowed names
     * 
     * @todo Strange bug won't let me allow x22 for names like O'Reilly and Al'Hasmouty
     * @return array
     */
    public static function nameRules()
    {
        return array(
            'firstname' => array(
                'filter' => FILTER_VALIDATE_REGEXP,
                'options'  => array('regexp' => "/^\\p{L}{1}(\\p{Pd}|\\x20||\\p{L}){0,98}$/u")
            ),
            'lastname' => array(
                'filter' => FILTER_VALIDATE_REGEXP,
                'options'  => array('regexp' => "/^\\p{L}{1}(\\p{Pd}|\\x20||\\p{L}){0,98}$/u")
            )
        );
    }
    
    /**
     * Set a new privilege level for the user
     * 
     * @todo Make setPri non static and instantiate user class
     * 
     * @param string $user  A iser object (So far only StdClass - rework!!!)
     * @param int    $level The new level
     * @param PDO    $dbh   DB-connection
     */
    public static function setPrivilege($user, $level, PDO $dbh)
    {
    	// Verify acceptable level request
    	// Admins are set through phpMyAdmin
    	$level = (int)$level;
    	switch ( $level ) {
	    case self::LOGGEDIN :
	    case self::WEBONLY  :
	    case self::TEXTBOOK :
	    case self::WORKBOOK :
	    case self::TEACHER  :
            // is ok
            break;
        default:
            trigger_error('Bad level request', E_USER_ERROR);
           return false;
    	}
    	if ( empty($user) ) {
    	    $email = $_SESSION['user'];
    	} else {
    	    $email = $user->email;
    	}
        try {
            $sql = "UPDATE users SET privileges = :privileges, privlevel_since = NOW() WHERE email = :email";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':privileges', $level);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            session_regenerate_id();
            // Do not do the sesssion stuff if it's not the currently logged in user
            $_SESSION['userdata']->privileges = $level;
        }
        catch (Exception $e) {
            // TODO Better error handling UPDATE users SET privlevel_since
            $GLOBALS['FIREPHP']->log("DB failure setting privilege level.");
            $GLOBALS['FIREPHP']->log($e->getMessage());
            return false;
        }
        return $level;
    }
}
/*
     *   user::requires(user::TEACHER)  // restrict page to teachers or better
     *   user::validate(user::TEACHER)  // returns true if teacher or better
     *   user::email() // returns user email
     *   user::fullname() // returns user first + last name
*/


// TODO: Lists of users
// Per groups as students (belonging)
// Per groups as teachers (teaching)
