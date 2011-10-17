<?php
// När du gör denna uppgift, använd en textredigerare som kan spara med teckenkodningen UTF-8
// Du behöver ännu inte förstå alla detaljer utan det viktiga är att se 

// Detta är PHP-kod. Den exekveras (körs) på servern och resultatet skickas till webbläsaren
date_default_timezone_set("Europe/Stockholm");
setlocale(LC_TIME, "sv_SE", "swedish");
$time = strftime("%c");
// Nu lämnar vi PHP kod och går över till HTML
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <title>Webbteknik exempel</title>
  <style>
    /* Här följer CSS kod som styr utseendet */
    html {
        background-color: 
    }
    body {
        font-family: sans-serif; /* typsnitt */
        width: 600px;
        margin: auto;
        background-color: 
    }
    </style>
  </head>
  <body>
    <h1>Webbteknik exempel</h1>
    <p>Här kommer det ett stycke text. Det är ganska <em>kort</em>.</p>
    <p>Tid från servern: <?php echo $time; ?></p>
    <p>Tid på klienten: <span id="time">okänd</span></p>
  </body>
</html> 
