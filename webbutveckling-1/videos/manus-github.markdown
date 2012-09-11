# Videos för git och github

## Manus video 1

http://jsbin.com/ahofam/.../

    Pil r r r  - till "Vad är Git?"
    Enter -> SVG
    Klick x 4 för att ta fram förklaring
    "Vad kallas åtgärderna?"
    a add
    r reset
    c commit
    p push
    "OK, hur hämtar jag kod från Github"
    c - Klona, om ja vill börja med någon annans kod
    l - Pull (fetch + merge) om det redan finns en relation mellan mitt repo och det på Github
    t - checkout

    space (visa alla)

## Video 2: Skapa konto på Github och installera Git

### Konto

Signup

Skapa ett projekt när vi ändå är igång (wu1-demo)

### Vilken version av Git ska jag installera

 * Om du lär dig systemets bakomliggande tanke, så spelar det mindre roll
 * MEN: All hjälp ges till den som använder kommandoraden

Alltså: Om det strular, så måste du ändå använda kommandoraden, förr eller senare

Böcker, tipsen på nätet, StackOverflow, etc. Alla förutsätter kommandoraden!

Hämta och installera [Git for Windows](http://msysgit.github.com/)

### Kommandon

 * git config --global user.name "Your Name"
 * git config --global user.email "you@example.com"

Skapa och testa förbindelsen med Github

Du behöver en autentiseringsmekanism - nyckel + lösenord (pass phrase)

Detta som nu görs behöver upprepas en gång PER DATOR, inte per projekt

Credential manager för Windows?

 * SSH nyckel krävs (än så länge) om man inte börjar med att klona
 * Programmet kraschade för mig!
 * Jag använder Git GUI på Windows
 * För andra system, läs hjälpen

 1. Git GUI
 2. Steg 4 och framåt på [Githubs guide](https://help.github.com/articles/generating-ssh-keys)

## Video 3
 
I katalogen där jag jobbar

 * git init
 * git remote add origin https://github.com/username/projectname.git


Skapa en README.markdown


## Video 3: Workflow med Git och Github

git add . är ett farligt kommando
Du kan göra en "reset", dock
git commit -am funkar bara på befintliga filer

Ignorera vissa filer

git config --global core.excludesfile ~/.gitignore_global

 * Skapa en .gitignore fil
   * Skräpfiler
 * Gör din texteditor automatisk backup (Sluta med det, du har Git!)
   * Ignorera *~
 * Ignorera också binärfiler (programkod, ljud, video, ev också bilder)

Superviktigt

 * Spara aldrig lösenord på Github!!
   * En lokal .gitignore för varje projekt
   * Har du gjort det? Be _omedelbart_ om experthjälp!
 * Använd för säkerhets skull aldrig samma lösenord för dina projekt som du har på andra platser
   * En olycka händer lätt!


### Hjälpkommandon

 * git status
 * git diff
 * git log
 * git show

## Konkret daglig användning

 * git add (filnamn)
 * git add .
 * git reset (filnamn)
 * git commit -m "Vad har hänt"
 * git commit -am "Vad har hänt"
 * git git push

Hämta en annan version

 * git checkout 
 * git pull [fetch + merge]
 * git clone

Jobba direkt på GitHub och gör en pull...

### Börja med att klona?

(Egen video)

