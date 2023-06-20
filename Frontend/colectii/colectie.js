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
  fetch(`/Proiect/Backend/Controllers/CollectionController.php/colectii/list?userId=${userId}`, {
      method: 'GET',
      headers: headers
    })
      .then(response => response.json())
      .then(data => {
          if (data) {
              const container = document.getElementById("containerPlante");
              if(container.innerHTML)
                  container.innerHTML = '';
              if(Array.isArray(data))
              data.forEach(item => {
                  const button = document.createElement('button');
                  button.onclick = function() {
                    var expirationDate = new Date();
                    expirationDate.setTime(expirationDate.getTime() + 24 * 60 * 60 * 1000);
                    var cukie = JSON.stringify(item);
                    document.cookie = "collection=" + cukie + "; expires=" + expirationDate.toUTCString() + "; path=/";
                    window.location.href = '../plante/plante.html';  
                  };
                  container.appendChild(button);
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
                          const name = document.createElement("p"); 
                         name.innerHTML+=item.name;
                         button.appendChild(name);
                      })
                      .catch(error => {
                          console.error('Error:', error);
                      }); 
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
function decodeJwt(token) {
  const base64Url = token.split('.')[1]; 
  const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/'); 
  const jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2); 
  }).join(''));

  return JSON.parse(jsonPayload);
}