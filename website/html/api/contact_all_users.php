<?php
/**
 * List of users, to used for sending email
 *
 */

// exit("Sluta spamma");
session_start();
require_once '../../includes/loadfiles.php';

user::setSessionData();

user::requires(user::ADMIN);

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

header("Content-type: text/plain; charset=utf-8");

$subject = "God jul från webbteknik.nu";

echo "Påbörjar utskick\n"; 

$text = <<<TXT

Det är bara några dagar kvar tills terminsslut. Jag kommer dock fortsätta
jobba med fler filmer under lovet.

Inatt har jag lagt upp filmen som presenterar hur man kan göra layout, till
kapitel 11. Det blev en lite längre film, i stället för ett par kortare. Man kan titta 
i omgångar.

http://webbteknik.nu/userpage/video/wu-lb-11/

Jag visar inte bara float i filmen, utan också CSS-tabeller och flexbox,
som precis i dagarna har börjat kunna testas med den färdiga syntaxen.

Jag följer inte koden till lächjälpen exakt, utan visar i videon mer hur det fungerar
i princip och varför koden skrivs på ett visst sätt.


Nu finns det också tips på lite läsning under lovet. Länkarna till kapitel 1-8 har
kommit upp:

http://webbteknik.nu/resources/links/


Och inte minst vill jag önska er alla drygt 700 användare en riktigt god jul!


Lars Gunther


TXT;

$only_me   = "SELECT * FROM users WHERE email = 'gunther@keryx.se'";
$all_users = "SELECT * FROM users ORDER BY user_since ASC";
$teachers  = "SELECT * FROM users WHERE privileges > 30 ORDER BY user_since ASC";


$headers = "From: webbteknik.nu admin<gunther@keryx.se>\r\n" .
		   "Reply-to: Lars Gunther <gunther@keryx.se>\r\n".
           "MIME-Version: 1.0\r\n" .
           "Content-Type: text/plain; charset=utf-8\r\n" .
           "Content-Transfer-Encoding: 8bit\r\n\r\n";


error_reporting(E_ALL);
ini_set("display_errors", "1");
echo "UTF-8\n";

foreach ( $dbh->query($all_users) as $row) {
	$to = "{$row['firstname']} {$row['lastname']} <{$row['email']}>";
    if (mail($to, $subject, $text, $headers) ) {
        echo "{$to} kontaktad\n";
    } else {
        echo "Kunde INTE kontakta $to\n";
    }
}
