<?php
error_reporting(0);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
$username = (isset($_POST['name']) ?  $_POST['name'] : '');
$today = (isset($_POST['today']) ?  $_POST['today'] : '');
$type = (isset($_POST['type']) ?  $_POST['type'] : '');
$location = (isset($_POST['location']) ?  $_POST['location'] : '');
$explan = (isset($_POST['explan']) ?  $_POST['explan'] : '');
$remark = (isset($_POST['remark']) ?  $_POST['remark'] : '');
$duty_time = (isset($_POST['time']) ?  $_POST['time'] : '');
$latitude = (isset($_POST['latitude']) ?  $_POST['latitude'] : 0.0);
$longitude = (isset($_POST['longitude']) ?  $_POST['longitude'] : 0.0);
$piclatitude = (isset($_POST['piclatitude']) ?  $_POST['piclatitude'] : 0.0);
$piclongitude = (isset($_POST['piclongitude']) ?  $_POST['piclongitude'] : 0.0);
$photo_time = (isset($_POST['photo_time']) ?  $_POST['photo_time'] : '');
$photo_gps = (isset($_POST['photo_gps']) ?  $_POST['photo_gps'] : '');

$img = !empty($_POST['base64image']) ? $_POST['base64image'] : "";
$img = str_replace('data:image/png;base64,', '', $img);
$img = str_replace('data:image/jpeg;base64,', '', $img);
$img = str_replace(' ', '+', $img);
if($img != "")
    $fileData = base64_decode($img);


include_once 'config/core.php';

include_once 'config/database.php';
include_once 'objects/on_duty_v.php';
include_once 'config/conf.php';

$database = new Database();
$db = $database->getConnection();

$onduty = new OnDuty($db);

try {

    $filename = "";
    $s_lat = 0.0;
    $s_lng = 0.0;
    $s_time = "";


    try {
        if (isset($fileData)) {
            $conf = new Conf();
            $key = "myKey";
            $time = time();
            $hash = hash_hmac('sha256', $time . rand(1, 65536), $key);
            $ext = "jpg";
            $filename = $time . $hash . "." . $ext;

            file_put_contents($conf::$upload_path . $filename, $fileData);

                $s_lat =  0.0;
                $s_lng =  0.0;
                $s_time = "";
            

            compress_image($conf::$upload_path . $filename, $conf::$upload_path . $filename, 60);
        }
    }catch (Exception $e){

        //http_response_code(401);

        //echo json_encode(array("message" => "Access denied."));
        //die();
    }

    $onduty->username = $username;
    $onduty->duty_date = str_replace("-", "/", $today);
    $onduty->duty_type = $type;
    $onduty->location = $location;
    $onduty->remark = $remark;
    $onduty->duty_time = date("h:i:sa");
    $onduty->explain = $explan;
    $onduty->pic_url = $filename;
    $onduty->pic_time = $photo_time;
    $onduty->lat = $latitude;
    $onduty->lng = $longitude;
    $onduty->pic_lat = $piclatitude;
    $onduty->pic_lng = $piclongitude;
    $onduty->pic_server_time = $s_time;
    $onduty->pic_server_lat = $s_lat;
    $onduty->pic_server_lng = $s_lng;

    $id = $onduty->create();

    if(empty($id))
    {
        http_response_code(200);
        echo json_encode(array("message" => "Punch Fail at" . date("Y-m-d") . " " . date("h:i A")));
    }else
    {
        http_response_code(200);
        if($type == 'A')
            echo json_encode(array("message" => "Punch in success at " . date("Y-m-d") . " " . date("h:i A")));
        if($type == 'B')
            echo json_encode(array("message" => "Punch out success at " . date("Y-m-d") . " " . date("h:i A")));
    }

}
    // if decode fails, it means jwt is invalid
catch (Exception $e){

    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));

    }


function triphoto_getGPS($fileName)
{
    //get the EXIF
    try{
        $exif = exif_read_data($fileName);
    }catch (Exception $e)
    {
        $result['latitude'] = 0.0;
        $result['longitude'] = 0.0;
        $result['time'] = "";
        return $result;
    }

    $GPSLatitudeRef = (isset($exif["GPSLatitudeRef"]) ? $exif["GPSLatitudeRef"] : "");
    $GPSLongitudeRef = (isset($exif["GPSLongitudeRef"]) ? $exif["GPSLongitudeRef"] : "");
    $LatM = 1; $LongM = 1;


    //get the Hemisphere multiplier

    if($GPSLatitudeRef == 'S')
    {
        $LatM = -1;
    }
    if($GPSLongitudeRef == 'W')
    {
        $LongM = -1;
    }

    //get the GPS data
    $gps['LatDegree']=(isset($exif["GPSLatitude"][0]) ? $exif["GPSLatitude"][0] : 0.0);
    $gps['LatMinute']=(isset($exif["GPSLatitude"][1]) ? $exif["GPSLatitude"][1] : 0.0);
    $gps['LatgSeconds']=(isset($exif["GPSLatitude"][2]) ? $exif["GPSLatitude"][2] : 0.0);
    $gps['LongDegree']=(isset($exif["GPSLongitude"][0]) ? $exif["GPSLongitude"][0] : 0.0);
    $gps['LongMinute']=(isset($exif["GPSLongitude"][1]) ? $exif["GPSLongitude"][1] : 0.0);
    $gps['LongSeconds']=(isset($exif["GPSLongitude"][2]) ? $exif["GPSLongitude"][2] : 0.0);

    $DateTimeOriginal = (isset($exif["DateTimeOriginal"]) ? $exif["DateTimeOriginal"] : "");

    //convert strings to numbers
    foreach($gps as $key => $value)
    {
        $pos = strpos($value, '/');
        if($pos !== false)
        {
            $temp = explode('/',$value);
            if(!is_null($temp[1]) && $temp[1] != 0)
                $gps[$key] = $temp[0] / $temp[1];
            else
                $gps[$key] = 0.0;
        }
    }

    //calculate the decimal degree
    $result['latitude'] = $LatM * ($gps['LatDegree'] + ($gps['LatMinute'] / 60) + ($gps['LatgSeconds'] / 3600));
    $result['longitude'] = $LongM * ($gps['LongDegree'] + ($gps['LongMinute'] / 60) + ($gps['LongSeconds'] / 3600));
    $result['time'] = $DateTimeOriginal;

    return $result;

}

function compress_image($source_url, $destination_url, $quality)
{
    $info = getimagesize($source_url);
    if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($source_url);
    elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($source_url);
    elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($source_url);
    imagejpeg($image, $destination_url, $quality);
    //echo "Image uploaded successfully.";
}