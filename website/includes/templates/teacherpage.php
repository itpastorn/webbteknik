<?php
/**
 * Template for default teacher page
 */
?>
<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <title>Lärarsida - webbteknik.nu</title>
  <?php require "../includes/snippets/dochead.php"; ?>
</head>
<body class="wide">
  <h1>Lärarsida - webbteknik.nu</h1>
  <?php 
    require "../includes/snippets/mainmenu.php";
    echo <<<SECNAV
  <ul class="secondarynav">
    <li><a href="{$pageref}#mygroups">Mina grupper</a></li>
    <li><a href="{$pageref}#admingroup">Skapa grupp</a></li>
    <li><a href="{$pageref}#myschools">Skolanslutning</a></li>
  </ul>
SECNAV;
?>
  <h2>Läxhjälpen</h2>
  <p>
    Här finns <a href="laxhjalpen-demowebb/">Läxhjälpen &ndash; bokens demowebbplats</a>.
  </p>
  <h2 id="mygroups">Mina grupper</h2>
<?php
foreach ($cur_groups as $cgroup ):
    echo <<<CGROUP
  <h3>{$cgroup->getName()} ({$cgroup->getId()}) med {$cgroup->numStudents} anslutna elever</h3>
  <p>
    <strong>Vad som kommer:</strong> Statistik om gruppen som helhet. Min/max/medel i antal gjorda uppgifter.
  </p>
  <ul>
    <li>Visa gruppens framsteg</li>
    <li>Redigera medlemslistan</li>
    <li>Redigera gruppdata</li>
  </ul>
  <p class="unobtrusive">
    Gruppen startade {$cgroup->groupStartDate}
  </p>  

CGROUP;
endforeach;
?>
  <h2 id="admingroup">Skapa ny grupp/redigera grupp</h2>
  <?php 
  if ( empty($workplaces) ):
      echo "<p>Du kan inte skapa grupper ännu, eftersom du inte angivit någon skola där du jobbar.</p>";
  else:
  echo <<<FORMCONTENTS1
  <form method="post" action="{$pageref}#admingroup_form" id="admingroup_form">
    <fieldset class="blocklabels">
      <legend>Allmän information</legend>
      <!-- g_group_id and g_group_id_msg must exist in order to modify existing group -->
      {$g_new_group_save_msg}
      <p>
        <label for="g_school_id">
          Skola<strong class="errormsg">{$new_group->errorMessage('schoolID', true)}</strong>
        </label>
        <select name="g_school_id" id="g_school_id">
          {$g_schools}
        </select>
      </p>
      <p>
        <label for="g_course_id">
          Kurs<strong class="errormsg">{$new_group->errorMessage('courseID', true)}</strong>
        </label>
        <select name="g_course_id" id="g_course_id">
          {$g_course}
        </select>
      </p>
      <p>
        <label for="g_group_nickname">
          Smeknamn på gruppen (Hur ni pratar om gruppen i dagligt tal, inga mellanslag)
          <strong class="errormsg">{$new_group->errorMessage('name', true)}</strong>
        </label>
        <input type="text" id="g_group_nickname" name="g_group_nickname" value="{$new_group->getName()}"
               placeholder="Exempel: Webbettan-13" required />
      </p>
    </fieldset>
    <fieldset class="blocklabels">
      <legend>Gruppinformation</legend>
      <p>
        <!-- Should be limited to the number of books bought for one year (with some grace overlap)-->
        <label for="g_group_max_size">
          Max antal elever i gruppen
          <strong class="errormsg">{$new_group->errorMessage('groupMaxSize', true)}</strong>
        </label>
        <input type="number" id="g_group_max_size" name="g_group_max_size" value="{$new_group->groupMaxSize}" required />
      </p>
      <p>
        <label for="g_group_start_date">
          Kursstart (gruppens livslängd är 12 månader)
          <strong class="errormsg">{$new_group->errorMessage('groupStartDate', true)}</strong>
        </label>
        <input type="date" id="g_group_start_date" name="g_group_start_date" value="{$new_group->groupStartDate}" required />
      </p>
      <p>
        <em>OBS! Man kan aldrig sätta startdatum längre in i framtiden än vad det sattes första gången.</em>
      </p>
      <p>
        <label for="g_group_url">
          Länk till eventuell kurssida
          <strong class="errormsg">{$new_group->errorMessage('groupUrl', true)}</strong>
        </label>
        <input type="url" id="g_group_url" name="g_group_url" value="{$new_group->getUrl()}"
               placeholder="Länk till kursens webbplats på din skola" />
      </p>
      <p>
        <em>OBS! Man kan aldrig sätta startdatum längre in i framtiden än vad det sattes första gången.</em>
      </p>
    </fieldset>
    <fieldset>
      <legend>Skicka gruppdata</legend>
      <p>
        <input type="hidden" name="admingroup_form_submitted" value="yes" />
        <input type="submit" value="Skicka" />
      </p>
    </fieldset>
  </form>

FORMCONTENTS1;
endif;
    echo <<<FORMCONTENTS2
  <h2 id="myschools">Mina skolor</h2>
  <form action="{$pageref}#my_schools_form" method="post" id="my_schools_form">
    <fieldset class="blocklabels">
      <legend>Skolor där du jobbar</legend>
      <div class="explanation">
        Just nu kan endast admin ändra på skolors namn, eller ta bort en lärare från en skola.
        Har ni gjort en liten felstavning, så registrera ingen ny skola. Det kan åtgärdas i sinom tid.
        Mejla gunther@keryx.se om något behöver åtgärdas.
      </div>
      <p>
        Du är registrerade som lärare på följande skolor:
      </p>
      <ul>
        {$wp_list}
      </ul>
    </fieldset>
  </form>
  <form action="{$pageref}#add_workplace_form" method="post" id="add_workplace_form">
    <fieldset class="blocklabels">
      <legend>Lägg till ny arbetsplats (koppla ihop dig med en skola)</legend>
      {$new_workplace_save_msg}
      <div class="explanation">
        Om inte din skola redan finns med i systemet så börja med att bock för <q>Skolan finns inte med i listan</q>.
        Då visas ytterligare ett formulär som ska användas först. Men om någon annan lärare på din skola redan gjort
        detta moment så kan du börja skriva din skolas namn i fältet till vänster.
      </div>
      <p>
        <label for="s_school_id">Lägg till plats där du jobbar
          (du måste använda ett färdigt förslag i detta fält)</label>
        <input type="text" name="s_school_id" id="s_school_id" list="s_school_list" required autocomplete="off" /> 
          <datalist id="s_school_list">
            <select name="s_school_sel">
              {$select_school}
            </select>
        </datalist>
      </p>
      <p>
        <input type="checkbox" id="s_new_school" name="s_new_school" value="yes" /> 
        <label for="s_new_school">Skolan finns inte i listan (markeras denna kan du lägga till den)</label>
      </p>
      <p>
        <input type="hidden" name="new_workplace_added" value="yes" />
        <input type="submit" value="Skicka" />
      </p>
    </fieldset>
  </form>
  <form action="{$pageref}#new_school_form" method="post" id="new_school_form">
    <fieldset class="blocklabels">
      <legend>Lägg till ny skola eller annan arbetsplats i systemet</legend>
      {$new_school_save_msg}
      <p>
        <label for="new_school_school_name">
          Namn på skolan
          <strong class="errormsg">{$new_school->errorMessage('name', true)}</strong>
        </label>
        <input type="text"
               placeholder="Ex. Jurtaskolan" 
               value="{$new_school->getName()}"
               {$new_school->isError('name', true)}
               name="new_school_school_name"
               id="new_school_school_name"
               maxlength="99" required />
      </p>
      <p>
        <label for="new_school_school_place">
          Ort eller annan plats för skolan
          <strong class="errormsg">{$new_school->errorMessage('schoolPlace', true)}</strong>
        </label>
        <input type="text"
               placeholder="Västra utmarken" 
               value="{$new_school->getPlace()}"
               {$new_school->isError('schoolPlace', true)}
               name="new_school_school_place"
               id="new_school_school_place"
               maxlength="49" required />
      </p>
      <p>
        <label for="new_school_school_url">
          Skolans webbplats
          <strong class="errormsg">{$new_school->errorMessage('schoolUrl', true)}</strong>
        </label>
        <input type="url" 
               placeholder="http://example.com/" 
               value="{$new_school->getUrl()}"
               {$new_school->isError('schoolUrl', true)}
               name="new_school_school_url"
               id="new_school_school_url"
               maxlength="99" />
      </p>
      <p>
        <input type="hidden" name="new_school_school_added" value="yes" />
        <input type="submit" value="Skicka" />
      </p>
      <p>
        <!--<del>Adminstratörer måste godkänna att nya skolor läggs till. Detta kan ta ett par dagar.</del>
        <big>Just nu godkänns alla skolor direkt!</big>
        Använd <a href="contact.php" class="nonimplemented">kontaktformuläret</a> om inget hänt inom 48 timmar.
      </p>-->
FORMCONTENTS2;
?>
    </fieldset>
  </form>
  <?php require "../includes/snippets/footer.php"; ?>
  <script>
(function (win, doc, undefined) {  
    if ( win.location.hash !== "#new_school_form") {
        $("#new_school_form").hide().removeClass("yellowfade");
        // Remove checked set by browser history
        $("#s_new_school").removeAttr("checked");
    }
    
    $("#s_new_school").on("click", function () {
        // Add class yellowfade when opening = check to see if open
        if ( !$("#new_school_form").hasClass("yellowfade") ) {
            $("#new_school_form").show().addClass("yellowfade").get()[0].scrollIntoView(false);
            $("#new_school_school_name").focus();
        } else {
            $("#new_school_form").hide().removeClass("yellowfade");
        }
    });
    
    $("#g_school").on("change", function () {
        if ( $(this).val() === "null" ) {
            $("#myschools").get()[0].scrollIntoView(true);
            $("#s_school_id").focus();
            win.location.hash = "#myschools";
        }
    });
    // Set focus on first field that has an error
    // TODO: Move to common
    if ( $(".error").length ) {
        $(".error")[0].focus();
    }
    
    // Debug use only
    $("#add_workplace_form").submit(
        function (e) {
            console.log($("#s_school_id").val());
            // return false;
        }
    );
    
    var add_as_workplace = $("#add_as_workplace");
    if ( add_as_workplace.length ) {
        add_as_workplace.attr("href", "#").html("Lägg till denna skola som arbetsplats.").on("click", function() {
            var schoolid = add_as_workplace.data("schoolid");
            console.log("setting " + schoolid + " as workplace");
            // Populate form above and submit
            $("#s_school_id").val(
                $("#new_school_school_name").val() + ", " + $("#new_school_school_place").val() + " (" + schoolid + ")"
            );
            // Hide the form we no longer need
            $("#new_school_form").hide().removeClass("yellowfade");
            // Focus attention on the form we are going to use
            $("#add_workplace_form").get()[0].scrollIntoView(true);
            win.location.hash = "add_workplace_form";
            $("#add_workplace_form").submit();
            return false;
        });
    }
    // If user does not want to use the convenience link, hide it.
    $("#new_school_name").on('focus', function (e) {
        if ( add_as_workplace.length ) {
            add_as_workplace.hide();
        }
    });
    
    var goto_create_group = $("#goto_create_group");
    if ( goto_create_group.length ) {
        goto_create_group.attr("href", "#").html("Skapa en grupp för den här skolan.").on("click", function (e) {
            e.preventDefault();
            $("#admingroup").get()[0].scrollIntoView(true);
            $("#g_school").val(goto_create_group.data('schoolinfostring'));

        });
    }
    // If user does not want to use the convenience link, hide it.
    $("#s_school_id").on('focus', function (e) {
        if ( goto_create_group.length ) {
            goto_create_group.hide();
        }
    });
    
}(window, window.document));
  </script>
  
</body>
</html>
