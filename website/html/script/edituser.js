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
    /*
    if ( win.location.hash !== "#testmode" ) {
        $("input[name='priv']").each( function () {
            if ( +$(this).val() <= origlevel ) {
                $(this).attr("disabled", "disabled");
            }
        });
    }
    */
    
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

    $("#group_code_no input").click( function () {
        // Unless user has set first- and lastname, this is verboten

        // TODO: Make the same check server side
        if ( $("#firstname").val() === "" || $("#lastname").val() === "" ) {
            alert("Du måste ange ditt namn först");
            return false;
        }
        // If unchecking, do nothing
        if ( !this.checked ) {
            return true;
        }
        
        var clicked_checkbox = this;
        
        // 2013-09-09: Changing to use bookID instead

        // User chooses a privilege-level. Ajax to verify.
        var bookID       = $(this).val();
        console.log("BookID: " + bookID);

        $.ajax({
            type : 'POST',
            url  : 'api/privilege-check.php',
            data : { bookID: bookID },
            success : function (cquestion, status, xhr) {
                cquestion = JSON.parse(cquestion);
                if ( !cquestion.question ) {
                    if ( cquestion.error === "unavailable" ) {
                        console.log("Felaktigt bokval.");
                        alert("Boken kan ännu inte väljas.");
                        return false;
                    } else if ( cquestion.error === "bad call") {
                        console.log("Fel data skickade.");
                        alert("Tekniskt fel. Kontakta admin.");
                        return false;
                    }
                }
                var canswer = prompt(cquestion.question);
                console.log(canswer);
                if ( canswer !== "" && canswer !== null ) {
                    var msgLabel = $(clicked_checkbox).parent();
                    $.ajax({
                        type: 'POST',
                        url: 'api/privilege-check.php',
                        data: { answer: canswer },
                        success: function (verified, status, xhr) {
                            // Correctly answered question?
                            verified = JSON.parse(verified);
                            if ( verified.duplicate === true ) {
                                console.log("Duplicate entry in DB");
                                msgLabel.removeClass("wrong");
                                $(clicked_checkbox).attr("disabled", "disabled");
                                alert("Du har redan åtkomst till den boken");
                                return false;
                            } else if ( verified.istrue === true ) {
                                if ( verified.newlevel ) {
                                    console.log("Behörighet given på nivå " + verified.newlevel);
                                }
                                $(clicked_checkbox).attr("disabled", "disabled");
                                msgLabel.removeClass("wrong").addClass("updated");
                                return true;
                            } else if (verified.istrue === false) {
                                // Explicitly false
                                console.log("Fel svar!");
                                msgLabel.removeClass("updated").addClass("wrong");
                                $(clicked_checkbox).removeAttr("checked");
                                return false;
                            } else {
                                // Technical problem
                                alert("Tekniskt fel uppstod. Meddela admin. Bifoga gärna dump från konsollen.");
                                return false;
                            }
                        },
                        error: function (errorMsg, status, xhr) {
                            alert("Tekniskt fel med Ajax-kommunikation när svaret skulle kontrolleras.");
                            console.log("errorMsg: " + errorMsg);
                            return false;
                        }
                    });
                } else {
                    $(clicked_checkbox).removeAttr("checked");
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