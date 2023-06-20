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
                var cuvinte = txt.value.split(" ");
                for (var i = 0; i < cuvinte.length; i++) {
                    var cuvant = cuvinte[i];
                    if (cuvant === "rosu" || cuvant === "rosie" || cuvant === "rosi" || cuvant === "rosii"
                        || cuvant === "red" || cuvant === "ros")
                        color = "rosu";
                    if (cuvant === "galben" || cuvant === "galbe" || cuvant === "glben"
                        || cuvant === "galbena" || cuvant === "galbene" || cuvant === "galbeni")
                        color = "galben";
                    if (cuvant === "albastru" || cuvant === "albastra" || cuvant === "albastre"
                        || cuvant === "albastri" || cuvant === "alabastri" || cuvant === "albastr")
                        color = "albastru";
                    if (cuvant === "portocaliu" || cuvant === "portocalie" || cuvant === "portocala"
                        || cuvant === "prtocal" || cuvant === "orange" || cuvant === "portocalii")
                        color = "portocaliu";
                    if (cuvant === "verde" || cuvant === "verzi" || cuvant === "verd"
                        || cuvant === "verzi" || cuvant === "verzui" || cuvant === "turcuaz")
                        color = "verde";
                    if (cuvant === "alba" || cuvant === "albe" || cuvant === "alb"
                        || cuvant === "albi" || cuvant === "ab" || cuvant === "al")
                        color = "alba";
                    if (cuvant === "roz" || cuvant === "roze" || cuvant === "rozi")
                        color = "roz";
                    if (cuvant === "lila" || cuvant === "lil" || cuvant === "violet"
                        || cuvant === "mov" || cuvant === "move" || cuvant === "movi")
                        color = "lila";
                    if (cuvant === "medicinala" || cuvant === "medicinale" || cuvant === "medicinal")
                        tip = "medicinala";
                    if (cuvant === "feriga" || cuvant === "ferigi" || cuvant === "ferige")
                        tip = "feriga";
                    if (cuvant === "carnivor" || cuvant === "carnivora" || cuvant === "carnivore" || cuvant === "carnivori")
                        tip = "carnivor";
                    if (cuvant === "suculent" || cuvant === "suculenta" || cuvant === "suculente" || cuvant === "suculenti")
                        tip = "suculent";
                    if (cuvant === "aromatice" || cuvant === "aromatic" || cuvant === "aromatici" || cuvant === "aroma")
                        tip = "aromatice";
                    if (cuvant === "montana" || cuvant === "munte" || cuvant === "munti" || cuvant === "inalte")
                        regiune = "montana";
                    if (cuvant === "ecuatoriala" || cuvant === "ecuator" || cuvant === "ecuatorial")
                        regiune = "ecuatoriala";
                    if (cuvant === "mlastina" || cuvant === "mlasinoasa" || cuvant === "mlastinos" || cuvant === "mlastinose")
                        regiune = "mlastina";
                    if (cuvant === "deset" || cuvant === "desert" || cuvant === "desertica" || cuvant === "desertice")
                        regiune = "desertica";
                    if (cuvant === "jungla" || cuvant === "salbatica" || cuvant === "jungl")
                        regiune = "jungla";
                    if (cuvant === "primavara" || cuvant === "primavaratice" || cuvant === "primavaratic")
                        anotimp = "primavara";
                    if (cuvant === "vara" || cuvant === "varatice" || cuvant === "varatic")
                        anotimp = "vara";
                    if (cuvant === "toamna" || cuvant === "tomnatice" || cuvant === "tomatictic")
                        anotimp = "toamna";
                    if (cuvant === "iarna" || cuvant === "iernatice" || cuvant === "iernatic")
                        anotimp = "iarna";
                }
                localStorage.setItem("color",color);
                localStorage.setItem("tip", tip);
                localStorage.setItem("anotimp", anotimp);
                localStorage.setItem("regiune", regiune);
                window.location.href = "../search/search.html";
            }
            )
        }
    }
    )
});