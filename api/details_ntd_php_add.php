<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
require_once '../vendor/autoload.php';

use \Firebase\JWT\JWT;

$method = $_SERVER['REQUEST_METHOD'];


if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . "Access denied."));
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
}

header('Access-Control-Allow-Origin: *');

include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();
$db->beginTransaction();
$conf = new Conf();

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
$id = (isset($_POST['id']) ?  $_POST['id'] : 0);

$payment = (isset($_POST['payment']) ?  $_POST['payment'] : '[]');
$payment_array = json_decode($payment, true);

$client_name = $payment_array['client_name'];
$payee_name = $payment_array['payee_name'];
$amount =  $payment_array['amount'];
$amount_php = $payment_array['amount_php'];
$rate =  $payment_array['rate'];
$rate_yahoo = $payment_array['rate_yahoo'];
$total_receive =  $payment_array['total_receive'];
$overpayment = $payment_array['overpayment'];
$pay_date = $payment_array['pay_date'];
$payee = $payment_array['payee'];
$remark = $payment_array['remark'];

$details = $payment_array['details'];

$id = $id == '' ? 0 : $id;

try {

    if($id == 0) {
        // now you can apply
        $query = "INSERT INTO details_ntd_php
        SET
        `client_name` = :client_name,
        `payee_name` = :payee_name,
        `rate` = :rate,
        `rate_yahoo` = :rate_yahoo,
        `pay_date` = :pay_date,
        `payee` = :payee,
        `remark` = :remark, ";

        if ($amount != ''  && !is_null($amount)) {
            $query .= "`amount` = :amount, ";
        }
        if ($amount_php != ''  && !is_null($amount_php)) {
            $query .= "`amount_php` = :amount_php, ";
        }
        if ($total_receive != ''  && !is_null($total_receive)) {
            $query .= "`total_receive` = :total_receive, ";
        }
        if ($overpayment != ''  && !is_null($overpayment)) {
            $query .= "`overpayment` = :overpayment, ";
        }

        $query .= "
        `status` = 1,
        `crt_user` = :crt_user,
        `crt_time` = now()";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':client_name', $client_name);
        $stmt->bindParam(':payee_name', $payee_name);
        $stmt->bindParam(':rate', $rate);
        $stmt->bindParam(':rate_yahoo', $rate_yahoo);
        $stmt->bindParam(':pay_date', $pay_date);
        $stmt->bindParam(':payee', $payee);
        $stmt->bindParam(':remark', $remark);

        if ($amount != '' && !is_null($amount)) {
            $stmt->bindParam(':amount', $amount);
        }
        if ($amount_php != '' && !is_null($amount_php)) {
            $stmt->bindParam(':amount_php', $amount_php);
        }
        if ($total_receive != '' && !is_null($total_receive)) {
            $stmt->bindParam(':total_receive', $total_receive);
        }
        if ($overpayment != '' && !is_null($overpayment)) {
            $stmt->bindParam(':overpayment', $overpayment);
        }

        $stmt->bindParam(':crt_user', $user_name);

        $last_id = 0;
        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                $last_id = $db->lastInsertId();
            } else {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                $db->rollback();
                http_response_code(501);
                echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                die();
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }

        for ($i = 0; $i < count($details); $i++) {
            $query = "INSERT INTO details_ntd_php_record
                SET
                    `sales_id` = :sales_id,
                    `receive_date` = :receive_date,
                    `payment_method` = :payment_method,
                    `account_number` = :account_number,
                    `check_details` = :check_details,
                    `receive_amount` = :receive_amount,
                    `status` = 1,
                    `crt_time` = now()";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':sales_id', $last_id);
            $stmt->bindParam(':receive_date', $details[$i]['receive_date']);
            $stmt->bindParam(':payment_method', $details[$i]['payment_method']);
            $stmt->bindParam(':account_number', $details[$i]['account_number']);
            $stmt->bindParam(':check_details', $details[$i]['check_details']);
            $stmt->bindParam(':receive_amount', $details[$i]['receive_amount']);

            try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
        }

    }
    else {
        // now you can apply
        $query = "update details_ntd_php
        SET
        `client_name` = :client_name,
        `payee_name` = :payee_name,
        `rate` = :rate,
        `rate_yahoo` = :rate_yahoo,
        `pay_date` = :pay_date,
        `payee` = :payee,
        `remark` = :remark, ";

        //if ($amount != ''  && !is_null($amount)) {
            $query .= "`amount` = :amount, ";
        //}
        //if ($amount_php != ''  && !is_null($amount_php)) {
            $query .= "`amount_php` = :amount_php, ";
        //}
        //if ($total_receive != ''  && !is_null($total_receive)) {
            $query .= "`total_receive` = :total_receive, ";
        //}
        //if ($overpayment != ''  && !is_null($overpayment)) {
            $query .= "`overpayment` = :overpayment, ";
        //}

        $query .= "
        `status` = 1,
        `mdf_user` = :mdf_user,
        `mdf_time` = now()
        where id = :id";

        $nul = null;

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':client_name', $client_name);
        $stmt->bindParam(':payee_name', $payee_name);
        $stmt->bindParam(':rate', $rate);
        $stmt->bindParam(':rate_yahoo', $rate_yahoo);
        $stmt->bindParam(':pay_date', $pay_date);
        $stmt->bindParam(':payee', $payee);
        $stmt->bindParam(':remark', $remark);

        if ($amount != '' && !is_null($amount)) {
            $stmt->bindParam(':amount', $amount);
        }
        else
            $stmt->bindParam(':amount', $nul);

        if ($amount_php != '' && !is_null($amount_php)) {
            $stmt->bindParam(':amount_php', $amount_php);
        }
        else
            $stmt->bindParam(':amount_php', $nul);

        if ($total_receive != '' && !is_null($total_receive)) {
            $stmt->bindParam(':total_receive', $total_receive);
        }
        else
            $stmt->bindParam(':total_receive', $nul);

        if ($overpayment != '' && !is_null($overpayment)) {
            $stmt->bindParam(':overpayment', $overpayment);
        }
        else
            $stmt->bindParam(':overpayment', $nul);

        $stmt->bindParam(':mdf_user', $user_name);
        $stmt->bindParam(':id', $id);

        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
  
            } else {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                $db->rollback();
                http_response_code(501);
                echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                die();
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }

        // petty_list
        $query = "DELETE FROM details_ntd_php_record
        WHERE
        `sales_id` = :sales_id";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':sales_id', $id);

        try {
        // execute the query, also check if query was successful
        if (!$stmt->execute()) {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
            die();
        }
        } catch (Exception $e) {
            error_log($e->getMessage());
            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }

        for ($i = 0; $i < count($details); $i++) {
            $query = "INSERT INTO details_ntd_php_record
                SET
                    `sales_id` = :sales_id,
                    `receive_date` = :receive_date,
                    `payment_method` = :payment_method,
                    `account_number` = :account_number,
                    `check_details` = :check_details,
                    `receive_amount` = :receive_amount,
                    `status` = 1,
                    `crt_time` = now()";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':sales_id', $id);
            $stmt->bindParam(':receive_date', $details[$i]['receive_date']);
            $stmt->bindParam(':payment_method', $details[$i]['payment_method']);
            $stmt->bindParam(':account_number', $details[$i]['account_number']);
            $stmt->bindParam(':check_details', $details[$i]['check_details']);
            $stmt->bindParam(':receive_amount', $details[$i]['receive_amount']);

            try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
                    $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
        }
    }

    $db->commit();

    http_response_code(200);
    echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
} catch (Exception $e) {

    error_log($e->getMessage());
    $db->rollback();
    http_response_code(501);
    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
    die();
}
