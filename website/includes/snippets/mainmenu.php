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
?>
    </p>
    <ul>
      <li><a href="./">Startsidan</a></li>
      <li><a href="userpage.php">Arbetssida</a></li>
      <li><a href="joblist.php">Arbetsplanering</a></li>
      <li><a href="flashcards.php">Flaschards (demo)</a></li>
      <li><a href="edituser.php">Redigera användaruppgifter</a></li>
    </ul>
  </nav>

