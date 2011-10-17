<?php
/*
 * Enkelt dokument med lite CSS
 *
 * Skall användas i kapitel 1
 *
 * Eleverna uppmuntras att lägga in en rad i taget i CSS-filen och se hur
 * utseendet successivt ändras. Eller så görs detta med slides och iframes/skärmdumpar.
 * Eleven får, men måste inte läsa Wikipedia-artikeln. Den är mest ett exempel på en länk.
 */
?>
<!DOCTYPE html>
<html lang="sv">
  <head>
    <meta charset="utf-8" />
    <title>Enkelt HTML dokument med en aning CSS (kodexempel 1b)</title>
    <style>
      body {
          font-family: sans-serif;
          width: 500px;
          margin: auto;
          background-color: navajowhite;
      }
      h1 {
          text-align: center;
          color: brown;
          text-shadow: 2px 2px 1px white;
      }
      span {
          font-style: italic;
      }
    </style>
  </head>
  <body>
    <h1>Enkelt HTML dokument med en aning CSS</h1>
    <p>
      Här står det lite text i ett stycke. Stycke heter <span lang="en">paragraph</span>
      på engelska, därför är bokstaven p använd som namn på taggarna som omger stycket.
    </p>
    <p>
      Wikipedia har <a href="">mer information om
      <abbr title="Cascading Style Sheets">CSS</abbr></a>.
    </p>
  </body>
</html>