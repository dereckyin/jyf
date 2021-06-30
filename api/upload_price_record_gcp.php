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

//upload.php
require_once '../vendor/autoload.php';
include_once 'config/conf.php';
include_once 'config/database.php';


use Google\Cloud\Storage\StorageClient;

$user_id = '';

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
          $user_id = $decoded->data->id;

      }

      catch (Exception $e){
      
          http_response_code(401);
     
        echo json_encode(array("message" => "Access denied."));
        die();
      }
}

header('Access-Control-Allow-Origin: *');  

$database = new Database();
$db = $database->getConnection();
$conf = new Conf();


$batch_type = (isset($_POST['batch_type']) ?  $_POST['batch_type'] : '');
$batch_id = (isset($_POST['batch_id']) ?  $_POST['batch_id'] : 0);
$today = (isset($_POST['today']) ?  $_POST['today'] : '');

$image = '';

if(isset($_FILES['file']['name']))
{
    $image_name = $_FILES['file']['name'];
    $valid_extensions = array("jpg","jpeg","png","gif","pdf","docx","doc","xls","xlsx","ppt","pptx","zip","rar","7z","txt","dwg","skp","psd","evo");
    $extension = pathinfo($image_name, PATHINFO_EXTENSION);
    if(in_array(strtolower($extension), $valid_extensions))
    {
        //$upload_path = 'img/' . time() . '.' . $extension;

        $storage = new StorageClient([
            'projectId' => 'predictive-fx-284008',
            'keyFilePath' => $conf::$gcp_key
        ]);

        $bucket = $storage->bucket('feliiximg');

        $upload_name = pathinfo($today.$image_name, PATHINFO_FILENAME) . '.' . $extension;

        if($bucket->upload(
          fopen($_FILES['file']['tmp_name'], 'r'),
          ['name' => $upload_name]
        ))
        {
            $message = 'Uploaded';
            $code = 0;
            $image = $image_name;
        }
        else
        {
            $message = 'There is an error while uploading file';
        }
    }
    else
    {
        $message = 'Only Images or Office files allowed to upload';
    }
}
else
{
    $message = 'None file';
}

$output = array(
    'code' => $code,
    'message'  => $message,
    'image'   => $image
);

echo json_encode($output);


?>