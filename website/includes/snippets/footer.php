<?php
/**
 * The default page footer
 * 
 * It includes JQuery and all common JS-files
 *
 */
?>
  <footer>
    <ul>
      <li><a href="contact.php" class="nonimplemented">Kontakt</a></li>
      <li><a href="userterms.php" class="nonimplemented">Användarvillkor</a></li>
      <li><a href="http://www.skolportalen.se/">Skolportalen</a></li>
      <!-- toggle explanations on -->
    </ul>
    <pre>
      <?php var_dump($_GET); var_dump($_SERVER); ?>
    </pre>
  </footer>
  <script src="http://code.jquery.com/jquery-1.7.2.min.js"></script>
  <script src="script/common.js"></script>
  