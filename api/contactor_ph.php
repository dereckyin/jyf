<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
if ( !isset( $jwt ) ) {
    http_response_code(401);
 
    echo json_encode(array("message" => "Access denied."));
    die();
}
else
{
  try {
          // decode jwt
          $decoded = JWT::decode($jwt, $key, array('HS256'));
   
      }
      // if decode fails, it means jwt is invalid
      catch (Exception $e){
      
          http_response_code(401);
     
        echo json_encode(array("message" => "Access denied."));
        die();
      }
}

      header('Access-Control-Allow-Origin: *');  

      require_once "db.php";

      $method = $_SERVER['REQUEST_METHOD'];

      $user = $decoded->data->username;

      switch ($method) {
          case 'GET':
            $id = stripslashes((isset($_GET['id']) ?  $_GET['id'] : ""));
            $page = stripslashes((isset($_GET['page']) ?  $_GET['page'] : ""));
            $size = stripslashes((isset($_GET['size']) ?  $_GET['size'] : ""));

            $keyword = (isset($_GET['keyword']) ?  $_GET['keyword'] : "");
            $keyword = urldecode($keyword);


            $sql = "SELECT 0 as is_checked, id, company, customer, address, COALESCE(phone, '') phone, COALESCE(fax, '') fax, COALESCE(mobile, '') mobile, email, remark, crt_time, crt_user  FROM contactor_ph where `status` = '' ".($id ? " and id=$id" : ''); 

            if($keyword != "") {
                $sql = $sql . " and (company like '%" . $keyword . "%' ";
                $sql = $sql . " or customer like '%" . $keyword . "%' ";
                $sql = $sql . " or phone like '%" . $keyword . "%' ";
                $sql = $sql . " or address like '%" . $keyword . "%' ";
                $sql = $sql . " or fax like '%" . $keyword . "%' ";
                $sql = $sql . " or mobile like '%" . $keyword . "%' ";
                $sql = $sql . " or email like '%" . $keyword . "%' ";
                $sql = $sql . " or remark like '%" . $keyword . "%' )";
            }


            if(!empty($_GET['page'])) {
                $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
                if(false === $page) {
                    $page = 1;
                }
            }

            $sql = $sql . " ORDER BY customer ";

            if(!empty($_GET['size'])) {
                $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
                if(false === $size) {
                    $size = 10;
                }

                $offset = ($page - 1) * $size;

                $sql = $sql . " LIMIT " . $offset . "," . $size;
            }

            // run SQL statement
            $result = mysqli_query($conn,$sql);

            // die if SQL statement failed
            if (!$result) {
                  http_response_code(404);
                  die(mysqli_error($conn));
            }

            if (!$id) echo '[';
            for ($i=0 ; $i<mysqli_num_rows($result) ; $i++) {
                  echo ($i>0?',':''). json_encode(mysqli_fetch_object($result), ENT_QUOTES);
            }
            if (!$id) echo ']';
            elseif ($method == 'POST')
                  echo json_encode($result);
            else
                  echo mysqli_affected_rows($conn);
            break;

        case 'POST':
            $company = stripslashes($_POST["company"]);
            $customer = stripslashes($_POST["customer"]);
            $address = stripslashes($_POST["address"]);
            $phone = stripslashes($_POST["phone"]);
            $fax = stripslashes($_POST["fax"]);
            $mobile = stripslashes($_POST["mobile"]);
            $email = stripslashes($_POST["email"]);
            $remark = stripslashes($_POST["remark"]);
        
            $crud = stripslashes($_POST["crud"]);
            $id = stripslashes($_POST["id"]);

            switch ($crud) 
            {
              case 'insert':
            /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
                $sql = "insert into contactor_ph (company, 
                customer, 
                address, 
                phone, 
                fax,
                mobile,
                email,
                remark,
                                                      crt_user) 
                                            values (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql); 
                $stmt->bind_param("sssssssss",
                                    $company, 
                                    $customer, 
                                    $address, 
                                    $phone, 
                                    $fax, 
                                    $mobile,
                                    $email,
                                    $remark,  
                                    $user);
                $stmt->execute();
                $stmt->close();

                $last_id = mysqli_insert_id($conn);

                echo $last_id;

                break;

            case "update":
                
                    /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
                    $sql = "update contactor_ph set company = ?, 
                                                          customer = ?, 
                                                          address = ?, 
                                                          phone = ?, 
                                                          fax = ?,
                                                          mobile = ?,
                                                          email = ?,
                                                          remark = ?,
                                                    
                                                          mdf_time = now(),
                                                          mdf_user = ?
                                                where id = ?";
                    $stmt = $conn->prepare($sql); 
                    $stmt->bind_param("sssssssssd",
                                        $company, 
                                        $customer, 
                                        $address, 
                                        $phone, 
                                        $fax, 
                                        $mobile,
                                        $email,
                                        $remark, 
                                        $user,
                                        $id);
                    $stmt->execute();
                    $affected_rows = mysqli_stmt_num_rows($stmt);
                    $stmt->close();
               
                

                echo $affected_rows;

                break;

              case 'del':
                $sql = "update contactor_ph set status = 'D', del_time = now(), del_user = '$user' where id in ($id)";
                
                $query = $conn->query($sql);
     
                if($query){
                    $out['message'] = "Member Deleted Successfully";
                }
                else{
                    $out['error'] = true;
                    $out['message'] = "Could not delete Member";
                }
               
                break;
            }

            break;
      }

      // Close connection
      mysqli_close($conn);


?>
