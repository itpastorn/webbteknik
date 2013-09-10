/**
 * Report progress automatically while watching a video
 *
 * First turn text into JS-operated buttons
 * 
 * @TODO Do not update DOM if Ajax request fails
 *
 */
(function (win, doc, undefined) {
    "use strict";
    $('table td:nth-child(2):not(.job_is_video)').each( function () {
        var curstate;
        var cell  = $(this),
            jobid = cell.data('jobid');
        
        // What is the present state of this task
        if ( $(cell).parent().hasClass("begun") ) {
            curstate = "begun";
        } else if ( $(cell).parent().hasClass("finished") ) {
            curstate = "finished";
        } else if ( $(cell).parent().hasClass("skipped") ) {
            curstate = "skipped";
        } else {
            curstate = "unset";
        }

        var btnfin = doc.createElement("button");
        btnfin.innerHTML = "Klar";
        btnfin.title = "Rapportera att uppgiften är klar";
        if ( curstate === "finished" ) {
            $(btnfin).attr("disabled", "disabled").
                      addClass("curstate");
        }
        $(btnfin).on('click', function () {
            send_progressreport(jobid, "finished");
            $(this).parent().find(".curstate").removeClass("curstate").removeAttr("disabled");
            $(this).addClass("curstate").attr("disabled", "disabled");
            // If finished one must not be able to skip (reset + skip works though)
            $(this).parent().find(".btnskip").attr("disabled", "disabled");
        });

        // If finished one must not be able to skip (reset + skip works though)
        var btnskip = doc.createElement("button");
        btnskip.innerHTML = "Skip";
        btnskip.className = "btnskip";
        btnskip.title = "Rapportera att uppgiften hoppas över";
        if ( curstate === "skipped" || curstate === "finished" ) {
            $(btnskip).attr("disabled", "disabled");
        }
        if ( curstate === "skipped" ) {
            $(btnskip).addClass("curstate");
        }
        $(btnskip).on('click', function () {
            send_progressreport(jobid, "skipped");
            $(this).parent().find(".curstate").removeClass("curstate").removeAttr("disabled");
            $(this).addClass("curstate").attr("disabled", "disabled");
        });

        var btnreset = doc.createElement("button");
        btnreset.innerHTML = "Ej klar";
        btnreset.title = "Gör om uppgiften";
        if ( curstate === "unset" ) {
            $(btnreset).attr("disabled", "disabled").
                        addClass("curstate");
        }
        $(btnreset).on('click', function () {
            send_progressreport(jobid, "reset");
            $(this).parent().find(".curstate").removeClass("curstate").removeAttr("disabled");
            $(this).addClass("curstate").attr("disabled", "disabled");
            $(this).parent().find(".btnskip").removeAttr("disabled");
        });
        
        $(this).html(""); // Delete text
        $(this).append(btnfin, btnskip, btnreset);
        // $(this).data('vidnum');

    });
    
    var send_progressreport = function (jobid, status) {
        console.log("Sending report: jobid=" + jobid + ", status=" + status);
        $.post('./api/progressreport.php', { "jobid": jobid, "status": status }, reportSuccessCallback);
    };

    function reportSuccessCallback(serverdata) {
        console.log("Server status message was: " + serverdata);
        serverdata = JSON.parse(serverdata);
        // Update DOM to show what has happened
        $("td[data-jobid='" + serverdata.jobid + "']").parent().attr('class', serverdata.status);
        
    }
    
    // Buttons to toggle hide/show for finished and skipped jobs
    
    var toggle_finished_button = doc.createElement("button");
    toggle_finished_button.innerHTML = "Dölj avklarade uppgifter";
    var finished_button_text = "Dölj";
    $(toggle_finished_button).on('click', function () {
        // Toggle text on the clicked button
        finished_button_text = ( finished_button_text === "Visa" ) ? "Dölj" : "Visa";
        $(this).html(finished_button_text + " avklarade uppgifter");
        $(".finished").toggle();
    });
    
    var toggle_skipped_button = doc.createElement("button");
    toggle_skipped_button.innerHTML = "Dölj överhoppade uppgifter";
    var skipped_button_text = "Dölj";
    $(toggle_skipped_button).on('click', function () {
        // Toggle text on the clicked button
        skipped_button_text = ( skipped_button_text === "Visa" ) ? "Dölj" : "Visa";
        $(this).html(skipped_button_text + " överhoppade uppgifter");
        $(".skipped").toggle();
    });
    
    $("#showhidebuttons").append(toggle_finished_button, toggle_skipped_button);

}(window, window.document));