<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: multipart/form-data; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : '');

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
        $sql = "SELECT r.id, r.photo, f.gcp_name, r.date_receive, r.real_pick_time, r.real_payment_time  FROM receive_record r
                LEFT JOIN gcp_storage_file f ON r.id = f.batch_id
                WHERE date_receive <= '2022/12/31' AND date_receive <> ''
                AND real_pick_time <= '2022/12/31' AND real_pick_time <> ''
                AND real_payment_time <= '2022/12/31' AND real_payment_time <> ''
                AND f.batch_type = 'RECEIVE'";
                
        $stmt = $db->prepare($sql);
        $stmt->execute();
        
        $arr = array();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            extract($row);
            $arr[] = array(
                "id" => $id,
                "photo" => $photo,
                "gcp_name" => $gcp_name,
                "date_receive" => $date_receive,
                "real_pick_time" => $real_pick_time,
                "real_payment_time" => $real_payment_time
            );
        }

        $storage = new StorageClient([
            'projectId' => 'predictive-fx-284008',
            'keyFilePath' => $conf::$gcp_key
        ]);

        $bucket = $storage->bucket('feliiximg');

        for($i=0 ; $i < count($arr) ; $i++)
        {
            $object = $bucket->object($arr[$i]['gcp_name']);
            //$object->delete();
            //$info = $object->info();

        }

        for($i=0 ; $i < count($arr) ; $i++)
        {
            $sql = "UPDATE receive_record SET photo = '' WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':id', $arr[$i]['id']);
            $stmt->execute();
        }

        http_response_code(200);
        echo json_encode(array($arr));
        echo json_encode(array("message" => " Update success at " . date("Y-m-d") . " " . date("h:i:sa")));

    } // if decode fails, it means jwt is invalid
    catch (Exception $e) {

        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));
    }

}
