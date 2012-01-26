var kurser = {
    "arkark0"  : "Arkitektur hus",
    "bilbil01a1" : "Bild och form 1a1",
    "bilbil01a2" : "Bild och form 1a2",
    "cadcad01" : "CAD 1",
    "cadcad02" : "CAD 2",
    "daodat01a" : "Datorteknik 1a",
    "dardat01" : "Datorstyrd produktion 1",
    "desdes01" : "Design 1",
    "fysfys02" : "Fysik 2",
    "grägrä0" : "Gränssnittsdesign",
    "hålhåb0" : "Hållbart samhällsbyggande",
    "hålmij0"  : "Miljö och energikunskap",
    "kotkos01" : "Konstruktion 1",
    "matmat04" : "Matematik 4",
    "mekmek01" : "Mekatronik 1",
    "prdpro01" : "Produktionskunskap 1",
    "pruprd01s" : "Produktionsutrustning 1",
    "pruprd02s" : "Produktionsutrustning 2",
    "prrprr01" : "Programmering 1",
    "prrprr02" : "Programmering 2",
    "webweu01" : "Webbutveckling 1",
    "webweu02" : "Webbutveckling 2",
    "webweb01" : "Webbserverprogrammering 1"
};

var inriktningar = {
    "design" : {
        "name"    : "Design och produktutveckling",
        "blockar" : ["des1"],
        "passar4" : "prod1",
        "typ"     : "design",
        "kurser"  : ["bilbil01a1", "cadcad01", "desdes01", "kotkos01"]
    },
    "produktion" : {
        "name"    : "Produktionsteknik",
        "blockar" : ["prod1"],
        "passar4" : "des1",
        "typ"     : "produktion",
        "kurser"  : ["mekmek01", "prdpro01", "pruprd01s"]
    },
    "it" : {
        "name"    : "IT och media",
        "blockar" : [],
        "passar4" : "it1",
        "typ"     : "it",
        "kurser"  : ["webweu01", "prrprr01", "daodat01a"]
    },
    "samhall" : {
        "name" : "Samhällsbyggande och miljö",
        "blockar" : ["ark1", "sam2"],
        "passar4" : null,
        "typ" : "samhall",
        "kurser" : ["arkark0", "hålhåb0", "hålmij0"]
    }
};

// Byggs utifrån "paket" tabellen
var paket1 = {
    "prod1"  : {
        "kurser" : ["prdpro01", "mekmek01"],
        "req"    : null,
        "typ"  : "produktion"
    },
    "it1"    : {
        "kurser" : ["grägrä0", "webweb01"],
        "req"    : "it",
        "typ"    : "it"
    },
    "ark1"   : {
        "kurser" : ["arkark0", "cadcad02", "bilbil01a2"],
        "req"    : "design",
        "typ"    : "design"
    },
    "des1"   : {
        "kurser" : ["kotkos01", "cadcad01", "bilbil01a1"],
        "req"    : null,
        "typ"    : "design"
    }
};
var paket2 = {
	    "civing" : {
	        "kurser" : ["matmat04", "fysfys02"],
	        "req"    : null,
	        "typ"    : "civing"
	    },
        "it2" : {
            "kurser" : ["prrprr02", "webweu02"],
            "req"    : "it",
            "typ"    : "it"
        },
        "prod2" : {
            "kurser" : ["pruprd02s", "dardat01"],
            "req"    : "produktion",
            "typ"    : "produktion"
        },
        "sam2" : {
            "kurser" : ["hålhåb0", "hålmij0"],
            "req"    : null,
            "typ"    : "samhall"
        }
};

// http://www.skolverket.se/
// forskola-och-skola/gymnasieutbildning/amnes-och-laroplaner/amnesplaner-och-kurser-for-gymnasieskolan-2011/
// subject.htm?subjectCode={AMNE}&courseCode={KURSKOD}#anchor_{KURSKOD}", // Platshållare för regexp
