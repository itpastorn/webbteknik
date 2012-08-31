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

$subject = "Webbteknik.nu växer";

$text = <<<TXT

Hej och välkommen till webbteknik.nu alla nya användare.\r\n

(En gång till, nu med tekniskt förtydligande att jag använder UTF-8.)

Nu växer ert antal stadigt och det ger mig som utvecklare
angenäm stress. Bandbredden på webbhotellet sköt i höjden
och jag behövde köpa mer.

Under veckan som kommer, så ska jag flytta alla videos till
ett generösa webbhotell. Därefter kommer hela webbplatsen
flyttas till det. Det kommer ske en natt när ingen troligen
är uppkopplad.

Är du *lärare* och saknar befogenhet att skapa grupper
för dina elever, så hör av dig till gunther@keryx.se

Likaså kan man höra av sig dit om något strular. Akuta fixar försöker
jag ordna genast. Det finns annars en lång lista av issues på
https://github.com/itpastorn/webbteknik/issues

Jag betar av dem så fort jag hinner. Än så länge går funktion före 
form, så det tråkiga utseendet kommer tyvärr bestå någon månad till.


Låt mig förklara några saker som kan verka konstiga:

1. Det står 9 videos på startsidan, men 3 av dem gäller inte
   boken. En är för inloggningen av webbplatsen, en om PHP
   och XAPP (den finns på startsidan) och en är bara för
   lärare, men den syns inre alls än....

2. Det kan hända att man måste klicka två gånger på logga in
   knappen. Jag vet just nu inte varför.

3. Det finns nu en provisorisk sida där övningsfiler kan hämtas.
   Det finns redan elever som är förbi kapitel ett och det blev bråttom.

Hoppas ni kommer rycka webbteknik är givande!

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
