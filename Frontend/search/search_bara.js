document.addEventListener('DOMContentLoaded', () => {
    var token = getCookie('token');
    if(token===null){
        const confirmed = confirm('Sesiunea a expirat, trebuie sa va autentificati');
      
      if (confirmed) {
        window.location.href = '../sign/sign.html';
      }
      }
    var headers = new Headers();
    headers.append('Authorization', 'Bearer ' + token);
    headers.append('Content-Type', 'application/json');
    const decodedToken = decodeJwt(token);
    const userId = decodedToken.userId;
    color=localStorage.getItem("color");
    anotimp=localStorage.getItem("anotimp");
    tip=localStorage.getItem("tip");
    regiune=localStorage.getItem("regiune");
fetch(`/Proiect/Backend/Controllers/CollectionController.php/filtru/culoare?culoare=${color}&anotimp=${anotimp}&tip=${tip}&regiune=${regiune}`, {
    method: 'GET',
    headers: headers
})
    .then(response => response.json())
    .then(data => {
        if (data) {
            const container = document.getElementById("containerPlante");
            if (container.innerHTML)
                container.innerHTML = '';
                exist = 0;
            Array.isArray(data) && data.forEach(item => {
                if(item.userId!=userId){
                exist=1;
                const colectieDiv = document.createElement('div');
                colectieDiv.classList.add('colectie');

                const button = document.createElement('button');
                button.onclick = function () {
                    var expirationDate = new Date();
                    expirationDate.setTime(expirationDate.getTime() + 360000);
                    var cukie = JSON.stringify(item);
                    document.cookie = "collection=" + cukie + "; expires=" + expirationDate.toUTCString() + "; path=/";
                    window.location.href = '../plante/plante.html';
                };
                fetch(`/Proiect/Backend/Controllers/PlantController.php/colectii?collectionId=${item.id}`, {
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        Accept: 'image/jpeg'
                    }
                })
                    .then(response => {
                        if (response.ok) {
                            return response.blob();
                        } else {
                            throw new Error('Error: ' + response.status);
                        }
                    })
                    .then(blob => {
                        const image = document.createElement('img');
                        image.src = URL.createObjectURL(blob);
                        image.alt = 'not found';
                        button.appendChild(image);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                
                const searchtext = document.getElementById('searchh');
                searchtext.innerHTML=localStorage.getItem("text");                    
                const descriereDiv = document.createElement('div');
                descriereDiv.classList.add('descriereColectie');

                const nameParagraph = document.createElement('p');
                nameParagraph.className="numePlanta";
                nameParagraph.innerHTML= item.name;
                descriereDiv.appendChild(nameParagraph);


                const nameutilizator = document.createElement('p');
                nameutilizator.className="caractPlanta";
                nameutilizator.innerHTML='Nume utilizator: ' + item.firstName;
                descriereDiv.appendChild(nameutilizator);

                const prenume = document.createElement('p');
                prenume.className="caractPlanta";
                prenume.innerHTML='Prenume utilizator: ' + item.lastName;
                descriereDiv.appendChild(prenume);

                const viewsParagraph = document.createElement('p');
                viewsParagraph.className="caractPlanta";
               viewsParagraph.innerHTML='Numar vizualizari: ' + item.views;
                descriereDiv.appendChild(viewsParagraph);

                colectieDiv.appendChild(button);
                colectieDiv.appendChild(descriereDiv);

                container.appendChild(colectieDiv);
                }
            });
               
            if (exist === 0) {
                const not=document.createElement('div');
                not.className='not';
                not.innerHTML = 'No results found ';
                container.appendChild(not);
            } }
            else {
                console.log('User has no collections');
            }
    })
    .catch(error => {
        console.error('Error:', error);
    });
colorGen=localStorage.getItem("colorGen");
anotimpGen=localStorage.getItem("anotimpGen");
tipGen=localStorage.getItem("tipGen");
regiuneGen=localStorage.getItem("regiuneGen");
document.getElementById('but').addEventListener('click',function(){
        var cukie = {
            color: colorGen,
            anotimp: anotimpGen,
            tip: tipGen,
            regiune: regiuneGen
        }
        cukie = JSON.stringify(cukie);
        var expirationDate = new Date();
        expirationDate.setTime(expirationDate.getTime() + 24 * 60 * 60 * 1000);
        document.cookie = "collection="+ cukie + "; expires=" + expirationDate.toUTCString() + "; path=/";
        window.location.href = "../plante/plante.html";
    });
function getCookie(name) {
    var cookieArr = document.cookie.split(';');
    for (var i = 0; i < cookieArr.length; i++) {
        var cookiePair = cookieArr[i].split('=');
        if (cookiePair[0].trim() === name) {
            return cookiePair[1];
        }
    }
    return null;
}

function decodeJwt(token) {
    const base64Url = token.split('.')[1];
    const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
    const jsonPayload = decodeURIComponent(atob(base64).split('').map(function (c) {
        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2); 
    }).join(''));

    return JSON.parse(jsonPayload);
}
});