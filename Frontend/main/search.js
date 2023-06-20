document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        if (form.id == 'srch') {
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                const txt = document.getElementById("search");
                localStorage.setItem("text", txt.value);
                var color = 'c';
                var tip = 'i';
                var anotimp = 'a'
                var regiune = 'r';
                var colorGen = '';
                var tipGen = '';
                var anotimpGen = '';
                var regiuneGen = '';
                var cuvinte = txt.value.split(" ");
                for (var i = 0; i < cuvinte.length; i++) {
                    var cuvant = cuvinte[i];
                    if (cuvant === "rosu" || cuvant === "rosie" || cuvant === "rosi" || cuvant === "rosii"
                        || cuvant === "red" || cuvant === "ros")
                        {color = "rosu"; colorGen="red";}
                    if (cuvant === "galben" || cuvant === "galbe" || cuvant === "glben"
                        || cuvant === "galbena" || cuvant === "galbene" || cuvant === "galbeni")
                        {color = "galben"; colorGen="yellow";}
                    if (cuvant === "albastru" || cuvant === "albastra" || cuvant === "albastre"
                        || cuvant === "albastri" || cuvant === "alabastri" || cuvant === "albastr")
                        {color = "albastru"; colorGen="blue"}
                    if (cuvant === "portocaliu" || cuvant === "portocalie" || cuvant === "portocala"
                        || cuvant === "prtocal" || cuvant === "orange" || cuvant === "portocalii")
                        {color = "portocaliu"; colorGen="orange";}
                    if (cuvant === "verde" || cuvant === "verzi" || cuvant === "verd"
                        || cuvant === "verzi" || cuvant === "verzui" || cuvant === "turcuaz")
                        {color = "verde"; colorGen="green"}
                    if (cuvant === "alba" || cuvant === "albe" || cuvant === "alb"
                        || cuvant === "albi" || cuvant === "ab" || cuvant === "al")
                        {color = "alba"; colorGen="white"}
                    if (cuvant === "roz" || cuvant === "roze" || cuvant === "rozi")
                        {color = "roz"; colorGen="pink";}
                    if (cuvant === "lila" || cuvant === "lil" || cuvant === "violet"
                        || cuvant === "mov" || cuvant === "move" || cuvant === "movi")
                        {color = "lila"; colorGen="purple";}
                    if (cuvant === "medicinala" || cuvant === "medicinale" || cuvant === "medicinal")
                        {tip = "medicinala"; tipGen="medicinal"}
                    if (cuvant === "feriga" || cuvant === "ferigi" || cuvant === "ferige")
                        {tip = "feriga"; tipGen="fig";}
                    if (cuvant === "carnivor" || cuvant === "carnivora" || cuvant === "carnivore" || cuvant === "carnivori")
                        {tip = "carnivor"; tipGen="carnivorous";}
                    if (cuvant === "suculent" || cuvant === "suculenta" || cuvant === "suculente" || cuvant === "suculenti")
                        {tip = "suculent"; tipGen="succulent";}
                    if (cuvant === "aromatice" || cuvant === "aromatic" || cuvant === "aromatici" || cuvant === "aroma")
                        {tip = "aromatice"; tipGen="aromatic";}
                    if (cuvant === "montana" || cuvant === "munte" || cuvant === "munti" || cuvant === "inalte")
                        {regiune = "montana"; regiuneGen="mountain";}
                    if (cuvant === "ecuatoriala" || cuvant === "ecuator" || cuvant === "ecuatorial")
                        {regiune = "ecuatoriala"; regiuneGen="equator";}
                    if (cuvant === "mlastina" || cuvant === "mlasinoasa" || cuvant === "mlastinos" || cuvant === "mlastinose")
                        {regiune = "mlastina"; regiuneGen="swamp";}
                    if (cuvant === "deset" || cuvant === "desert" || cuvant === "desertica" || cuvant === "desertice")
                        {regiune = "desertica"; regiuneGen="desert";}
                    if (cuvant === "jungla" || cuvant === "salbatica" || cuvant === "jungl")
                        {regiune = "jungla"; regiuneGen="jungle";}
                    if (cuvant === "primavara" || cuvant === "primavaratice" || cuvant === "primavaratic")
                        {anotimp = "primavara"; anotimpGen="spring";}
                    if (cuvant === "vara" || cuvant === "varatice" || cuvant === "varatic")
                        {anotimp = "vara"; anotimpGen="summer";}
                    if (cuvant === "toamna" || cuvant === "tomnatice" || cuvant === "tomatictic")
                        {anotimp = "toamna"; anotimpGen="autumn";}
                    if (cuvant === "iarna" || cuvant === "iernatice" || cuvant === "iernatic")
                        {anotimp = "iarna"; anotimpGen="winter";}
                }
                localStorage.setItem("color",color);
                localStorage.setItem("tip", tip);
                localStorage.setItem("anotimp", anotimp);
                localStorage.setItem("regiune", regiune);
                localStorage.setItem("colorGen",colorGen);
                localStorage.setItem("tipGen", tipGen);
                localStorage.setItem("anotimpGen", anotimpGen);
                localStorage.setItem("regiuneGen", regiuneGen);
                window.location.href = "../search/search.html";
            }
            )
        }
    }
    )
});