<?php
/**
 * Primary navigation for the site
 */

$userdata       = '';
$userlink_class = '';
if ( isset($_SESSION['userdata']) ) {
	if ( empty($_SESSION['userdata']->firstname) ) {
	    // Bug user to submit real name
	    $userlink_class = 'errormsg';
	    $firstname = 'Förnamn saknas!';
	} else {
	    $firstname = $_SESSION['userdata']->firstname;
	}
	if ( empty($_SESSION['userdata']->lastname) ) {
	    // Bug user to submit real name
	    $userlink_class = 'errormsg';
	    $lastname = 'Efternamn saknas!';
	} else {
	    $lastname = $_SESSION['userdata']->lastname;
	}
    $userdata = <<<USERDATA
    <a href="edituser.php" title="Redigera användaruppgifter" class="{$userlink_class}">
      {$firstname} {$lastname} ({$_SESSION['userdata']->email})
    </a>
USERDATA;
}
$teacherpage = '';
if ( user::validate(user::TEACHER ) ) {
    $teacherpage = '<li><a href="teacherpage.php">Lärarsida</a></li>';
}
?>

  <nav class="mainmenu">
    <p class="userdata">    
<?php
echo $userdata;
?>
    </p>
    <ul>
      <li><a href="./">Startsidan</a></li>
      <li><a href="userpage.php">Arbetssida</a></li>
      <li><a href="joblist.php">Arbetsplanering</a></li>
      <li><a href="assignments.php">Övningsuppgifter</a></li>
      <li><a href="flashcards.php">Flaschards (demo)</a></li>
      <?php echo $teacherpage; ?>
    </ul>
    <!--p>
      <small>En större uppdatering pågår just nu. Under tiden den sker
      kan somliga saker sluta fungera. Vi ber om ursäkt för detta.</small>
    </p-->
  </nav>

