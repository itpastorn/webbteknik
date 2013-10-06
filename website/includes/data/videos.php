<?php
/**
 * Class definition for videos
 * 
 * @author <gunther@keryx.se>
 * @version "Under construction 1"
 * @license http://www.mozilla.org/MPL/
 * @package webbteknik.nu
 * 
 */

/**
 * videos
 *
 * Videos available on the web site
 */
class data_videos extends items implements data
{

    /**
     * The video url
     */
    protected $videoUrl = null;
    
    /**
     * The start of SQL commands to fetch data for this object
     * 
     * Can be used to help build outside queries for 2nd param of loadAll method
     * FIXME This sort will only work as long as there only are 2 books
     */
    const SELECT_SQL = <<<SQL
        SELECT vid.videoname AS id, vid.title AS name, vid.bookID, books.booktitle, vid.chapter, bs.section AS bs_section, bs.title as bs_title
        FROM videos AS vid
        LEFT JOIN booksections AS bs USING (booksectionID)
        LEFT JOIN books ON (vid.bookID = books.bookID)
        WHERE ( vid.acl <= :privileges  AND books.bookID LIKE :bookID ) OR vid.acl <= 3
        ORDER BY FIELD(vid.bookID, :bookID, 'git', 'site', :otherbook), ISNULL(vid.bookID) DESC, ISNULL(booksectionID), bs.sortorder
SQL;

    private function __construct($id, $name, $videoUrl)
    {
        $this->id   = $id;
        $this->name = $name;
        // $this->videoUrl  = 'fff' . $videoUrl2; // 'userpage.php?video=' . $name;
        // $this->testing = "hej";
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
        $GLOBALS['FIREPHP']->log($params['privileges']);
        $stmt->bindParam(':privileges', $params['privileges']);
        $bookID = "{$_SESSION['currentbook']}%"; // Search using like
        $stmt->bindParam(':bookID', $bookID);
        // FIXME This sort will only work as long as there only are 2 books
        $otherbook = ( $_SESSION['currentbook'] === 'wu1' ) ? 'ws1' : 'wu1';
        $stmt->bindParam(':otherbook', $otherbook);
        $stmt->execute();
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
        return 'userpage/video/' . $this->id . '/';
    }
    
    public static function isExistingId($id, PDO $dbh=null)
    {
        if ( empty($dbh) ) {
            $dbx = config::get('dbx');
            $dbh = keryxDB2_cx::get($dbx);
        }
        // TODO Validate single prop, before invoking DB
        $sql  = "SELECT count(*) FROM videos where videoID = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * A function that lists all videos in a table
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
        $tableHTML .= "<caption>Alla videofilmer</caption>\n";
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
                $tableHTML .= <<<THEAD
                    <thead>
                      <tr>
                        <th colspan="2">Videos till {$item->booktitle}</th>
                      </tr>
                      <tr>
                        <th>
                          Videonamn
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


