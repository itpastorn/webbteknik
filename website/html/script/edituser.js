(function (win, doc, undefined) {
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
    if ( win.location.hash !== "#testmode" ) {
        $("input[name='priv']").each( function () {
            if ( +$(this).val() <= origlevel ) {
                $(this).attr("disabled", "disabled");
            }
        });
    }
    
    // Has got course invite?
    $("#group_code_yes").hide();
    $("#group_code_no").hide();
    // Yes I do
    $("#group_code_set_yes").removeAttr("checked").on('change', function (e) {
        $("#group_code_yes").show("fast");
        $("#my_group_id").focus().addClass("yellowfade");
        $("#group_code_no").hide().removeClass("yellowfade");
        $("#group_code_yes > input[type='submit']").removeAttr("disabled");
    });
    // No I don't
    $("#group_code_set_no").removeAttr("checked").on('change', function (e) {
        $("#group_code_no").show("fast").addClass("yellowfade").get(0).scrollIntoView(true);
        $("#group_code_yes").hide();
        $("#my_group_id").removeClass("yellowfade");
        $("#group_code_yes > input[type='submit']").attr("disabled", "disabled");
    });
    
    // Submitting a course id
    $("#privileges").on('submit', function (e) {
        e.preventDefault();
        var gid = $("#my_group_id").val();
        $("label[for='my_group_id'] > strong").removeClass("errormsg greenfade yellowfade");
        $.ajax({
            type: 'POST',
            url: 'api/join-group.php',
            data: { group_id: gid },
            success: function (answer, status, xhr) {
                var feedback = $("label[for='my_group_id'] > strong");
                answer = JSON.parse(answer);
                switch (answer.result) {
                case "joined":
                    feedback.html("Du är nu ansluten till gruppen. <a href='userpage.php'>Börja jobba</a>").addClass("greenfade");
                    break;
                case "ismember":
                    feedback.html("Du är redan med i den gruppen.").addClass("yellowfade");
                    break;
                case "nogroup":
                    feedback.html("Gruppen finns inte. Kolla koden.").addClass("errormsg");
                    break;
                default:
                    feedback.html("Servern kunde inte behandla din ansökan. Kontakta admin.").addClass("errormsg");
                }
            },
            error: function (answer, status, xhr) {
                var feedback = $("label[for='my_group_id'] > strong");
                feedback.html("Fel uppstod i kommunikationen med servern. Kontakta admin.").addClass("errormsg");
            }
        });
        
    });

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
            } else if ( win.location.hash !== "#testmode" ) {
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
}(window, window.document));
/*
Database will be immediately updated - user shall be notified
*/