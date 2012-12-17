/*
 jshint forin:true, eqnull:true, noarg:true, noempty:true, eqeqeq:true, bitwise:true,
  strict:true, undef:true, unused:true, curly:true, browser:true, devel:true, jquery:true,
  es5:true, indent:4, maxerr:50, newcap:true, trailing: true
*/
(function () {
    "use strict";
    $("button").click( function () {
        var whichbutton = this.id;
        switch (whichbutton) {
        case "table":
            $("html").removeClass("flex").removeClass("float").removeClass("ib").addClass("table");
            window.location.hash = "type_" + whichbutton;
            break;
        case "flex":
            $("html").removeClass("table").removeClass("float").removeClass("ib").addClass("flex");
            window.location.hash = "type_" + whichbutton;
            break;
        case "float":
            $("html").removeClass("table").removeClass("flex").removeClass("ib").addClass("float");
            window.location.hash = "type_" + whichbutton;
            break;
        case "ib":
            $("html").removeClass("table").removeClass("flex").removeClass("float").addClass("ib");
            window.location.hash = "type_" + whichbutton;
            break;
        default:
            $("html").removeClass("par1 par2 par3 par5").addClass(whichbutton);
            break;
            
        }
    });
    try {
        var initial_type = window.location.hash.match(/type_([a-z]+)/)[1];
        $("html").removeClass("flex").removeClass("float").removeClass("ib").removeClass("table").
                  addClass(initial_type);
    }
    catch (e) {
        // Not set do nothing
    }
}());
