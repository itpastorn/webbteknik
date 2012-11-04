<?php
/**
 * List of users, to used for sending email
 *
 */

exit("STOP SPAMMING");
session_start();
require_once '../../includes/loadfiles.php';

user::setSessionData();

user::requires(user::ADMIN);

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

header("Content-type: text/plain; charset=utf-8");

$subject = "Strul och nytt på webbteknik.nu";

echo "Påbörjar utskick\n"; 

$text = <<<TXT

Hej alla som använder webbteknik.nu

På sidan arbetsplanering, så syns inte kapitel 9.
En märklig bugg, som jag jobbar på att lösa.

Det ni främst missar är länken till en nyupplagd video om SVG:

http://webbteknik.nu/userpage.php?video=wu-lb-9-4-4

Sidan med planering för kapitel 9 finns också om man
skriver dess URL manuellt:

http://webbteknik.nu/joblist.php?book=wu1&c=9

Arbetsplanering för kapitel 11 och framåt saknas ännu i databasen.

Men övningsfilen finns för uppgift 13B.
http://webbteknik.nu/assignments.php


mvh
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
