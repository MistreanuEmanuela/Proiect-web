document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('.form');

form.addEventListener('submit', (e) => {
  e.preventDefault(); 
  const username = document.getElementById('uname').value;
  const email = document.getElementById('email').value;
  const password = document.getElementById('password').value;
  const password2=document.getElementById('conf_pass').value;
  const firstName = document.getElementById('firstname').value;
  const lastName = document.getElementById('lastname').value;

  const user = {
    username,
    email,
    password,
    firstName,
    lastName,
  };
  
  var errors = [];

  if (username.trim() === '') {
    errors.push('Username is required');
  }
  if (username.length < 8) {
    errors.push('Username need to be longer(min 8 characters)');
  }

  if (email.trim() === '') {
    errors.push('Email is required');
  }

  if (password.trim() === '') {
    errors.push('Password is required');
  }

  if (password !== password2) {
    errors.push('Passwords do not match');
  }


  var errorContainer = document.getElementById('error-container');
  if(errorContainer){
  errorContainer.innerHTML = ''; 
  }
  if (errors.length > 0) {
    for (var i = 0; i < errors.length; i++) {
      var errorElement = document.createElement('p');
      errorElement.innerText = errors[i];
      errorContainer.appendChild(errorElement);
    }
  
  }
else
{
  let userData = {};
  const requestData = {
    username: username
  };
  
  fetch('/Proiect/Backend/Controllers/UserController.php/finduser', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify(requestData)
  })
    .then(response => response.json())
    .then(data => {
      if (data) {
        userData = data;
         if (Object.keys(userData).length === 1) {
          fetch('/Proiect/Backend/Controllers/UserController.php/inregistrare', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
            },
            body: JSON.stringify(user),
          })
          .catch(error => {
            console.error('Error:', error);
          });
          console.log('User not found');
          window.location.href = "../sign/sign.html"; 
          }

          else {
            errors.push('This username already exists. Please choose another one.');
            var errorContainer = document.getElementById('error-container');
            errorContainer.innerText = errors[0];
          }
      }
      })

    }
}); 
});