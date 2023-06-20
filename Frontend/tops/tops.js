document.addEventListener('DOMContentLoaded', () => {
    var token = getCookie('token');
    var headers = new Headers();
    headers.append('Authorization', 'Bearer ' + token);
    headers.append('Content-Type', 'application/json');
    const decodedToken = decodeJwt(token);
    const userId = decodedToken.userId;
    fetch(`/Proiect/Backend/Controllers/StatsController.php/list`, {
        method: 'GET',
        headers: headers
      })
        .then(response => response.json())
        .then(data => {
            if (data) {
                var container = document.getElementById("userCount");
                if(container.innerHTML)
                    container.innerHTML = '';
                container.innerHTML = "Numar useri curent: " + data['userCount']+". In total au fost " + data['userTotal']+ ", dintre care "+data['userMonth']+" in ultima luna!";
                var container = document.getElementById("plantCount");
                if(container.innerHTML)
                    container.innerHTML = '';
                container.innerHTML = "Numar plante: " + data['plantCount']+". In total au fost " + data['plantTotal']+ ", dintre care "+data['plantMonth']+" in ultima luna!";
                var container = document.getElementById("collectionCount");
                if(container.innerHTML)
                    container.innerHTML = '';
                container.innerHTML = "Numar colectii: " + data['collectionCount']+". In total au fost " + data['collectionTotal']+ ", dintre care "+data['collectionMonth']+" in ultima luna!";
                var container = document.getElementById("plantViews");
                if(container.innerHTML)
                    container.innerHTML = '';
                container.innerHTML = "Plantele au fost vizionate de " + data['pViewsTotal']+" , dintre care "+data['pViewsMonth']+" in ultima luna!";
                var container = document.getElementById("collectionViews");
                if(container.innerHTML)
                    container.innerHTML = '';
                container.innerHTML = "Colectiile au fost vizionate de " + data['cViewsTotal']+" , dintre care "+data['cViewsMonth']+" in ultima luna!";
            }
          })
          .catch(error => {
            console.error('Error:', error);
          });
    fetch(`/Proiect/Backend/Controllers/CollectionController.php/biggest`, {
        method: 'GET',
        headers: headers
        })
        .then(response => response.json())
        .then(data => {
            if (data) {
                var container = document.getElementById("biggestCollections");
                if(container.innerHTML)
                  container.innerHTML = '';
                data.forEach(item =>{
                    const button = document.createElement('button');
                    button.onclick = function() {
                        var expirationDate = new Date();
                        expirationDate.setTime(expirationDate.getTime() + 360000);
                        var cukie = JSON.stringify(item);
                        document.cookie = "collection=" + cukie + "; expires=" + expirationDate.toUTCString() + "; path=/";
                        window.location.href = '../plante/plante.html';  
                    }
                button.innerHTML="'"+item.colName+"' a lui "+item.userName+", cu "+item.pCount+" plante!";
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
                container.appendChild(button);
                });
            }
            })
            .catch(error => {
            console.error('Error:', error);
            });
    fetch(`/Proiect/Backend/Controllers/UserController.php/mostCol`, {
        method: 'GET',
        headers: headers
        })
        .then(response => response.json())
        .then(data => {
            if (data) {
                var container = document.getElementById("mostCollections");
                if(container.innerHTML)
                    container.innerHTML = '';
                var place=1;
                data.forEach(item =>{
                    const button = document.createElement('button');
                button.innerHTML=item.userName+", cu "+item.cCount+" colectii!";
               
                const image = document.createElement('img');
                image.src = './Trophy'+place+'.png';
                image.alt = 'not found';
                button.appendChild(image);
                place=place+1;

                container.appendChild(button);
                });
            }
            })
            .catch(error => {
            console.error('Error:', error);
            });
    fetch(`/Proiect/Backend/Controllers/UserController.php/mostPlants`, {
        method: 'GET',
        headers: headers
        })
        .then(response => response.json())
        .then(data => {
            if (data) {
                var container = document.getElementById("mostPlants");
                if(container.innerHTML)
                    container.innerHTML = '';
                var place=1;
                data.forEach(item =>{
                    const button = document.createElement('button');
                button.innerHTML=item.userName+", cu "+item.pCount+" plante!";
                const image = document.createElement('img');
                image.src = './Trophy'+place+'.png';
                image.alt = 'not found';
                button.appendChild(image);
                place=place+1;
                container.appendChild(button);
                });
            }
            })
            .catch(error => {
            console.error('Error:', error);
            });
    fetch(`/Proiect/Backend/Controllers/PlantController.php/mostViews?nr=3`, {
        method: 'GET',
        headers: headers
        })
        .then(response => response.json())
        .then(data => {
            if (data) {
                var container = document.getElementById("mostViewedP");
                if(container.innerHTML)
                    container.innerHTML = '';
                data.forEach(item =>{
                    const button = document.createElement('button');
                    button.onclick = function() {
                        window.location.href = '../info_planta/info_planta.html';
                        var expirationDate = new Date();
                        expirationDate.setTime(expirationDate.getTime() + 360000);
                        document.cookie = "plantId=" + item.id + "; expires=" + expirationDate.toUTCString() + "; path=/";
                        document.cookie = "ownerId=" +  item.userId+ "; expires=" + expirationDate.toUTCString() + "; path=/";
                      };
                button.innerHTML="'"+item.name+"' a lui "+item.userName+", cu "+item.views+" vizualizari!";
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
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                container.appendChild(button);
                });
            }
            })
            .catch(error => {
            console.error('Error:', error);
            });
    fetch(`/Proiect/Backend/Controllers/CollectionController.php/mostViews`, {
        method: 'GET',
        headers: headers
        })
        .then(response => response.json())
        .then(data => {
            if (data) {
                var container = document.getElementById("mostViewedC");
                if(container.innerHTML)
                    container.innerHTML = '';
                data.forEach(item =>{
                    const button = document.createElement('button');
                    button.onclick = function() {
                        var expirationDate = new Date();
                        expirationDate.setTime(expirationDate.getTime() + 360000);
                        var cukie = JSON.stringify(item);
                        document.cookie = "collection=" + cukie + "; expires=" + expirationDate.toUTCString() + "; path=/";
                        window.location.href = '../plante/plante.html';  
                    }
                button.innerHTML="'"+item.name+"' a lui "+item.userName+", cu "+item.views+" vizualizari!";
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
                container.appendChild(button);
                });
            }
            })
            .catch(error => {
            console.error('Error:', error);
            });
    const CSV = document.getElementById('CSV');
    CSV.addEventListener('click', downloadCSV);

    function downloadCSV() { 
        fetch(`/Proiect/Backend/Controllers/StatsController.php/file/CSV`, {
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token,
        }
        })
        .then(response => response.blob())
        .then(blob =>{
            const fileURL = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = fileURL;
            link.setAttribute('download', 'HEMA_Statistics.csv');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(fileURL);
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    const PDF = document.getElementById('PDF');
    PDF.addEventListener('click', downloadPDF);

    function downloadPDF() { 
        fetch(`/Proiect/Backend/Controllers/StatsController.php/file/PDF`, {
        method: 'GET',
        headers: {
            'Authorization': 'Bearer ' + token,
        }
        })
        .then(response => response.blob())
        .then(blob =>{
            const fileURL = window.URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = fileURL;
            link.setAttribute('download', 'HEMA_Statistics.pdf');
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            window.URL.revokeObjectURL(fileURL);
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }
    document.getElementById('bigColRSS').addEventListener('click', function() {
        fetch('/Proiect/Backend/Controllers/CollectionController.php/RSS/big')
          .then(response => response.text())
          .then(data => {
            const rssWindow = window.open();
            const preserveXML = rssWindow.document.createElement('pre');
            preserveXML.textContent = data;
            rssWindow.document.body.appendChild(preserveXML);
          })
          .catch(error => {
            console.error(error);
          });
      });
    document.getElementById('mostColRSS').addEventListener('click', function() {
        fetch('/Proiect/Backend/Controllers/UserController.php/RSS/col')
          .then(response => response.text())
          .then(data => {
            const rssWindow = window.open();
            const preserveXML = rssWindow.document.createElement('pre');
            preserveXML.textContent = data;
            rssWindow.document.body.appendChild(preserveXML);
          })
          .catch(error => {
            console.error(error);
          });
      });
    document.getElementById('mostPRSS').addEventListener('click', function() {
    fetch('/Proiect/Backend/Controllers/UserController.php/RSS/plants')
        .then(response => response.text())
        .then(data => {
        const rssWindow = window.open();
        const preserveXML = rssWindow.document.createElement('pre');
        preserveXML.textContent = data;
        rssWindow.document.body.appendChild(preserveXML);
        })
        .catch(error => {
        console.error(error);
        });
    });
    document.getElementById('mostVPRSS').addEventListener('click', function() {
        fetch('/Proiect/Backend/Controllers/PlantController.php/RSS')
          .then(response => response.text())
          .then(data => {
            const rssWindow = window.open();
            const preserveXML = rssWindow.document.createElement('pre');
            preserveXML.textContent = data;
            rssWindow.document.body.appendChild(preserveXML);
          })
          .catch(error => {
            console.error(error);
          });
      });
    document.getElementById('mostVCRSS').addEventListener('click', function() {
        fetch('/Proiect/Backend/Controllers/CollectionController.php/RSS/view')
          .then(response => response.text())
          .then(data => {
            const rssWindow = window.open();
            const preserveXML = rssWindow.document.createElement('pre');
            preserveXML.textContent = data;
            rssWindow.document.body.appendChild(preserveXML);
          })
          .catch(error => {
            console.error(error);
          });
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