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
  var plant = JSON.parse(getCookie('plantId'));
  var owner = JSON.parse(getCookie('ownerId'));
  if (owner === userId) {
    const di = document.getElementById('butoane');
    const deleteButton = document.createElement('button');
    deleteButton.className = 'deletebut';
    deleteButton.id = 'delete';
    deleteButton.textContent = 'Delete Plant';
    deleteButton.addEventListener('click', deletePlant);
    di.appendChild(deleteButton);

    const desc = document.getElementById("descriere");
    const editButtonn = document.createElement('button');
    editButtonn.className = 'edit';
    editButtonn.id = 'editCollectie';
    desc.appendChild(editButtonn);

    const exit = document.getElementById("exit");
    exit.addEventListener('click', function () {
      editContainer.classList.toggle('hidden');
    });

    const titlu = document.getElementById("nume");
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
    const exit2 = document.getElementById("exit2");
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
          "id": plant,
          "name": nam.value
        });

        var requestOptions = {
          method: 'PUT',
          headers: headers,
          body: raw,
          redirect: 'follow'
        }

        fetch("/Proiect/Backend/Controllers/PlantController.php/updateName", requestOptions)
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
          "id": plant,
          "desc": nam.value
        });

        var requestOptions = {
          method: 'PUT',
          headers: headers,
          body: raw,
          redirect: 'follow'
        }

        fetch("/Proiect/Backend/Controllers/PlantController.php/updateDesc", requestOptions)
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
  fetch(`/Proiect/Backend/Controllers/PlantController.php/info/list?id=${plant}`, {
    method: 'GET',
    headers: headers
  })
    .then(response => response.json())
    .then(data => {
      const desc = document.getElementById("descriere");
      headers = new Headers();
      headers.append('Authorization', 'Bearer ' + token);
      var requestOptions = {
        method: 'PUT',
        headers: headers,
        body: plant,
        redirect: 'follow'
      }
      fetch('/Proiect/Backend/Controllers/PlantController.php/info/view', requestOptions)
        .then(response => response.text())
        .catch(error => console.log('error', error));
      fetch(`/Proiect/Backend/Controllers/PlantController.php/info/view?plantId=${plant}`, {
        method: 'GET',
        headers: headers
      }).then(response => response.json())
        .then(info => {
          const div = document.createElement('div');
          div.innerHTML += "<br> Vizualizari: " + info.views + "<br>";
          desc.appendChild(div);
        })
        .catch(error => console.log('error', error));
      if (data) {
        const title = document.getElementById("nume");
        const nod = document.createTextNode(data.name);
        editName = document.getElementById("editName");
        editName.value = data.name;
        title.appendChild(nod);
        const nodd = document.createElement('div');
        nodd.innerHTML += "Descriere: " + data.desc;
        desc.appendChild(nodd);
        const changedesc = document.getElementById("editdesc");
        changedesc.value = data.desc;
        const color = document.getElementById("color");
        if (data.color == "default") {
          color.innerHTML += "-";
        } else {
          color.innerHTML += data.color;
        }
        const season = document.getElementById("season");
        if (data.season == "default") {
          season.innerHTML += "-";
        } else {
          season.innerHTML += data.season;
        }
        const type = document.getElementById("type");
        if (data.type == "default") {
          type.innerHTML += "-";
        } else {
          type.innerHTML += data.type;
        }
        const zone = document.getElementById("zone");
        if (data.zone == "default") {
          zone.innerHTML += "-";
        } else {
          zone.innerHTML += data.zone;
        }
        const imagine = document.getElementById("imagine");
        fetch(`/Proiect/Backend/Controllers/PlantController.php/info/image?id=${plant}`, {
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
            imagine.appendChild(image);
          })
          .catch(error => {
            console.error('Error:', error);
          });
      } else {
        console.log('User has no collections');

      }
    })
    .catch(error => {
      console.error('Error:', error);
    });

  function deletePlant() {
    const confirmed = confirm('Are you sure you want to delete this plant?');

    if (confirmed) {
      fetch(`/Proiect/Backend/Controllers/PlantController.php/info/delete?plantaId=${plant}`, {
        method: 'DELETE',
        headers: {
          'Authorization': 'Bearer ' + token,
          'Content-Type': 'application/json'
        }
      })
        .then(response => {
          if (response.ok) {
            console.log('Plant deleted successfully');
            window.location.href = '../plante/plante.html';
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