<?php
/*
 * gradient demo
 */
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="utf-8" />
  <title>Demo av gradienter</title>
  <style>
.foo {
    margin: 2em auto;
    width: 400px;
    height: 100px;
    border-radius: 20px;
    background-image: -moz-linear-gradient(0% 50% 0deg,red, green, blue 100%);
    background-image: -webkit-gradient(linear, 0% 0%, 0% 100%, from(red), to(green), color-stop(1,blue))
}
  </style>
</head>
<body>
  <div class="foo"></div>
</body>
</html>