# SQL – kom ihåg lapp

## [SELECT](http://dev.mysql.com/doc/refman/5.6/en/select.html)

    SELECT
    SELECT \<kolumner\>
    FROM \<tabell\>
    WHERE \<villkor\>
    ORDER BY \<kolumn\> ASC/DESC [, \<kolumn\> ASC/DESC]

## [INSERT](http://dev.mysql.com/doc/refman/5.6/en/insert.html)

    INSERT
    INSERT [INTO] \<tabell\> ( \<kolumner\> ) 
    VALUES ( \<värden\> )

Tips! Utelämna det fält som är auto_increment ifrån kolumnuppräkningen.

## [DELETE](http://dev.mysql.com/doc/refman/5.6/en/delete.html)

    DELETE
    DELETE FROM \<tabell\>
    WHERE \<villkor\>
    \[LIMIT 0,1]     tar bara bort en rad även om villkoret matchar flera.

Glöm aldrig att använda where!

## [UPDATE](http://dev.mysql.com/doc/refman/5.6/en/update.html)

    UPDATE \<tabell\>
    SET \<kol_1\> = \<värde_1\>,
    \<kol_2\> = \<värde_2\>,
    \<kol_3\> = \<värde_3\>,
    etc.
    WHERE \<villkor\>

Glöm aldrig att använda where!

## Tips om tabellnamn och kolumnnamn

 * MySQL klarar, men undvik ändå, alla icke engelska bokstäver. Det strular förr eller senare.
 * MySQL skiljer – på tvärs mot standarden – på stora och små bokstäver.
 * Om tabellen heter ”foo” så är det en enkel idé att låta primärnyckelfältet heta ”fooID” eller ”foo_id”

## Värden

 * Numeriska värden skrivs rakt upp och ner som en siffra
 * Strängar skrivs inom (vanligen) enkla citationstecken
 * Om man använder prepared statements i PHP, så fixar PHP citationstecknen åt dig om de skulle behövas.
 * Sökning på strängar är normalt inte skiftlägeskänslig.

## Villkor

 * Till skillnad från PHP så används inte dubbla likamedstecken.
 * Normala matematiska jämförelser kan användas: =, \<, \>, \<=, \>=, \<\> (det sista betyder ”inte lika med”)
 * Man kan leta efter alternativa värden med IN:

    SELECT * FROM tab WHERE field IN (2,4,6,8)

* Man kan söka på jokertecken I strängar med % (matchar en eller flera förekomster) och LIKE

    SELECT * FROM tab WHERE field LIKE ‘foo%’

Sista exemplet hittar alla som börjar på “foo”

## Aggregatfunktioner

Du kan hitta max, medel, minimum, etc

    SELECT MAX(age) FROM users
    WHERE country IN (’sweden’,’denmark’,’finland’,iceland’,’norway’)

Exemplet hittar den äldste användaren som bor i norden.

## [Tidsfunktioner](http://dev.mysql.com/doc/refman/5.6/en/date-and-time-functions.html)

Just nu: NOW()

    INSERT INTO inlagg (subject,message,time)
    VALUES (‘Jag tycker’,’Det var bra sagt…’,NOW())

Unix timestamp (antalet sekunder sedan midnatt 1970-01-01)

    UNIX_TIMESTAMP(\<datum och tid som sträng\>)


