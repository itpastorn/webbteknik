<?php
/**
 * Class definition for books
 * 
 * @author <gunther@keryx.se>
 * @version "Under construction 1"
 * @license http://www.mozilla.org/MPL/
 * @package webbteknik.nu
 * 
 */

/**
 * Books
 *
 * 
 * @todo interface and/or abstract class for all data types
 */
class data_books extends items implements data
{

    protected $author, $authormail, $isbn, $type, $bookurl, $courseID;
    protected $readables = array('id', 'name', 'author', 'authormail', 'isbn', 'type', 'bookurl', 'courseID');
    
    /**
     * The start of SQL commands to fetch data for this object
     * 
     * Can be used to help build outside queries for 2nd param of loadAll method
     */
    const SELECT_SQL = "SELECT bookID AS id, booktitle AS name, author, authormail, isbn, type, bookurl, courseID FROM books ";


    private function __construct($id, $name, $author, $authormail, $isbn, $type, $bookurl, $courseID)
    {
        $this->id         = $id;
        $this->name       = $name;
        $this->author     = $author;
        $this->authormail = $authormail;
        $this->isbn       = $isbn;
        $this->type       = $type;
        $this->bookurl    = $bookurl;
        $this->courseID   = $type;
    }
    
    public function __get($prop)
    {
    	if ( in_array($prop, $this->readables) ) {
    		return $this->$prop;
    	}
        trigger_error("Trying to read non allowed or non existing property on instance of " . __CLASS__ );
    }
    
    public function __set($prop, $val)
    {
        trigger_error("Trying to write non allowed or non existing property on instance of " . __CLASS__ );
    }

    public static function loadOne($id, PDO $dbh)
    {
        $sql  = self::SELECT_SQL . "FROM books WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, __CLASS__, array('id', 'name', 'bookUrl')); 
        return $stmt->fetch();
    }
    
    public static function loadAll(PDO $dbh, $sql=false, $params=array())
    {
        $sql  = self::SELECT_SQL . "WHERE type = 'textbook'";
        $stmt = $dbh->prepare($sql);
        $stmt->execute($params);
        $stmt->setFetchMode(
            PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE,
            __CLASS__, array('id', 'booktitle', 'author', 'authormail', 'isbn', 'type', 'bookurl', 'courseID')
        );
        $books = array();
        while ( $bk = $stmt->fetch() ) {
        	$books[$bk->id] = $bk;
        }
        return $books;
    }
    
    /**
     * Get the url
     * 
     * @return string
     */
    public function getUrl()
    {
        return $this->bookurl;
    }
    
    public static function isExistingId($id, PDO $dbh=null)
    {
        if ( empty($dbh) ) {
            $dbx = config::get('dbx');
            $dbh = keryxDB2_cx::get($dbx);
        }
    	// TODO Validate single prop, before invoking DB
        $sql  = "SELECT count(*) FROM books where bookID = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
    

}
