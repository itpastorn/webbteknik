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

$subject = "Ofullbordad inloggning till webbteknik.nu";

$text = <<<TXT

Detta mejl går till dig som påbörjat en inloggning
på webbteknik.nu, men som inte fullbordat processen.

Om det beror på att det kändes svårt, så titta gärna
på instruktionsvideon:

http://webbteknik.nu/sign-in.php

Är det något tekniskt strul, som din lärare inte
kan hjälpa dig med, så svara på det här mejlet.

Har det bara inte blivit av, så tveka inte ;-)




Lars Gunther


TXT;

$headers = "From: webbteknik.nu admin<gunther@keryx.se>\r\n" .
		   "Reply-to: Lars Gunther <gunther@keryx.se>\r\n".
           "MIME-Version: 1.0\r\n" .
           "Content-Type: text/plain; charset=utf-8\r\n" .
           "Content-Transfer-Encoding: 8bit\r\n\r\n";


error_reporting(E_ALL);
ini_set("display_errors", "1");
echo "UTF-8\n";

foreach ( $dbh->query("SELECT * FROM users ORDER BY user_since ASC") as $row) {
	$to = "{$row['firstname']} {$row['lastname']} <{$row['email']}>";
    if (mail($to, $subject, $text, $headers) ) {
        echo "{$to} kontaktad\n";
    } else {
        echo "Kunde INTE kontakta $to\n";
    }
}
