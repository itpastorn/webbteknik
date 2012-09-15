(function () {
    "use strict";
    /*
    What to capability detect?
        - classList
        - querySelectorAll
        - previousElementSibling & nextElementSibling
        - 3D-transforms...
        - transitionend
    */
    var the_list = document.getElementById("flashcards");
    var cur_num  = 1 * document.getElementById("curnum").innerHTML;
    var tot_num  = 1 * document.getElementById("totnum").innerHTML;

    if ( cur_num === tot_num ) {
        document.getElementById("goto_nextcard").disabled = "disabled";
    } else {
        document.getElementById("goto_nextcard").removeAttribute("disabled");
    }
    if ( cur_num === 1 ) {
        document.getElementById("goto_prevcard").disabled = "disabled";
    } else {
        document.getElementById("goto_prevcard").removeAttribute("disabled");
    }

    var flipcard = function () {
        the_list.classList.toggle("flipped"); // temp solution with global var
        if ( !the_list.classList.contains("flipped") ) {
            setTimeout( function () {
                move_forward_or_backward("next");
            }, 700);
            /* TODO: Set with transition-end instead of timeOut */
        }
    }
    var move_forward_or_backward = function (direction) {
        // Avoid live DOM Collections by using querySelector
        var active_cards = the_list.querySelectorAll(".activecard");
        var prev_cards   = the_list.querySelectorAll(".prevcard");
        var next_cards   = the_list.querySelectorAll(".nextcard");
        /*
        console.log("a = " + active_cards.length);
        console.log("n = " + next_cards.length);
        console.log("p = " + prev_cards.length);
        */
        // Are there any more cards in the list?
        var cards_before_prev = prev_cards.length && prev_cards[0].previousElementSibling;
        var cards_after_next  = next_cards.length && next_cards[1].nextElementSibling;

        if ( direction === "next" && next_cards.length ) {
            if ( cards_after_next ) {
            next_cards[1].nextElementSibling.classList.add("nextcard");
            next_cards[1].nextElementSibling.nextElementSibling.classList.add("nextcard");
            } else {
                // TODO: Disable button
                // And find a way to-re-enable when moving the other way
                document.getElementById("goto_nextcard").disabled = "disabled";
            }
            document.getElementById("goto_prevcard").removeAttribute("disabled");

            next_cards[0].classList.remove("nextcard");
            next_cards[1].classList.remove("nextcard");
            next_cards[2] && next_cards[2].classList.remove("nextcard");

            next_cards[0].classList.add("activecard");
            next_cards[1].classList.add("activecard");
            next_cards[2] && next_cards[2].classList.add("activecard");

            active_cards[0].classList.remove("activecard");
            active_cards[1].classList.remove("activecard");
            active_cards[2] && active_cards[2].classList.remove("activecard");
            active_cards[0].classList.add("prevcard");
            active_cards[1].classList.add("prevcard");
            active_cards[2] && active_cards[2].classList.add("prevcard");

            if ( prev_cards.length ) {
                prev_cards[0].classList.remove("prevcard");
                prev_cards[1].classList.remove("prevcard");
                prev_cards[2] && prev_cards[2].classList.remove("prevcard");
            }
            document.getElementById("curnum").innerHTML = ++cur_num;

        } else if ( direction === "prev" && prev_cards.length ) {

        if ( cards_before_prev ) {
                prev_cards[0].previousElementSibling.classList.add("prevcard");
                prev_cards[0].previousElementSibling.previousElementSibling.classList.add("prevcard");
            } else {
                // TODO: Disable button
                // And find a way to-re-enable when moving the other way
                document.getElementById("goto_prevcard").disabled = "disabled";
            }
        document.getElementById("goto_nextcard").removeAttribute("disabled");

            prev_cards[0].classList.remove("prevcard");
            prev_cards[1].classList.remove("prevcard");
            prev_cards[0].classList.add("activecard");
            prev_cards[1].classList.add("activecard");

            active_cards[0].classList.remove("activecard");
            active_cards[1].classList.remove("activecard");
            active_cards[0].classList.add("nextcard");
            active_cards[1].classList.add("nextcard");

            if ( next_cards.length ) {
            next_cards[0].classList.remove("nextcard");
            next_cards[1].classList.remove("nextcard");
            }
            document.getElementById("curnum").innerHTML = --cur_num;

        } else {
            // TODO: Round robin ?
        }
    };

    var goto_next = function () {
        if ( the_list.classList.contains("flipped") ) {
            the_list.classList.remove ("flipped");
            setTimeout( function () {
                move_forward_or_backward("next");
            }, 700);
            /* TODO: Set with transition-end instead of timeOut */
        } else {
            move_forward_or_backward("next");
        }
    }
    var goto_prev = function () {
        if ( the_list.classList.contains("flipped") ) {
            the_list.classList.remove ("flipped");
            setTimeout( function () {
                move_forward_or_backward("prev");
            }, 700);
            /* TODO: Set with transition-end instead of timeOut */
        } else {
            move_forward_or_backward("prev");
        }
    }
    the_list.onclick = flipcard;
    document.getElementById("goto_nextcard").onclick = goto_next;
    document.getElementById("goto_prevcard").onclick = goto_prev;
    window.onkeydown = function (e) {
        switch (e.keyCode) {
        case 32:
            flipcard();
            return false;
        case 37:
            goto_prev();
            return false;
        case 39:
            goto_next();
            return false;
        }
        return true;
    }

    /*
    function hide_explain() {
        var now     = new Date(),
            nowYear = now.getFullYear(),
            futYear = +nowYear + 2,
            expires = now.toGMTString().replace(nowYear, futYear);
        document.cookie = "explainFlashcards=no; expires=" + expires;
        explain.classList.add("hidden");
        doexplain.classList.remove("hidden");
    }
    var explain         = document.getElementById("explain");
    var noexplain       = document.createElement("button");
    noexplain.innerHTML = "Dölj guide (använder cookie)";
    var doexplain       = document.createElement("button");
    doexplain.innerHTML = "Visa guide";
    doexplain.classList.add("hidden");
    doexplain.classList.add("doexplain");
    var navigationbuttons = document.getElementById("navigationbuttons");
    navigationbuttons.appendChild(doexplain);
    explain.appendChild(noexplain)

    doexplain.onclick = function () {
        explain.classList.remove("hidden");
        doexplain.classList.add("hidden");
        document.cookie = "explainFlashcards=yes; expires=0";
    }
    noexplain.onclick = hide_explain;

    if ( document.cookie.match("explainFlashcards=no") ) {
        // console.log("hidden");
        hide_explain();
    }
    */
}());
