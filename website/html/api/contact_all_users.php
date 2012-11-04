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

Hej lärare med elever på webbteknik.nu

Under veckan som gick fick jag några rapporter om att
videos inte laddade som de skulle. Jag tror att problemet är
löst nu.

Skriver man

www.webbteknik.nu

så ska man bli omdirigerad till

webbteknik.nu (utan www)

Detta funkade inte. Bara de första kapitlens video funkar med www i adressen.


Om det fortfarande strular, så hjälp mig helst åtgärda felet snabbt
med en detaljerad felrapport:

1. Exakt vilken video är det som inte kan ses?

2. Kolla i konsollen vad som står där.

3. På Chrome, kolla också "Nät". Jag behöver se HTTP-anropen.


Elever som skrivit adressen med "www" kan behöva logga in på nytt.
Inloggningen sker per domän.


Snabb och svår teknisk förklaring för den hågade:

Omdirigeringen slutade fungera häromveckan, då servern fick nya default-regler för mod_rewrite

Så här ser regeln ut i min .htaccess-fil, där rad 1 nu har lagts till

RewriteEngine On
RewriteCond %{HTTP_HOST} ^www\.webbteknik\.nu$ [NC]
RewriteRule ^(.*)$ http://webbteknik.nu/$1 [R=301,L]



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
