<?php
/**
 * List of users, to used for sending email
 *
 */

exit("Sluta spamma");
session_start();
require_once '../../includes/loadfiles.php';

// Database settings and connection
$dbx = config::get('dbx');
// init
$dbh = keryxDB2_cx::get($dbx);

user::setSessionData();

user::requires(user::ADMIN);

header("Content-type: text/plain; charset=utf-8");

$subject = "Film om nyheter på webbteknik.nu";

echo "Påbörjar utskick\n"; 

$text = <<<TXT

Hej igen

Nu har jag börjat rulla ut systemet som gör att ni som lärare ser vad
era elever rapporterat att de gjort.

Det finns en film bara för er om detta:

http://bis.webbteknik.nu/media/nyheter-webbteknik.nu-13-10-11.webm
eller
http://bis.webbteknik.nu/media/nyheter-webbteknik.nu-13-10-11.mp4


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
