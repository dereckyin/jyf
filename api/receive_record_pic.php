<?php
error_reporting(0);
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
require_once '../vendor/autoload.php';

$conf = new Conf();

use Google\Cloud\Storage\StorageClient;

require_once "db.php";


      header('Access-Control-Allow-Origin: *');  

      $method = $_SERVER['REQUEST_METHOD'];

    $user = $decoded->data->username;
    $create_id = $decoded->data->id;

      switch ($method) {
          

          case 'POST':
  
            $id = (isset($_POST['id']) ?  $_POST['id'] : 0);
            $pid = (isset($_POST['pid']) ? $_POST['pid'] : 0);
            
            $library = "RECEIVE";
            
          	/* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
                $sql = "update receive_record set photo = 'RECEIVE' where id = ?";
                $stmt = $conn->prepare($sql); 
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $stmt->close();

                $query = "update gcp_storage_file
                    SET
                        batch_id = ?,
                        batch_type = ?
                    where id in (" . $pid . ") and batch_type = 'LIBRARY'
                ";

                    // prepare the query
                    $stmt = $conn->prepare($query);

                    // bind the values
                    $stmt->bind_param(
                        "is",
                        $id,
                        $library
                    );

                    try {
                        // execute the query, also check if query was successful
                        if ($stmt->execute()) {
                            mysqli_insert_id($conn);
                        } else {
                            error_log(mysqli_errno($conn));
                        }
                    } catch (Exception $e) {
                        error_log($e->getMessage());
                        mysqli_rollback($conn);
                        http_response_code(501);
                        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                        die();
                    }
                }


      // Close connection
      mysqli_close($conn);

?>
