<?php
/**
 * List of users, to used for sending email
 *
 */

//exit("Sluta spamma");
session_start();
require_once '../../includes/loadfiles.php';

user::setSessionData();

user::requires(user::ADMIN);

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

header("Content-type: text/plain; charset=utf-8");

$subject = "DVD-skivans innehåll klart";

echo "Påbörjar utskick\n"; 

$text = <<<TXT

Hej lärare

Den som väntar för länge väntar på nåot gott, hoppas jag.

Jag har nu lämnat över det som hittills är klart för att vi ska
få ut DVD-skivan till er. Jag har tingats dra ner på ambitionerna,
enligt parollen hellre något bra idag, än något perfekt aldrig.

Jag hoppas skivan kommer bli till nytta!

# Kommentarer till lärar-DVD #

Skivan har följande kataloger:

 * boken-kodexempel      Här finns HTML- och CSS-kod från boken
   * advanced            Extra kodexempel. Här finns också ineraktiva demos.
 * css	                 Denna är till för slide-systemet. Kan ignoreras.
 * laxhjalpen-demowebb   Den färdiga läxhjälpen-webbplatsen
 * media                 Alla filmer i mp4- och webm-format
 * practice-files        Filer att ge eleverna när de gör uppgifter i övningsboken
 * script                Denna är till för slide-systemet. Kan ignoreras.
 * slides                Webbteknikbaserade slides (tänk PowerPoint)
 * videos-kod            Kod som jag använt när jag gjort videofilmerna

Allt detta finns inte än, men kommer att finnas, också på [webbplatsen](http://webbteknik.nu/).



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
