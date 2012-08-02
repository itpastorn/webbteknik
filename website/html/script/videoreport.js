(function () {
    var vid = document.querySelector("#videocontainer video");
    // The name of the video source, excluding type, using capturing parenthesis
    var video_src       = vid.getElementsByTagName("source")[0].src.match(/\/([^/]+)\.[a-z]{3,4}$/)[1],
        video_reporting = false,
        video_duration  = false
        video_start_at  = 0,
        video_first_run = true,
        video_progress  = $("#vidprogress");

    // Starting video position, from global variable in inline script
    vid.currentTime = wtglobal_start_video_at;
    
    // wtglobal_old_progressdata holds data from previous pageviews
    
    var create_report = function () {

        // Prepare an object to send via XHR to server
        var reportobj = {};
        reportobj.src       = video_src;
        reportobj.viewTotal = 0;
        reportobj.stops     = [];

        // Previous data
        var data  = {};
        data.prev = {
            stops: wtglobal_old_progressdata.stops,
            // viewTotal: wtglobal_old_progressdata.viewTotal,
            // firstStop: wtglobal_old_progressdata.firstStop
        };

        // How many snippets have been watched this time?
        var snippets  = vid.played.length;
        // Current stops, temporary array
        data.cur = {};
        data.cur.stops = [];
        for ( var i = 0; i < snippets; i += 1 ) {
            data.cur.stops[i] = { "start": vid.played.start(i), "end": vid.played.end(i) };
        }
        
        // stops index
        var si   = {};
        si.merge = 0,
        si.prev  = 0,
        si.cur   = 0;
        
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
        while ( data.prev.stops[si.prev] ) {
            console.log("adding lone prev: " + si.prev + ":" + JSON.stringify(data.prev.stops[si.prev]));
            console.log("si.merge: " + si.merge);
            reportobj.stops[si.merge] = data.prev.stops[si.prev];
            si.prev  += 1;
            si.merge += 1;
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


        // TODO loop after merge
        for ( var i = 0; i < si.merge; i += 1 ) {
            reportobj.viewTotal += reportobj.stops[i].end - reportobj.stops[i].start;
        }
        // Less than 10 seconds left = It is finished
        if ( reportobj.viewTotal >= video_duration - 10 ) {
            reportobj.status = 'finished';
        }
        reportobj.percentage_complete = Math.floor(100 * reportobj.viewTotal / video_duration);
        return reportobj;
    }
    
    var send_video_report = function() {
        var reportobj = create_report();
        video_progress.html("Du har sett " + reportobj.percentage_complete + " % av videon");
        reportdata = JSON.stringify(reportobj);
//        $.post('./api/videoreport.php', { "reportdata": reportdata }, reportSuccessCallback);
    }
    // TODO: Handle failure....
    function reportSuccessCallback(serverdata) {
        // console.log("Report received by server");
        // console.log(serverdata);
    }

    // Only "playing" is reliable, but metadataloaded would be better for fetching duration
    $(vid).on('playing', function() {
        if ( video_first_run ) {
            video_duration = this.duration;
            console.log("video duration: " + video_duration);
            video_first_run = false;
        }
        if ( typeof vid.played === "undefined" ) {
            video_progress.html("Kan inte mäta ditt tittande. (video.played attributet stöds inte)");
        } else {
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
})();
