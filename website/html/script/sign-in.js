(function (win, doc, undefined) {
    "use strict";
    $("#browserid").click( function () {
        navigator.id.request({
            siteName: "webbteknik.nu"
        });
        // navigator.id.get(gotAssertion);
        return false;
    });
    navigator.id.watch({
        loggedInUser: undefined,
        onlogin: gotAssertion,
        onlogout: function () {
            console.log("Log out not yet implemented");
        }
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
                    // userdata = JSON.parse(userdata);
                    // Already interpreted as an object
                    if ( userdata.email == null ) {
                        // Fail or logout
                        // loggedOut();
                        console.log("Login fail: " +  userdata.reason);
                    } else {
                        loggedIn(userdata);
                    }
                },
                error : function (res, status, xhr) {
                    // res = JSON.parse(res.responseText);
                    console.log("Login fel: " +  res.reason);
                }
            });
        } else {
            // loggedOut();
            console.log("Assertion was null - loggedOut()");
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

}(window, window.document));
