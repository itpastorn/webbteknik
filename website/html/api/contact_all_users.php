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

$subject = "God jul lärare på webbteknik.nu";

echo "Påbörjar utskick\n"; 

$text = <<<TXT

Hej alla ni lärare som använder mitt material.

Låt mig börja med att be om ursäkt för alla förseningar med CD:n.

En tanke är att all kod som används till videofilmerna ska vara med
och när filmerna dröjer, så dröjer också CD:n.

Ni kan titta på koden här för alla klara filmer:

https://github.com/itpastorn/webbteknik/tree/master/webbutveckling-1/videos-kod

Klicka på ett filnamn och klicka sedan på knappen "raw", så kan ni hämta er egen kopia.


Jag förstår frustrationen över att saker drar ut på tiden. Jag är nog mest frusterad av
oss alla över detta!

När vårterminen drar igång, så är min förhoppning att resterande filmer ska vara gjorda.


Jag har i min mailbox ett litet antal oavslutade supportärenden. Någon har bett om hjälp,
och då mötts av följdfrågor från min sida. Men sedan har det blivit tyst. Jag antar att
problemen då löst sig.

Det vanligaste strulet är med inloggningen. Som ni kanske märkt är tekniken nu
enklare, då webbläsaren kommer ihåg att man är inloggad. Men när detta inte är fallet
så säger några användare att man måste klicka flera gånger. Jag har väldigt svårt
att rekonstruera problemet. Troligen har det att göra med känslighet för latens.

Ju mer tekniska detaljer jag får kring ett problem, desto enklare är det för mig att
åtgärda dem, så försök hjälpa era elever med detta. En beskrivning som "jag kan inte logga in"
eller "jag ser inte filmen" ger mig ingen vägledning alls.


I övrigt önskar jag också er GOD JUL!


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
