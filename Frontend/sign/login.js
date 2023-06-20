document.addEventListener('DOMContentLoaded', () => {

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
var token = getCookie('token');

if(token)
{
  window.location.href = "/Proiect/Frontend/main/main.html";
}

const form = document.querySelector('.form');
  
  form.addEventListener('submit', (e) => {
    e.preventDefault(); 
    const username = document.getElementById('uname').value;
    const password = document.getElementById('psw').value;
    

var formdata = new FormData();
formdata.append("username", username);
formdata.append("password", password);

var requestOptions = {
  method: 'POST',
  body: formdata,
  redirect: 'follow'
};


fetch("/Proiect/Backend/Controllers/UserController.php/login", requestOptions)
  .then(response => response.json())
  .then(data => {
    if (data.token) {
      window.location.href = "/Proiect/Frontend/main/main.html";
    } else {
      var errorContainer = document.getElementById('error-container');
      errorContainer.innerText = 'Username si/sau parola incorecta';
    }
  })
  .catch(error => {
    console.error(error);
  });
    });
  
  });

