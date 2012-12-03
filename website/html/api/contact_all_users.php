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

$subject = "Videolista och enklare länkar";

echo "Påbörjar utskick\n"; 

$text = <<<TXT

Hej återigen all på webbteknik.nu

Det finns nu en ny sida där alla filmer visas i en tabell.

http://webbteknik.nu/resources/videos/

En länk finns till denna sida i menyn. Dock ser man inte statusrapporten
(om man sett filmen), som via Arbetsplaneringssidan.

Det finns några filmer som inte dyker upp om man bläddrar med föregående
och nästa knapparna. Dessa kommer man alltså åt via denna nya sida (eller
via sidan Arbetsplanering).

Dessutom finns det en sida där alla hittills gjorda set med flashcards
visas:

http://webbteknik.nu/resources/flashcards/

Där finns också några set som kan användas för kursen Datorteknik 1.

Nu är också alla länkar förenklade. Det borde ni egentligen inte
märka utom genom att webbläsaren markerar sidor ni redan besökt
som obesökta (med länkens färg).

Alla gamla URL:er fortsätter dock fungera.

Exempel på gamla URL:er
http://webbteknik.nu/assignments.php
http://webbteknik.nu/userpage.php?video=kap-1-a-1
http://webbteknik.nu/userpage.php?vidnum=11
http://webbteknik.nu/joblist.php?book=wu1&c=3

Vad de nu heter
http://webbteknik.nu/assignments/
http://webbteknik.nu/userpage/video/kap-1-a-1/
http://webbteknik.nu/userpage/wu1/vidnum/11/
http://webbteknik.nu/joblist/wu1/3/

Den tekniska förklaringen är att jag gömmer GET-parametrarna
med hjälp av rewrite-regler. Sådant får man lära sig i kursen
Webbutveckling 1 (mot slutet).


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
