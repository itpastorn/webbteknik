/**
 * Ritar cirkeldiagram
 * 
 * @param svgid
 * @param data
 * @param classes
 */
function drawPieChart(svgid, data, classes) {
    // Circle basics
    var cx = 0, cy = 0, r = 130;
    // Utility variables
    var len = data.length, sum = 0, angle, totangle = 0, totrad = 0, big, d, txt = [], x1, x2, y1, y2, lastytext = 0, diff;
    var ns = "http://www.w3.org/2000/svg";
    var svgelem = document.getElementById(svgid)
    for ( var i = 0; i < len; i += 1 ) {
        sum += data[i];
    }
    for ( i = 0; i < len; i += 1 ) {
        angle = 360 * data[i] / sum;
        totangle += angle;
        radians = angle * Math.PI / 180;
        totrad += radians;
        x1 = cx;
        y1 = cy - r;
        x2 = cx + r * Math.sin(radians);
        y2 = cy - r * Math.cos(radians);
        var p = document.createElementNS(ns, "path");
        big = ( angle > 180 ) * 1; // Större än 180 grader = ta långa varvet
        d = "M 0,0 L "+ x1 + "," + y1 + " A " + r + "," + r + ",0," + big + ",1," + x2 + "," + y2 +" Z"
        p.setAttribute("d", d);
        p.setAttribute("transform", "rotate(" + (totangle - angle) + ")");
        p.classList.add(classes[i]);
        svgelem.appendChild(p);

        txt[i] = document.createElementNS(ns, "text");
        // Re-use x1 and x2
        x1 = cx + r * 0.9 * Math.sin(totrad - radians / 2);
        y1 = - (cy + r * 0.9 * Math.cos(totrad - radians / 2));
        diff = Math.abs(y1 - lastytext);
        if ( diff < 12 ) {
            y1 -= (12 - diff) * diff/Math.abs(diff);
        }
        txt[i].setAttribute("x", x1);
        txt[i].setAttribute("y", y1);
        lastytext = y1;
        if ( data[i] === 0 ) {
    	    continue;
    	}
        txt[i].textContent = classes[i] + "=" + data[i];
    }
    for ( i = 0; i < len; i += 1 ) {
    	svgelem.appendChild(txt[i]);
    }
}
