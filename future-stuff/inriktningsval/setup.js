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
    kurskod = paket[id].kurser[num];
    var td    = document.createElement("td");
    var label =  document.createElement("label");
    var link  =  document.createElement("a");
    link.innerHTML = kurser[paket[id].kurser[num]];
    var href  = "http://www.skolverket.se/" +
                "forskola-och-skola/gymnasieutbildning/amnes-och-laroplaner/amnesplaner-och-kurser-for-gymnasieskolan-2011/" +
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
        console.log(paket[id].name);
        if ( paket[id].name ) {
            var caption = document.createElement("caption");
            var label   = document.createElement("label");
            label.setAttribute("for", "r_" + id);
            label.innerHTML = paket[id].name;
            caption.appendChild(label);
            newtable.appendChild(caption);
        }
        var tr     = document.createElement("tr");
        var td     = document.createElement("td");
        var input  = document.createElement("input");
        input.type = "radio";
        input.name = blockid;
        input.id   = "r_" + id;
        td.appendChild(input);
        td.appendChild(document.createTextNode(id));
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

createBlock("inriktningar", inriktingar);
createBlock("block1", paket1);
createBlock("block2", paket2);

// TODO: Inte om man klickar på en länk...
$("table").click(function () {
    $(this).find("input[type='radio']").attr("checked", "checked");
    $(this).parent().find("table").removeClass("chosen");
    $(this).addClass("chosen");
});
$("input[type='radio']").change(function () {
    $(this).parent().parent().parent().parent().parent().find("table").removeClass("chosen");
    $(this).parent().parent().parent().parent().addClass("chosen");
});
