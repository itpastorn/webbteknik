(function (window, undefined) {
    var vid = document.querySelector("#videocontainer video");
    if ( !vid ) {
        return;
    }
    // The name of the video source, excluding type, using capturing parenthesis
    var video_src       = vid.getElementsByTagName("source")[0].src.match(/\/([^/]+)\.[a-z]{3,4}$/)[1],
        video_reporting = false,
        video_duration  = false
        video_start_at  = 0,
        video_first_run = true,
        video_progress  = $("#vidprogress"),
        video_status    = wtglobal_old_status;

    // Starting video position, from global variable in inline script
    // TODO Check why this sometimes says
    //     "InvalidStateError: An attempt was made to use an object that is not, or is no longer, usable"
    //     Page reload is needed, but why?
    vid.currentTime = wtglobal_start_video_at;
    
    // Tell old status
    var status_string = {
        begun : "Påbörjad",
        skipped : "Överhoppad",
        finished : "Färdigsedd",
        unset : "Ej påbörjad"
    };
    video_progress.html("Status för denna video: " + status_string[video_status]);

    // wtglobal_old_progressdata holds data from previous pageviews
    
    var create_report = function () {

        // Prepare an object to send via XHR to server
        var reportobj = {};
        reportobj.src       = video_src;
        reportobj.viewTotal = 0;
        reportobj.stops     = [];
        reportobj.status    = video_status;
        // Previous data
        var data  = {};
        // TODO Check that old data exists and is not corrupt
        if (
            wtglobal_old_progressdata.stops     && 
            wtglobal_old_progressdata.viewTotal && 
            wtglobal_old_progressdata.firstStop
        ) {
            data.prev = {
                stops: wtglobal_old_progressdata.stops,
                viewTotal: wtglobal_old_progressdata.viewTotal,
                firstStop: wtglobal_old_progressdata.firstStop
            };
        } else {
            data.prev = {
                stops: null,
                viewTotal: null,
                firstStop: null
            };
        }

        if ( typeof vid.played === "undefined" ) {
            video_progress.html("Kan inte mäta ditt tittande. (video.played attributet stöds inte)");

            // Keep old data in order not to accidenally set them to null
            // Code duplicated below FIXME
            reportobj.stops     = data.prev.stops;
            reportobj.viewTotal = data.prev.viewTotal;
            reportobj.firstStop = data.prev.firstStop;
            console.log("Only old data - played attribute not supported: " + JSON.stringify(reportobj));
            return reportobj;
        } else {

            // How many snippets have been watched this time?
            var snippets  = vid.played.length;

            // Has anything been played at all since page load?
            if ( !snippets ) {
                // Code duplication FIXME
	            // Keep old data in order not to accidenally set them to null
	            reportobj.stops     = data.prev.stops;
	            reportobj.viewTotal = data.prev.viewTotal;
	            reportobj.firstStop = data.prev.firstStop;
	            console.log("Only old data - no new snippets: " + JSON.stringify(reportobj));
	            return reportobj;
            }
            
            // Current stops, temporary array
            data.cur = {};
            data.cur.stops = [];
            for ( var i = 0; i < snippets; i += 1 ) {
                data.cur.stops[i] = { "start": vid.played.start(i), "end": vid.played.end(i) };
            }
            
            // Was there any old data? If not, no need to merge
            
            // si = stops index
            var si   = {};
            si.merge = 0,
            si.prev  = 0,
            si.cur   = 0;

            if ( data.prev.stops ) {
	            
	            if ( data.prev.stops[si.prev].start <= data.cur.stops[si.cur].start ) {
	                var use   = 'prev',
	                    other = 'cur';
	            } else {
	                var use   = 'cur',
	                    other = 'prev';
	            }
	            while ( si.merge < 8 && (data.prev.stops[si.prev] && data.cur.stops[si.cur]) ) {
	                console.log("Using: " + use);
	                console.log("si[cur]: " + si['cur']);
	                console.log("si[prev]: " + si['prev']);
	    
	                reportobj.stops[si.merge]       = {};
	                reportobj.stops[si.merge].start = data[use].stops[si[use]].start;
	    
	                while ( data[use].stops[si[use]].end > data[other].stops[si[other]].start - 0.2 ) {
	                    // Subtract 0.2 above to get a little margin
	                    if ( data[other].stops[si[other]].end < data[use].stops[si[use]].end ) {
	                        si[other] += 1;
	                        if ( typeof data[other].stops[si[other]] === "undefined" ) {
	                            console.log("Breaking - no more other = "+ other);
	                            break;
	                        }
	                    } else {
	                        // Switch roles, the other end is further up
	                        var temp = use;
	                        use = other;
	                        other = temp;
	                        console.log("switcherooo, use is now: " + use);
	                    }
	                }
	    
	                reportobj.stops[si.merge].end = data[use].stops[si[use]].end;
	                si.merge += 1;
	    
	                // Find next lowest start
	                si[use] += 1;
	                if ( data.prev.stops[si.prev] && data.cur.stops[si.cur] ) {
	                    if ( data.prev.stops[si.prev].start <= data.cur.stops[si.cur].start ) {
	                        var use   = 'prev',
	                            other = 'cur';
	                    } else {
	                        var use   = 'cur',
	                            other = 'prev';
	                    }
	                }
	            }
	            // Any more old stops?
	            while ( data.prev.stops[si.prev] ) {
	                console.log("adding lone prev: " + si.prev + ":" + JSON.stringify(data.prev.stops[si.prev]));
	                console.log("si.merge: " + si.merge);
	                reportobj.stops[si.merge] = data.prev.stops[si.prev];
	                si.prev  += 1;
	                si.merge += 1;
	            }
            }

            while ( data.cur.stops[si.cur] ) {
                console.log("adding lone cur: " + si.cur + ":" + JSON.stringify(data.cur.stops[si.cur]));
                console.log("si.merge: " + si.merge);
                reportobj.stops[si.merge] = data.cur.stops[si.cur];
                si.cur   += 1;
                si.merge += 1;
            }
    
            reportobj.firstStop = reportobj.stops[0].end;
            console.log("P:" + JSON.stringify(data.prev.stops));
            console.log("C:" + JSON.stringify(data.cur.stops));
            console.log("M:" + JSON.stringify(reportobj.stops));
    
            // Calculate total watch length
            for ( var i = 0; i < si.merge; i += 1 ) {
                reportobj.viewTotal += reportobj.stops[i].end - reportobj.stops[i].start;
            }
            // Less than 10 seconds left = It is finished
            if ( reportobj.viewTotal >= video_duration - 10 ) {
                video_status = 'finished';
            }
            reportobj.percentage_complete = Math.floor(100 * reportobj.viewTotal / video_duration);
        }
        return reportobj;
    }
    
    var send_video_report = function() {
        var reportobj = create_report();
        if ( reportobj.percentage_complete ) {
            video_progress.html("Du har sett " + reportobj.percentage_complete + " % av videon");
        }
        reportdata = JSON.stringify(reportobj);
        $.post('./api/videoreport.php', { "reportdata": reportdata }, reportSuccessCallback);
    }
    // TODO: Handle failure....
    function reportSuccessCallback(serverdata) {
        // console.log("Report received by server");
        console.log("Server status message was: " + serverdata);
    }

    // Only "playing" is reliable, but metadataloaded would be better for fetching duration
    $(vid).on('playing', function() {
        if ( video_first_run ) {
            video_duration = this.duration;
            console.log("video duration: " + video_duration);
            video_first_run = false;
            if ( video_status === "unset" ) {
                video_status = "begun";
            }
        }
        if ( typeof vid.played !== "undefined" ) {
            // Report every 10th second
            video_reporting = setInterval(send_video_report, 4000);
        }
    });
    $(vid).on('pause', function() {
        console.log("Video paused");
        video_reporting && clearInterval(video_reporting);
        send_video_report();
        video_reporting = false;
    });
    $(vid).on('ended', function() {
        console.log("Video ended");
        video_reporting && clearInterval(video_reporting);
        send_video_report();
        video_reporting = false;
    });
    // Skip this video manually
    // TODO: Skip button only active if needed, else replaced with message
    $('#skipvid').removeAttr("disabled").on('click', function () {
        if ( video_status === "unset" || video_status === "begun" ) {
            video_status = "skipped";
        }
        video_reporting && clearInterval(video_reporting);
        console.log("Skipping this video");
        send_video_report();
        video_reporting = false;
    });
    
})(window);
