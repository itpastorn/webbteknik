(function () {
    "use strict";
    var required_tech = {
        video : {
            // The name of the tech to test for
            tname : "HTML5 video med WebM eller MP4",
            // The link to MDN to explain it
            tlink : "https://developer.mozilla.org/en-US/docs/Using_HTML5_audio_and_video",
            // A boolean test
            ttest : function () {
                return !!document.createElement("video").canPlayType("video/webm") ||
                       !!document.createElement("video").canPlayType("video/mp4");
            }
        },
        strict : {
            tname : "JavaScript strict mode",
            tlink : "https://developer.mozilla.org/en-US/docs/JavaScript/Reference/Functions_and_function_scope/Strict_mode",
            ttest : function () {
                return (function(){ return this === undefined; })();
            }
        },
        classList : {
            tname : "classList",
            tlink : "https://developer.mozilla.org/en-US/docs/DOM/element.classList",
            ttest : function () {
                return !!document.createElement("span").classList; 
            }
        },
        JSON : {
            tname : "JSON",
            tlink : "https://developer.mozilla.org/en-US/docs/JavaScript/Reference/Global_Objects/JSON",
            ttest : function () {
                return !!window.JSON && !!window.JSON.parse; 
            }
        },
        getElementsByClassName : {
            tname : "getElementsByClassName",
            tlink : "https://developer.mozilla.org/En/DOM/document.getElementsByClassName",
            ttest : function () {
                return !!document.getElementsByClassName;
            }
        },
        querySelectorAll : {
            tname : "querySelectorAll",
            tlink : "https://developer.mozilla.org/En/DOM/document.querySelectorAll",
            ttest : function () {
                return !!document.querySelectorAll;
            }
        },
        DOMEvents : {
            tname : "DOM 2 events",
            tlink : "https://developer.mozilla.org/en-US/docs/DOM/element.addEventListener",
            ttest : function () {
                return !!document.body.addEventListener;
            }
        },
        SVG : {
            tname : "Inline SVG",
            tlink : "https://developer.mozilla.org/en-US/docs/SVG",
            ttest : function () {
                return !!document.createElementNS && 
                       !!document.createElementNS('http://www.w3.org/2000/svg', "svg").createSVGRect;
            }
        },
        passed_all_tests : true
    };
    var nice_to_have_tech = {
        vidplayed : {
            tname : "videoelement.played med TimeRanges",
            tlink : "https://developer.mozilla.org/en-US/docs/DOM/TimeRanges",
            ttest : function () {
                //; 
            }
        },
        // No use for Canvas yet
        canvas : {
            tname : "Canvas",
            tlink : "https://developer.mozilla.org/en-US/docs/HTML/Canvas/Drawing_Graphics_with_Canvas",
            ttest : function () {
            	var elem = document.createElement("canvas");
                return !!elem.getContext && !!elem.getContext("2d");
            }
        },
        // No use for WebGL yet
        webgl : {
            tname : "WebGL",
            tlink : "https://developer.mozilla.org/en-US/docs/WebGL/Getting_started_with_WebGL",
            ttest : function () {
                return !!window.WebGLRenderingContext;
            }
        },
    };
    
    var rtech_table = document.getElementById('required_tech');
    for ( var test_tech in required_tech ) {
        if ( required_tech[test_tech].ttest ) {
            var ntr = document.createElement("tr"),
                nth = document.createElement("th"),
                ntd = document.createElement("td"),
                nlk = document.createElement("a");
            nlk.href = required_tech[test_tech].tlink;
            nlk.innerHTML = required_tech[test_tech].tname;
            nth.scope = "row";
            nth.appendChild(nlk);
            ntr.appendChild(nth);
            ntr.appendChild(ntd);
            if ( required_tech[test_tech].ttest() ) {
                ntd.className = "tsupported";
                ntd.innerHTML = "Stöds";
            } else {
                ntd.className = "tunsupported";
                ntd.innerHTML = "Stöds inte";
                required_tech.passed_all_tests = false;
            }
        }
        rtech_table.tBodies[0].appendChild(ntr);
    }
    // Alert user of test results
    var signinbox = document.getElementsByClassName("signinbox")[0];
    if ( required_tech.passed_all_tests ) {
        var loginlink  = document.createElement("a"),
            loginimage = document.createElement("img");
        loginlink.href  = "#";
        loginlink.title = "Logga in med BrowserID";
        loginlink.id    = "browserid";
        loginimage.src = "img/sign-in-green.png";
        loginimage.alt = 'Logga in';
        loginlink.appendChild(loginimage);
        signinbox.innerHTML = ""; // reset
        signinbox.appendChild(loginlink);
    } else {
        // TODO: move messages to template object
        signinbox.innerHTML = "Inloggnig ej aktiverad. Din browser uppfyller inte de tekniska kraven. Läs nedan.";
    }
    
}());
/*
Switch to Modernizr?
Tests to add
 - More ES 5.1 - perhaps

 - drag and drop
 - hashchange

 - CSS transitions, transforms, 3D transforms, animation
 - gradients
 - media queries
*/
