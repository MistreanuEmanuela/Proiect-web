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
  var collection = JSON.parse(getCookie('collection'));

  var requestOptions = {
    method: 'GET',
    headers: headers,
    redirect: 'follow'
  };
  fetch(`/Proiect/Backend/Controllers/PlantController.php/plante/list?collectionId=${collection.id}`, {
    method: 'GET',
    headers: headers
  })
    .then(response => response.json())
    .then(data => {

      headers = new Headers();
      headers.append('Authorization', 'Bearer ' + token);
      var requestOptions = {
        method: 'PUT',
        headers: headers,
        body: collection.id,
        redirect: 'follow'
      };
      fetch('/Proiect/Backend/Controllers/CollectionController.php/view', requestOptions)
        .catch(error => console.log('error', error));
      fetch(`/Proiect/Backend/Controllers/CollectionController.php/view?collectionId=${collection.id}`, {
        method: 'GET',
        headers: headers
      }).then(response => response.json())
        .then(info => {
          const vi=document.createElement('div');
          vi.innerHTML += "Numar vizualizari: " + info.views;
          description.appendChild(vi);
        })
        .catch(error => console.log('error', error));
      if (data) {
        const container = document.getElementById("containerPlante");
        if (container.innerHTML)
          container.innerHTML = '';
        if(Array.isArray(data))
        data.forEach(item => {
          const button = document.createElement('button');
          button.onclick = function () {
            window.location.href = '../info_planta/info_planta.html';
            var expirationDate = new Date();
            expirationDate.setTime(expirationDate.getTime() + 24 * 60 * 60 * 1000);
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

  fetch(`/Proiect/Backend/Controllers/CollectionController.php/info?collectionId=${collection.id}}`, requestOptions)
    .then(response => response.text())
    .then(result => {
      owner = JSON.parse(result).user;
      const title = document.getElementById("desc");
      const tit=document.createTextNode(JSON.parse(result).name);
      title.appendChild(tit);
      editName = document.getElementById("editName");
      editName.value = JSON.parse(result).name;
      if (JSON.parse(result).user === userId) {
        const di = document.getElementById('butoane');
        const deleteButton = document.createElement('button');
        deleteButton.className = 'delete';
        deleteButton.id = 'delete';
        deleteButton.textContent = 'Sterge Colectia';
        deleteButton.addEventListener('click', deleteCollection);


        
        const newCol = document.createElement('button');
        newCol.className = 'new';
        newCol.id = 'new';
        newCol.textContent = "Adauga o noua planta";

        newCol.addEventListener('click', function () {
          window.location.href = '../adaugare_planta/adaugare_planta.html';
        });
        di.appendChild(newCol);
        di.appendChild(deleteButton);

        const desc = document.getElementById("description");
        const editButtonn = document.createElement('button');
        editButtonn.className = 'edit';
        editButtonn.id = 'editCollectie';
        desc.appendChild(editButtonn);

        const exit=document.getElementById("exit");
        exit.addEventListener('click', function () {
          editContainer.classList.toggle('hidden');
        });

        const titlu = document.getElementById("desc");
        const editButton = document.createElement('button');
        editButton.className = 'edit';
        editButton.id = 'ediNume';
        titlu.appendChild(editButton);
        const editContainer = document.querySelector('.edit-container');
        editButton.addEventListener('click', () => {
          editContainer.classList.toggle('hidden');
        });

        const editContainerDesc = document.querySelector('.edit-containerDesc');
        editButtonn.addEventListener('click', () => {
          editContainerDesc.classList.toggle('hidden');
        });
       const exit2=document.getElementById("exit2");
       exit2.addEventListener('click', () => {
        editContainerDesc.classList.toggle('hidden');
      });
        const saveName = document.getElementById("saveName");
        saveName.addEventListener('click', () => {
          const nam = document.getElementById("editName");
          let errorMessage = '';
          if (nam.value === '') {
            errorMessage += 'Introduceti o valoare corecta.\n';
          }
          const errorContainer = document.querySelector('.error-message');
          if (errorMessage !== '') {
            errorContainer.textContent = errorMessage;
            errorContainer.classList.remove('hidden');
          }
          else {
            const raw = JSON.stringify({
              "id": collection.id,
              "name": nam.value
            });
    
            var requestOptions = {
              method: 'PUT',
              headers: headers,
              body: raw,
              redirect: 'follow'
            }
    
            fetch("/Proiect/Backend/Controllers/CollectionController.php/updateName", requestOptions)
              .then(response => {
                if (response.ok) {
                  location.reload();
                } else {
                  throw new Error('Failed to update profile: ' + response.statusText);
                }
              })
              .catch(error => {
                console.error('Error:', error);
              });
            errorContainer.classList.add('hidden');
          }
        });
    
        const saveDesc = document.getElementById("saveDesc");
        saveDesc.addEventListener('click', () => {
          const nam = document.getElementById("editdesc");
          let errorMessage = '';
    
          if (nam.value === '') {
            errorMessage += 'Introduceti o valoare corecta.\n';
          }
          const errorContainer = document.querySelector('.error-message2');
          if (errorMessage !== '') {
            errorContainer.textContent = errorMessage;
            errorContainer.classList.remove('hidden');
          }
          else {
            const raw = JSON.stringify({
              "id": collection.id,
              "desc": nam.value
            });
    
            var requestOptions = {
              method: 'PUT',
              headers: headers,
              body: raw,
              redirect: 'follow'
            }
    
            fetch("/Proiect/Backend/Controllers/CollectionController.php/updateDesc", requestOptions)
              .then(response => {
                if (response.ok) {
                  location.reload();
                } else {
                  throw new Error('Failed to update profile: ' + response.statusText);
                }
              })
              .catch(error => {
                console.error('Error:', error);
              });
            errorContainer.classList.add('hidden');
          }
    
    
        });
      
      }
      const description = document.getElementById('description');
      const descript=document.createElement('div');
      descript.innerHTML += '<br>';
      descript.innerHTML += "Descriere:" + (JSON.parse(result)).description;
      descript.innerHTML += '<br>';
      descript.innerHTML += '<br>';
     description.appendChild(descript);
     const changedesc=document.getElementById("editdesc");
     changedesc.value=(JSON.parse(result)).description;

    })
    .catch(error => console.log('error', error));



  function deleteCollection() {
    const confirmed = confirm('Are you sure you want to delete this plant?');

    if (confirmed) {
      fetch(`/Proiect/Backend/Controllers/PlantController.php/plante/delete?collectionId=${collection.id}`, {
        method: 'DELETE',
        headers: {
          'Authorization': 'Bearer ' + token,
          'Content-Type': 'application/json'
        }
      })
        .then(response => {
          if (response.ok) {
            console.log('Plant deleted successfully');
            window.location.href = '../colectii/colectie.html';
          } else {
            console.log('Failed to delete plant');
          }
        })
        .catch(error => {
          console.error('Error:', error);
        });
    }
  }

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
  const jsonPayload = decodeURIComponent(atob(base64).split('').map(function (c) {
    return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
  }).join(''));

  return JSON.parse(jsonPayload);
}