<?php
/*
 * Hur klasser funkar i CSS
 * 
 * Underlag för video
 */
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="utf-8" />
  <title>Några stycken med olika klasser + CSS</title>
  <style>
    body {
        background-color: silver;
        margin: 40px auto;
        width: 400px;
        font-family: sans-serif;
        font-size: large;
    }
    .foo {
        color: darkgreen;
        border-color: darkgreen;
    }
    .bar {
        color: darkred;
        border-color: darkred;
    }
    .bar.foo {
        color: navy;
    }
    p {
        padding: 10px;
        border: 2px solid pink;
        border-radius: 5px;
    }
  </style>
</head>
<body>
  <p class="foo">Stycke med class=&quot;foo&quot;. Lorem ipsum dolor sit amet.</p>
  <p class="bar">Stycke med class=&quot;bar&quot;. Lorem ipsum dolor sit amet.</p>
  <p class="foo bar unused">Stycke med class=&quot;foo bar&quot;. Lorem ipsum dolor sit amet.</p>
</body>
</html>
<!--
#. Varför har alla stycken typsnitt utan seriffer och x-large textstorlek? (Ärver från body)
#. Varför har alla stycken 2px bred kantlinje med runda hörn? (Typselektorn p)
#. Varför har inga stycken rosa kantlinje? (Mer specifika regler längre upp upphäver. Specificitet övertrumfar ordningsföljd.
      OBS! Man brukar dock för läsbarhetens skull skriva elementselektorer före mer specifika klass-selektorer.)
#. Varför har första stycket mörkgrön textfärg och kantlinje? (Klassen foo)
#. Varför har andra stycket mörkröd textfärg och kantlinje? (Klassen bar)
#. Varför har tredje stycket mörkröd kantlinje och inte mörkgrön? (Klassen bar står efter klassen foo i CSS-koden och de är lika specifika)
#. Varför har tredje stycket marinblå textfärg? (Selektorn .bar.foo tillämpas på alla element som har de två klasserna
      Lägg märke till att ordningen skiljer sig i selektorn och i HTML-koden och klassen "dummy" inte påverkar resultatet.)
-->