<?php
/*
 * Enkelt HTML-dokument
 *
 * Skall användas först i kapitel 1
 * Eleven får, men måste inte läsa Wikipedia-artikeln. Den är mest ett exempel på en länk.
 */

header("Content-type: text/html; charset=utf-8");
?>
<!DOCTYPE html>
<html lang="sv">
  <head>
    <meta charset="utf-8" />
    <title>Kombinationsselektorer</title>
    <style>
    #a > p {
        /* P-barn till element med id a */
        color: red;
    }
    #a span {
        /* Span-avkomlingar till element med id a */
        color: blue;
    }
    #a > .bb {
        /* Ett element med klassen bb som är barn till element med id a */
        color: green;
    }
    #a > .bb + p {
        /* Ett p-element som är närmast efterföljande syskon till ett element som har klassen bb och i sin tur är barn till id=a */
        color: black;
-moz-text-decoration-line: underline;
-moz-text-decoration-style: wavy;
-moz-text-decoration-color: orange;
    }
    </style>
  </head>
  <body>
    <h1>Kombinationsselektorer</h1>
    <div id="a">
      <p>Röd text.</p>
      <p class="bb">Grön text.</p>
      <p>Svart text. <span>Blå text.</span></p>
    </div>
  </body>
</html>
