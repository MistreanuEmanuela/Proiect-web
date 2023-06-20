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
    fetch(`/Proiect/Backend/Controllers/PlantController.php/mostViews?nr=7`, {
        method: 'GET',
        headers: headers
        })
        .then(response => response.json())
        .then(data => {
            if (data) {
                var container = document.getElementById("plants");
                if(container.innerHTML)
                    container.innerHTML = '';
                    data.forEach(item => {
                        const button = document.createElement('button');
                        button.onclick = function () {
                          window.location.href = '../info_planta/info_planta.html';
                          var expirationDate = new Date();
                          expirationDate.setTime(expirationDate.getTime() + 360000);
                          document.cookie = "plantId=" + item.id + "; expires=" + expirationDate.toUTCString() + "; path=/";
                          document.cookie = "ownerId=" + owner + "; expires=" + expirationDate.toUTCString() + "; path=/";
                        };
                        fetch(`/Proiect/Backend/Controllers/PlantController.php/plante/image?id=${item.id}`, {
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
                            const name = document.createElement("p");
                            name.innerHTML += item.name;
                            button.appendChild(name);
                          })
                          .catch(error => {
                            console.error('Error:', error);
                          });
                        container.appendChild(button);
                      });
                    } else {
                      console.log('User has no collections');
              
                    }
                  })
                  .catch(error => {
                    console.error('Error:', error);
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
    });