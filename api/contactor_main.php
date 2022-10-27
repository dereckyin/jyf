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

            $s_keyword = (isset($_GET['s_keyword']) ?  $_GET['s_keyword'] : "");
            $s_keyword = urldecode($s_keyword);

            $c_keyword = (isset($_GET['c_keyword']) ?  $_GET['c_keyword'] : "");
            $c_keyword = urldecode($c_keyword);

            $keyword = (isset($_GET['keyword']) ?  $_GET['keyword'] : "");
            $keyword = urldecode($keyword);


            
            if($keyword != "") {
                $sql = $sql . " and (shipping_mark like '%" . $keyword . "%' ";
                $sql = $sql . " or customer like '%" . $keyword . "%' ";
                $sql = $sql . " or c_fax like '%" . $keyword . "%' ";
                $sql = $sql . " or c_email like '%" . $keyword . "%' ";
                $sql = $sql . " or c_phone like '%" . $keyword . "%' ";
                $sql = $sql . " or s_phone like '%" . $keyword . "%' ";
                $sql = $sql . " or s_fax like '%" . $keyword . "%' ";
                $sql = $sql . " or s_email like '%" . $keyword . "%' ";
                $sql = $sql . " or company_title like '%" . $keyword . "%' ";
                $sql = $sql . " or vat_number like '%" . $keyword . "%' ";
                $sql = $sql . " or address like '%" . $keyword . "%' ";
                $sql = $sql . " or supplier like '%" . $keyword . "%' )";
            }

            if($s_keyword != "") {
                $sql = "select * from (SELECT shipping_mark, supplier, COALESCE(s_phone, '') s_phone, COALESCE(s_fax, '') s_fax, s_email FROM contactor where status = '' ".($id ? " and id=$id" : ''); 

                //$sql = $sql . " and (shipping_mark like '%" . $s_keyword . "%' ";
                $sql = $sql . "  and ( company_title like '%" . $s_keyword . "%' ";
                $sql = $sql . " or s_phone like '%" . $s_keyword . "%' ";
                $sql = $sql . " or s_fax like '%" . $s_keyword . "%' ";
                //$sql = $sql . " or vat_number like '%" . $s_keyword . "%' ";
                //$sql = $sql . " or address like '%" . $s_keyword . "%' ";
                $sql = $sql . " or supplier like '%" . $s_keyword . "%' )";

                $sql = $sql . " UNION select distinct '' shipping_mark ,  supplier , '' s_phone , '' s_fax , '' c_email  from receive_record where status = '' and supplier like '%$s_keyword%') a ";
                $sql = $sql . " ORDER BY a.supplier ";
            }

            if($c_keyword != "") {
                $sql = "select * from (SELECT  shipping_mark, customer, COALESCE(c_phone, '') c_phone, COALESCE(c_fax, '') c_fax, c_email FROM contactor where status = '' ".($id ? " and id=$id" : ''); 

                $sql = $sql . " and (shipping_mark like '%" . $c_keyword . "%' ";
                $sql = $sql . " or customer like '%" . $c_keyword . "%' ";
                $sql = $sql . " or c_fax like '%" . $c_keyword . "%' ";
                $sql = $sql . " or c_email like '%" . $c_keyword . "%' ";
                $sql = $sql . " or c_phone like '%" . $c_keyword . "%' )";
                //$sql = $sql . " or company_title like '%" . $c_keyword . "%' ";
                //$sql = $sql . " or vat_number like '%" . $c_keyword . "%' ";
                //$sql = $sql . " or address like '%" . $c_keyword . "%' )";

                $sql = $sql . " UNION select distinct '' shipping_mark ,  customer, '' c_phone, '' c_fax , '' c_email  from receive_record where status = '' and customer like '%$c_keyword%') a ";
                $sql = $sql . " ORDER BY a.customer ";
            }

            if(!empty($_GET['page'])) {
                $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
                if(false === $page) {
                    $page = 1;
                }
            }

            // $sql = $sql . " ORDER BY a.customer ";

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
            $shipping_mark = stripslashes($_POST["shipping_mark"]);
            $customer = stripslashes($_POST["customer"]);
            $c_phone = stripslashes($_POST["c_phone"]);
            $c_fax = stripslashes($_POST["c_fax"]);
            $c_email = stripslashes($_POST["c_email"]);
            $supplier = stripslashes($_POST["supplier"]);
            $s_phone = stripslashes($_POST["s_phone"]);
            $s_fax = stripslashes($_POST["s_fax"]);
            $s_email = stripslashes($_POST["s_email"]);
            $company_title = stripslashes($_POST["company_title"]);
            $vat_number = stripslashes($_POST["vat_number"]);
            $address = stripslashes($_POST["address"]);

            $crud = stripslashes($_POST["crud"]);
            $id = stripslashes($_POST["id"]);

            switch ($crud) 
            {
              case 'insert':
            /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
                $sql = "insert into contactor (shipping_mark, 
                                                      customer, 
                                                      c_phone, 
                                                      c_fax, 
                                                      c_email,
                                                      supplier,
                                                      s_phone,
                                                      s_fax,
                                                      s_email,
                                                      company_title,
                                                      vat_number,
                                                      address,
                                                      crt_user) 
                                            values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql); 
                $stmt->bind_param("sssssssssssss",
                                    $shipping_mark, 
                                    $customer, 
                                    $c_phone, 
                                    $c_fax, 
                                    $c_email, 
                                    $supplier,
                                    $s_phone,
                                    $s_fax, 
                                    $s_email, 
                                    $company_title, 
                                    $vat_number, 
                                    $address, 
                                    $user);
                $stmt->execute();
                $stmt->close();

                $last_id = mysqli_insert_id($conn);

                echo $last_id;

                break;

            case "update":
                
                    /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
                    $sql = "update contactor set shipping_mark = ?, 
                                                          customer = ?, 
                                                          c_phone = ?, 
                                                          c_fax = ?, 
                                                          c_email = ?,
                                                          supplier = ?,
                                                          s_phone = ?,
                                                          s_fax = ?,
                                                          s_email = ?,
                                                          company_title = ?,
                                                          vat_number = ?,
                                                          address = ?,
                                                          mdf_time = now(),
                                                          mdf_user = ?
                                                where id = ?";
                    $stmt = $conn->prepare($sql); 
                    $stmt->bind_param("sssssssssssssd",
                                        $shipping_mark, 
                                        $customer, 
                                        $c_phone, 
                                        $c_fax, 
                                        $c_email, 
                                        $supplier,
                                        $s_phone,
                                        $s_fax, 
                                        $s_email, 
                                        $company_title, 
                                        $vat_number, 
                                        $address, 
                                        $user,
                                        $id);
                    $stmt->execute();
                    $affected_rows = mysqli_stmt_num_rows($stmt);
                    $stmt->close();
               
                

                echo $affected_rows;

                break;

              case 'del':
                $sql = "update contactor set status = 'D', del_time = now(), del_user = '$user' where id in ($id)";
                
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
