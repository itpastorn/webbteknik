/**
 * Report progress automatically while watching a video
 *
 * First turn text into JS-operated buttons
 * 
 * @TODO Do not update DOM if Ajax request fails
 *
 */
(function (win, doc, undefined) {
    $('table td:nth-child(2):not(.job_is_video)').each( function () {
        var cell  = $(this),
            jobid = cell.data('jobid');
        
        // What is the present state of this task
        if ( $(cell).parent().hasClass("begun") ) {
            var curstate = "begun";
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
    }

    function reportSuccessCallback(serverdata) {
        // console.log("Report received by server");
        console.log("Server status message was: " + serverdata);
    }

}(window, window.document));