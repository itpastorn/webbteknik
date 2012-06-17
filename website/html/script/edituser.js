(function () {
    "use strict";
    $(".disabled").click( function() {
        return false;
    });
    
    // User sets his/her name
    

    // Privilege level - user has bought a book!
    // Original level
    var origlevel = +$("#origlevel").val();
    console.log("Current level: " + origlevel);
    // Disable lower levels
    if ( window.location.hash !== "#testmode" ) {
        $("input[name='priv']").each( function () {
            if ( +$(this).val() <= origlevel ) {
                $(this).attr("disabled", "disabled");
            }
        });
    }
    $("input[name='priv']").click( function () {
    	// Unless user has set first- and lastname, this is verboten
    	// TODO: Make the same check server side
    	if ( $("#firstname").val() === "" || $("#lastname").val() === "" ) {
    	    alert("Du måste ange ditt namn först");
    	    return false;
    	}
    	
        // User chooses a privilege-level. Ajax to verify.
    	var levelrequest = +$(this).val();
    	console.log("Levelrequest: "  + levelrequest);
    	
    	// Check for dumb downgrades (inputs should be disabled though)
    	// TODO: This shall not be a way to pretend that contract has been renewed
        if ( levelrequest <= origlevel ) {
            var dumb = confirm("Vill du verkligen nedgradera din behörighet?");
            if ( dumb !== true ) {
                return false;
            } else if ( window.location.hash !== "#testmode" ) {
                alert("Nej, nu pratar du i nattmössan!");
                return false;
            }
        }
    	
    	if ( levelrequest === 1 ) {
    	    return true; // No privileges = OK
    	}
    	if ( levelrequest === 3 ) {
    		alert("Ej klar funktion.");
            return false; // Not implemented yet
        }
        if ( levelrequest > 31 ) {
    		alert("Ej klar funktion. Skicka ett mejl till gunther@keryx.se.");
    	    return false; // Not implemented yet
        }
        $.ajax({
            type : 'POST',
            url  : 'api/privilege-check.php',
            data : { level: levelrequest },
            success : function (cquestion, status, xhr) {
                cquestion = JSON.parse(cquestion);
                if ( !cquestion.question ) {
                    if ( cquestion.error === "unavailable" ) {
                    	console.log("Ej implementerad nivå.");
                        alert("Nivån kan ännu inte väljas. Utvecklingsarbete pågår!");
                        $("input[value='" + origlevel + "']").attr("checked", "checked"); // 1 (av 3) förekomster
                        return false;
                    }
                }
                var canswer = prompt(cquestion.question);
                console.log(canswer);
                if ( canswer !== "" && canswer !== null ) {
            	    var msgLabel = $("input[name='priv']:checked").parent();
	                $.ajax({
	                    type: 'POST',
	                    url: 'api/privilege-check.php',
	                    data: { answer: canswer },
	                    success: function (verified, status, xhr) {
	                        // Correctly answered question?
	                    	verified = JSON.parse(verified);
	                    	if ( verified.istrue === true ) {
	                    		origlevel = levelrequest;
	                    	    console.log("Behörighet given på nivå " + origlevel);
	                    	    $("input[name='priv']:checked").attr("disabled", "disabled");
	                    	    msgLabel.removeClass("wrong").addClass("updated");
	                    	    return true;
	                    	} else {
	                    	    //
	                    		console.log("Fel svar!");
	                    	    msgLabel.removeClass("updated").addClass("wrong");
	                            $("input[value='" + origlevel + "']").attr("checked", "checked");; // 2 (av 3) förekomster
	                    	    return false;
	                    	}
	                    },
	                    error: function (errorMsg, status, xhr) {
	                        //
	                    	console.log("Fel med Ajax-kommunikation när svaret skulle kollas.");
	                    	return false;
	                    }
	                });
                } else {
                    $("input[value='" + origlevel + "']").attr("checked", "checked");; // 3 (av 3) förekomster
                    return false;
                }
            },
            error : function (confirm, status, xhr) {
                confirm = JSON.parse(confirm);
                alert("Login fel: " +  confirm.reason);
                
            }
        });
    });
}());
/*
Database will be immediately updated - user shall be notified
*/