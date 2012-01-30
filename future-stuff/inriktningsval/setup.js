/**
 * Skapar en td med en label och en länk till Skolverkets kursbeskrivning
 *
 * Amvänder den globala variabeln kurser just nu
 *
 * @param string id Nyckeln i paketbeskrivningen
 * @param object paket Vilket JSON objekt med kursurval som används
 * @param number num Vilken kurs i paketet som åsyftas
 * @return HtmlTdElement (heter det så?)
 * 
 * @todo label saknar "for"
 */
function createTableDataLabel(id, paket, num) {
    kurskod = paket[id].kurser[num].toUpperCase();
    // Slutar koden på bokstav i början av alfabetet ska den vara lowercase
    kurskod = kurskod.replace(/[A-F]$/, kurskod.substring(kurskod.length-1).toLowerCase());
    // TODO
    // Slutar koden på siffra+bokstav+siffra skall den bokstaven också vara lowercase
    if ( /[0-9][A-F][0-9]$/.test(kurskod) ) {
        var len = kurskod.length;
        kurskod = kurskod.substr(0,len-2) + kurskod.substr(len-2, 1).toLowerCase() + kurskod.substr(len-1);
    }
    var td    = document.createElement("td");
    var label =  document.createElement("label");
    var link  =  document.createElement("a");
    link.innerHTML = kurser[paket[id].kurser[num]];
    var href  = "http://www.skolverket.se/" +
                "forskola-och-skola/gymnasieutbildning/amnes-och-laroplaner/sok-program-och-amnesplaner/" +
                "subject.htm?subjectCode={AMNE}&courseCode={KURSKOD}#anchor_{KURSKOD}";
    href = href.replace("\{AMNE\}", kurskod.substring(0, 3));
    href = href.replace("\{KURSKOD\}", kurskod, "g");
    link.href = href;
    label.appendChild(link);
    td.appendChild(label);
    return td;
}

function createBlock(blockid, paket) {
    var tables = document.createDocumentFragment();
    for ( var id in paket ) {
        var newtable = document.createElement("table");
        newtable.id = id;
        $(newtable).addClass(blockid + " " + paket[id].typ);
        if ( paket[id].name ) {
            var caption = document.createElement("caption");
            var label   = document.createElement("label");
            label.setAttribute("for", "r_" + id);
            label.innerHTML = paket[id].name;
            caption.appendChild(label);
            newtable.appendChild(caption);
        }
        var tr      = document.createElement("tr");
        var td      = document.createElement("td");
        var input   = document.createElement("input");
        input.type  = "radio";
        input.name  = blockid;
        input.id    = "r_" + id;
        input.value = id;
        td.appendChild(input);
        // td.appendChild(document.createTextNode(id));
        td.setAttribute("rowspan", paket[id].kurser.length);
        tr.appendChild(td);

        td = createTableDataLabel(id, paket, 0);

        tr.appendChild(td);
        newtable.appendChild(tr);
        for ( var i = 1; i < paket[id].kurser.length; i += 1 ) {
            tr = document.createElement("tr");
            tr.appendChild(createTableDataLabel(id, paket, i));
            newtable.appendChild(tr);
        }
        tables.appendChild(newtable);
    }
    document.getElementById(blockid).appendChild(tables);

}

createBlock("inriktningar", inriktningar);
createBlock("block1", paket1);
createBlock("block2", paket2);

// TODO: Inte om man klickar på en länk...
$("table").click(function () {
	if ( $(this).hasClass("disabled") ) {
	    return false;
	}
    $(this).find("input[type='radio']").attr("checked", "checked");
    $(this).parent().find("table").removeClass("chosen");
    $(this).addClass("chosen");
});

// När man väljer inriktning
$("#inriktningar table").click(function () {
	// Nollställer
	$("#block1 table, #block2 table").removeClass("disabled");
	$("#block1 table input, #block2 table input").removeAttr("disabled");
	$("button").removeClass("chosen").removeClass("notchosen");
	// Tag bort blockerade val
    var id = this.id;
    for ( var i = 0; i < inriktningar[id].blockar.length; i += 1 ) {
        $("#" + inriktningar[id].blockar[i]).addClass("disabled").removeClass("chosen");
        $("#" + inriktningar[id].blockar[i] + " input").attr("disabled", "disabled").removeAttr("checked");
    }
    // Öppna upp val som kräver förkunskaper
    // Stäng alla val som kräver förkunskaper
    thepacks = [paket1, paket2];
    for ( var pnum = 0; pnum < 2; pnum += 1) {
    	pt = thepacks[pnum];
	    for ( i in pt ) {
	        if ( pt[i].req === id ) {
	            $("#" + i).removeClass("disabled");
	            $("#" + i + " input").removeAttr("disabled");
	        } else if ( pt[i].req ) {
	            $("#" + i).addClass("disabled");
	            $("#" + i + " input").attr("disabled", "disabled");
	        }
	    }
    }
});

$("#mafyja").click( function () {
    var inr_val = $("input[name='inriktningar']:checked").val();
    if ( ! inr_val ) {
        alert("Du måste välja inriktning först");
        return false;
    }
    $("#block2 table").addClass("disabled");
    $("#block2 input").attr("disabled", "disabled");
    $("#civing").removeClass("disabled").addClass("chosen");
    $("#r_civing").removeAttr("disabled").attr("checked", "checked");
    $(this).addClass("chosen").removeClass("notchosen");
    $("#mafynej").addClass("notchosen");
});
$("#mafynej").click( function () {
    var inr_val = $("input[name='inriktningar']:checked").val();
    if ( ! inr_val ) {
        alert("Du måste välja inriktning först");
        return false;
    }
    $("#block2 table").removeClass("disabled");
    $("#block2 input").removeAttr("disabled");
    // simulera att klick på inriktningen man valt för att nollställa rätt
    $("#" + inr_val).click();
    $(this).addClass("chosen").removeClass("notchosen");
    $("#mafyja").addClass("notchosen");
});

$("#te4ja").click( function () {
    // Kolla att man valt inriktning först
    var inr_val = $("input[name='inriktningar']:checked").val();
    if ( ! inr_val ) {
        alert("Du måste välja inriktning först");
        return false;
    }
    // Hitt paketet som passar
    var passar4 = inriktningar[inr_val].passar4;
    if ( ! passar4 ) {
        alert("Vi erbjuder inte detta. Skriv din önskan i kommentaren.");
        return false;
    }
    // T4 alternativen ligger i block1
    $("#block1 table").removeClass("chosen").addClass("disabled");
    $("#block1 input").attr("disabled", "disabled").removeAttr("checked");
    $("#" + passar4).addClass("chosen").removeClass("disabled");
    $("#" + passar4 + " input").removeAttr("disabled").attr("checked", "checked");
    $(this).addClass("chosen").removeClass("notchosen");
    $("#te4nej").addClass("notchosen");
});
$("#te4nej").click( function () {
    // simulera att klick på inriktningen man valt för att nollställa rätt
    var inr_val = $("input[name='inriktningar']:checked").val();
    if ( ! inr_val ) {
        alert("Du måste välja inriktning först");
        return false;
    }
    if ( $("#mafyja").hasClass("chosen") ) {
        var mafy = "ja";
    } else if ( $("#mafynej").hasClass("chosen") ) {
        mafy = "nej";
    } else {
        alert("Du måste svara på frågorna i ordning.");
        return false;
    }
    // Simulera (återställ) inriktningsvalet
    $("#" + inr_val).click();
    // Simulera knappvalet från Ma/Fy
    if ( mafy === "ja" ) {
        $("#mafyja").click();
    } else {
    	$("#mafynej").click();
    }
    $(this).addClass("chosen").removeClass("notchosen");
    $("#te4ja").addClass("notchosen");
});



