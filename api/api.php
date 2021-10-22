<?php

include_once("./core.php");
require_once('../vendor/autoload.php');
require_once('./se.php');
require_once('./userfunction.php');

//sqsuser is from the userfunction.php which will represent database
$sqsdb = new sqsuser;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\RedirectResponse;
//||
$request = Request::createFromGlobals();
$response = new Response();
$session = new Session(new NativeSessionStorage(), new AttributeBag());

if(isset($_SERVER['HTTP_REFERER'])) {
$http_origin = $_SERVER['HTTP_REFERER'];
if ( $http_origin == 'https://clotheshopproj2.herokuapp.com/' ||$http_origin ==  'https://clothesshopadmin.herokuapp.com/')
{
    $response->headers->set('Access-Control-Allow-Origin', $http_origin);
}}
else{
    $response->setStatusCode(400);
}
$response->headers->set('Content-Type', 'application/json');
$response->headers->set('Access-Control-Allow-Headers', 'origin, content-type, accept');
$response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
$response->headers->set('Access-Control-Allow-Credentials', 'true');
//put session here because here is the place the action started

ini_set('session.cookie_samesite', "None");
ini_set('session.cookie_secure', "1");
$session->start();

if (!$session->has('sessionObj')) {
    $session->set('sessionObj', new sqsSession);
}
if (empty($request->query->all())) {
    $response->setStatusCode(400);
} elseif ($request->cookies->has('PHPSESSID')) {

    if ($session->get('sessionObj')->is_rate_limited()) {
        //$response->setStatusCode(429);
    }
    if ($session->get('sessionObj')->day_rate_limited()) {
        $response->setStatusCode(429);
    }
    //if the request is post , the code will start the action which is in the POST Block
    if ($request->getMethod() == 'POST') {
        // register  
        if ($request->query->getAlpha('action') == 'register') {
            if ($request->request->has('username')) {
                $res = $sqsdb->userExists($request->request->get('username'));
                if ($res) {
                    $response->setStatusCode(418);
                } else {
                    if (
                        $request->request->has('username') and
                        $request->request->has('email') and
                        $request->request->has('phone') and
                        $request->request->has('postcode') and
                        $request->request->has('password') and
                        $request->request->has('password2')
                    ) {
                        $username = $session->get('sessionObj')->input_testing($request->request->getAlpha('username'));
                        $email = $session->get('sessionObj')->input_testing($request->request->get('email'));
                        $phone = $session->get('sessionObj')->input_testing($request->request->get('phone'));
                        $postcode = $session->get('sessionObj')->input_testing($request->request->get('postcode'));
                        $password = $session->get('sessionObj')->input_testing($request->request->get('password'));
                        $res = $session->get('sessionObj')->register(
                            $username,
                            $email,
                            $phone,
                            $postcode,
                            $password,
                            $request->request->get('csrf')
                        );
                        if ($res === true) {
                            $response->setStatusCode(201);
                        } elseif ($res === false) {
                            $response->setStatusCode(403);
                        } elseif ($res === 0) {
                            $response->setStatusCode(500);
                        }
                    }
                }
            } else {
                $response->setStatusCode(400);
            }
        } elseif ($request->query->getAlpha('action') == 'login') {
            if ($request->request->has('username') and $request->request->has('password')) {
                $username = $session->get('sessionObj')->input_testing($request->request->getAlpha('username'));
                $password = $session->get('sessionObj')->input_testing($request->request->get('password'));
                $res = $session->get('sessionObj')->login(
                    $username,
                    $password
                );
                if ($res === false) {
                    $response->setContent(json_encode($request->request));
                    $response->setStatusCode(401);
                } elseif (count($res) == 1) {
                    $response->setStatusCode(203);
                    $response->setContent(json_encode($res));
                } elseif (count($res) > 1) {
                    $response->setStatusCode(201);
                    $response->setContent(json_encode($res));
                    $ip = $request->getClientIp();
                    $res = $session->get('sessionObj')->logEvent($ip, 'login', $request->cookies->get('PHPSESSID'));
                }
            } else {
                $response->setContent(json_encode($request));
                $response->setStatusCode(404);
            }
        } elseif ($request->query->getAlpha('action') == 'isloggedin') {
            $res = $session->get('sessionObj')->isLoggedIn();
            if ($res == false) {
                $response->setStatusCode(403);
            } elseif (count($res) == 1) {
                $response->setStatusCode(203);
                $response->setContent(json_encode($res));
            }
        }
        elseif ($request->query->getAlpha('action') == 'adminisloggedin') {
            $res = $session->get('sessionObj')->adminisLoggedIn();
            if ($res == false) {
                $response->setStatusCode(403);
            } elseif (count($res) == 1) {
                $response->setStatusCode(203);
                $response->setContent(json_encode($res));
            }
        }
         elseif ($request->query->getAlpha('action') == 'update') {
            $ip = $request->getClientIp();
            $res = $session->get('sessionObj')->logEvent($ip, 'update', $request->cookies->get('PHPSESSID'));
            $res = $session->get('sessionObj')->isLoggedIn();
            if (($request->request->has('username')) && (count($res) == 1)) {
                $res = $sqsdb->userExists($request->request->get('username'));

                if ($res) {
                    $response->setStatusCode(400);
                } else {
                    if (

                        $request->request->has('username') and
                        $request->request->has('email') and
                        $request->request->has('phone') and
                        $request->request->has('postcode') and
                        $request->request->has('password') and
                        $request->request->has('password2')
                    ) {
                        $username = $session->get('sessionObj')->input_testing($request->request->getAlpha('username'));
                        $email = $session->get('sessionObj')->input_testing($request->request->get('email'));
                        $phone = $session->get('sessionObj')->input_testing($request->request->get('phone'));
                        $postcode = $session->get('sessionObj')->input_testing($request->request->get('postcode'));
                        $password = $session->get('sessionObj')->input_testing($request->request->get('password'));
                        $res = $session->get('sessionObj')->update(
                            //    $res = $sqsdb->userid($request->request->get('currentusername')),
                            $username,
                            $email,
                            $phone,
                            $postcode,
                            $password,
                            $csrf
                        );
                        if ($res === true) {
                            $response->setStatusCode(201);
                        } elseif ($res === false) {
                            $response->setStatusCode(403);
                        } elseif ($res === 0) {
                            $response->setStatusCode(500);
                        }
                    }
                }
            } else {
                $response->setStatusCode(402);
            }
        } elseif ($request->query->getAlpha('action') == 'displayorder') {
            $res = $session->get('sessionObj')->adminisLoggedIn();
            if ($res == false) {
                $response->setStatusCode(400);
            } else {
                $res = $session->get('sessionObj')->displayorder();
                $response->setContent(json_encode($res));
                $response->setStatusCode(200);
            }
        } elseif ($request->query->getAlpha('action') == 'displayordercontent') {
            $res = $session->get('sessionObj')->adminisLoggedIn();
            if ($res == false) {
                $response->setStatusCode(400);
            } else {
                $res = $session->get('sessionObj')->displayordercontent();
                $response->setContent(json_encode($res));
                $response->setStatusCode(200);
            }
        }
        elseif ($request->query->getAlpha('action') == 'orderdelete') {
            $ip = $request->getClientIp();
            $res = $session->get('sessionObj')->logEvent($ip, 'orderdelete', $request->cookies->get('PHPSESSID'));
            $res = $session->get('sessionObj')->orderdelete(
                $request->request->get('orderitem_ID')
            );
      
            if ($res === true) {
                $response->setStatusCode(201);
            } elseif ($res === false) {
                $response->setStatusCode(403);
            } elseif ($res === 0) {
                $response->setStatusCode(500);
            }
        } 
        elseif ($request->query->getAlpha('action') == 'adminorderdelete') {
            $ip = $request->getClientIp();
            $res = $session->get('sessionObj')->logEvent($ip, 'admin orderdelete', $request->cookies->get('PHPSESSID'));
            $res = $session->get('sessionObj')->orderdelete(
                $request->request->get('orderitem_ID')
            );
            $session->get('sessionObj')->adminsumtotalprice( $request->request->get('orderID'));
            if ($res === true) {
                $response->setStatusCode(201);
            } elseif ($res === false) {
                $response->setStatusCode(403);
            } elseif ($res === 0) {
                $response->setStatusCode(500);
            }
        } 
     
      elseif ($request->query->getAlpha('action') == 'mendisplay') {
            $res = $session->get('sessionObj')->isLoggedIn();
            if ($res == false) {
                $response->setStatusCode(403);
            } elseif (count($res) == 1) {
                $res = $session->get('sessionObj')->mendisplay();
                $response->setContent(json_encode($res));
                $response->setStatusCode(200);
            }
        } elseif ($request->query->getAlpha('action') == 'otherdisplay') {
            $res = $session->get('sessionObj')->isLoggedIn();
            if ($res == false) {
                $response->setStatusCode(403);
            } elseif (count($res) == 1) {
                $res = $session->get('sessionObj')->otherdisplay();
                $response->setContent(json_encode($res));
                $response->setStatusCode(200);
            }
        } elseif ($request->query->getAlpha('action') == 'displayproduct') {
            $res = $session->get('sessionObj')->adminisLoggedIn();
            if ($res == false) {
                $response->setStatusCode(403);
            } elseif (count($res) == 1) {
                $res = $session->get('sessionObj')->displayproduct();
                $response->setContent(json_encode($res));
                $response->setStatusCode(200);
            }
        } 
        
        elseif ($request->query->getAlpha('action') == 'womendisplay') {
            $res = $session->get('sessionObj')->isLoggedIn();
            if ($res == false) {
                $response->setStatusCode(403);
            } elseif (count($res) == 1) {
                $res = $session->get('sessionObj')->womendisplay();
                $response->setContent(json_encode($res));
                $response->setStatusCode(200);
            }
        } elseif ($request->query->getAlpha('action') == 'addproduct') {

            if (
                $request->request->has('productname') and
                $request->request->has('price')   and
                $request->request->has('types') and
                $request->request->has('image')
            ) {
                $response->setStatusCode(201);
                $ip = $request->getClientIp();
                $res = $session->get('sessionObj')->adminlogEvent($ip, 'createproduct', $request->cookies->get('PHPSESSID'));
                $productname = $session->get('sessionObj')->input_testing($request->request->get('productname'));
                $price = $session->get('sessionObj')->input_testing($request->request->get('price'));
                $types = $session->get('sessionObj')->input_testing($request->request->get('types'));
                $image = $session->get('sessionObj')->input_testing($request->request->get('image'));

                $res = $session->get('sessionObj')->addproduct(
                    $productname,
                    $price,
                    $types,
                    $image
                );
                if ($res === true) {
                    $response->setStatusCode(201);
                } elseif ($res === false) {
                    $response->setStatusCode(403);
                } elseif ($res === 0) {
                    $response->setStatusCode(500);
                }
            } else {
                $response->setStatusCode(400);
            }
        }
         elseif ($request->query->getAlpha('action') == 'orderproduct') {
            $res = $session->get('sessionObj')->orderproduct(
                $request->request->get('productID'),
                $request->request->get('productname'),
                $request->request->get('price'),
                $request->request->get('size'),
                $request->request->get('image')

            );
            if ($res === true) {
                $ip = $request->getClientIp();
                $res = $session->get('sessionObj')->logEvent($ip, 'order clothes', $request->cookies->get('PHPSESSID'));
                $response->setStatusCode(201);
            } elseif ($res === false) {
                $response->setStatusCode(403);
            } elseif ($res === 0) {
                $response->setStatusCode(500);
            }
        } elseif ($request->query->getAlpha('action') == 'orderotherproduct') {
            $res = $session->get('sessionObj')->orderotherproduct(
                $request->request->get('productID'),
                $request->request->get('productname'),
                $request->request->get('price'),
                $request->request->get('image')

            );
            if ($res === true) {
                $ip = $request->getClientIp();
                $res = $session->get('sessionObj')->logEvent($ip, 'order accessories', $request->cookies->get('PHPSESSID'));
                $response->setStatusCode(201);
            } elseif ($res === false) {
                $response->setStatusCode(403);
            } elseif ($res === 0) {
                $response->setStatusCode(500);
            }
        } elseif ($request->query->getAlpha('action') == 'deleteProduct') {
            $res = $session->get('sessionObj')->deleteProduct(
                $request->request->get('productID')
            );
            if ($res === true) {
                $ip = $request->getClientIp();
                $res = $session->get('sessionObj')->adminlogEvent($ip, 'delete product', $request->cookies->get('PHPSESSID'));
                $response->setStatusCode(201);
            } elseif ($res === false) {
                $response->setStatusCode(403);
            } elseif ($res === 0) {
                $response->setStatusCode(500);
            }
        }
        elseif ($request->query->getAlpha('action') == 'displaysingleproduct') {
            $res = $session->get('sessionObj')->displaysingleproduct( $request->request->get('productID'));
            $response->setContent(json_encode($res));
            $response->setStatusCode(200);
        }
        elseif ($request->query->getAlpha('action') == 'displaysingleorder') {
            $res = $session->get('sessionObj')->displaysingleorder( $request->request->get('orderID'));
            $response->setContent(json_encode($res));
            $response->setStatusCode(200);
        }
        elseif ($request->query->getAlpha('action') == 'displaysingleuser') {
            $res = $session->get('sessionObj')->displaysingleuser( $request->request->get('CustomerID'));
            $response->setContent(json_encode($res));
            $response->setStatusCode(200);
        }
        elseif ($request->query->getAlpha('action') == 'deleteOrder') {
            $res = $session->get('sessionObj')->deleteOrder(
                $request->request->get('orderID')
            );
           
            if ($res === true) {
                $ip = $request->getClientIp();
                $res = $session->get('sessionObj')->adminlogEvent($ip, 'delete orderID', $request->cookies->get('PHPSESSID'));
                $response->setStatusCode(201);
            } elseif ($res === false) {
                $response->setStatusCode(403);
            } elseif ($res === 0) {
                $response->setStatusCode(500);
            }
        }
         elseif ($request->query->getAlpha('action') == 'updateproduct') {
            if (
                $request->request->has('productID') and
                $request->request->has('productname') and
                $request->request->has('price')   and
                $request->request->has('types') and
                $request->request->has('image')
            ) {
                $productID = $session->get('sessionObj')->input_testing($request->request->get('productID'));
                $productname = $session->get('sessionObj')->input_testing($request->request->get('productname'));
                $price = $session->get('sessionObj')->input_testing($request->request->get('price'));
                $types = $session->get('sessionObj')->input_testing($request->request->get('types'));
                $image = $session->get('sessionObj')->input_testing($request->request->get('image'));
                $res = $session->get('sessionObj')->updateproduct(
                    $productID,
                    $productname,
                    $price,
                    $types,
                    $image
                );
                if ($res === true) {
                    $ip = $request->getClientIp();
                    $res = $session->get('sessionObj')->adminlogEvent($ip, 'update product', $request->cookies->get('PHPSESSID'));
                    $response->setStatusCode(201);
                } elseif ($res === false) {
                    $response->setStatusCode(403);
                } elseif ($res === 0) {
                    $response->setStatusCode(500);
                }
            }
        } elseif ($request->query->getAlpha('action') == 'createorder') {
            $res = $session->get('sessionObj')->isLoggedIn();
            if ($res == false) {
                $response->setStatusCode(403);
            } else {
                $res = $session->get('sessionObj')->createorder();
                if ($res === true) {
                    $response->setStatusCode(201);
                    $ip = $request->getClientIp();
                    $res = $session->get('sessionObj')->logEvent($ip, 'start order', $request->cookies->get('PHPSESSID'));
                } elseif ($res === false) {
                    $response->setStatusCode(403);
                } elseif ($res === 0) {
                    $response->setStatusCode(500);
                } else {
                    $response->setStatusCode(400);
                }
            }
        }  elseif ($request->query->getAlpha('action') == 'checkoutupdate') {
            $res = $session->get('sessionObj')->checkoutupdate($request->request->get('orderID'));
            if ($res === true) {
                $response->setStatusCode(201);
            } elseif ($res === false) {
                $response->setStatusCode(403);
            } elseif ($res === 0) {
                $response->setStatusCode(500);
            }
        }  //==========admin==================
        elseif ($request->query->getAlpha('action') == 'registeradmin') {
            if ($request->request->has('username')) {
                $res = $sqsdb->adminExists($request->request->get('username'));
                $ip_addr = $request->getClientIp();
                if ($res) {
                    $response->setStatusCode(418);
                } else {
                    if (
                        $request->request->has('username') and                
                        $request->request->has('password') 
                    ) {
                        $username = $session->get('sessionObj')->input_testing($request->request->getAlpha('username'));
                        $password = $session->get('sessionObj')->input_testing($request->request->get('password'));
                        $res = $session->get('sessionObj')->registeradmin(
                            $username,  $password ,$ip_addr
                        );
                        if ($res === true) {
                            $response->setStatusCode(201);
                        } elseif ($res === false) {
                            $response->setStatusCode(403);
                        } elseif ($res === 0) {
                            $response->setStatusCode(500);
                        }
                    }
                }
            } else {
                $response->setStatusCode(400);
            }
        } elseif ($request->query->getAlpha('action') == 'adminlogin') {
            $ip = $request->getClientIp();
        
            if ($request->request->has('username') and $request->request->has('password')) {
                $res = $session->get('sessionObj')->adminlogin(
                    $request->request->get('username'),
                    $request->request->get('password'),
                    $ip
                );
                if ($res === false) {
                    $response->setContent(json_encode($request->request));
                    $response->setStatusCode(201);
            
                } elseif (count($res) == 1) {
                    $response->setStatusCode(203);
                    $response->setContent(json_encode($res));
                } elseif (count($res) > 1) {
                 
                    $res = $session->get('sessionObj')->adminlogEvent($ip, 'admin login', $request->cookies->get('PHPSESSID'));
                    $response->setStatusCode(200);
                    $response->setContent(json_encode($res));
                }
            } else {
                $response->setContent(json_encode($request));
                $response->setStatusCode(404);
            }
        } elseif ($request->query->getAlpha('action') == 'adminupdate') {
            $res = $session->get('sessionObj')->isLoggedIn();
            if (($request->request->has('username')) && ($res != false)) {
                $res = $sqsdb->userExists($request->request->get('username'));
                if ($res) {
                    $response->setStatusCode(400);
                } else {
                    if (
                        $request->request->has('currentusername') and
                        $request->request->has('username') and
                        $request->request->has('email') and
                        $request->request->has('phone') and
                        $request->request->has('postcode') and
                        $request->request->has('password') and
                        $request->request->has('password2')
                    ) {
                        $username = $session->get('sessionObj')->input_testing($request->request->getAlpha('username'));
                        $email = $session->get('sessionObj')->input_testing($request->request->get('email'));
                        $phone = $session->get('sessionObj')->input_testing($request->request->get('phone'));
                        $postcode = $session->get('sessionObj')->input_testing($request->request->get('postcode'));
                        $password = $session->get('sessionObj')->input_testing($request->request->get('password'));
                        $res = $session->get('sessionObj')->adminupdate(
                            //    $res = $sqsdb->userid($request->request->get('currentusername')),
                            $username,
                            $email,
                            $phone,
                            $postcode,
                            $password,
                            $csrf
                        );
                        if ($res === true) {
                            $ip = $request->getClientIp();
                            $res = $session->get('sessionObj')->adminlogEvent($ip, 'edit profile', $request->cookies->get('PHPSESSID'));
                            $response->setStatusCode(201);
                        } elseif ($res === false) {
                            $response->setStatusCode(403);
                        } elseif ($res === 0) {
                            $response->setStatusCode(500);
                        }
                    }
                }
            }
        } elseif ($request->query->getAlpha('action') == 'adminadduser') {
            $res = $session->get('sessionObj')->adminisLoggedIn();
            if ($res == false) {
                $response->setStatusCode(400);
            } else {
                if ($request->request->has('username')) {
                    $res = $sqsdb->userExists($request->request->get('username'));
                    if ($res) {
                        $response->setStatusCode(418);
                    } else {
                        $username = $session->get('sessionObj')->input_testing($request->request->getAlpha('username'));
                        $email = $session->get('sessionObj')->input_testing($request->request->get('email'));
                        $phone = $session->get('sessionObj')->input_testing($request->request->get('phone'));
                        $postcode = $session->get('sessionObj')->input_testing($request->request->get('postcode'));
                        $password = $session->get('sessionObj')->input_testing($request->request->get('password'));
                        if (
                            empty($username)===false and
                            empty($email)=== false and
                            empty($phone)=== false and
                            empty($postcode) === false and
                            empty($password) === false
                        ) {
                          
                            $res = $session->get('sessionObj')->adduser(
                                $username,
                                $email,
                                $phone,
                                $postcode,
                                $password
                            );
                            if ($res === true) {
                                $ip = $request->getClientIp();
                                $res = $session->get('sessionObj')->adminlogEvent($ip, 'adduser', $request->cookies->get('PHPSESSID'));
                                $response->setStatusCode(201);
                            } elseif ($res === false) {
                                $response->setStatusCode(403);
                            } elseif ($res === 0) {
                                $response->setStatusCode(500);
                            }
                        }
                    }
                } else {
                    $response->setStatusCode(400);
                }
            }
        } elseif ($request->query->getAlpha('action') == 'displayuser') {
            $res = $session->get('sessionObj')->adminisLoggedIn();
            if ($res == false) {
                $response->setStatusCode(400);
            } else {
                $res = $session->get('sessionObj')->displayuser();
                $response->setContent(json_encode($res));
                $response->setStatusCode(200);
            }
        } elseif ($request->query->getAlpha('action') == 'deleteuser') {
            $res = $session->get('sessionObj')->adminisLoggedIn();
            if ($res === false) {
                $response->setStatusCode(400);
            } else {
                $res = $session->get('sessionObj')->deleteuser(
                    $request->request->get('CustomerID')
                );
                if ($res === true) {
                    $ip = $request->getClientIp();
                    $res = $session->get('sessionObj')->adminlogEvent($ip, 'delete user', $request->cookies->get('PHPSESSID'));
                    $response->setStatusCode(201);
                } elseif ($res === false) {
                    $response->setStatusCode(403);
                } elseif ($res === 0) {
                    $response->setStatusCode(500);
                }
            }
        } elseif ($request->query->getAlpha('action') == 'updateuser') {
            $res = $session->get('sessionObj')->adminisLoggedIn();
            if ($res === false) {
                $response->setStatusCode(400);
            } else {
                $CustomerID = $session->get('sessionObj')->input_testing($request->request->get('CustomerID'));
                $username = $session->get('sessionObj')->input_testing($request->request->getAlpha('username'));
                $email = $session->get('sessionObj')->input_testing($request->request->get('email'));
                $phone = $session->get('sessionObj')->input_testing($request->request->get('phone'));
                $postcode = $session->get('sessionObj')->input_testing($request->request->get('postcode'));
                $password = $session->get('sessionObj')->input_testing($request->request->get('password'));
                    if (
                        empty($CustomerID)=== false and
                        empty($username)=== false and
                        empty($email)=== false and
                        empty($phone)=== false and
                        empty($postcode)=== false and
                        empty($password) === false
                      
                    ) {
                      
                        $res = $session->get('sessionObj')->updateuser(
                            $CustomerID,
                            $username,
                            $email,
                            $phone,
                            $postcode,
                            $password
                            
                        );
                        if ($res === true) {
                            $response->setStatusCode(201);
                            $ip = $request->getClientIp();
                            $res = $session->get('sessionObj')->adminlogEvent($ip, 'update user', $request->cookies->get('PHPSESSID'));
                        } elseif ($res === false) {
                            $response->setStatusCode(403);
                        } elseif ($res === 0) {
                            $response->setStatusCode(500);
                        }
                    } else {
                        $response->setStatusCode(403);
                    }
                
            }
        }
        elseif ($request->query->getAlpha('action') == 'updateorder') {
            $res = $session->get('sessionObj')->adminisLoggedIn();
            if ($res === false) {
                $response->setStatusCode(400);
            } else {
                $CustomerID = $session->get('sessionObj')->input_testing($request->request->get('CustomerID'));
                $orderstatus = $session->get('sessionObj')->input_testing($request->request->get('orderstatus'));
                $orderID = $session->get('sessionObj')->input_testing($request->request->get('orderID'));
                $totalprice = $session->get('sessionObj')->input_testing($request->request->get('totalprice'));
                    if (
                        empty($CustomerID)=== false and
                        empty($orderstatus)=== false and
                        empty($orderID)=== false 
                    ) {
                      
                        $res = $session->get('sessionObj')->updateorder(
                            $orderID,
                            $orderstatus,
                            $CustomerID,
                            $totalprice
                            
                        );
                        if ($res === true) {
                            $response->setStatusCode(201);
                            $ip = $request->getClientIp();
                            $res = $session->get('sessionObj')->adminlogEvent($ip, 'update order', $request->cookies->get('PHPSESSID'));
                        } elseif ($res === false) {
                            $response->setStatusCode(403);
                        } elseif ($res === 0) {
                            $response->setStatusCode(500);
                        }
                    } else {
                        $response->setStatusCode(403);
                    }
                
            }
        }
        elseif ($request->query->getAlpha('action') == 'addorder') {
            $res = $session->get('sessionObj')->adminisLoggedIn();
            if ($res === false) {
                $response->setStatusCode(400);
            } else {
                $CustomerID = $session->get('sessionObj')->input_testing($request->request->get('CustomerID'));
                $orderstatus = $session->get('sessionObj')->input_testing($request->request->get('orderstatus'));
                $totalprice = $session->get('sessionObj')->input_testing($request->request->get('totalprice'));
                    if (
                        empty($CustomerID)=== false and
                        empty($orderstatus)=== false 
                       
                      
                      
                    ) {
                      
                        $res = $session->get('sessionObj')->addorder(
                            $orderstatus,
                            $CustomerID,
                            $totalprice
                            
                        );
                        if ($res === true) {
                            $response->setStatusCode(201);
                            $ip = $request->getClientIp();
                            $res = $session->get('sessionObj')->adminlogEvent($ip, 'add order', $request->cookies->get('PHPSESSID'));
                        } elseif ($res === false) {
                            $response->setStatusCode(403);
                        } elseif ($res === 0) {
                            $response->setStatusCode(500);
                        }
                    } else {
                        $response->setStatusCode(403);
                    }
                
            }
        }
        elseif ($request->query->getAlpha('action') == 'addorderitem') {
            $res = $session->get('sessionObj')->adminisLoggedIn();
            if ($res === false) {
                $response->setStatusCode(400);
            } else {
                $orderID = $session->get('sessionObj')->input_testing($request->request->get('orderID'));
                $ProductID = $session->get('sessionObj')->input_testing($request->request->get('ProductID'));
                $Size = $session->get('sessionObj')->input_testing($request->request->get('Size'));
                    if (
                        empty($orderID)=== false and
                        empty($ProductID)=== false             
                    ) {
                       
                        $res = $session->get('sessionObj')->addorderitem(
                            $ProductID,
                            $Size,
                            $orderID
                        );
                        $session->get('sessionObj')->adminsumtotalprice($orderID);
                        if ($res === true) {
                            $response->setStatusCode(201);
                            $ip = $request->getClientIp();
                            $res = $session->get('sessionObj')->adminlogEvent($ip, 'add order', $request->cookies->get('PHPSESSID'));
                        } elseif ($res === false) {
                            $response->setStatusCode(403);
                        } elseif ($res === 0) {
                            $response->setStatusCode(500);
                        }
                    } else {
                        $response->setStatusCode(403);
                    }
                
            }
        }
    
    }
    //if the request from the front-end JS is GET , the code will start the action which is in the GET Block
    if ($request->getMethod() == 'GET') {
        if ($request->query->getAlpha('action') == 'accountexists') {
            if ($request->query->has('username')) {
                $res = $sqsdb->userExists($request->query->get('username'));
                if ($res) {
                    $response->setStatusCode(400);
                } else {
                    $response->setStatusCode(204);
                }
            }
        } elseif ($request->query->getAlpha('action') == 'logout') {
            $ip = $request->getClientIp();
            $res = $session->get('sessionObj')->logEvent($ip, 'logout', $request->cookies->get('PHPSESSID'));
            $response->setStatusCode(200);
            $session->get('sessionObj')->logout();
        } elseif ($request->query->getAlpha('action') == 'adminlogout') {
            $ip = $request->getClientIp();
            $res = $session->get('sessionObj')->adminlogEvent($ip, 'adminlogout', $request->cookies->get('PHPSESSID'));
            $session->get('sessionObj')->adminlogout();
            $response->setStatusCode(200);
        } elseif ($request->query->getAlpha('action') == 'orderID') {
            $res = $session->get('sessionObj')->orderID();
        } elseif ($request->query->getAlpha('action') == 'sumtotalprice') {
            $ip = $request->getClientIp();
            $res = $session->get('sessionObj')->logEvent($ip, 'complete order', $request->cookies->get('PHPSESSID'));
            $res = $session->get('sessionObj')->sumtotalprice();
            if ($res === true) {
                $response->setStatusCode(201);
            } elseif ($res === false) {
                $response->setStatusCode(403);
            } elseif ($res === 0) {
                $response->setStatusCode(500);
            } else {
                $response->setStatusCode(418);
            }
        } elseif ($request->query->getAlpha('action') == 'showorderform') {
            $res = $session->get('sessionObj')->showorderform();
            $response->setContent(json_encode($res));
            $response->setStatusCode(200);
        } elseif ($request->query->getAlpha('action') == 'confirmorderform') {
            $res = $session->get('sessionObj')->confirmorderform();
            $response->setContent(json_encode($res));
            $response->setStatusCode(200);
        }
    }
    if ($request->getMethod() == 'DELETE') {           // delete queue, delete comment
        $response->setStatusCode(400);
    }
    if ($request->getMethod() == 'PUT') {              // enqueue, add comment
        $response->setStatusCode(400);
    }
} else {
    $redirect = new RedirectResponse($_SERVER['REQUEST_URI']);
}

// Do logging just before sending response?

$response->send();
