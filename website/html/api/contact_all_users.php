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

$subject = "DVD-skivans innehåll klart";

echo "Påbörjar utskick\n"; 

$text = <<<TXT

Hej nya och gamla lärare på webbteknik.nu

Välkomna till ett nytt läsår. Lite snabb information om vad som hänt sedan sist.

JUST NU LIGGER SERVERN SOM HAR ALLA VIDEOS NERE! Jag har kontaktat webbhotellet
och deras tjänster är utsatta för en DDOS-attack. Förhoppningsvis är det löst
inom några timmar. (Resten av systemet ligger på ett annat webbhotell.)


1. För den som eventuellt missat det så har jag skrivit klart läroboken
   Webbserverprogrammering 1. Läs om den på http://keryx.se/blogg-72

2. Än så länge finns det inga videos till den nya boken. Elever som är nya
   i systemet kan behöva en förklaring att de inte ska titta på de befintliga
   filmerna.

   Så här ser min planering ut:
   
   * Göra ett antal tekniska uppdateringar på webblatsen som behövs för att
     kunna hantera två olika kurser.

   * Skriva klart nya övningsboken (ca en vecka + några dagar för tryckeriet
     om inget dyker upp).

   * Göra några videos om PHP. Kom gärna med önskemål på vilka moment som behöver
     kompletteras på detta vis utöver vad som står i boken.
   
   * Skriva klart Lärarhandledningen. Återigen tar jag gärna emot tips på
     vad som kan ingå. Responsen på lärarhandledningen till Webbutveckling 1
     har varit god, men jag tar gärna emot konkreta förbättringsförslag.

3.  Webbplatsen har inte fått den kärlek jag utlovat under året. Flyttar
    (pluralis), nytt jobb och andra saker har kommit emellan. Här kommer några
    råd under tiden:
    
    * Skapa nya grupper för det nya läsåret. De ni använde förra året
      kommer att avaktiveras.
      
      HAR NI REDAN GIVIT ELEVERNA EN KOD TILL EN BEFINTLIG GAMMAL GRUPP
      SÅ FORTSÄTT ANVÄNDA DEN. Jag kommer fixa det på annat sätt. 

    * Jag har två gamla elever som driver en webbfirma idag. De ska förbättra den
      grafiska designen åt mig, så att jag kan koncentrera mig på innehållet.

4. Vill du att ditt lärakonto ska vara vilande under året så mejla mig på
   gunther@keryx.se. (Liksom för alla andra sorters frågor.)


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
