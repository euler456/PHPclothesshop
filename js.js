function openNav() {
    document.getElementById("mySidenav").style.width = "250px";
}

function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
}

function opencontent(evt, idlocate) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
   if(idlocate=="Register" || idlocate=="Home" ){
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
   else{
    fetch('C:/Users/euler/OneDrive/desktop/githubstuffs/clothes/websitepage/api/api.php?action=isloggedin', {
        method: 'POST',
        credentials: 'include'
    })
    .then(function (headers) {
        if (headers.status == 403) {
            console.log('Did not login');
           
        }
       
        if (headers.status == 203 ) {
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            tablinks = document.getElementsByClassName("tablinks");
            for (i = 0; i < tablinks.length; i++) {
                tablinks[i].className = tablinks[i].className.replace(" active", "");
            }
            document.getElementById(idlocate).style.display = "block";
            evt.currentTarget.className += " active";
            // only need csrf
        }
    
    })
    .catch(function (error) {
        console.log(error)
    });
}
    
    
}

document.getElementById('loginform').addEventListener('submit', function (e) {
    fetchlogin(e)
});

function fetchlogin(evt) {
    evt.preventDefault()
    var fd = new FormData();
    fd.append('username', loginuser.value);
    fd.append('password', loginpass.value);

    fetch('C:/Users/euler/OneDrive/desktop/githubstuffs/clothes/websitepage/api/api.php?action=login', {
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
                otherdisplay();
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
    fetch('C:/Users/euler/OneDrive/desktop/githubstuffs/clothes/websitepage/api/api.php?action=logout', {
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
    fetch('C:/Users/euler/OneDrive/desktop/githubstuffs/clothes/websitepage/api/api.php?action=register', {
            method: 'POST',
            body: fd,
            credentials: 'include'
        })
        .then(function (headers) {
            if (headers.status == 418) {
                $("#target").removeClass('d-none');
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
fetch('C:/Users/euler/OneDrive/desktop/githubstuffs/clothes/websitepage/api/api.php?action=mendisplay',
{
    method: 'POST',
    credentials: 'include'
}
).then((res)=>res.json())
.then(response=>{console.log(response);
    let output = '';
    for(let i in response){
        output+=`<tr class="container">
        <td class="pdimg" style="visibility:hidden">${response[i].image}</td>
        <td ><img src='./images/${response[i].image}.jpg' style="width: 500px; height:400px;margin-top:20px;"></td>
        <td class="prdid" style="visibility:hidden">${response[i].productID}</td>
        <td class="pdname">${response[i].productname}</td>
        <td class="pdprice">${response[i].price}</td>
        <td><select  class="vat">
  <option value="S">S</option>
  <option value="M">M</option>
  <option value="L">L</option>
</select><td>
        <buttom class="order"  value="order">order</buttom>
        </tr>`;
    }
    document.querySelector('#Menproduct').innerHTML = output;
}).catch(error=>console.error(error));
}

function womendisplay(){
fetch('C:/Users/euler/OneDrive/desktop/githubstuffs/clothes/websitepage/api/api.php?action=womendisplay',
{
    method: 'POST',
    credentials: 'include'
}
).then((res)=>res.json())
.then(response=>{console.log(response);
    let output = '';
    for(let i in response){
        output+=`<tr class="container">
        <td class="pdimg" style="visibility:hidden">${response[i].image}</td>
        <td><img  src='./images/${response[i].image}.jpg' style="width: 500px; height:400px;margin-top:20px;"></td>
        <td class="prdid" style="visibility:hidden">${response[i].productID}</td>
        <td  class="pdname">${response[i].productname}</td>
        <td class="pdprice">${response[i].price}</td>
        <td><select  class="vat">
  <option value="S">S</option>
  <option value="M">M</option>
  <option value="L">L</option>
</select><td>
<buttom class="order"  value="order">order</buttom>
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
    fetch('C:/Users/euler/OneDrive/desktop/githubstuffs/clothes/websitepage/api/api.php?action=update', 
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
    var image = $(this).closest('.container').find('.pdimg').html();
    var price = $(this).closest('.container').find('.pdprice').html();
    var productid = $(this).closest('.container').find('.prdid').html();
    var fd = new FormData();
    fd.append('productID',productid );
    fd.append('productname', productname );
    fd.append('price', price );
    fd.append('size', size );
    fd.append('image', image );
    fetch('C:/Users/euler/OneDrive/desktop/githubstuffs/clothes/websitepage/api/api.php?action=orderproduct', 
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
            $(".ordersuccessalert").removeAttr("style");
            $(".ordersuccessalert").removeClass('invisible');
            return;
        }
       
    })
    .catch(function(error) {console.log(error)});
    
  });

  function otherdisplay(){
    fetch('C:/Users/euler/OneDrive/desktop/githubstuffs/clothes/websitepage/api/api.php?action=otherdisplay',
    {
        method: 'POST',
        credentials: 'include'
    }
    ).then((res)=>res.json())
    .then(response=>{console.log(response);
        let output = '';
        for(let i in response){
            output+=`<tr class="container">
            <td class="pdimg" style="visibility:hidden">${response[i].image}</td>
            <td ><img  src='./images/${response[i].image}.jpg' style="width: 500px; height:400px;margin-top:20px;"></td>
            <td class="prdid" style="visibility:hidden">${response[i].productID}</td>
            <td  class="pdname">${response[i].productname}</td>
            <td class="pdprice">${response[i].price}</td>
            <td><buttom class="orderother"  value="order">order</buttom></td>
    </tr>`;
        }
        document.querySelector('#otherproduct').innerHTML = output;
    }).catch(error=>console.error(error));
    }
$(document).on('click', '.orderother', function(event) {
    var productname = $(this).closest('.container').find('.pdname').html();
    var image = $(this).closest('.container').find('.pdimg').html();
    var price = $(this).closest('.container').find('.pdprice').html();
    var productid = $(this).closest('.container').find('.prdid').html();
    var fd = new FormData();
    fd.append('productID',productid );
    fd.append('productname', productname );
    fd.append('price', price );
    fd.append('image', image );
    fetch('C:/Users/euler/OneDrive/desktop/githubstuffs/clothes/websitepage/api/api.php?action=orderotherproduct', 
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
            $(".ordersuccessalert").removeAttr("style");
            $(".ordersuccessalert").removeClass('invisible');
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
    fetch('C:/Users/euler/OneDrive/desktop/githubstuffs/clothes/websitepage/api/api.php?action=createorder', 
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

    $(document).on('click', '.delete', function(event) {
        
        var orderitem_ID = $(this).closest('.chartcontainer').find('.oditem').html();
        var fd = new FormData();
        fd.append('orderitem_ID',orderitem_ID );
        fetch('C:/Users/euler/OneDrive/desktop/githubstuffs/clothes/websitepage/api/api.php?action=orderdelete', 
        {
            method: 'POST',
            body: fd,
            credentials: 'include'
        })
        .then(function(headers) {
            if(headers.status == 400) {
                console.log('can not delete');
                return;
            }
         
            if(headers.status == 201) {
                console.log('delete succussful');
                updateDiv();
                return;
            }
           
        })
        .catch(function(error) {console.log(error)});
        
      });
    function fetchislogin() {
        fetch('C:/Users/euler/OneDrive/desktop/githubstuffs/clothes/websitepage/api/api.php?action=isloggedin', {
                method: 'POST',
                credentials: 'include'
            })
            .then(function (headers) {
                if (headers.status == 403) {
                    console.log('Did not login');
                   
                    return false;
                }
               
                if (headers.status == 203) {
                    return true;  
                    // only need csrf
                }
            
            })
            .catch(function (error) {
                console.log(error)
            });
    }
    function updateDiv()
{ 
    $( "#showorderform" ).load(window.location.href + " #showorderform" );
    orderchart();
}
function orderchart(){
    fetch('C:/Users/euler/OneDrive/desktop/githubstuffs/clothes/websitepage/api/api.php?action=showorderform',
    {
        method: 'GET',
        credentials: 'include'
    }
    ).then((res)=>res.json())
    .then(response=>{console.log(response);
        let output = '';
        for(let i in response){
            output+=`<tr class="chartcontainer">
            <td class="prdid" style="display:none">${response[i].productID}</td>
            <td class="oditem" style="display:none">${response[i].orderitem_ID}</td>
            <td ><img src='./images/${response[i].image}.jpg' style="width: 100px; height:100px;margin-top:20px;"></td>
            <td class="pdname">${response[i].productname}</td>
            <td>${response[i].size}</td>
            <td class="pdprice">${response[i].price}</td>
            <td><buttom class="delete"  value="delete">delete</buttom></td>
            </tr>`;
        }
        document.querySelector('#showorderform').innerHTML = output;
    }).catch(error=>console.error(error));
    }

function sumtotalpriceff(){
    fetch('C:/Users/euler/OneDrive/desktop/githubstuffs/clothes/websitepage/api/api.php?action=sumtotalprice',
    {
        method: 'GET',
        credentials: 'include'
    }
    ).then(function(headers) {
        if(headers.status == 400) {
            console.log('sumtotalprice');
            return;
        }
     
        if(headers.status == 201) {
            console.log('fail to sum');
            
            fetch('C:/Users/euler/OneDrive/desktop/githubstuffs/clothes/websitepage/api/api.php?action=confirmorderform',
            {
                method: 'GET',
                credentials: 'include'
            }
            ).then((res)=>res.json())
            .then(response=>{console.log(response);
                let output = '';
                for(let i in response){
                    output+=`<tr>
                    <td type="text" class="orderID">OrderID:${response[i].orderID}</td>
                    <td type="datetime" class="ordertime">ordertime:${response[i].ordertime}</td>
                    <td type="number" class="totalprice">Price:${response[i].totalprice}</td>
                    </tr>`;
                }
                document.querySelector('#completeorder').innerHTML = output;
            }).catch(error=>console.error(error));
            return;
        }
    })
    .catch(function(error) {console.log(error)});
}

function fetchcheckoutupdate() {
    fetch('C:/Users/euler/OneDrive/desktop/githubstuffs/clothes/websitepage/api/api.php?action=checkoutupdate', {
            method: 'POST',
            credentials: 'include'
        })
        .then(function (headers) {
            if (headers.status == 403) {
                console.log('did not check');
               
                return false;
            }
           
            if (headers.status == 201) {
                console.log('check out successful');
                return true;  
                // only need csrf
            }
        
        })
        .catch(function (error) {
            console.log(error)
        });
}
function alertreshow(){
    
    $(".ordersuccessalert").addClass('invisible').fadeOut();
}