<?php
/**
 * Import links from markdown files
 *
 * Links must be on one line in this format 
 * E.g. 1:ref:[HTML Introduction](https://developer.mozilla.org/en/HTML/Introduction)
 * n.n:   book-section
 * type:  ref=Reference (read to learn more), note=Footnote,book=Booklink, deep="Dig deeper", tip=Tip
 * [text] Markdown link text
 * (url)  Markdown url
 * 
 * Parsed by:
 * preg_match_all("/([0-9\\.]+):([a-z]{3,4}):\\[([^]]+)]\\(([^)]+)\\)/", $file, $links, PREG_SET_ORDER);
 *
 * @author <gunther@keryx.se>
 * @version "Under construction 1"
 * @license http://www.mozilla.org/MPL/
 * @package webbteknik.nu
 * 
 */

session_start();
require_once '../includes/loadfiles.php';

user::setSessionData();

user::requires(user::TEXTBOOK);

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

$file = file_get_contents("/home/gunther/arkiv/workspace/webbteknik/webbutveckling-1/links-kap-1.markdown");


$haslinks = preg_match_all("/([0-9\\.]+):([a-z]{3,4}):\\[([^]]+)]\\(([^)]+)\\)/", $file, $links, PREG_SET_ORDER);


/*
Produces an array where each item is an array like this one
array(5) {
    [0] =>
    string(120) full-match
    [1] =>
    string(4) "1.11"
    [2] =>
    string(3) "ref"
    [3] =>
    string(29) "Historical artifacts to avoid"
    [4] =>
    string(78) "https://developer.mozilla.org/en/Web_development/Historical_artifacts_to_avoid"
}
*/
header("Content-type: text/plain; charset=utf-8");
echo "Number of parsed links in file: " . $haslinks . "\n\n";

$sql = <<<SQL
    INSERT INTO links (linkID, linktext, linkurl, linktype, section, time_added, bookID)
    VALUES (null, :linktext, :linkurl, :linktype, :section, NOW(), 'wu1')
SQL;
$stmt = $dbh->prepare($sql);

$stmt->bindParam(':linktext', $linktext);
$stmt->bindParam(':linkurl', $linkurl);
$stmt->bindParam(':linktype', $linktype);
$stmt->bindParam(':section', $section);

$duplicates = 0;
foreach ( $links as $lnk ) {
    list($fullmatch, $section, $linktype, $linktext, $linkurl) = $lnk;
    try {
        $stmt->execute();
        echo "Added link: {$fullmatch}\n\n";
    }
    catch ( Exception $e ) {
    	if ( $e->getCode() == "23000" ) {
    	    $duplicates++;
    	    echo "LINK ALREADY EXISTS: {$fullmatch}\n\n";
    	} else {
    	    trigger_error($e->getMessage(), E_USER_ERROR);
    	}
    }
}
ob_flush();
flush();
echo "Fixing relations - wait\n\n";

// TODO remove booksection and do this directly!

$sql = <<<SQL
    SELECT linkID, section FROM links ORDER BY linkID
SQL;
$stmt = $dbh->prepare($sql);
$stmt->execute();

$sql = <<<SQL
    UPDATE links SET booksectionID = :booksectionID WHERE linkID = :linkID
SQL;
$stmtfix = $dbh->prepare($sql);
$stmtfix->bindParam(':booksectionID', $booksectionID);
$stmtfix->bindParam(':linkID', $linkID);

$sql = <<<SQL
    SELECT booksectionID FROM booksections WHERE section = :section;
SQL;
$stmtfixfetch = $dbh->prepare($sql);
$stmtfixfetch->bindParam(':section', $section);

foreach ( $stmt as $row ) {
	$linkID  = $row['linkID'];
	$section = $row['section'];
    echo "Fixing link {$linkID}\n";
    $stmtfixfetch->execute();
    $booksectionID = $stmtfixfetch->fetchColumn();    
    $stmtfix->execute();
    
}
echo "Finished";
