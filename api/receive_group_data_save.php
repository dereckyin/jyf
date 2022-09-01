<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'config/conf.php';
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


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

require_once "db.php";


      header('Access-Control-Allow-Origin: *');  

      $method = $_SERVER['REQUEST_METHOD'];

    $user = $decoded->data->username;
    $user_id = $decoded->data->id;

      switch ($method) {
       
        case 'POST':
            $group_ids = isset($_POST['group_ids']) ? $_POST['group_ids'] : "";
            $group1_ids = isset($_POST['group1_ids']) ? $_POST['group1_ids'] : "";
            $group2_ids = isset($_POST['group2_ids']) ? $_POST['group2_ids'] : "";
            $group_name = isset($_POST['group_name']) ? $_POST['group_name'] : "";

            if($group_name == '1')
            {
                if($group1_ids != "")
                {
                    $sql = "update receive_record set flag = ''  where id in ($group1_ids)";
                
                    $query = $conn->query($sql);
            
                    if($query){
                        http_response_code(200);
                        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
                    }
                    else{
                        http_response_code(501);
                        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Error uploading, Please use laptop to upload again."));
                        die();
                    }
                }
                
                if($group_ids != "")
                {
                    $sql = "update receive_record set flag = '1'  where id in ($group_ids)";

                    $query = $conn->query($sql);

                    if($query){
                        http_response_code(200);
                        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
                    }
                    else{
                        http_response_code(501);
                        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Error uploading, Please use laptop to upload again."));
                        die();
                    }
                }

                
            }

            if($group_name == '2')
            {
                
                if($group2_ids != '')
                {
                    $sql = "update receive_record set flag = ''  where id in ($group2_ids)";
                    
                    $query = $conn->query($sql);
            
                    if($query){
                        http_response_code(200);
                        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
                    }
                    else{
                        http_response_code(501);
                        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Error uploading, Please use laptop to upload again."));
                        die();
                    }
                }

                if($group_ids != '')
                {
                    $sql = "update receive_record set flag = '2'  where id in ($group_ids)";

                    $query = $conn->query($sql);

                    if($query){
                        http_response_code(200);
                        echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
                    }
                    else{
                        http_response_code(501);
                        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Error uploading, Please use laptop to upload again."));
                        die();
                    }
                }
                
            }
               
          break;
      }

      // Close connection
      mysqli_close($conn);


?>
