/*
Visa
 - Canvas som bild

Undo
 - Canvas-taggarna, bredd och höjd
 - Fallback
 - Script
 - CSS för canvas-elementet - inte samma bredd och höjd!
   + Gör om till block bara för denna demo
   + Bakgrundsfärg

Skriv
 - Växla till JS
   + Rad för rad
   + Funktionen
     + Synka med bildskärmen

Visa
 - En enkel demo jag gjort
 - Cut the rope
 - WebGL Maze
 - Bananabread

*/
var context = document.getElementById("cs").getContext("2d");
// context.fillStyle = "rgba(255, 0, 0, 1)";                  /* 1 */
// context.fillRect(10, 10, 160, 120);
// context.fillStyle = "rgba(0, 255, 0, 0.5)";                /* 2 */
// context.fillRect(130, 70, 160, 120);

var x = 0, y = 0, xdir = 1, ydir = 1, speed = 2;

function moveBlock() {
    context.clearRect(0, 0, 300, 200);
    context.fillRect(x, y, 20, 20);
    
    if ( x > 279 ) {
        xdir = -1;
    } else if ( x < 1 ) {
        xdir = 1;
    }
    if ( y > 179 ) {
        ydir = -1;
    } else if ( y < 1 ) {
        ydir = 1;
    }

    x = x + xdir * speed;
    y = y + ydir * speed;

    /* window.setTimeout(moveBlock, 20); */
    window.mozRequestAnimationFrame(moveBlock);
}
moveBlock();


