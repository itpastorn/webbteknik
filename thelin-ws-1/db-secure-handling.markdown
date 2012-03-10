= Säkra databasuppkopplingar i PHP =

Här kommer lite råd och tips för hur du bör hantera dina uppkopplingsinställlningar för PHP-skript.

Av praktiska skäl måste vi dock göra en del undantag på ne.keryx.se och det är ett av skälen till varför jag tvingar er använda andra lösenord än vad ni själva hade valt.


== Backup ==

Ska tas regelbundet och finnas "off-site". Om backupen ligger på samma maskin som databasen och den kraschar, så hjälper backupen föga.

På ne.keryx.se tas backup en gång/vecka.


== Inte root ==

Viktigast!

PHP-skript ska inte köras med root-privilegier, utan med en dedikerad användare med *minsta* möjliga privilegier.


== Unika lösenord ==

Sannolikt kommer ditt lösenord någonstans ligga i klartext och även om det inte gör det, så kan det ändå avslöjas.

 * Använd inte samma lösenord som du har på Skype/Facebook/Hotmail, etc
 * Använd inte samma lösenord som du har för att logga in på servern/webbhotellet!
 * Använd inte samma lösenord för olika applikationer du gjort, utan låt varje vara unikt.
 
Om ett lösenord kommer på villovägar, så ska alla dina andra applikationer vara säkra.


== Akta dig! ==

Lägg inte upp filen som innehåller dina inloggningsuppgifter på ett öppet projekt på Github eller liknande.

Det har också hänt att folk av misstag postar sina lösenord på hjälpforum, när de ber om just hjälp...


== Inte i webbroten ==

Filen som innehåller uppkopplingsinställningarna bör placeras _nedanför_ eller vid _sidan_ om webbroten.

Över huvud taget bör det i webbroten bara finnas sådana filer som motsvarar en URL. Allt som inkluderas (settings, funktioner, klassser, mallar, etc) bör placeras på samma sätt.


== Säkrade filer med _shared hosting_ ==

På webbhotellet ska inte en användare kunna komma åt en annan användares filer.

 * chroot är ett minimumkrav
 * Helst dedikerad virtuell maskin (vilket inte är detsamma som en vhost i Apache)


== Enterprise security ==
 
 Om du bygger en applikation som driver en applikation för ett företag eller av andra skäl är av kritisk betydelse:
 
 * Lösenordet ska aldrig finnas i en textfil i klartext. Det _finns_ andra lösningar.
 * Applikationens loggar ska kollas med täta och jämna mellanrum för att upptäcka misstänkta intrångsförsök.
 

