<?php

class sqsuser
{

    private $dbconn;

    public function __construct()
    {
        //here to connect the database in the computer
        $dbURI = 'mysql:host=' . 'localhost' . ';port=3307;dbname=' . 'clothes';
        $this->dbconn = new PDO($dbURI, 'root', '');
        $this->dbconn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // $cleardb_url = parse_url(getenv("mysql://b48b00a18bfa2e:1964f939@us-cdbr-east-04.cleardb.com/heroku_be04d0b5c2244ab?reconnect=true"));
        //$cleardb_server = $cleardb_url["us-cdbr-east-04.cleardb.com"];
        //$cleardb_username = $cleardb_url["b48b00a18bfa2e"];
        //$cleardb_password = $cleardb_url["1964f939"];
        //$cleardb_db = substr($cleardb_url["heroku_be04d0b5c2244ab"],1);
        //$active_group = 'default';
        //$query_builder = TRUE;
        // Connect to DB


    }

    function daylimit()
    {
    }


    function checkLogin($u, $p)
    {
        // Return uid if user/password tendered are correct otherwise 0
        $sql = "SELECT * FROM customer WHERE username = :username";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':username', $u, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $retVal = $stmt->fetch(PDO::FETCH_ASSOC);
            if (strlen($retVal['password']) > 0) {
                if ($retVal['password'] == MD5($p) && $retVal['usertype'] == 'user') { // encrypt & decrypt
                    return array(
                        'CustomerID' => $retVal['CustomerID'],
                        'username' => $retVal['username'],
                        'email' => $retVal['email'],
                        'phone' => $retVal['phone'],
                        'postcode' => $retVal['postcode']
                    );
                } else {
                    return false;
                }
            } else {
                return array('username' => $retVal['username']);
            }
        } else {
            return false;
        }
    }
    function userExists($u)
    {
        $sql = "SELECT * FROM customer WHERE username = :username";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':username', $u, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
    function userid($c)
    {
        $sql = "SELECT CustomerID FROM customer WHERE username = :username";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':username', $c, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll();
        return $result;
    }
    function registerUser($username, $email, $phone, $postcode, $password)
    {
        // Retister user into system, assume validation has happened.
        // return UID created or false if fail
        //            $sql = "UPDATE customer SET Username = :Username, Pass = :Pass, Email = :Email, Phone = :Phone=1 WHERE CustomerID = :CustomerID";

        //            $lastCustID = $this->dbconn->lastInsertID();

        //            $sql = "INSERT INTO customer(CustomerID,Username,Pass,Email,Phone)  VALUES (:CustomerID,:Username,:Pass,:Email, :Phone)";
        $sql = "INSERT INTO customer (username,email,phone,postcode,password,usertype)  VALUES (:username,:email, :phone,:postcode,MD5(:password),'user');";
        $stmt = $this->dbconn->prepare($sql);
        //            $stmt->bindParam(':CustomerID', $lastCustID, PDO::PARAM_INT);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_INT);
        $stmt->bindParam(':postcode', $postcode, PDO::PARAM_INT);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);

        $result = $stmt->execute();
        if ($result === true) {
            return true;
        } else {
            return false;
        }
    }

    function updateprofile($CustomerID, $username, $email, $phone, $postcode, $password)
    {
        // Retister user into system, assume validation has happened.
        // return UID created or false if fail
        //            $sql = "UPDATE customer SET Username = :Username, Pass = :Pass, Email = :Email, Phone = :Phone=1 WHERE CustomerID = :CustomerID";

        //            $lastCustID = $this->dbconn->lastInsertID();

        //            $sql = "INSERT INTO customer(CustomerID,Username,Pass,Email,Phone)  VALUES (:CustomerID,:Username,:Pass,:Email, :Phone)";
        // $currentuserid = "SELECT CustomerID FROM customer WHERE username = '$username'";
        $sql = "UPDATE customer SET username = :username,password = MD5(:password), email = :email, phone = :phone, postcode = :postcode WHERE CustomerID = :CustomerID";
        $stmt = $this->dbconn->prepare($sql);
        //            $stmt->bindParam(':CustomerID', $lastCustID, PDO::PARAM_INT);
        $stmt->bindParam(':CustomerID', $CustomerID, PDO::PARAM_INT);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_INT);
        $stmt->bindParam(':postcode', $postcode, PDO::PARAM_INT);
        $result = $stmt->execute();
        if ($result === true) {
            return true;
        } else {
            return false;
        }
    }
    function logevent($CustomerID, $ip_addr, $action, $PHPSESSID)
    {
        $sql = "INSERT INTO logtable (CustomerID ,ip_addr, action ,usertype,PHPSESSID) 
                VALUES (:CustomerID,:ip_addr,:action,'user',:PHPSESSID);";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':CustomerID', $CustomerID, PDO::PARAM_INT);
        $stmt->bindParam(':ip_addr',  $ip_addr, PDO::PARAM_STR);
        $stmt->bindParam(':action', $action, PDO::PARAM_STR);
        $stmt->bindParam(':PHPSESSID', $PHPSESSID, PDO::PARAM_STR);
        $result = $stmt->execute();
        if ($result === true) {
            return true;
        } else {
            return false;
        }
    }
    function mendisplayproduct()
    {
        $sql = "SELECT * FROM products WHERE types='men'";
        $stmt = $this->dbconn->prepare($sql);
        $result = $stmt->execute();
        $row = $stmt->fetchAll();
        return  $row;
    }
    function womendisplayproduct()
    {
        $sql = "SELECT * FROM products WHERE types='women'";
        $stmt = $this->dbconn->prepare($sql);
        $result = $stmt->execute();
        $row = $stmt->fetchAll();
        return  $row;
    }
    function otherdisplayproduct()
    {
        $sql = "SELECT * FROM products WHERE types='other'";
        $stmt = $this->dbconn->prepare($sql);
        $result = $stmt->execute();
        $row = $stmt->fetchAll();
        return  $row;
    }
    function sumtotalpriceff($CustomerID)
    {
        $sql = "UPDATE orderform SET totalprice = (SELECT SUM(price) FROM orderitem 
        WHERE orderID = (SELECT max(orderID) orderID FROM (SELECT max(orderID) orderID FROM orderform 
        WHERE CustomerID =:CustomerID) AS T )) WHERE orderID= (SELECT max(orderID) orderID FROM (SELECT max(orderID) orderID FROM orderform 
        WHERE CustomerID =:CustomerID ) AS T ) ;";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':CustomerID', $CustomerID, PDO::PARAM_INT);
        $result = $stmt->execute();
        if ($result === true) {
            return true;
        } else {
            return false;
        }
    }



    function displayshoworderform($CustomerID)
    {
        $sql = "SELECT * FROM orderitem where orderID=(SELECT max(orderID) orderID FROM orderform where CustomerID= :CustomerID );";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':CustomerID', $CustomerID, PDO::PARAM_INT);
        $result = $stmt->execute();
        $row = $stmt->fetchAll();
        return $row;
    }
    function deleteorderfood($orderitem_ID)
    {
        $sql = "DELETE FROM orderitem where orderitem_ID = :orderitem_ID;";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':orderitem_ID', $orderitem_ID, PDO::PARAM_INT);
        $result = $stmt->execute();
        if ($result === true) {
            return true;
        } else {
            return false;
        }
    }
    function deleteorder($orderID)
    {
        $sql = "DELETE FROM orderitem where orderID = :orderID;";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':orderID', $orderID, PDO::PARAM_INT);
        $result = $stmt->execute();
        if ($result === true) {
            return true;
        } else {
            return false;
        }
    }
    function deleteoorder($orderID)
    {
        $sql = "DELETE FROM orderform where orderID = :orderID;";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':orderID', $orderID, PDO::PARAM_INT);
        $result = $stmt->execute();
        if ($result === true) {
            return true;
        } else {
            return false;
        }
    }

    function getorderID($CustomerID)
    {
        $sql = "SELECT max(orderID)  orderID FROM orderform where CustomerID=:CustomerID ";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':CustomerID', $CustomerID, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll();
        exit(json_encode($result));
    }
    function displayProduct()
    {
        $sql = "SELECT * FROM products";
        $stmt = $this->dbconn->prepare($sql);
        $result = $stmt->execute();
        $row = $stmt->fetchAll();
        return  $row;
    }

    function createorderform($CustomerID)
    {
        $sql = "INSERT INTO orderform (orderstatus,CustomerID,totalprice)  VALUES ('Notpayed',:CustomerID,'0');";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':CustomerID', $CustomerID, PDO::PARAM_INT);
        $result = $stmt->execute();
        if ($result === true) {
            return true;
        } else {
            return false;
        }
    }

    function orderquantityfood($productID, $productname, $price, $quantity, $totalprice, $CustomerID)
    {

        $sql = "INSERT INTO orderitem (productID,productname,price,quantity,totalprice,orderID)  VALUES (:productID,:productname,:price,:quantity,:totalprice,(SELECT max(orderID) orderID FROM orderform where CustomerID= :CustomerID ));";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':productID', $productID, PDO::PARAM_INT);
        $stmt->bindParam(':productname', $productname, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':totalprice', $totalprice, PDO::PARAM_INT);
        $stmt->bindParam(':CustomerID', $CustomerID, PDO::PARAM_INT);
        $result = $stmt->execute();
        if ($result === true) {
            return true;
        } else {
            return false;
        }
    }
    function getconfirmorderform($CustomerID)
    {
        $sql = "SELECT * FROM orderform where orderID=(SELECT max(orderID) FROM orderform where CustomerID=:CustomerID)";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':CustomerID', $CustomerID, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->execute();
        $row = $stmt->fetchAll();
        return  $row;
    }
    function checkoutff($CustomerID, $cname, $ccnum, $expmonth, $expyear, $cvv)
    {

        $sql = "INSERT INTO payment (CustomerID,cname,ccnum,expmonth,expyear,cvv)  VALUES (:CustomerID,:cname,:ccnum,:expmonth,:expyear,:cvv);";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':CustomerID', $CustomerID, PDO::PARAM_INT);
        $stmt->bindParam(':cname', $cname, PDO::PARAM_STR);
        $stmt->bindParam(':ccnum', $ccnum, PDO::PARAM_INT);
        $stmt->bindParam(':expmonth', $expmonth, PDO::PARAM_STR);
        $stmt->bindParam(':expyear', $expyear, PDO::PARAM_INT);
        $stmt->bindParam(':cvv', $cvv, PDO::PARAM_INT);
        $result = $stmt->execute();
        if ($result === true) {
            return true;
        } else {
            return false;
        }
    }


    function checkoutupdateff($CustomerID)
    {
        $sql = "UPDATE orderform SET orderform.orderstatus = 'completepayment' WHERE orderform.orderID= 
        (SELECT max(orderID) orderID FROM (SELECT max(orderID) orderID FROM orderform WHERE CustomerID =:CustomerID) AS T );";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':CustomerID', $CustomerID, PDO::PARAM_INT);
        $result = $stmt->execute();
        if ($result === true) {
            return true;
        } else {
            return false;
        }
    }

    //======================admin panel==========================

    function admincheckLogin($u, $p)
    {
        // Return uid if user/password tendered are correct otherwise 0
        $sql = "SELECT * FROM customer WHERE username = :username";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':username', $u, PDO::PARAM_STR);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            $retVal = $stmt->fetch(PDO::FETCH_ASSOC);
            if (strlen($retVal['password']) > 0) {
                //only usertype is admin can login admin panel
                if ($retVal['password'] == MD5($p) && $retVal['usertype'] == 'admin') {
                    return array(
                        'CustomerID' => $retVal['CustomerID'],
                        'username' => $retVal['username'],
                        'email' => $retVal['email'],
                        'phone' => $retVal['phone'],
                        'postcode' => $retVal['postcode'],
                        'usertype' => $retVal['usertype']
                    );
                } else {
                    return false;
                }
            } else {
                return array('username' => $retVal['username']);
            }
        } else {
            return false;
        }
    }

    function registerUseradmin($username, $email, $phone, $postcode, $password)
    {

        $sql = "INSERT INTO customer (username,email,phone,postcode,password,usertype)  
    VALUES (:username,:email, :phone,:postcode,MD5(:password),'admin');";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_INT);
        $stmt->bindParam(':postcode', $postcode, PDO::PARAM_INT);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $result = $stmt->execute();
        if ($result === true) {
            return true;
        } else {
            return false;
        }
    }

    function adminlogevent($CustomerID, $ip_addr, $action, $PHPSESSID)
    {
        $sql = "INSERT INTO logtable (CustomerID ,ip_addr, action ,usertype,PHPSESSID) 
    VALUES (:CustomerID,MD5(:ip_addr),:action,'admin',MD5(:PHPSESSID));";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':CustomerID', $CustomerID, PDO::PARAM_INT);
        $stmt->bindParam(':ip_addr',  $ip_addr, PDO::PARAM_INT);
        $stmt->bindParam(':action', $action, PDO::PARAM_STR);
        $stmt->bindParam(':PHPSESSID', $PHPSESSID, PDO::PARAM_STR);
        $result = $stmt->execute();
        if ($result === true) {
            return true;
        } else {
            return false;
        }
    }
    //============================ Control user function===================================
    //WHERE usertype ='user'
    function userdisplay()
    {
        $sql = "SELECT * FROM customer";
        $stmt = $this->dbconn->prepare($sql);
        $result = $stmt->execute();
        $row = $stmt->fetchAll();
        return  $row;
    }
    function displayOrder()
    {
        $sql = "SELECT * FROM orderform";
        $stmt = $this->dbconn->prepare($sql);
        $result = $stmt->execute();
        $row = $stmt->fetchAll();
        return  $row;
    }
    function displayorderContent()
    {
        $sql = "SELECT * FROM orderitem";
        $stmt = $this->dbconn->prepare($sql);
        $result = $stmt->execute();
        $row = $stmt->fetchAll();
        return  $row;
    }
    function useradd($username, $email, $phone, $postcode, $password, $usertype)
    {
        $sql = "INSERT INTO customer (username,email,phone,postcode,password,usertype)  VALUES (:username,:email, :phone,:postcode,MD5(:password),:usertype);";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_INT);
        $stmt->bindParam(':postcode', $postcode, PDO::PARAM_INT);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':usertype', $usertype, PDO::PARAM_STR);
        $result = $stmt->execute();
        if ($result === true) {
            return true;
        } else {
            return false;
        }
    }
    function userdelete($CustomerID)
    {
        $sql = "DELETE FROM customer where CustomerID = :CustomerID;";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':CustomerID', $CustomerID, PDO::PARAM_INT);
        $result = $stmt->execute();
        if ($result === true) {
            return true;
        } else {
            return false;
        }
    }
    function userupdate($CustomerID, $username, $email, $phone, $postcode, $password, $usertype)
    {
        $sql = "UPDATE customer SET username = :username,password = MD5(:password) , email = :email, phone = :phone, postcode = :postcode ,usertype=:usertype WHERE CustomerID = :CustomerID";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':CustomerID', $CustomerID, PDO::PARAM_INT);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $password, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_INT);
        $stmt->bindParam(':postcode', $postcode, PDO::PARAM_INT);
        $stmt->bindParam(':usertype', $usertype, PDO::PARAM_STR);
        $result = $stmt->execute();
        if ($result === true) {
            return true;
        } else {
            return false;
        }
    }


    //===================food control===============
    function addproductditem($productname, $price, $types, $image)
    {

        $sql = "INSERT INTO products (productname,price,types,image)  VALUES (:productname,:price,:types,:image);";
        $stmt = $this->dbconn->prepare($sql);
        //  $stmt->bindParam(':productID', $productID, PDO::PARAM_INT);   
        $stmt->bindParam(':productname', $productname, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_INT);
        $stmt->bindParam(':types', $types, PDO::PARAM_STR);
        $stmt->bindParam(':image', $image, PDO::PARAM_STR);
        $result = $stmt->execute();
        if ($result === true) {
            return true;
        } else {
            return false;
        }
    }
    function deleteproduct($productID)
    {
        $sql = "DELETE FROM products where productID = :productID;";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':productID', $productID, PDO::PARAM_INT);
        $result = $stmt->execute();
        if ($result === true) {
            return true;
        } else {
            return false;
        }
    }
    function displaysingleProduct($productID)
    {
        $sql = "SELECT * FROM products where productID = :productID;";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':productID', $productID, PDO::PARAM_INT);
        $result = $stmt->execute();
        $row = $stmt->fetchAll();
        return  $row;
    }
    function orderProduct($productID, $productname, $price, $size, $CustomerID, $image)
    {
        $sql = "INSERT INTO orderitem (productID,productname,price,size,orderID,image)  VALUES (:productID,:productname,:price,:size,(SELECT max(orderID) orderID FROM orderform where CustomerID= :CustomerID ),:image);";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':productID', $productID, PDO::PARAM_INT);
        $stmt->bindParam(':size', $size, PDO::PARAM_STR);
        $stmt->bindParam(':productname', $productname, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_INT);
        $stmt->bindParam(':CustomerID', $CustomerID, PDO::PARAM_INT);
        $stmt->bindParam(':image', $image, PDO::PARAM_STR);
        $result = $stmt->execute();
        if ($result === true) {
            return true;
        } else {
            return false;
        }
    }
    function orderotherProduct($productID, $productname, $price, $CustomerID, $image)
    {
        $sql = "INSERT INTO orderitem (productID,productname,price,orderID,image)  VALUES (:productID,:productname,:price,(SELECT max(orderID) orderID FROM orderform where CustomerID= :CustomerID ),:image);";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':productID', $productID, PDO::PARAM_INT);
        $stmt->bindParam(':productname', $productname, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_INT);
        $stmt->bindParam(':CustomerID', $CustomerID, PDO::PARAM_INT);
        $stmt->bindParam(':image', $image, PDO::PARAM_STR);
        $result = $stmt->execute();
        if ($result === true) {
            return true;
        } else {
            return false;
        }
    }
    function updateproductitem($productID, $productname, $price, $types, $image)
    {

        $sql = "UPDATE products SET productname = :productname,price = :price , types = :types, image = :image WHERE productID = :productID";
        $stmt = $this->dbconn->prepare($sql);
        $stmt->bindParam(':productID', $productID, PDO::PARAM_INT);
        $stmt->bindParam(':productname', $productname, PDO::PARAM_STR);
        $stmt->bindParam(':price', $price, PDO::PARAM_INT);
        $stmt->bindParam(':types', $types, PDO::PARAM_STR);
        $stmt->bindParam(':image', $image, PDO::PARAM_STR);
        $result = $stmt->execute();
        if ($result === true) {
            return true;
        } else {
            return false;
        }
    }



    public function uploadimg($pdo, $image_path)
    {
        $hashed_password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $query = "INSERT INTO users`(userID`, email, password,  userpic) VALUES (:UI,:EM,:PW,:UP)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':UI', $_POST['userID']);
        $stmt->bindParam(':EM', $_POST['email']);
        $stmt->bindParam(':PW', $hashed_password);
        $stmt->bindParam(':UP', $image_path);
        $stmt->execute();
        http_response_code(201);
        echo "register passed";
    }
}
