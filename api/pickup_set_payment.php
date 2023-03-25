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

require_once '../vendor/autoload.php';

require_once "db.php";

header('Access-Control-Allow-Origin: *');  

$method = $_SERVER['REQUEST_METHOD'];

$user = $decoded->data->username;
$user_id = $decoded->data->id;

    switch ($method) {
    
        case 'POST':
    
        $id = (isset($_POST['id']) ?  $_POST['id'] : "");
        $record = (isset($_POST['record']) ?  $_POST['record'] : "");
        $pre_record = (isset($_POST['pre_record']) ?  $_POST['pre_record'] : "");
        $encode_status = (isset($_POST['encode_status']) ?  $_POST['encode_status'] : "");
        $detail_array = json_decode($record, true);
        $detail_pre_array = json_decode($pre_record, true);

        $measure_id = 0;

        $has_taiwan_pay = false;

        $conn->begin_transaction();

        // for payment
        $query = "update payment 
                set status = -1,
                mdf_user = '" . $user . "', 
                mdf_time = now()
        WHERE detail_id = " . $id;

        $stmt = $conn->prepare($query);

        try {
        // execute the query, also check if query was successful
        if (!$stmt->execute()) {
            $conn->rollback();
            http_response_code(501);
            echo json_encode("Failure2 at " . date("Y-m-d") . " " . date("h:i:sa") . " " . mysqli_errno($conn));
            die();
        }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $conn->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }

        $detail_id = array();
        for ($i = 0; $i < count($detail_array); $i++) {
            if($detail_array[$i]['detail_id'] != '' && $detail_array[$i]['detail_id'] != '0')
            {
                // if in array
                if(!in_array($detail_array[$i]['detail_id'], $detail_id))
                {
                    array_push($detail_id, $detail_array[$i]['detail_id']);
                }
            }
           
        }

        if(count($detail_id) > 0)
        {
            $detail_id = implode(",", $detail_id);

            $query = "update payment 
                    set status = -1,
                    mdf_user = '" . $user . "', 
                    mdf_time = now()
            WHERE detail_id in (" . $detail_id . ")";

            $stmt = $conn->prepare($query);

            try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
                    $conn->rollback();
                    http_response_code(501);
                    echo json_encode("Failure2 at " . date("Y-m-d") . " " . date("h:i:sa") . " " . mysqli_errno($conn));
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                $conn->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
        }

        // delete pre data
        //if(count($detail_array) == 0)
        //{
            $detail_id = array();
            for ($i = 0; $i < count($detail_pre_array); $i++) {
                if($detail_pre_array[$i]['detail_id'] != '' && $detail_pre_array[$i]['detail_id'] != '0')
                {
                    // if in array
                    if(!in_array($detail_pre_array[$i]['detail_id'], $detail_id))
                    {
                        array_push($detail_id, $detail_pre_array[$i]['detail_id']);
                    }
                }
            
            }

            if(count($detail_id) > 0)
            {
                $detail_id = implode(",", $detail_id);

                $query = "update payment 
                        set status = -1,
                        mdf_user = '" . $user . "', 
                        mdf_time = now()
                WHERE detail_id in (" . $detail_id . ")";

                $stmt = $conn->prepare($query);

                try {
                    // execute the query, also check if query was successful
                    if (!$stmt->execute()) {
                        $conn->rollback();
                        http_response_code(501);
                        echo json_encode("Failure2 at " . date("Y-m-d") . " " . date("h:i:sa") . " " . mysqli_errno($conn));
                        die();
                    }
                } catch (Exception $e) {
                    error_log($e->getMessage());
                    $conn->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                    die();
                }
            }
        //}
        

        for ($i = 0; $i < count($detail_array); $i++) {
            $id = ($detail_array[$i]['id'] == '') ? 0 : $detail_array[$i]['id'];
            $type = ($detail_array[$i]['type'] == '') ? 0 : $detail_array[$i]['type'];
            $detail_id = ($detail_array[$i]['detail_id'] == '') ? 0 : $detail_array[$i]['detail_id'];
            $issue_date = ($detail_array[$i]['issue_date'] == '') ? "" : $detail_array[$i]['issue_date'];
            $payment_date = ($detail_array[$i]['payment_date'] == '') ? "" : $detail_array[$i]['payment_date'];
            $person = ($detail_array[$i]['person'] == '') ? "" : $detail_array[$i]['person'];
            $amount = ($detail_array[$i]['amount'] == '') ? 0 : $detail_array[$i]['amount'];
            $change = ($detail_array[$i]['change'] == '') ? 0 : $detail_array[$i]['change'];
            $courier = ($detail_array[$i]['courier'] == '') ? 0 : $detail_array[$i]['courier'];
            $remark = ($detail_array[$i]['remark'] == '') ? "" : $detail_array[$i]['remark'];

            if($type == "4")
                $has_taiwan_pay = true;

/*
            if($id != 0)
            {
                // for payment
                $query = "update payment 
                        set status = -1,
                        mdf_user = '" . $user . "', 
                        mdf_time = now()
                WHERE id = " . $id;

                $stmt = $conn->prepare($query);

                try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
                    $conn->rollback();
                    http_response_code(501);
                    echo json_encode("Failure2 at " . date("Y-m-d") . " " . date("h:i:sa") . " " . mysqli_errno($conn));
                    die();
                }
                } catch (Exception $e) {
                error_log($e->getMessage());
                $conn->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
                }
            }
*/
            
            $query = "insert into payment (detail_id, `type`, issue_date, payment_date, person, amount, `change`, courier, remark, status, crt_time, crt_user)
                VALUES (" . $detail_id . ", " . $type . ", '" . $issue_date . "', '" . $payment_date . "', '" . $person . "', " . $amount . ", " . $change . ", " . $courier . ", '" . $remark . "', 0, now(), '" . $user . "')";  
          
            $stmt = $conn->prepare($query);

            try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
                    $conn->rollback();
                    http_response_code(501);
                    echo json_encode("Failure2 at " . date("Y-m-d") . " " . date("h:i:sa") . " " . mysqli_errno($conn));
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                $conn->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }

        }

         // if group id <> 0 then update detail in group ids 
        $group_id = GetGroupId($detail_id, $conn);

        // get all received record's ids
        $pre_receive_record= array();

        if($group_id != 0)
        {
            $query = "select id, date_receive, customer, quantity, supplier, taiwan_pay, description, remark from receive_record where id in (SELECT record_id from measure_record_detail WHERE detail_id in (select measure_detail_id from pick_group where group_id = " . $group_id . "))";

            $result = $conn->query($query);

            while ($row = $result->fetch_assoc()) 
                array_push($pre_receive_record, $row);
        }
        else
        {
            $query = "select id, date_receive, customer, quantity, supplier, taiwan_pay, description, remark from receive_record where id in (SELECT record_id from measure_record_detail WHERE detail_id = " . $detail_id . ")";
            $result = $conn->query($query);

            while ($row = $result->fetch_assoc()) {
                array_push($pre_receive_record, $row);
            }
        }


        if($group_id != 0)
        {
            // for status
            $query = "UPDATE measure_detail
                SET
                payment_status = '" . $encode_status . "'
                WHERE id in (select measure_detail_id from pick_group where group_id = " . $group_id . ")";

            $stmt = $conn->prepare($query);

            try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
                    $conn->rollback();
                    http_response_code(501);
                    echo json_encode("Failure2 at " . date("Y-m-d") . " " . date("h:i:sa") . " " . mysqli_errno($conn));
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                $conn->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }

            // for receive record
            if($encode_status == 'C')
            {
                $query = "UPDATE receive_record
                    SET
                    real_payment_time = '" . date("Y/m/d") . "'
                    WHERE id in (SELECT record_id from measure_record_detail WHERE detail_id in (select measure_detail_id from pick_group where group_id = " . $group_id . "))";

                $stmt = $conn->prepare($query);

                try {
                    // execute the query, also check if query was successful
                    if (!$stmt->execute()) {
                        $conn->rollback();
                        http_response_code(501);
                        echo json_encode("Failure2 at " . date("Y-m-d") . " " . date("h:i:sa") . " " . mysqli_errno($conn));
                        die();
                    }
                } catch (Exception $e) {
                    error_log($e->getMessage());
                    $conn->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                    die();
                }
            }
        }
        else
        {
            // for status
            $query = "UPDATE measure_detail
                SET
                payment_status = '" . $encode_status . "'
                WHERE id = " . $detail_id;

            $stmt = $conn->prepare($query);

            try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
                    $conn->rollback();
                    http_response_code(501);
                    echo json_encode("Failure2 at " . date("Y-m-d") . " " . date("h:i:sa") . " " . mysqli_errno($conn));
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                $conn->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }

            // for receive record
            if($encode_status == 'C')
            {
                $query = "UPDATE receive_record
                    SET
                    real_payment_time = '" . date("Y/m/d") . "'
                    WHERE id in (SELECT record_id from measure_record_detail WHERE detail_id = " . $detail_id . ")";

                $stmt = $conn->prepare($query);

                try {
                    // execute the query, also check if query was successful
                    if (!$stmt->execute()) {
                        $conn->rollback();
                        http_response_code(501);
                        echo json_encode("Failure2 at " . date("Y-m-d") . " " . date("h:i:sa") . " " . mysqli_errno($conn));
                        die();
                    }
                } catch (Exception $e) {
                    error_log($e->getMessage());
                    $conn->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                    die();
                }
            }
        }

        $conn->commit();
    }

    // if has_taiwan_pay = true iterate pre_receive_record to see if taiwan_pay is 0, then update to 1 and send mail
    if($has_taiwan_pay)
    {
        foreach($pre_receive_record as $record)
        {
            if($record['taiwan_pay'] == 0)
            {
                $query = "UPDATE receive_record
                    SET
                    taiwan_pay = 1
                    WHERE id = " . $record['id'];

                $stmt = $conn->prepare($query);

                try {
                    // execute the query, also check if query was successful
                    if (!$stmt->execute()) {
                        // 1. rollback
                    }
                } catch (Exception $e) {
                    // 2. rollback
                }

                // send mail
                sendMail($record['date_receive'], $record['customer'], $record['quantity'], $record['supplier'], $record['description'], $record['remark']);
            }
        }
    }


    // Close connection
    mysqli_close($conn);

function GetGroupId($detail_id, $db)
{
    $group_id = 0;
    $query = "select group_id from pick_group where measure_detail_id = " . $detail_id;
    $result = $db->query($query);
    if($result->num_rows > 0)
    {
        while($row = $result->fetch_assoc())
        {
            $group_id = $row['group_id'];
        }
    }
    return $group_id;
}


function sendMail($date_receive, $customer, $quantity, $supplier, $description, $remark) {
    $conf = new Conf();
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    $mail->SMTPDebug  = 2;
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = "ssl";
    $mail->Port       = 465;
    $mail->SMTPKeepAlive = true;
    $mail->Host       = $conf::$mail_Host;
    $mail->Username   = $conf::$mail_Username;
    $mail->Password   = $conf::$mail_Password;

    // $mail = new PHPMailer(true);
    // $mail->isSMTP();
    // $mail->Host = 'smtp.ethereal.email';
    // $mail->SMTPAuth = true;
    // $mail->Username = 'fernando.witting79@ethereal.email';
    // $mail->Password = 'e2eDHfEwJtrRstkQYn';
    // $mail->SMTPSecure = 'tls';
    // $mail->Port = 587;
    // $mail->CharSet = 'UTF-8';
    // $mail->Encoding = 'base64';

    $mail->IsHTML(true);
    $mail->AddAddress("jyf_lu@hotmail.com", "jyf_lu");
 
    $mail->SetFrom("servictoryshipment@gmail.com", "servictoryshipment");
    $mail->AddReplyTo("servictoryshipment@gmail.com", "servictoryshipment");
   
    $mail->Subject = "[通知] Lailani 標註了某筆收貨記錄為台灣付";
    $content = "<p>Dear All,</p>";
    $content = $content . "<p>Lailani 標註了某筆收貨記錄為台灣付，該筆收貨記錄資料如下。</p>";
    $content = $content . "<p>收貨日期: " . $date_receive . "</p>";
    $content = $content . "<p>收件人: " . $customer . "</p>";
    $content = $content . "<p>件數: " . $quantity . "</p>";
    $content = $content . "<p>寄貨人: " . $supplier . "</p>";
    $content = $content . "<p>描述: " . $description . "</p>";
    $content = $content . "<p>備註: " . $remark . "</p>";


    $mail->MsgHTML($content);
    if(!$mail->Send()) {
        echo "Error while sending Email.";
        var_dump($mail);
    } else {
        echo "Email sent successfully";
    }
}

?>
