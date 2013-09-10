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

}


