<?php
ob_start();
//error_reporting(0);
error_reporting(E_ALL);
ini_set('log_errors', true);
//header("Access-Control-Allow-Origin: https://feliix.myvnc.com");
header("Access-Control-Allow-Origin: *");
header("Content-Type: multipart/form-data; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

$id = (isset($_POST['id']) ?  $_POST['id'] : 0);
$schedule_Name = (isset($_POST['schedule_Name']) ?  $_POST['schedule_Name'] : '');
$date_use = (isset($_POST['date_use']) ?  $_POST['date_use'] : '');
$car_use = (isset($_POST['car_use']) ?  $_POST['car_use'] : '');
$driver = (isset($_POST['driver']) ?  $_POST['driver'] : '');
$helper = (isset($_POST['helper']) ?  $_POST['helper'] : '');
$time_out = (isset($_POST['time_out']) ?  $_POST['time_out'] : '');
$time_in = (isset($_POST['time_in']) ?  $_POST['time_in'] : '');
$notes = (isset($_POST['notes']) ?  $_POST['notes'] : '');
$items = (isset($_POST['items']) ?  $_POST['items'] : []);
$status = (isset($_POST['status']) ?  $_POST['status'] : 0);

$check_date_use = (isset($_POST['check_date_use']) ?  $_POST['check_date_use'] : '');
$check_car_use = (isset($_POST['check_car_use']) ?  $_POST['check_car_use'] : '');
$check_driver = (isset($_POST['check_driver']) ?  $_POST['check_driver'] : '');
$check_time_out = (isset($_POST['check_time_out']) ?  $_POST['check_time_out'] : '');
$check_time_in = (isset($_POST['check_time_in']) ?  $_POST['check_time_in'] : '');

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';

require_once '../vendor/autoload.php';
include_once 'config/database.php';
// include_once 'objects/work_calender.php';
include_once 'config/conf.php';

include_once 'mail.php';


$database = new Database();
$db = $database->getConnection();
$conf = new Conf();

//$workCalenderMain = new WorkCalenderMain($db);
//$workCalenderDetails = new WorkCalenderDetails($db);
//$workCalenderMessages = new WorkCalenderMessages($db);
//$le = new Leave($db);

use \Firebase\JWT\JWT;

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied1."));
    die();
} else {

    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
        $user_name = $decoded->data->username;
        //if(!$decoded->data->is_admin)
        //{
        //  http_response_code(401);

        //  echo json_encode(array("message" => "Access denied."));
        //  die();
        //}
    }
    // if decode fails, it means jwt is invalid
    catch (Exception $e) {

        http_response_code(401);

        echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
        die();
    }

    // get current car_calendar_main status
    $current_status = "";
    $sql = "select status from car_calendar_main where id = :id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $current_status = $row['status'];

    $tout = $date_use . " " . $time_out;
    $tin = $date_use . " " . $time_in;

    $check_tout = $check_date_use . " " . $check_time_out;
    $check_tin = $check_date_use . " " . $check_time_in;

    if($status == '0')
    {
        if($current_status == "2")
        {
            $att = get_car_schedule_word($id, "1", $check_date_use, $check_car_use, $check_driver, $check_tout, $check_tin);
            send_car_approved_withdraw_mail_7($id, $user_name, $check_date_use, $check_car_use, $check_driver, $check_tout, $check_tin, $att);
        }

        if($current_status == "1")
        {
            $att = get_car_schedule_word($id, "0", $date_use, $car_use, $driver, $tout, $tin);
            send_car_withdraw_mail_8($id, $user_name, $date_use, $car_use, $driver, $tout, $tin, $att);
        }
    }

    if($status == '-1')
    {
        if($current_status == "2")
        {
            $att = get_car_schedule_word($id, "1", $check_date_use, $check_car_use, $check_driver, $check_tout, $check_tin);
            send_car_approved_delete_mail_9($id, $user_name, $check_date_use, $check_car_use, $check_driver, $check_tout, $check_tin, $att);
        }

        if($current_status == "1")
        {
            $att = get_car_schedule_word($id, "0", $date_use, $car_use, $driver, $tout, $tin);
            send_car_delete_mail_10($id, $user_name, $date_use, $car_use, $driver, $tout, $tin, $att);
        }
    }
    

    // if status = 1 (send), and not any same car and date_use in car_calendar_main, insert car_calendar_check
    if($status == '1')
    {
        $sql = "select count(*) as cnt 
                    from car_calendar_check ck 
                where 
                1 = 1
                and ck.car_use = :car_use 
                and ck.date_use = :date_use 
                and ck.status <> -1 ";

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':car_use', $car_use);
        $stmt->bindParam(':date_use', $date_use);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        
        if($row['cnt'] == 0)
        {
            try {
                $sql = "insert into car_calendar_check
                            (sid, kind, date_use, car_use, driver, time_out, time_in,  created_by, created_at)
                        values
                            (:sid, '1', :date_use, :car_use, :driver, :time_out, :time_in, :created_by, now())";

                $stmt = $db->prepare($sql);

                $stmt->bindParam(':sid', $id);
                $stmt->bindParam(':date_use', $date_use);
                $stmt->bindParam(':car_use', $car_use);
                $stmt->bindParam(':driver', $driver);
                $stmt->bindParam(':time_out',  $tout);
                $stmt->bindParam(':time_in',  $tin);
                $stmt->bindParam(':created_by', $user_name);


                $stmt->execute();
            } catch (Exception $e) {
                http_response_code(501);
                echo json_encode(array("insertion error" => $e->getMessage()));
                die();
            }

            $status = 2;
        }

        // for requestor
        $requestor = "";
        $sql = "select requestor from car_calendar_main where id = :id";

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':id', $id);
        $stmt->execute();

        // read old and append into array
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $requestor = $row['requestor'];
        }
        
        if($requestor == "")
            $requestor = $user_name;
        else
            $requestor = $requestor . "," . $user_name;

        // update requestor
        try {
            $sql = "update 
                        car_calendar_main
                    set 
                        requestor = :requestor
                    where id = :id";

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':requestor', $requestor);
            $stmt->bindParam(':id', $id);

            $stmt->execute();

        } catch (Exception $e) {
            http_response_code(501);
            echo json_encode(array("insertion error" => $e->getMessage()));
            die();
        }

    }

    
    
    try {
        $sql = "update 
                    car_calendar_main
                set 
                    updated_by = :updated_by,
                    updated_at = now(),
                    status = :status
                where id = :id";

        $stmt = $db->prepare($sql);

        $stmt->bindParam(':updated_by', $user_name);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);

        $stmt->execute();

        // if status = 0 (withdraw), set car_calendar_check status = 0
        if($status == 0 || $status == -1)
        {
            $sql = "update 
                        car_calendar_check
                    set 
                        status = -1,
                        deleted_at = now(),
                        deleted_by = :deleted_by
                    where `feliix` = 0 and sid = :id";

            $stmt = $db->prepare($sql);

            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':deleted_by', $user_name);

            $stmt->execute();
        }

        http_response_code(200);
        echo json_encode(array("sid" => $id, "updated_by" => $user_name, "updated_at" => date("Y-m-d H:i:s"), "status" => "success"));

    } catch (Exception $e) {
        http_response_code(501);
        echo json_encode(array("insertion error" => $e->getMessage()));
        die();
    }

    if($status == '2')
    {
        $att = get_car_schedule_word($id, "1", $date_use, $car_use, $driver, $tout, $tin);
        send_car_approval_mail_1($id, $user_name, $date_use, $car_use, $driver, $tout, $tin, $att);
    }
    

    if($status == '1')
    {
        $att = get_car_schedule_word($id, "0", $date_use, $car_use, $driver, $tout, $tin);
        send_car_request_mail_2($id, $user_name, $date_use, $car_use, $driver, $tout, $tin, $att);
    }

    

}