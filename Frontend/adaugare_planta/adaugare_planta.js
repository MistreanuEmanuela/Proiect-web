document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('.form');
  var token = getCookie('token');
  if(token===null){
    const confirmed = confirm('Sesiunea a expirat, trebuie sa va autentificati');
  
  if (confirmed) {
    window.location.href = '../sign/sign.html';
  }
  }
  form.addEventListener('submit', (e) => {
    e.preventDefault();

    var name = document.getElementById('name').value;
    var desc = document.getElementById('desc').value;
    var image = document.getElementById('image').files[0];
    var color = document.getElementById('color').value;
    var type = document.getElementById('tip').value;
    var zona = document.getElementById('zona').value;
    var anotimp = document.getElementById('anotimp').value;


   
    var headers = new Headers();
    headers.append('Authorization', 'Bearer ' + token);
    var collectionn = JSON.parse(getCookie('collection'));


    const formData = new FormData();
    formData.append("name", name);
    formData.append("desc", desc);
    formData.append("image", image);
    formData.append("collection", collectionn.id);
    formData.append("culoare", color);
    formData.append("type", type);
    formData.append("zona", zona);
    formData.append("anotimp", anotimp);


    var requestOptions = {
      method: 'POST',
      headers: headers,
      body: formData,
      redirect: 'follow'
    };

    fetch('/Proiect/Backend/Controllers/PlantController.php/planta', requestOptions)
      .then(response => response.text())
      .then(result => {
        if (!result.includes('error')) {
          window.location.href = '../plante/plante.html';
        }
      })
      .catch(error => console.log('error', error));

    if (color != "default") {

      var raw = JSON.stringify({
        "collectionId": collectionn.id,
        "tag": color
      });

      var requestOptions = {
        method: 'POST',
        headers: headers,
        body: raw,
        redirect: 'follow'
      };

      fetch("/Proiect/Backend/Controllers/TagsController.php/tag", requestOptions)
        .catch(error => console.log('error', error));
    }
    if (type != "default") {

      var raw = JSON.stringify({
        "collectionId": collectionn.id,
        "tag": type
      });

      var requestOptions = {
        method: 'POST',
        headers: headers,
        body: raw,
        redirect: 'follow'
      };

      fetch("/Proiect/Backend/Controllers/TagsController.php/tag", requestOptions)
        .catch(error => console.log('error', error));
    }
    if (zona != "default") {

      var raw = JSON.stringify({
        "collectionId": collectionn.id,
        "tag": zona
      });

      var requestOptions = {
        method: 'POST',
        headers: headers,
        body: raw,
        redirect: 'follow'
      };

      fetch("/Proiect/Backend/Controllers/TagsController.php/tag", requestOptions)
        .catch(error => console.log('error', error));
    }
    if (anotimp != "default") {

      var raw = JSON.stringify({
        "collectionId": collectionn.id,
        "tag": anotimp
      });

      var requestOptions = {
        method: 'POST',
        headers: headers,
        body: raw,
        redirect: 'follow'
      };

      fetch("/Proiect/Backend/Controllers/TagsController.php/tag", requestOptions)
        .catch(error => console.log('error', error));
    }
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
