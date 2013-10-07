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

$subject = "Uppdateringar (bakom kulisserna) gjorda i helgen på webbteknik.nu";

echo "Påbörjar utskick\n"; 

$text = <<<TXT

Hej alla lärare på webbteknik.nu

1. Arbetsboken Webbserverprogrammering 1 är klar. Beställ på Thelin om ni vill ha den.
   (Lärarhandledningen kommer inte vara klar för tryck på minst 3 veckor. Precis
   som till Webbutvecklig 1 kommer den innehålla en hel del mer än bara facit.)

2. I helgen har ett stort antal uppdateringar gjorts på webbplatsen som förbereder
   marken för att rulla ut filmerna till Webbserverprogrammering. Alla som är behöriga
   att komma åt båda böckerna behöver göra ett val vilken bok de ska jobba med. Om
   allt funkar som det ska så skickas man automatiskt till sin användarsida om detta
   val inte gjorts. Valet är inte permanent, utan frågan är vilken bok man ska jobba
   med för stunden.
   
   Som smakprov är några filmer från den "andra" boken också tillgängliga och syns
   på sidan "Videos".
   
   Lärare har automatiskt behörighet till båda böckerna.
   
   Om man har skaffat konto som elev genom att svara på en kontrollfråga så gäller
   behörigheten den bok som frågan ställdes ur. Detta innebär att några elever kanske
   inte kommer åt rätt bok, de behöver då göra om proceduren.
   
   Om man fått konto genom att gå med i en grupp så kanske gruppen skapades för fel
   kurs. Hör av er till mig i så fall. Just nu är det bara en enda skola (Säffle)
   som har en grupp registrerad för Webbserverprogrammering.

3. Om ni stöter på problem så berätta så detaljerat som möjligt om vad det är.
   Säg vilken webbläsare det gäller (om någon enskild) och gärna vad som syns i
   konsollen. Ibland får jag en felrapport och svarar att jag inte kan rekonstruera
   problemet och ber om fler detaljer bara för att mötas av tystnad. Då är det
   svårt att veta om problemet löst sig eller inte.

4. Ni som har Lärarhandledningen för Webbutveckling 1 för gärna höra av er med
   konkret respons. Kom ihåg att den boken fick ni för en engångskostnad, så alla
   förbättringar i nästa upplaga får ni gratis!


Lars Gunther

P.S. För alla som tycker illa om PHP. Kolla in 
https://github.com/itpastorn/webbteknik/blob/master/website/includes/acl.php

Försök göra getList (rad 66ff) i ett annat språk och se hur bökigt det blir...

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
