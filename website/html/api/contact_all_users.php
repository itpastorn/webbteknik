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

Hej alla som använder webbteknik.nu

Under natten så uppstod ett tekniskt strul på servern som jag hoppas ska vara åtgärdat nu.

Jag har kommunicerat med webbhotellet och hoppas inom några dagar fixa detta, så att det inte
kan återupprepas.

Några nya filmer har kommit upp. Ni når dem via kapitel 9 och 10 i arbetsplaneringen.

Inloggningen ska förhoppningsvis vara enklare nu. Webbplatsen ska komma ihåg era inloggningar,
så länge som ni är inloggad på Persona-systemet. Utloggningsfunktion, som efterlysts
av somliga, är på G.

Skulle något nu strula, så hör av er. Som vanligt underlättas min felsökning om 
ni berättar vilken webbläsare ni använder och vad som syns i konsollen när ni försöker
göra något.


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
