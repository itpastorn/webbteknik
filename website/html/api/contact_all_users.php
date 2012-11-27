<?php
/**
 * List of users, to used for sending email
 *
 */

exit("Sluta spamma");
session_start();
require_once '../../includes/loadfiles.php';

user::setSessionData();

user::requires(user::ADMIN);

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

header("Content-type: text/plain; charset=utf-8");

$subject = "Videostrul fixat hoppas jag";

echo "Påbörjar utskick\n"; 

$text = <<<TXT

Hej alla lärare på webbteknik.nu

Jag får frågor om CD:n som hör till lärarhandledningen.

Jag har blivit försenad med den och ber om ursäkt. Mina
första planer var att göra ett antal interaktiva demos
till varje kapitel, men det tar helt enkelt för lång tid.

Det har också känts mer akut att åtgärda webbplatsen och göra 
fler videofilmer till den. Nu är det över 700 användare (kul) och
det innebär att varje uppdatering måste göras med stor försiktighet.

Här kommer dock ett par saker som gjorts, fast några av er har sett
dem förut:

http://webbteknik.nu/interactive/box-models.html
http://webbteknik.nu/interactive/color-wheel.html


Jag tar gärna emot tips på vad ni önskar se rent konkret
som slides (typ PowerPoint).


Titelskärmen för videon om SVG blev fel. Den säger att det är
för bokavsnitt 1.0 och 1.1... Jag kommer vara på 
"fel" dator ett par dagar, så detta fixas först i slutet på veckan.


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

foreach ( $dbh->query($teachers) as $row) {
	$to = "{$row['firstname']} {$row['lastname']} <{$row['email']}>";
    if (mail($to, $subject, $text, $headers) ) {
        echo "{$to} kontaktad\n";
    } else {
        echo "Kunde INTE kontakta $to\n";
    }
}
