(function (win, doc, undefined) {
    "use strict";

    $("#browserid").click( function () {
        navigator.id.request({
            siteName: "webbteknik.nu"
        });
        return false;
    });

    $("#logoutbutton").on('click', function (e) {
        console.log("Attempting logout"); // Fires
        navigator.id.logout();
        e.preventDefault();
    });

    navigator.id.watch({
        loggedInUser: undefined,
        onlogin: gotAssertion,
        onlogout: function () { console.log("logout fired")}
        //onlogout: loggedOut
    });

    function gotAssertion(assertion) {
        console.log("Assertion: " + assertion);
        console.log("Assertion length: " + assertion.length);
        if ( assertion !== null ) {
            $.ajax({
                type : 'POST',
                url  : 'api/login.php',
                data : { assertion: assertion },
                success : function (userdata, status, xhr) {
                    console.log(userdata);
                    if ( userdata.email == null ) {
                        // Fail or logout
                        // loggedOut();
                        console.log("Login fail: " +  userdata.reason);
                    } else {
                        loggedIn(userdata);
                    }
                },
                error : function (res, status, xhr) {
                    console.log("Login fel: " +  res.reason);
                    navigator.id.logout();
                }
            });
        } else {
            console.log("Assertion was null - loggedOut()");
            navgator.id.logout();
        }
    }

    // Runs if assertion has been verified as OK
    // Which also means that $_SESSION has been set and DB updated with login data
    function loggedIn(userdata) {
        console.log(userdata.email + " has now logged in");
        console.log("Privlevel: " + userdata.privileges);
        // New users should be directed to edit page
        if ( +userdata.privileges < 3 ) {
            window.location.href="edituser.php";
            return true; // Function is finished
        }
        // What URL are you on? Special? Stay. Otherwise load personal start page.
        if ( window.ref ) {
            console.log("Going to " + window.ref);
            window.location.href = window.ref;
        } else {
            console.log("Going to userpage");
            window.location.href = "./userpage.php";
        }
    }
    
    // Logout
    function loggedOut() {
        console.log("Watching log out event"); // Does not fire
        $.ajax({
            type : 'POST',
            url  : 'logout.php',
            success : function (res, status, xhr) {
                console.log(res);
                window.location.href = "./";
            },
            error : function (res, status, xhr) {
                console.log(res);
                alert("Logout fungerade inte. Ajaxfel.");
            }
        });
    }
    

}(window, window.document));
