<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: multipart/form-data; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

$merged_results = array();

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

include_once 'config/database.php';
include_once 'config/conf.php';
require_once '../vendor/autoload.php';


use Google\Cloud\Storage\StorageClient;

$database = new Database();
$db = $database->getConnection();

$conf = new Conf();

use \Firebase\JWT\JWT;
if ( !isset( $jwt ) ) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied1."));
    die();
}
else
{

    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $query = "SELECT ss.id, 
                        substring(date_receive, 1, 7) date_receive, 
                        sn,
                        ss.`status` from airship_records ss ";

        $query = $query . " order by  ss.date_receive, id  ";

        $stmt = $db->prepare($query);
        $stmt->execute();
    
        $date_receive = "";
        $sn = 1;
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if($date_receive != $row['date_receive'])
            {
                $date_receive = $row['date_receive'];
                $sn = 1;
            }
            else
            {
                $sn++;
            }

            $id = $row['id'];
            
            $sql = "update airship_records set sn = " . $sn . " where id = " . $id;
            $stmt1 = $db->prepare($sql);
            $stmt1->execute();
            
        }

        // response in json format
        echo json_encode(array(
            "message" => "Finished."
        ));
      
    }
    catch (Exception $e){
    
        // set response code
        http_response_code(401);
    
        // show error message
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}
