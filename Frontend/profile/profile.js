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
  
    var raw = JSON.stringify({
      "id": userId
    });
  
    var requestOptions = {
      method: 'POST',
      headers: headers,
      body: raw,
      redirect: 'follow'
    };
  var password;
  var firstname;
  var lastName;
    fetch("/Proiect/Backend/Controllers/UserController.php/profile", requestOptions)
      .then(response => response.json()) 
      .then(result => {
  
        document.querySelector('.uname').textContent = result.username;
        document.querySelector('.email').textContent = 'E-mail: ' + result.email;
        document.querySelector('.lastname').textContent = 'Lastname: ' + result.lastName;
        document.querySelector('.firstname').textContent = 'Firstname: ' + result.firstName;
        document.querySelector('.imagine img').src = 'p1.png';
        password=result.password;
        firstname=result.firstName; 
        lastName=result.lastName;
        document.getElementById('edit-firstname').value =firstname;
        document.getElementById('edit-lastname').value =lastName;
      
      })
      .catch(error => console.log('error', error));
  
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

  const editButton = document.querySelector('.edit-button');
  const editContainer = document.querySelector('.edit-container');
  const saveButton = document.querySelector('.save-button');
  const changePasswordButton = document.querySelector('.change-password-button');
  const changePasswordContainer = document.querySelector('.change-password-container');
  const savePasswordButton = document.querySelector('.save-password-button');


  editButton.addEventListener('click', () => {
    editContainer.classList.toggle('hidden');
  });

  async function updateProfile() {
    const editedFirstName = document.getElementById('edit-firstname').value;
    const editedLastName = document.getElementById('edit-lastname').value;
    var errors = [];
    if(editedFirstName==='' || editedLastName==='')
    {
      errors.push('You need to enter a value');
      var errorContainer = document.getElementById('error-container');
      errorContainer.innerText = errors[0];
    }
    if(errors.length===0){
    const raw = JSON.stringify({
        "id": userId, 
        "firstName": editedFirstName,
        "lastName": editedLastName
      });
    var requestOptions ={
        method: 'PATCH',
        headers: headers,
        body: raw,
        redirect: 'follow'
      }
      
      fetch("/Proiect/Backend/Controllers/UserController.php/profile", requestOptions)
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
    }
  }
  saveButton.addEventListener('click', updateProfile);

  changePasswordButton.addEventListener('click', () => {
    changePasswordContainer.classList.toggle('hidden');
  });

  function updatePassword( newPassword) {
    const raw = JSON.stringify({
        "id": userId, 
        "password": newPassword,
      });
 
    var requestOptions ={
        method: 'PUT',
        headers: headers,
        body: raw,
        redirect: 'follow'
      }
      
      fetch("/Proiect/Backend/Controllers/UserController.php/profile", requestOptions)
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
  }
  savePasswordButton.addEventListener('click', () => {
    const currentPassword = document.getElementById('current-password').value;
    const newPassword = document.getElementById('new-password').value;
    const confirmNewPassword = document.getElementById('confirm-new-password').value;

   
    let errorMessage = '';
    if (currentPassword === '') {
      errorMessage += 'Please enter your current password.\n';
    }
    if (currentPassword !==password ) {
        errorMessage += 'The current password is wrong.\n';
      }
    if (newPassword === '') {
      errorMessage += 'Please enter a new password.\n';
    }
    if (confirmNewPassword === '') {
      errorMessage += 'Please confirm your new password.\n';
    }
    if (newPassword !== confirmNewPassword) {
      errorMessage += 'The new password and confirm password do not match.\n';
    }

 
    const errorContainer = document.querySelector('.error-message');
    if (errorMessage !== '') {
      errorContainer.textContent = errorMessage;
      errorContainer.classList.remove('hidden');
    } else {
      errorContainer.classList.add('hidden');
      updatePassword( newPassword);
     
    }
  });

  });
  
  function decodeJwt(token) {
    const base64Url = token.split('.')[1]; 
    const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
    const jsonPayload = decodeURIComponent(atob(base64).split('').map(function (c) {
      return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2); 
    }).join(''));
  
    return JSON.parse(jsonPayload);
  }  