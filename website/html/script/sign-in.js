(function () {
    "use strict";
	$("#browserid").click( function () {
	   navigator.id.get(gotAssertion);
	   return false;
	});
	function gotAssertion(assertion) {
	    if ( assertion !== null ) {
	        $.ajax({
	            type : 'POST',
	            url  : 'api/login.php',
	            data : { assertion: assertion },
	            success : function (userdata, status, xhr) {
	            	userdata = JSON.parse(userdata);
	                if ( userdata.email == null ) {
	                    // Fail or logout
	                    // loggedOut();
	                    console.log("Login fail: " +  userdata.reason);
	                } else {
	                    loggedIn(userdata);
	                }
	            },
	            error : function (res, status, xhr) {
	                res = JSON.parse(res);
	                alert("Login fel: " +  res.reason);
	            }
	        });
	    } else {
	        // loggedOut();
	        console.log("Assertion was null - loggedOut()")
	    }
	}
	// Runs if assertion has been verified as OK
	// Which also means that $_SESSION has been set and DB updated with login data
	function loggedIn(userdata) {
	    console.log(userdata.email + " has now logged in");
	    // New users should be directed to edit page
	    if ( +userdata.privileges < 3 ) {
	        window.location.href="edituser.php";
	    }
	    // What URL are you on? Special? Stay. Otherwise load personal start page.
	    if ( window.ref ) {
	        window.location.href = window.ref;
	    } else {
	        window.location.href = "./userpage.php";
	    }
	}
}());
