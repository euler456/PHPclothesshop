function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
}

function opencontent(evt, idlocate) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(idlocate).style.display = "block";
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
           
            if (headers.status == 201) {
                mendisplay();
                womendisplay();
                fetchcreateorder();
                opencontent(evt,'Mens');
             
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
            if (headers.status == 418) {
                $("#target").removeClass('invisible');
                console.log('user exists');
                
                return;
            }

            if (headers.status == 201) {
                console.log('registration updated');
                opencontent(evt, 'Home')
                return;
            }

        })
        .catch(function (error) {
            console.log(error)
        });
}


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
        output+=`<tr class="container">
        <td ><img src='../images/${response[i].image }' style="width: 500px; height:400px;margin-top:20px;"></td>
        <td class="prdid" style="visibility:hidden">${response[i].productID}</td>
        <td class="pdname">${response[i].productname}</td>
        <td>${response[i].description}</td>
        <td class="pdprice">${response[i].price}</td>
        <td><select  class="vat">
  <option value="S">S</option>
  <option value="M">M</option>
  <option value="L">L</option>
</select><td>
        <td><buttom class="order"  value="order">order</buttom></td>
        </tr>`;
    }
    document.querySelector('#Menproduct').innerHTML = output;
}).catch(error=>console.error(error));
}

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
        output+=`<tr class="container">
        <td ><img src='../images/${response[i].image }' style="width: 500px; height:400px;margin-top:20px;"></td>
        <td class="prdid" style="visibility:hidden">${response[i].productID}</td>
        <td  class="pdname">${response[i].productname}</td>
        <td>${response[i].description}</td>
        <td class="pdprice">${response[i].price}</td>
        <td><select  class="vat">
  <option value="S">S</option>
  <option value="M">M</option>
  <option value="L">L</option>
</select><td>
<td><buttom class="order"  value="order">order</buttom></td>
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
            opencontent(evt,'Mens');
            return;
        }
       
    })
    .catch(function(error) {console.log(error)});
}

$(document).on('click', '.order', function(event) {
    var size = $(this).closest('.container').find('.vat').val();
    var productname = $(this).closest('.container').find('.pdname').html();
    var price = $(this).closest('.container').find('.pdprice').html();
    var productid = $(this).closest('.container').find('.prdid').html();
    var fd = new FormData();
    fd.append('productID',productid );
    fd.append('productname', productname );
    fd.append('price', price );
    fd.append('size', size );
    alert(productname );
    fetch('http://localhost/clothesshop/api/api.php?action=orderproduct', 
    {
        method: 'POST',
        body: fd,
        credentials: 'include'
    })
    .then(function(headers) {
        if(headers.status == 400) {
            console.log('can not order');
            return;
        }
     
        if(headers.status == 201) {
            console.log('order succussful');
            return;
        }
       
    })
    .catch(function(error) {console.log(error)});
    
  });
  
function fetchorder(size,productID) {
    var fd = new FormData();
    fd.append('productID', productID);
  
}

function fetchcreateorder(evt) {
    
    var orderstatus= "Notpayed";
    var totalprice= 0 ;
    var fd = new FormData();
    fd.append('orderstatus', orderstatus );
    fd.append('totalprice', totalprice );
    fetch('http://localhost/clothesshop/api/api.php?action=createorder', 
    {
        method: 'POST',
        body: fd,
        credentials: 'include'
    })
    .then(function(headers) {
        if(headers.status == 400) {
            console.log('can not order you are not loggedin');
            return;
        }
     
        if(headers.status == 201) {
            console.log('going to order');
            return;
        }
       
    })
    .catch(function(error) {console.log(error)});
}
