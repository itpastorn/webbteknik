/**
 * Adminscripts
 * 
 * TODO: Om man bekräftat ska det vara svårare att ångra
 */

// Skiljetecken underscore client side och kolon server side

$("input").click( function (e) {
    // 
    // alert(this.name + "|" +this.value);
	var what = this.name.split("_")[0];
	if ( what === "regret" ) {
		// TOD0: Bekräfta fråga ska inkludera elevens namn och klass
	    var careful = confirm("Är du säker på att eleven har ångrat sig?")
	    if ( !careful ) {
	        return false;
	    }
	}
	
    $.ajax({
        url: "ajax-regret-confirm.php?vad=" + this.name,
        success: function (data) {
            console.log(data);
            data = data.split(":");
            // alert(data[0] + data[1] + data[2]);
            if ( +data[2] === 0 ) {
                alert("Tekniskt fel. Databasen inte uppdaterad! Kontakta systemadministratör.");
                return false;
            }
            if (data[0] === "regret") {
                // Tag bort båda knapparna
            	$("input[name=regret_" + data[1] +"]").parent().html("");
            	$("input[name=confirm_" + data[1] +"]").parent().html("");
            } else if (data[0] === "confirm") {
                // Ersätt bekräfta knappen med checkmark
            	$("input[name=confirm_" + data[1] +"]").parent().html("<span title=\"Bekräftad\">&#x2713;</span>");
            	//
            } else {
                alert("Felaktigt svar från Ajaxanrop. Kontakta systemadministratör.");
            }
        },
        error: function (data) {
            console.log("Ajax sket sig. Gunther fixa!");
        }
    });
});
