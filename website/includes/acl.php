<?php
/**
 * Class definition for access control
 * 
 * @author <gunther@keryx.se>
 * @version "Under construction 1"
 * @license http://www.mozilla.org/MPL/
 * @package webbteknik.nu
 * 
 */

/**
 * acl
 *
 * Name and description of courses offered at the national level or by the institute
 * 
 * @todo interface and/or abstract class for all data types
 */
class acl
{
    /**
     * Check ACL
     * 
     * @param string $email
     * @param string $bookID
     * @param PDO    $dbh
     * @return mixed false (unauthorized) or date + time since access was set or groupdata
     */
    public static function get($email, $bookID, PDO $dbh)
    {
        // Check via ACL-table
        $sql = <<<SQL
            SELECT since FROM access_control
            WHERE email = :email AND bookID = :bookID
SQL;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':bookID', $bookID);
        $stmt->execute();
        $since = $stmt->fetchColumn();
        if ( $since ) {
            return $since;
        }
        // Check via groups
        $sql = <<<SQL
            SELECT bg.*, groups.group_nickname, schools.school_name FROM `belonging_groups` AS bg
            INNER JOIN groups ON (bg.groupID = groups.groupID)
            INNER JOIN books ON (groups.courseID = books.courseID)
            INNER JOIN schools ON (groups.schoolID = schools.schoolID)
            WHERE bg.email = :email AND books.bookID = :bookID
SQL;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':bookID', $bookID);
        $stmt->execute();
        $group = $stmt->fetch();
        return $group;
    }

    /**
     * Get list of all books user may access
     * 
     * @param string $email
     * @param PDO    $dbh
     * @return mixed false (unauthorized) or date + time since access was set or groupdata
     */
    public static function getList($email, PDO $dbh)
    {
        // Admins and teachers have full access
        // TODO populate via DB
        if ( user::validate(user::TEACHER ) ) {
            $GLOBALS['FIREPHP']->log("Full access to books");
            $GLOBALS['FIREPHP']->log("(Line " . (__LINE__ - 1) . " in " . __CLASS__. ")");
            return array("wu1", "ws1");
        }
        
        // Check via ACL-table
        $sql = <<<SQL
            SELECT bookID FROM access_control
            WHERE email = :email
SQL;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $accessibles = array();

        $temp = $stmt->fetchAll();
        if ( $temp ) {
            // Flatten
            $accessibles = array_map('current', $temp);
        }

        // Check via groups
        $sql = <<<SQL
            SELECT DISTINCT books.bookID FROM books
            INNER JOIN groups ON (books.courseID = groups.courseID)
            INNER JOIN belonging_groups AS bg ON (groups.groupID = bg.groupID)
            WHERE bg.email = :email AND books.type = 'textbook'
SQL;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $temp = $stmt->fetchAll();
        if ( $temp ) {
            $accessibles = array_merge($accessibles, array_map('current', $temp));
        }
        return array_unique($accessibles);
    }

    /**
     * Set new ACL
     * 
     * @param string $email
     * @param string $bookID
     * @param PDO    $dbh
     * @return mixed true, "duplicate" or false
     */
    public static function set($email, $bookID, PDO $dbh)
    {
        // Check if record already exists
        $exists = self::get($email, $bookID, $dbh);
        if ( $exists ) {
            return "duplicate";
        }
        try {
            $sql = <<<SQL
                INSERT INTO access_control (aclID, email, bookID, since)
                VALUES (null, :email, :bookID, NOW())
SQL;
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':bookID', $bookID);
            $stmt->execute();
        }
        catch (Exception $e) {
            // With added check above we should never get here...
            if ( $stmt->errorCode() == "23000" ){
                // Duplicate entry
                return "duplicate";
            }
            $GLOBALS['FIREPHP']->log($e);
            throw new Exception("ACL could not be set. {$e->getMessage}", 1, $e);
            return false;
        }
        return true;
    }
    
    /**
     * What book user is working with
     * 
     * If user has not made a choice this function will make only possible choice
     * or redirect to page were the choice can be made
     * 
     * @param PDO   $dbh
     * @param array $current_privileges By reference- List of possible books to access.
     * @param bool  $redirect Set to false to prevent redirect 
     * @return string Currently made choice
     */
    public static function currentBookChoice(PDO $dbh, &$current_privileges = array(), $redirect = true)
    {
        $logbook = isset($_SESSION['currentbook']) ? $_SESSION['currentbook'] : 'No current book';
        $GLOBALS['FIREPHP']->log('Currentbook is: ' . $logbook);
        if ( empty($_SESSION['user']) ) {
            throw new Exception(__CLASS__ . '::' . __METHOD__ . ' called but $_SESSION["user"] is not set.');
            return false;
        }
        // Possible choices. Also returned by reference
        $current_privileges = self::getList($_SESSION['user'], $dbh);

        if ( !empty($_SESSION['currentbook']) ) {
            // Choice has been made
            return $_SESSION['currentbook'];
        }

        $possible_choices   = count($current_privileges);
        if ( 1 == $possible_choices ) {
            $_SESSION['currentbook'] = $current_privileges[0];
            $sql  = 'UPDATE users SET currentbook = :currentbook WHERE email = :email';
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':currentbook', $_SESSION['currentbook']);
            $stmt->bindParam(':email', $_SESSION['user']);
            $stmt->execute();
            return $_SESSION['currentbook'];
        }

        // Multiple choices or no choice possible
        // Only do this redirect once and only if user can chose a book
        if ( $redirect && $possible_choices > 1 && ! filter_has_var(INPUT_GET, 'choosebook') ) {
            header("Location: {$GLOBALS['PATHEXTRA']}edituser/?choosebook=1");
            exit;
        }
    }

}


