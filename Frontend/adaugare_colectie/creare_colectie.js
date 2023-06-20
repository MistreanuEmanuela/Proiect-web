
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
    var headers = new Headers();
    headers.append('Authorization', 'Bearer ' + token);
    headers.append('Content-Type', 'application/json');
    const decodedToken = decodeJwt(token);
    const userId = decodedToken.userId;
    var data = {
      name: name,
      desc: desc,
      userId: userId
    };


    var myHeaders = new Headers();
    myHeaders.append('Authorization', 'Bearer ' + token);
    myHeaders.append("Content-Type", "application/json");
    

    var requestOptions = {
      method: 'POST',
      headers: myHeaders,
      body: JSON.stringify(data),
      redirect: 'follow'
    };
    
    fetch("/Proiect/Backend/Controllers/CollectionController.php/addcolection", requestOptions)
      .then(response => {
        if (response.ok) {
          console.log('Plant deleted successfully');
          window.location.href = '../colectii/colectie.html';
        }
      })
      .catch(error => console.log('error', error));
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
