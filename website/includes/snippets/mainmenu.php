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
      <a href="#" title="Redigera användaruppgifter/logga ut" class="{$userlink_class} noclick">
        {$firstname} {$lastname} ({$_SESSION['userdata']->email})
      </a>
      <ul class="usermenu">
        <li><a href="edituser/">Redigera användaruppgifter</a></li>
        <li><a href="#" id="logoutbutton">Logga ut</a></li>
      </ul>

USERDATA;
}
$teacherpage = '';
if ( user::validate(user::TEACHER ) ) {
    $teacherpage = '<li><a href="teacherpage/">Lärarsida</a></li>';
}
?>

  <nav class="mainmenu">
    <div class="userdata">
<?php
echo $userdata;
?>
    </div>
    <ul class="primarymenu">
      <li><a href="./">Startsidan</a></li>
      <li><a href="userpage/">Arbetssida</a></li>
      <li><a href="joblist/">Arbetsplanering</a></li>
      <li><a href="resources/videos/">Videos</a></li>
      <li><a href="assignments/">Övningsuppgifter</a></li>
      <li><a href="resources/links/">Länkar</a></li>
      <li><a href="resources/flashcards/">Flaschards</a></li>
      <?php echo $teacherpage; ?>
    </ul>
    <!--p>
      <small>En större uppdatering pågår just nu. Under tiden den sker
      kan somliga saker sluta fungera. Vi ber om ursäkt för detta.</small>
    </p-->
  </nav>

