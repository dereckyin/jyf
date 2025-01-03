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
//$db->beginTransaction();
$conf = new Conf();

$jwt = (isset($_POST['jwt']) ?  $_POST['jwt'] : null);
$id = (isset($_POST['id']) ?  $_POST['id'] : 0);

$date_receive = (isset($_POST['date_receive']) ?  $_POST['date_receive'] : '');
$mode = (isset($_POST['mode']) ?  $_POST['mode'] : '');
$customer = (isset($_POST['customer']) ?  $_POST['customer'] : '');
$address = (isset($_POST['address']) ?  $_POST['address'] : '');
$description = (isset($_POST['description']) ?  $_POST['description'] : '');
$quantity = (isset($_POST['quantity']) ?  $_POST['quantity'] : '');
$kilo = (isset($_POST['kilo']) ?  $_POST['kilo'] : '');
$supplier = (isset($_POST['supplier']) ?  $_POST['supplier'] : '');
$flight = (isset($_POST['flight']) ?  $_POST['flight'] : '');
$flight_date = (isset($_POST['flight_date']) ?  $_POST['flight_date'] : '');
$currency = (isset($_POST['currency']) ?  $_POST['currency'] : '');
$total = (isset($_POST['total']) ?  $_POST['total'] : '');
$ratio = (isset($_POST['ratio']) ? $_POST['ratio'] : 0.0);
$total_php = (isset($_POST['total_php']) ?  $_POST['total_php'] : 0);
$pay_date = (isset($_POST['pay_date']) ?  $_POST['pay_date'] : '');
$pay_status = (isset($_POST['pay_status']) ?  $_POST['pay_status'] : '');
$payee = (isset($_POST['payee']) ?  $_POST['payee'] : '');
$date_arrive = (isset($_POST['date_arrive']) ?  $_POST['date_arrive'] : '');
$receiver = (isset($_POST['receiver']) ?  $_POST['receiver'] : '');
$remark = (isset($_POST['remark']) ?  $_POST['remark'] : '');

$amount = (isset($_POST['amount']) ?  $_POST['amount'] : 0);
$amount_php = (isset($_POST['amount_php']) ?  $_POST['amount_php'] : 0);

$details = (isset($_POST['details']) ?  $_POST['details'] : '[]');
$details_array = json_decode($details, true);

$details_php = (isset($_POST['details_php']) ?  $_POST['details_php'] : '[]');
$details_php_array = json_decode($details_php, true);

// remove space
$customer = trim($customer);
$supplier = trim($supplier);

$id = $id == '' ? 0 : $id;

$total = $total == 'null' ? '' : $total;
$kilo = $kilo == 'null' ? '' : $kilo;
$ratio = $ratio == '' ? 0.0 : $ratio;
$ratio = $ratio == 'null' ? 0.0 : $ratio;

try {

    if($id == 0) {
        // now you can apply
        $query = "INSERT INTO airship_records
        SET
        `date_receive` = :date_receive,
        `mode` = :mode,
        `customer` = :customer,
        `address` = :address,
        `description` = :description,
        `quantity` = :quantity, ";

    if ($kilo != ''  && !is_null($kilo)) {
        $query .= "`kilo` = :kilo, ";
    }else
        $query .= "`kilo` = null, ";

    $query .= "
        `supplier` = :supplier, 
        `flight` = :flight,
        `flight_date` = :flight_date,
        `currency` = :currency, ";
    
if ($total != ''  && !is_null($total)) {
    $query .= "`total` = :total, ";
}else
    $query .= "`total` = null, ";

if ($total_php != ''  && !is_null($total_php)) {
    $query .= "`total_php` = :total_php, ";
}else
    $query .= "`total_php` = null, ";

        $query .= "
        `pay_date` = :pay_date,
        `pay_status` = :pay_status,
        `payee` = :payee,
        `date_arrive` = :date_arrive,
        `receiver` = :receiver, 
        `remark` = :remark,
        `amount` = :amount,
        `ratio` = :ratio,
        `amount_php` = :amount_php, ";
        
        // sn
        $query .= " `sn` = 0, ";

        $query .= "
        `status` = 1,
        `crt_user` = :crt_user,
        `crt_time` = now()";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':date_receive', $date_receive);
        $stmt->bindParam(':mode', $mode);
        $stmt->bindParam(':customer', $customer);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':quantity', $quantity);

        if ($kilo != ''  && !is_null($kilo)) {
            $stmt->bindParam(':kilo', $kilo);
        }
       
        $stmt->bindParam(':supplier', $supplier);
        $stmt->bindParam(':flight', $flight);
        $stmt->bindParam(':flight_date', $flight_date);
        $stmt->bindParam(':currency', $currency);
        
        if ($total != ''  && !is_null($total)) {
            $stmt->bindParam(':total', $total);
        }
        

        if ($total_php != ''  && !is_null($total_php)) {
            $stmt->bindParam(':total_php', $total_php);
        }
      

        $stmt->bindParam(':pay_date', $pay_date);
        $stmt->bindParam(':pay_status', $pay_status);
        $stmt->bindParam(':payee', $payee);
        $stmt->bindParam(':date_arrive', $date_arrive);
        $stmt->bindParam(':receiver', $receiver);
        $stmt->bindParam(':remark', $remark);
        $stmt->bindParam(':ratio', $ratio);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':amount_php', $amount_php);
        
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
 //               $db->rollback();
                http_response_code(501);
                echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                die();
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
 //           $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }

 //       $db->commit();

        if($date_receive != "")
        {
            $sn = 0;
            // get count by date
            $query = "SELECT IFNULL(count(*), 0) as sn FROM airship_records WHERE substring(date_receive, 1, 7) = :date_receive";
            // prepare the query
            $date_rec = substr($date_receive, 0, 7);
            $stmt = $db->prepare($query);
            $stmt->bindParam(':date_receive', $date_rec);
            try {
                // execute the query, also check if query was successful
                if ($stmt->execute()) {
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    $sn = $row['sn'];
                } else {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
    //                   $db->rollback();
                    http_response_code(501);
                    echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
            // update sn by last id
            $query = "UPDATE airship_records
            SET
            `sn` = :sn
            WHERE
            `id` = :id";

            // prepare the query
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $last_id);
            $stmt->bindParam(':sn', $sn);
            try {
                // execute the query, also check if query was successful
                if ($stmt->execute()) {

                } else {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
 //                   $db->rollback();
                    http_response_code(501);
                    echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
  //              $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
            
        }
       

        for ($i = 0; $i < count($details_array); $i++) {
            $query = "INSERT INTO airship_records_detail
                SET
                    `airship_id` = :airship_id,
                    `title` = :title,
                    ";

if ($details_array[$i]['qty'] != ''  && !is_null($details_array[$i]['qty'])) {
    $query .= "`qty` = :qty, ";
}
      

if ($details_array[$i]['price'] != ''  && !is_null($details_array[$i]['price'])) {
    $query .= "`price` = :price, ";
}
        $query .= "
                    `type` = 'n',
                    `status` = 1,
                    `crt_user` = :crt_user,
                    `crt_time` = now()";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':airship_id', $last_id);
            $stmt->bindParam(':title', $details_array[$i]['title']);

            if ($details_array[$i]['qty'] != ''  && !is_null($details_array[$i]['qty'])) {
                $stmt->bindParam(':qty', $details_array[$i]['qty']);
            }
                  
            
            if ($details_array[$i]['price'] != ''  && !is_null($details_array[$i]['price'])) {
                $stmt->bindParam(':price', $details_array[$i]['price']);
            }


            
            
            $stmt->bindParam(':crt_user', $user_name);

            try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
 //                   $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
//                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
        }

        for ($i = 0; $i < count($details_php_array); $i++) {
            $query = "INSERT INTO airship_records_detail
                SET
                    `airship_id` = :airship_id,
                    `title` = :title, ";
                    if ($details_php_array[$i]['qty'] != ''  && !is_null($details_php_array[$i]['qty'])) {
    $query .= "`qty` = :qty, ";
}
      

if ($details_php_array[$i]['price'] != ''  && !is_null($details_php_array[$i]['price'])) {
    $query .= "`price` = :price, ";
}
        $query .= "
                    `type` = 'p',
                    `status` = 1,
                    `crt_user` = :crt_user,
                    `crt_time` = now()";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':airship_id', $last_id);
            $stmt->bindParam(':title', $details_php_array[$i]['title']);
            if ($details_php_array[$i]['qty'] != ''  && !is_null($details_php_array[$i]['qty'])) {
                $stmt->bindParam(':qty', $details_php_array[$i]['qty']);
            }
                  
            
            if ($details_php_array[$i]['price'] != ''  && !is_null($details_php_array[$i]['price'])) {
                $stmt->bindParam(':price', $details_php_array[$i]['price']);
            }

            $stmt->bindParam(':crt_user', $user_name);

            try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
  //                  $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
//                $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
        }

  //      $db->commit();

    }
    else {
        // now you can apply
        $query = "update airship_records
        set
            `date_receive` = :date_receive,
            `mode` = :mode,
            `customer` = :customer,
            `address` = :address,
            `description` = :description,
            `quantity` = :quantity, ";

            if ($kilo != ''  && !is_null($kilo)) {
                $query .= "`kilo` = :kilo, ";
            }
            else
                $query .= "`kilo` = null, ";

            $query .= "
            `supplier` = :supplier, 
            `flight` = :flight,
            `flight_date` = :flight_date,
            `currency` = :currency, ";

            if ($total != ''  && !is_null($total)) {
                $query .= "`total` = :total, ";
            }
            else
                $query .= "`total` = null, ";

            if ($total_php != ''  && !is_null($total_php)) {
                $query .= "`total_php` = :total_php, ";
            }
            else
                $query .= "`total_php` = null, ";

            $query .= "
            `pay_date` = :pay_date,
            `pay_status` = :pay_status,
            `payee` = :payee,
            `date_arrive` = :date_arrive,
            `receiver` = :receiver, 
            `remark` = :remark,
            `amount` = :amount,
            `ratio` = :ratio,
            `amount_php` = :amount_php, ";

        $query .= "
            `status` = 1,
            `mdf_user` = :mdf_user,
            `mdf_time` = now()
            where id = :id";

        $nul = null;

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':date_receive', $date_receive);
        $stmt->bindParam(':mode', $mode);
        $stmt->bindParam(':customer', $customer);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':quantity', $quantity);

        if ($kilo != ''  && !is_null($kilo)) {
            $stmt->bindParam(':kilo', $kilo);
        }
        

        $stmt->bindParam(':supplier', $supplier);
        $stmt->bindParam(':flight', $flight);
        $stmt->bindParam(':flight_date', $flight_date);
        $stmt->bindParam(':currency', $currency);

        if ($total != ''  && !is_null($total)) {
            $stmt->bindParam(':total', $total);
        }
       
         if ($total_php != ''  && !is_null($total_php)) {
            $stmt->bindParam(':total_php', $total_php);
        }
       

        $stmt->bindParam(':pay_date', $pay_date);
        $stmt->bindParam(':pay_status', $pay_status);
        $stmt->bindParam(':payee', $payee);
        $stmt->bindParam(':date_arrive', $date_arrive);
        $stmt->bindParam(':receiver', $receiver);
        $stmt->bindParam(':remark', $remark);
        $stmt->bindParam(':amount', $amount);
        $stmt->bindParam(':ratio', $ratio);
        $stmt->bindParam(':amount_php', $amount_php);

        $stmt->bindParam(':mdf_user', $user_name);
        $stmt->bindParam(':id', $id);

        // execute the query, also check if query was successful
        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
  
            } else {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
 //               $db->rollback();
                http_response_code(501);
                echo json_encode("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]);
                die();
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
//            $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }

        // petty_list
        $query = "DELETE FROM airship_records_detail
        WHERE
        `airship_id` = :airship_id";

        // prepare the query
        $stmt = $db->prepare($query);

        // bind the values
        $stmt->bindParam(':airship_id', $id);

        try {
        // execute the query, also check if query was successful
        if (!$stmt->execute()) {
            $arr = $stmt->errorInfo();
            error_log($arr[2]);
 //           $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
            die();
        }
        } catch (Exception $e) {
            error_log($e->getMessage());
 //           $db->rollback();
            http_response_code(501);
            echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
            die();
        }

        for ($i = 0; $i < count($details_array); $i++) {
            $query = "INSERT INTO airship_records_detail
                SET
                    `airship_id` = :airship_id,
                    `title` = :title,
                    ";

if ($details_array[$i]['qty'] != ''  && !is_null($details_array[$i]['qty'])) {
    $query .= "`qty` = :qty, ";
}
      

if ($details_array[$i]['price'] != ''  && !is_null($details_array[$i]['price'])) {
    $query .= "`price` = :price, ";
}
        $query .= "
                    `type` = 'n',
                    `status` = 1,
                    `crt_user` = :crt_user,
                    `crt_time` = now()";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':airship_id', $id);
            $stmt->bindParam(':title', $details_array[$i]['title']);

            if ($details_array[$i]['qty'] != ''  && !is_null($details_array[$i]['qty'])) {
                $stmt->bindParam(':qty', $details_array[$i]['qty']);
            }
                  
            
            if ($details_array[$i]['price'] != ''  && !is_null($details_array[$i]['price'])) {
                $stmt->bindParam(':price', $details_array[$i]['price']);
            }
            $stmt->bindParam(':crt_user', $user_name);

            try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
 //                   $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
 //               $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
        }

        for ($i = 0; $i < count($details_php_array); $i++) {
            $query = "INSERT INTO airship_records_detail
                SET
                    `airship_id` = :airship_id,
                    `title` = :title, ";
                    if ($details_php_array[$i]['qty'] != ''  && !is_null($details_php_array[$i]['qty'])) {
    $query .= "`qty` = :qty, ";
}
      

if ($details_php_array[$i]['price'] != ''  && !is_null($details_php_array[$i]['price'])) {
    $query .= "`price` = :price, ";
}
        $query .= "
                    `type` = 'p',
                    `status` = 1,
                    `crt_user` = :crt_user,
                    `crt_time` = now()";

            // prepare the query
            $stmt = $db->prepare($query);

            // bind the values
            $stmt->bindParam(':airship_id', $id);
            $stmt->bindParam(':title', $details_php_array[$i]['title']);
            
            if ($details_php_array[$i]['qty'] != ''  && !is_null($details_php_array[$i]['qty'])) {
                $stmt->bindParam(':qty', $details_php_array[$i]['qty']);
            }
                  
            
            if ($details_php_array[$i]['price'] != ''  && !is_null($details_php_array[$i]['price'])) {
                $stmt->bindParam(':price', $details_php_array[$i]['price']);
            }
            $stmt->bindParam(':crt_user', $user_name);

            try {
                // execute the query, also check if query was successful
                if (!$stmt->execute()) {
                    $arr = $stmt->errorInfo();
                    error_log($arr[2]);
//                    $db->rollback();
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $arr[2]));
                    die();
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
 //               $db->rollback();
                http_response_code(501);
                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
                die();
            }
        }

 //       $db->commit();
    }

    

    http_response_code(200);
    echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
} catch (Exception $e) {

    error_log($e->getMessage());
 //   $db->rollback();
    http_response_code(501);
    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
    die();
}
