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
     * Page access control
     * 
     * Put at top of page, after session_start()
     * @example user::requires(user::TEACHER);
     * 
     * @param int $req_level The level that is required for the page
     * @param int $userlevel The user's actual level
     */
    public static function requires($req_level, $userlevel = null) {
        if ( empty($userlevel) && isset($_SESSION['userlevel']) ) {
            $userlevel = $_SESSION['userlevel'];
        }
        if ( ($req_level & $userlevel) >= $req_level ) {
            return true;
        }
        // Access violation
        // TODO Error page
        exit("<h1>Verboten</h1>");
        
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
        if ( empty($userlevel) && isset($_SESSION['userlevel']) ) {
            $userlevel = $_SESSION['userlevel'];
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
    
}
/*
     *   user::requires(TEACHER)  // restrict page to teachers or better
     *   user::validate(TEACHER)  // returns true if teacher or better
     *   user::email() // returns user email
     *   user::fullname() // returns user email
*/