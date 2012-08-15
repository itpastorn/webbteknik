/**
 * Shared functions
 */
(function (win, doc, undefined) {
    "use strict";

    // Find all usertips, attach toggle link/button
    $('.usertip').each( function () {
        var tipname = $(this).data('tipname');
        // Create a button that is used to 
        var hidetipsbtn = document.createElement("button");
        hidetipsbtn.innerHTML = "Dölj tips (använder cookie)";

        $(hidetipsbtn).on('click', function () {
            hide_user_tip(tipname, $(this).parent());
        })
        
        this.appendChild(hidetipsbtn);
        
        var teststring = new RegExp(tipname + '=no',"g");
        if ( doc.cookie.match(teststring) ) {
            hide_user_tip(tipname, $('.usertip:[data-tipname=' + tipname + ']'));
        }
    });
    
    /**
     * Function that hides user tips
     * 
     * @param tipname String The name from data-attribute of the particular tip
     * @param tiptext JQuery-Element The element to hide
     */
    function hide_user_tip(tipname, tiptext) {
        var now     = new Date(),
            nowYear = now.getFullYear(),
            futYear = +nowYear + 2,
            expires = now.toGMTString().replace(nowYear, futYear);
        
        doc.cookie = tipname + "=no; expires=" + expires;

        tiptext.hide();
        
    }
    
    // If there are any user tips, perhaps hidden, add a reveal link to footer
    if ( $(".usertip").length ) {
        // Show tips using a link in the footer
        var show_tips_item = doc.createElement("li"),
            show_tips_link = doc.createElement("a");
        show_tips_link.href = "#";
        show_tips_link.innerHTML = "Visa användningstips på denna sida";
        $(show_tips_link).on("click", function (e) {
            e.preventDefault();
            // TODO Yellow flash/fade (use CSS animation!)
            $(".usertip").show().each( function () {
                var tipname = $(this).data('tipname');
                document.cookie = tipname + "=yes; expires=0";
            });
        });
        show_tips_item.appendChild(show_tips_link);
        $("footer ul").append(show_tips_item);
    }

    // Disable links to not yet implemented features
    $(".nonimplemented").on('click', function () { return false});
    
}(window, window.document));
