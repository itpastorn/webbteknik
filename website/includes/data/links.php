<?php
/**
 * Class definition for links
 * 
 * @author <gunther@keryx.se>
 * @version "Under construction 1"
 * @license http://www.mozilla.org/MPL/
 * @package webbteknik.nu
 * 
 */

/**
 * links
 *
 * links available on the web site
 */
class data_links extends items implements data
{

    /**
     * The link url
     */
    protected $linkUrl = null;
    
    /**
     * Types of links
     */
    public $linkTypes = array(
        'book' => "Boklänk",
        'ref'  => "Referens/läs mer",
        'note' => "Fotnot/faktakälla",
        'tip'  => "Tips",
        'deep' => "Fördjupning"
    );
    
    /**
     * The start of SQL commands to fetch data for this object
     * 
     * Can be used to help build outside queries for 2nd param of loadAll method
     */
    const SELECT_SQL = <<<SQL
        SELECT ls.linkID AS id, ls.linktext AS name, ls.bookID, ls.linkurl AS linkUrl, ls.linktype, ls.chapter,
                books.booktitle, bs.section AS bs_section, bs.title as bs_title
        FROM links AS ls
        LEFT JOIN books ON (ls.bookID = books.bookID)
        LEFT JOIN booksections AS bs USING (booksectionID)

SQL;

    /**
     * SQL for grouping an ordering when selecting multiple links
     */
    const GROUP_ORDER_SQL = <<<SQL
        GROUP BY ls.linkID
        ORDER BY ISNULL(ls.bookID) ASC, ls.bookID DESC, ISNULL(booksectionID), bs.sortorder

SQL;

    private function __construct($id, $name, $linkUrl)
    {
        $this->id      = $id;
        $this->name    = $name;
        $this->linkUrl = $linkUrl;
    }
    
    // Malfunctioning - using wrong SQL
    public static function loadOne($id, PDO $dbh)
    {
        $sql  = self::SELECT_SQL . "WHERE id = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, __CLASS__, array('id', 'name', 'linkUrl'));
        return $stmt->fetch();
    }
    
    /**
     * Load all links, or at least a subset, into an array
     * 
     * Load links for a specific book using the bookID parameter (in array)
     * Load links for a specific chapter using the bookID AND chapter parameter (in array)
     * 
     * @param PDO    Database-connection object
     * @param string $sql Custom SQL query for special cases (not implemented)
     * @param params Array Custom options
     * @return Array containg links-objects
     */
    public static function loadAll(PDO $dbh, $sql=false, $params=array())
    {
        $sql  = self::SELECT_SQL;
        if ( isset($params['bookID']) ) {
            $sql .= "WHERE ls.bookID = :bookID ";
        }
        if ( isset($params['chapter']) ) {
            $sql .= "AND ls.chapter = :chapter ";
        }
        $sql  .= self::GROUP_ORDER_SQL;

        $stmt = $dbh->prepare($sql);
        // TODO Investigate this: privileges for links
        // $stmt->bindParam(':privileges', $params['privileges']);

        $stmt->execute($params);
        $stmt->setFetchMode(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, __CLASS__, array('id', 'name', 'linkUrl'));
        return $stmt->fetchAll();
    }
    
    /**
     * Get the url
     * 
     * @return string
     */
    public function getUrl()
    {
        return $this->linkUrl;
    }
    
    /**
     * Get the URL as a complete a-tag
     * 
     * @return string
     */
    public function getUrlAsTag()
    {
        return <<<TAG
        <a href="{$this->getUrl()}">{$this->getName()}</a>
TAG;
    }

    /**
     * Get the URL as a complete a-tag, classified
     * 
     * @return string
     */
    public function getUrlAsTagWithType()
    {
        return <<<TAG
              <a href="{$this->getUrl()}" class="{$this->linktype}link">{$this->getName()}</a>
TAG;
    }
    
    /**
     * Get the URL as a complete a-tag, classified and with type in HTML
     * 
     * @return string
     */
    public function getUrlAsTagWithTypeVisible()
    {
        return <<<TAG
              <a href="{$this->getUrl()}" class="{$this->linktype}link"> 
                <span class="linktype" title="{$this->linkTypes[$this->linktype]}">[{$this->linktype}]</span> 
              {$this->getName()}</a>
TAG;
    }
    
    /**
     * Get bookinfo
     * 
     * @return string
     */
    public function getBookInfo($what)
    {
    	return $this->$what;
    }

    public static function isExistingId($id, PDO $dbh=null)
    {
        if ( empty($dbh) ) {
            $dbx = config::get('dbx');
            $dbh = keryxDB2_cx::get($dbx);
        }
        // TODO Validate single prop, before invoking DB
        $sql  = "SELECT count(*) FROM links where linkID = :id";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     * A function that lists all links in a table
     * 
     * @param array  $list      An array of instances of this class
     * @return string HTML-code
     */
    public static function makeTable($list)
    {
    	$cur_book    = null;
    	$cur_chapter  = null;
    	$firstbook   = true;
        $tableHTML   = "<table class=\"resourcelisting blackborder zebra\">";
        $tableHTML  .= "<caption>Alla Länkar</caption>\n";
        foreach ( $list as $item ) {

            $list_item = htmlspecialchars($item->getName());
            $list_link = $item->getUrlAsTagWithType();
            $list_chap = $item->getBookInfo('chapter') ?: 'övrigt' ;
            
            if ( !empty($item->bs_title) ) {
                $bsection = htmlspecialchars("{$item->bs_title} ({$item->bs_section})");
            } else {
                $bsection = '';
            }
            
            if ( $item->bookID != $cur_book ) {
	            if ( $firstbook ) {
	                $firstbook = false;
	            } else {
	                $tableHTML .= "</tbody>\n";
	            }
            	$cur_book    = $item->bookID;
            	$cur_chapter = $list_chap;
            	if ( empty($item->booktitle) ) {
            	    $item->booktitle = "annat";
            	}
                $tableHTML .= <<<THEAD
                    <thead>
                      <tr>
                        <th colspan="3">Länkar till {$item->booktitle}</th>
                      </tr>
                      <tr>
                        <th>
                          Länk
                        </th>
                        <th>
                          Boksektion
                        </th>
                        <th>
                          Typ av länk
                        </th>
                      </tr>

THEAD;
                if ( $list_chap > 0 ) {
                    $tableHTML .= <<<THEAD
                      <tr class="sub">
                        <th colspan="3">Kapitel {$list_chap}</th>
                      </tr>

THEAD;
                }
                $tableHTML .= <<<THEAD
                    </thead>
                    <tbody>

THEAD;
            } elseif ( $list_chap != $cur_chapter ) {
            	// Same book, new chapter
            	$cur_chapter = $list_chap;
                $tableHTML .= <<<THEAD
                    <thead>
                      <tr class="sub">
                        <th colspan="3">Kapitel {$list_chap}</th>
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
                   <td>{$item->linkTypes[$item->linktype]}</td>
                </tr>
    
TR;
        }
        $tableHTML .= "</tbody>\n</table>\n";
        return $tableHTML;
    }
    

}


