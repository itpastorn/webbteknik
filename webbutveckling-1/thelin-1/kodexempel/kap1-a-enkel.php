<?php
/*
 * Enkelt HTML-dokument
 *
 * Skall användas först i kapitel 1
 * Eleven får, men måste inte läsa Wikipedia-artikeln. Den är mest ett exempel på en länk.
 */
?>
<!DOCTYPE html>
<html lang="sv">
  <head>
    <meta charset="utf-8" />
    <title>Enkelt HTML dokument</title>
    <style>
     body {
         font-family: sans-serif;
         width: 500px;
         margin: auto;
         background-color: lime;
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
      <abbr title="Hyper Text Markup Language">HTML</abbr></a>.
    </p>
  </body>
</html>