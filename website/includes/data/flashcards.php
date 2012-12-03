<?php
/**
 * Class definition for flashcards
 * 
 * @author <gunther@keryx.se>
 * @version "Under construction 1"
 * @license http://www.mozilla.org/MPL/
 * @package webbteknik.nu
 * 
 */

/**
 * flashcards
 *
 * flashcards available on the web site
 */
class data_flashcards extends items implements data
{

    /**
     * The flashcard url
     */
    protected $flashcardUrl = null;
    
    /**
     * The start of SQL commands to fetch data for this object
     * 
     * Can be used to help build outside queries for 2nd param of loadAll method
     */
    const SELECT_SQL = <<<SQL
        SELECT fs.setID AS id, fs.setname AS name, fs.bookID, fs.chapter, books.booktitle, bs.section AS bs_section, bs.title as bs_title
        FROM flashcardsets AS fs
        LEFT JOIN books ON (fs.bookID = books.bookID)
        LEFT JOIN booksections AS bs USING (booksectionID)
        INNER JOIN flashcards ON (fs.setID = flashcards.setID)
        GROUP BY fs.setID
        ORDER BY ISNULL(fs.bookID) ASC, fs.bookID DESC, ISNULL(booksectionID), bs.sortorder
SQL;


    private function __construct($id, $name, $flashcardUrl)
    {
        $this->id   = $id;
        $this->name = $name;
    }
    
    // Malfunctioning - using wrong SQL
    public static function loadOne($id, PDO $dbh)
    {
        $sql  = self::SELECT_SQL . "WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, __CLASS__, array('id', 'name', 'bs_section')); 
        return $stmt->fetch();
    }
    
    public static function loadAll(PDO $dbh, $sql=false, $params=array())
    {
        $sql  = self::SELECT_SQL;
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':privileges', $params['privileges']);
        $stmt->execute($params);
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, __CLASS__, array('id', 'name', 'bs_section'));
        return $stmt->fetchAll();
    }
    
    /**
     * Get the url
     * 
     * @return string
     */
    public function getUrl()
    {
        return 'flashcards/' . $this->id . '/';
    }
    
    public static function isExistingId($id, PDO $dbh=null)
    {
        if ( empty($dbh) ) {
            $dbx = config::get('dbx');
            $dbh = keryxDB2_cx::get($dbx);
        }
        // TODO Validate single prop, before invoking DB
        $sql  = "SELECT count(*) FROM flashcards where flashcardID = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * A function that lists all flashcards in a table
     * 
     * If there is an URL, the item will contain a link
     * 
     * @param array  $list      An array of instances of this class
     * @return string HTML-code
     */
    public static function makeTable($list)
    {
    	$cur_book   = null;
    	$firstbook  = true;
        $tableHTML  = "<table class=\"resourcelisting blackborder zebra\">";
        $tableHTML .= "<caption>Alla flashcards</caption>\n";
        foreach ( $list as $item ) {

            $list_item = htmlspecialchars($item->getName());
            $list_link = "<a href=\"{$item->getUrl()}\">{$list_item}</a>\n";
            
            if ( !empty($item->bs_title) ) {
                $bsection = "{$item->bs_title} ({$item->bs_section})";
            } else {
                $bsection = "";
            }
            
            if ( $item->bookID != $cur_book ) {
	            if ( $firstbook ) {
	                $firstbook = false;
	            } else {
	                $tableHTML .= "</tbody>\n";
	            }
            	$cur_book   = $item->bookID;
            	if ( empty($item->booktitle) ) {
            	    $item->booktitle = "annat";
            	}
                $tableHTML .= <<<THEAD
                    <thead>
                      <tr>
                        <th colspan="2">Flashcards till {$item->booktitle}</th>
                      </tr>
                      <tr>
                        <th>
                          flashcardnamn
                        </th>
                        <th>
                          Boksektion
                        </th>
                      </tr>
                    </thead>
                    <tbody>

THEAD;
            }

            // Make ordinary rows
            $tableHTML .= <<<TR
                <tr>
                   <td>{$list_link}</td>
                   <td>{$bsection}</td>
                </tr>
    
TR;
        }
        $tableHTML .= "</tbody>\n</table>\n";
        return $tableHTML;
    }
    

}


