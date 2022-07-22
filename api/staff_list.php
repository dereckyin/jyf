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


            $sql = "SELECT 0 as is_checked, id, `staff`, phone, email, `address`, punch, crt_time, crt_user  FROM staff_list where status <> 'D' ".($id ? " and id=$id" : ''); 

            if($keyword != "") {
                $sql = $sql . " and (`staff` like '%" . $keyword . "%' ";
                $sql = $sql . " or phone like '%" . $keyword . "%' ";
                $sql = $sql . " or email like '%" . $keyword . "%' ";
                $sql = $sql . " or address like '%" . $keyword . "%' )";
            }

            if(!empty($_GET['page'])) {
                $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
                if(false === $page) {
                    $page = 1;
                }
            }

            $sql = $sql . " ORDER BY staff ";

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
            $staff = (isset($_POST['staff']) ?  $_POST['staff'] : '');
            $phone = (isset($_POST['phone']) ?  $_POST['phone'] : '');
            $email = (isset($_POST['email']) ?  $_POST['email'] : '');
            $address = (isset($_POST['address']) ?  $_POST['address'] : '');
            $punch = (isset($_POST['punch']) ?  $_POST['punch'] : 0);

            $crud = stripslashes($_POST["crud"]);
            $id = stripslashes($_POST["id"]);

            switch ($crud) 
            {
              case 'insert':
            /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
                $sql = "insert into staff_list (staff, 
                                                phone, 
                                                email, 
                                                address,
                                                punch,
                                                crt_user) 
                                            values (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql); 
                $stmt->bind_param("ssssds",
                                    $staff, 
                                    $phone, 
                                    $email, 
                                    $address, 
                                    $punch, 
                                    $user);
                $stmt->execute();
                $stmt->close();

                $last_id = mysqli_insert_id($conn);

                echo $last_id;

                break;

            case "update":
                
                    /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
                    $sql = "update staff_list set staff = ?, 
                                                    phone = ?, 
                                                    email = ?, 
                                                    address = ?, 
                                                    punch = ?,
                                                    mdf_time = now(),
                                                    mdf_user = ?
                                                where id = ?";
                    $stmt = $conn->prepare($sql); 
                    $stmt->bind_param("ssssdsd",
                                        $staff, 
                                        $phone, 
                                        $email, 
                                        $address, 
                                        $punch, 
                                        $mdf_user, 
                                        $id);
                    $stmt->execute();
                    $affected_rows = mysqli_stmt_num_rows($stmt);
                    $stmt->close();

                echo $affected_rows;

                break;

              case 'del':
                $sql = "update staff_list set status = 'D', del_time = now(), del_user = '$user' where id in ($id)";
                
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
