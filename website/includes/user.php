<?php
/**
 * User administration
 *
 */

 
/**
 * User class
 */
class user
{
    // Constant used for bit based checks
    /**
     * Constant to reoresent non anonymous users, but with no account
     */
    const LOGGEDIN = 1;
    /**
     * Constant to represent users without a book, but with access to web materials
     */
    const WEBONLY  = 3;
    /**
     * Constant to represent users with a textbook
     */
    const TEXTBOOK = 7;
    /**
     * Constant to represent users with a workbook
     */
    const WORKBOOK = 15;
    /**
     * Constant to represent teachers
     */
    const TEACHER  = 31;
    /**
     * Constant to represent admins (may add material)
     */
    const ADMIN    = 63;
    /**
     * Constant to represent superusers (me and JE)
     */
    const SUPER    = 127;
    /**
     * Constant to represent impossible level of authority, for tests
     */
    const HYPER    = 255;
    
    /**
     * Extract session variables from JSON if they do not exist
     * 
     * TODO: Remove this and rely on PHP serialization
     * @return bool True if userdata already is an abject or has been turned into an object
     */
    public static function setSessionData()
    {
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
        header("Location: sign-in.php?nopriv=1&ref=" . $_SERVER['PHP_SELF']);
        exit;
        
    }
    
    /**
     * Ask if user has a certain access level
     * 
     * @example if ( user::validate(user::TEACHER) ) {}
     * 
     * @param int $req_level The level that the inquiry is about
     * @param int $userlevel The user's actual level
     */
    public static function validate($req_level, $userlevel = null) {
        if ( empty($userlevel) && isset($_SESSION['userdata']->privileges) ) {
            $userlevel = $_SESSION['userdata']->privileges;
        }
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
}
/*
     *   user::requires(TEACHER)  // restrict page to teachers or better
     *   user::validate(TEACHER)  // returns true if teacher or better
     *   user::email() // returns user email
     *   user::fullname() // returns user email
*/