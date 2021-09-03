function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
}

function opencontent(evt, cityName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.className += " active";
}

document.getElementById('loginform').addEventListener('submit', function (e) {
    fetchlogin(e)
});

function fetchlogin(evt) {
    evt.preventDefault()
    var fd = new FormData();
    fd.append('username', loginuser.value);
    fd.append('password', loginpass.value);

    fetch('http://localhost/clothesshop/api/api.php?action=login', {
            method: 'POST',
            body: fd,
            credentials: 'include'
        })
        .then(function (headers) {
            if (headers.status == 401) {
                console.log('login failed');
                localStorage.removeItem('csrf');
                localStorage.removeItem('username');
                localStorage.removeItem('phone');
                localStorage.removeItem('email');
                localStorage.removeItem('postcode');
                localStorage.removeItem('CustomerID');
                return;
            }
            if (headers.status == 203) {
                console.log('registration required');
                // only need csrf
            }
            headers.json().then(function (body) {
                // BUG is this a 203 or 200?
                localStorage.setItem('csrf', body.Hash);
                localStorage.setItem('CustomerID', loginuser.value);
                localStorage.setItem('username', body.username);
                localStorage.setItem('email', body.email);
                localStorage.setItem('phone', body.phone);
                localStorage.setItem('postcode', body.postcode);
            })
        })
        .catch(function (error) {
            console.log(error)
        });
}

function fetchlogout() {
    fetch('http://localhost/clothesshop/api/api.php?action=logout', {
            method: 'GET',
            credentials: 'include'
        })
        .then(function (headers) {
            if (headers.status != 200) {
                console.log('logout failed Server-Side, but make client login again');
            }
            localStorage.removeItem('csrf');
            localStorage.removeItem('username');
            localStorage.removeItem('email');
            localStorage.removeItem('phone');
            localStorage.removeItem('postcode');
            localStorage.removeItem('CustomerID');
        })
        .catch(function (error) {
            console.log(error)
        });
}


document.getElementById('registerform').addEventListener('submit', function (e) {
    fetchregister(e)
});

function fetchregister(evt) {
    evt.preventDefault();
    var fd = new FormData();
    fd.append('username', regusername.value);
    fd.append('email', regemail.value); //lop off # in hex code
    fd.append('phone', regphone.value);
    fd.append('postcode', regpostcode.value);
    fd.append('password', regpassword.value);
    fd.append('password2', regpassword2.value);
    fd.append('csrf', localStorage.getItem('csrf'));
    fetch('http://localhost/clothesshop/api/api.php?action=register', {
            method: 'POST',
            body: fd,
            credentials: 'include'
        })
        .then(function (headers) {
            if (headers.status == 400) {
                console.log('user exists');
                return;
            }

            if (headers.status == 201) {
                console.log('registration updated');
                return;
            }

        })
        .catch(function (error) {
            console.log(error)
        });
}

document.getElementById('Menproduct').innerHTML=mendisplay();
function mendisplay(){
fetch('http://localhost/clothesshop/api/api.php?action=mendisplay',
{
    method: 'POST',
    credentials: 'include'
}
).then((res)=>res.json())
.then(response=>{console.log(response);
    let output = '';
    for(let i in response){
        output+=`<tr>
        <td>${response[i].productID}</td>
        <td>${response[i].productname}</td>
        <td>${response[i].description}</td>
        <td ><img src='../images/${response[i].image }' style="width: 100px; height: 100px;"></td>
        <td>${response[i].price}</td>
        <td><input type="submit" name="delete" value="delete"  onclick="fetchdelete(${response[i].productID})"></td>
        </tr>`;
    }
    document.querySelector('#Menproduct').innerHTML = output;
}).catch(error=>console.error(error));
}
document.getElementById('Womenproduct').innerHTML=womendisplay();
function womendisplay(){
fetch('http://localhost/clothesshop/api/api.php?action=womendisplay',
{
    method: 'POST',
    credentials: 'include'
}
).then((res)=>res.json())
.then(response=>{console.log(response);
    let output = '';
    for(let i in response){
        output+=`<tr>
        <td>${response[i].productID}</td>
        <td>${response[i].productname}</td>
        <td>${response[i].description}</td>
        <td ><img src='../images/${response[i].image }' style="width: 100px; height: 100px;"></td>
        <td>${response[i].price}</td>
        <td><input type="submit" name="delete" value="delete"  onclick="fetchdelete(${response[i].productID})"></td>
        </tr>`;
    }
    document.querySelector('#Womenproduct').innerHTML = output;
}).catch(error=>console.error(error));
}

document.getElementById('editform').addEventListener('submit', function(e) {fetchupdate(e)});
function fetchupdate(evt) {
    evt.preventDefault();
    var fd = new FormData();
    fd.append('username', upusername.value);
    fd.append('email', upemail.value); //lop off # in hex code
    fd.append('phone', upphone.value);
    fd.append('postcode', uppostcode.value);
    fd.append('password', uppassword.value);
    fd.append('password2', uppassword2.value);
    fd.append('csrf', localStorage.getItem('csrf'));
    fetch('http://localhost/clothesshop/api/api.php?action=update', 
    {
        method: 'POST',
        body: fd,
        credentials: 'include'
    })
    .then(function(headers) {
        if(headers.status == 400) {
            console.log('user exists');
            return;
        }
     
        if(headers.status == 201) {
            console.log(' updated');
            return;
        }
       
    })
    .catch(function(error) {console.log(error)});
}