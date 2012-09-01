  <nav class="mainmenu">
    <p class="userdata">    
<?php
if ( isset($_SESSION['userdata']) ) {
    echo <<<USERDATA
    {$_SESSION['userdata']->firstname}
    {$_SESSION['userdata']->lastname}
    ({$_SESSION['userdata']->email})
USERDATA;
}
$teacherpage = '';
if ( user::validate(user::TEACHER ) ) {
    $teacherpage = '<li><a href="teacherpage.php">Lärarsida</a></li>';
}
?>
    </p>
    <ul>
      <li><a href="./">Startsidan</a></li>
      <li><a href="userpage.php">Arbetssida</a></li>
      <li><a href="joblist.php">Arbetsplanering</a></li>
      <li><a href="flashcards.php">Flaschards (demo)</a></li>
      <li><a href="assignments.php">Övningsuppgifter</a></li>
      <li><a href="edituser.php">Redigera användaruppgifter</a></li>
      <?php echo $teacherpage; ?>
    </ul>
    <!--p>
      <small>En större uppdatering pågår just nu. Under tiden den sker
      kan somliga saker sluta fungera. Vi ber om ursäkt för detta.</small>
    </p-->
  </nav>

