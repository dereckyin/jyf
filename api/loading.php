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

if (!isset($jwt)) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
} else {
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
    }
    // if decode fails, it means jwt is invalid
    catch (Exception $e) {

        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}

header('Access-Control-Allow-Origin: *');

require_once "db.php";

$method = $_SERVER['REQUEST_METHOD'];

$user = $decoded->data->username;


function GetPic($picname, $photo, $id, $conn){
    $merged_results = array();

    if($picname != "")
    {
        array_push($merged_results, array(
            "pid" => $id,
            "batch_id" => $id,
            "is_checked" => true,
            "customer" => "",
            "date_receive" => "",
            "type" => "FILE",
            "quantity" => "",
            "remark" => "",
            "supplier" => "",
            "gcp_name" => $picname,
        ));
        
    }

    if($photo == 'RECEIVE')
    {
        $sql = "SELECT id, gcp_name FROM gcp_storage_file WHERE batch_id = ? AND batch_type = 'RECEIVE' AND STATUS <> -1";
        if ($stmt = mysqli_prepare($conn, $sql)) {

            mysqli_stmt_bind_param($stmt, "i", $id);
        
            /* execute query */
            mysqli_stmt_execute($stmt);

            $result1 = mysqli_stmt_get_result($stmt);

            while($row = mysqli_fetch_assoc($result1)) {
                $filename = $row['gcp_name'];
                $pid = $row['id'];
                array_push($merged_results, array(
                    "pid" => $pid,
                    "batch_id" => $id,
                    "is_checked" => true,
                    "customer" => "",
                    "date_receive" => "",
                    "type" => "RECEIVE",
                    "quantity" => "",
                    "remark" => "",
                    "supplier" => "",
                    "gcp_name" => $filename,
                ));
            }
        }
    }

    return $merged_results;
}

switch ($method) {
    case 'GET':
        $id = stripslashes((isset($_GET['id']) ?  $_GET['id'] : ""));
        $page = stripslashes((isset($_GET['page']) ?  $_GET['page'] : ""));
        $size = stripslashes((isset($_GET['size']) ?  $_GET['size'] : ""));
        $loading = stripslashes((isset($_GET['loading']) ?  $_GET['loading'] : ""));
        $record = stripslashes((isset($_GET['record']) ?  $_GET['record'] : ""));
        $query = stripslashes((isset($_GET['query']) ?  $_GET['query'] : ""));

        if ($loading == "1") {
            //$sql = "SELECT 0 as is_checked, lo.id, shipping_mark, estimate_weight, actual_weight, container_number, seal, so, ship_company, ship_boat, neck_cabinet, lo.date_sent, lo.etd_date, lo.ob_date, lo.eta_date, ld.date_sent date_send_his, ld.etd_date etd_date_his, ld.ob_date ob_date_his, ld.eta_date eta_date_his, broker, remark, (SELECT date_arrive FROM measure WHERE measure.id = measure_num) date_arrive, crt_time, crt_user  FROM loading lo LEFT JOIN loading_date_history ld ON lo.id = ld.loading_id where  status = '' ".($id ? " and lo.id=$id" : ''); 
            //$sql = $sql . " ORDER BY ship_company, date_sent ";

            $sql = "(SELECT 0 as is_checked, lo.id, shipping_mark, estimate_weight, actual_weight, container_number, seal, so, ship_company, ship_boat, neck_cabinet, shipper, lo.date_sent, lo.etd_date, lo.ob_date, lo.eta_date, lo.date_arrive, lo.date_arrive date_arrive_his, ld.date_sent date_send_his, ld.etd_date etd_date_his, ld.ob_date ob_date_his, ld.eta_date eta_date_his, ld.date_arrive date_arrive_his, broker, remark, (SELECT date_arrive FROM measure WHERE measure.id = measure_num) date_arrive_old, crt_time, crt_user, '9999/99/99' ords  FROM loading lo LEFT JOIN loading_date_history ld ON lo.id = ld.loading_id where  lo.STATUS = '' " . ($id ? " and lo.id=$id" : '') . " AND lo.date_sent = '' ORDER BY lo.date_sent ) ";
            $sql = $sql . "UNION ";
            $sql = $sql . "(SELECT 0 as is_checked, lo.id, shipping_mark, estimate_weight, actual_weight, container_number, seal, so, ship_company, ship_boat, neck_cabinet, shipper, lo.date_sent, lo.etd_date, lo.ob_date, lo.eta_date, lo.date_arrive, lo.date_arrive date_arrive_his, ld.date_sent date_send_his, ld.etd_date etd_date_his, ld.ob_date ob_date_his, ld.eta_date eta_date_his, ld.date_arrive date_arrive_his, broker, remark, (SELECT date_arrive FROM measure WHERE measure.id = measure_num) date_arrive_old, crt_time, crt_user, lo.date_sent ords FROM loading lo LEFT JOIN loading_date_history ld ON lo.id = ld.loading_id where  lo.STATUS = '' " . ($id ? " and lo.id=$id" : '') . " AND lo.date_sent <> '' ORDER BY lo.date_sent DESC ) ";
            $sql = $sql . "ORDER BY ords DESC, ship_company ";
        }

        if ($loading == "2") {
            //$sql = "SELECT 0 as is_checked, lo.id, shipping_mark, estimate_weight, actual_weight, container_number, seal, so, ship_company, ship_boat, neck_cabinet, lo.date_sent, lo.etd_date, lo.ob_date, lo.eta_date, ld.date_sent date_send_his, ld.etd_date etd_date_his, ld.ob_date ob_date_his, ld.eta_date eta_date_his, broker, remark, (SELECT date_arrive FROM measure WHERE measure.id = measure_num) date_arrive, crt_time, crt_user  FROM loading lo LEFT JOIN loading_date_history ld ON lo.id = ld.loading_id where  status = '' ".($id ? " and lo.id=$id" : ''); 
            //$sql = $sql . " ORDER BY ship_company, date_sent ";

            $sql = "(SELECT 0 as is_checked, lo.id, shipping_mark, estimate_weight, actual_weight, container_number, seal, so, ship_company, ship_boat, neck_cabinet, shipper, lo.date_sent, lo.etd_date, lo.ob_date, lo.eta_date, lo.date_arrive, lo.date_arrive date_arrive_his, ld.date_sent date_send_his, ld.etd_date etd_date_his, ld.ob_date ob_date_his, ld.eta_date eta_date_his, ld.date_arrive date_arrive_his, broker, remark, (SELECT date_arrive FROM measure WHERE measure.id = measure_num) date_arrive_old, crt_time, crt_user, '9999/99/99' ords, (SELECT COUNT(*) FROM receive_record WHERE batch_num = lo.id) cnt, (SELECT COUNT(*) FROM receive_record WHERE batch_num = lo.id AND mail_cnt <> 0) mail_cnt  FROM loading lo LEFT JOIN loading_date_history ld ON lo.id = ld.loading_id where  lo.STATUS = '' " . ($id ? " and lo.id=$id" : '') . " AND lo.date_sent = '' ORDER BY lo.date_sent ) ";
            $sql = $sql . "UNION ";
            $sql = $sql . "(SELECT 0 as is_checked, lo.id, shipping_mark, estimate_weight, actual_weight, container_number, seal, so, ship_company, ship_boat, neck_cabinet, shipper, lo.date_sent, lo.etd_date, lo.ob_date, lo.eta_date, lo.date_arrive, lo.date_arrive date_arrive_his, ld.date_sent date_send_his, ld.etd_date etd_date_his, ld.ob_date ob_date_his, ld.eta_date eta_date_his, ld.date_arrive date_arrive_his, broker, remark, (SELECT date_arrive FROM measure WHERE measure.id = measure_num) date_arrive_old, crt_time, crt_user, lo.date_sent ords,  (SELECT COUNT(*) FROM receive_record WHERE batch_num = lo.id) cnt, (SELECT COUNT(*) FROM receive_record WHERE batch_num = lo.id AND mail_cnt <> 0) mail_cnt FROM loading lo LEFT JOIN loading_date_history ld ON lo.id = ld.loading_id where  lo.STATUS = '' " . ($id ? " and lo.id=$id" : '') . " AND lo.date_sent <> '' ORDER BY lo.date_sent DESC ) ";
            $sql = $sql . "ORDER BY ords DESC, ship_company ";
        }

        if ($record != "" && $query != "1") {
            //$sql = "SELECT 1 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, crt_time, crt_user  FROM receive_record where batch_num = $record and status = '' ".($id ? " and id=$id" : ''); 

            //$sql = $sql . " union ";

            //$sql = $sql . " ( SELECT 0 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, crt_time, crt_user  FROM receive_record where batch_num = 0 and status = '' ".($id ? " and id=$id" : '') . " ORDER BY customer, date_receive ) "; 

            //$sql = $sql . " ORDER BY customer, date_receive ";

            $subquery = "";

            $merged_results = array();

            $key = array();

            $sql = "SELECT customer FROM  receive_record where batch_num = $record and date_receive <> '' and status = '' GROUP BY date_receive, customer  ORDER BY date_receive;";

            // $sql = "CALL createReceiveList(); ";
            // run SQL statement
            $result = mysqli_query($conn, $sql);

            /* fetch data */
            while ($row = mysqli_fetch_array($result)) {
                if (isset($row)) {

                    if (in_array(strtolower($row['customer']), $key)) {
                        continue;
                    } else {
                        array_push($key, strtolower($row['customer']));
                    }

                    $subquery = "SELECT 1 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, crt_time, crt_user, 1 as is_edited FROM receive_record where batch_num = $record and date_receive <> '' and status = ''  and customer = ? ORDER BY date_receive  ";

                    if ($stmt = mysqli_prepare($conn, $subquery)) {

                        mysqli_stmt_bind_param($stmt, "s", $row['customer']);

                        /* execute query */
                        mysqli_stmt_execute($stmt);

                        $result1 = mysqli_stmt_get_result($stmt);

                        while ($row = mysqli_fetch_assoc($result1)) {
                            $merged_results[] = $row;
                        }
                    }
                }
            }

            $subquery = "SELECT 1 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, crt_time, crt_user, 1 as is_edited  FROM receive_record where batch_num = $record and date_receive = '' and status = ''  ORDER BY id";

            $result1 = mysqli_query($conn, $subquery);
            if ($result1 != null) {
                while ($row = mysqli_fetch_assoc($result1))
                    $merged_results[] = $row;
            }

            // for batchnum = 0
            $subquery = "";

            $key = array();

            $sql = "SELECT customer FROM  receive_record where batch_num = 0 and date_receive <> '' and status = ''  GROUP BY date_receive, customer  ORDER BY date_receive;";

            // $sql = "CALL createReceiveList(); ";
            // run SQL statement
            $result = mysqli_query($conn, $sql);

            /* fetch data */
            while ($row = mysqli_fetch_array($result)) {
                if (isset($row)) {

                    if (in_array(strtolower($row['customer']), $key)) {
                        continue;
                    } else {
                        array_push($key, strtolower($row['customer']));
                    }

                    $subquery = "SELECT 0 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, crt_time, crt_user, 1 as is_edited FROM receive_record where batch_num = 0 and date_receive <> '' and status = ''  and customer = ? ORDER BY date_receive  ";

                    if ($stmt = mysqli_prepare($conn, $subquery)) {

                        mysqli_stmt_bind_param($stmt, "s", $row['customer']);

                        /* execute query */
                        mysqli_stmt_execute($stmt);

                        $result1 = mysqli_stmt_get_result($stmt);

                        while ($row = mysqli_fetch_assoc($result1)) {
                            $merged_results[] = $row;
                        }
                    }
                }
            }

            $subquery = "SELECT 0 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, crt_time, crt_user, 1 as is_edited  FROM receive_record where batch_num = 0 and date_receive = '' and status = ''  ORDER BY id";

            $result1 = mysqli_query($conn, $subquery);
            if ($result1 != null) {
                while ($row = mysqli_fetch_assoc($result1))
                    $merged_results[] = $row;
            }



            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
            break;
        }

        if ($record != "" && $query == "1") {
            //$sql = "SELECT 1 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, crt_time, crt_user  FROM receive_record where batch_num = $record and status = '' ".($id ? " and id=$id" : ''); 

            //$sql = $sql . " ORDER BY customer, date_receive ";

            $subquery = "";

            $merged_results = array();

            $key = array();

            $sql = "SELECT customer FROM  receive_record where batch_num = $record and date_receive <> '' and status = ''  GROUP BY date_receive, customer  ORDER BY date_receive;";

            // $sql = "CALL createReceiveList(); ";
            // run SQL statement
            $result = mysqli_query($conn, $sql);

            /* fetch data */
            while ($row = mysqli_fetch_array($result)) {
                if (isset($row)) {

                    if (in_array(strtolower($row['customer']), $key)) {
                        continue;
                    } else {
                        array_push($key, strtolower($row['customer']));
                    }

                    $subquery = "SELECT CASE WHEN mail_cnt > 0 THEN 0 ELSE 1 END as is_checked, id, date_receive, customer, email, email_customer, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, mail_cnt, mail_note, remark, picname, crt_time, crt_user, 1 as is_edited, photo FROM receive_record where batch_num = $record and date_receive <> '' and status = ''  and customer = ? ORDER BY date_receive  ";

                    if ($stmt = mysqli_prepare($conn, $subquery)) {

                        mysqli_stmt_bind_param($stmt, "s", $row['customer']);

                        /* execute query */
                        mysqli_stmt_execute($stmt);

                        $result1 = mysqli_stmt_get_result($stmt);

                        if($result1 != null)
            {
                while($row = mysqli_fetch_assoc($result1))
                {
                    $is_edited = $row['is_edited'];
                    $is_checked = $row['is_checked'];
                    $id = $row['id'];
                    $date_receive = $row['date_receive'];
                    $customer = $row['customer'];
                    $email_customer = $row['email_customer'];
                    $email = $row['email'];
                    $description = $row['description'];
                    $quantity = $row['quantity'];
                    $supplier = $row['supplier'];
                    $kilo = $row['kilo'];
                    $cuft = $row['cuft'];
                    $taiwan_pay = $row['taiwan_pay'];
                    $courier_pay = $row['courier_pay'];
                    $courier_money = $row['courier_money'];
                    $remark = $row['remark'];
                    $picname = $row['picname'];
                    $photo = $row['photo'];
                    $crt_time = $row['crt_time'];
                    $crt_user = $row['crt_user'];
                    $mail_cnt = $row['mail_cnt'];
                    $mail_note = $row['mail_note'];
                    
                    $pic = GetPic($picname, $photo, $id, $conn);

                    $merged_results[] = array(
                        "is_edited" => $is_edited,
                        "is_checked" => $is_checked,
                        "id" => $id,
                        "date_receive" => $date_receive,
                        "customer" => $customer,
                        "email_customer" => $email_customer,
                        "email" => $email,
                        "description" => $description,
                        "quantity" => $quantity,
                        "supplier" => $supplier,
                        "kilo" => $kilo,
                        "cuft" => $cuft,
                        "taiwan_pay" => $taiwan_pay,
                        "courier_pay" => $courier_pay,
                        "courier_money" => $courier_money,
                        "remark" => $remark,
                        "picname" => $picname,
                        "photo" => $photo,
                        "crt_time" => $crt_time,
                        "crt_user" => $crt_user,
                        "mail_note" => $mail_note,
                        "mail_cnt" => $mail_cnt,

                        "pic" => $pic,
                    
                    );
                }
            
            }
                    }
                }
            }

            $subquery = "SELECT CASE WHEN mail_cnt > 0 THEN 0 ELSE 1 END as is_checked, id, date_receive, customer, email, email_customer, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, mail_cnt, mail_note, remark, picname, crt_time, crt_user, 1 as is_edited, photo  FROM receive_record where batch_num = $record and date_receive = '' and status = ''  ORDER BY id";

            // $result1 = mysqli_query($conn, $subquery);

            $result1 = mysqli_query($conn,$subquery);
            if($result1 != null)
            {
                while($row = mysqli_fetch_assoc($result1))
                {
                    $is_edited = $row['is_edited'];
                    $is_checked = $row['is_checked'];
                    $id = $row['id'];
                    $date_receive = $row['date_receive'];
                    $customer = $row['customer'];
                    $email_customer = $row['email_customer'];
                    $email = $row['email'];
                    $description = $row['description'];
                    $quantity = $row['quantity'];
                    $supplier = $row['supplier'];
                    $kilo = $row['kilo'];
                    $cuft = $row['cuft'];
                    $taiwan_pay = $row['taiwan_pay'];
                    $courier_pay = $row['courier_pay'];
                    $courier_money = $row['courier_money'];
                    $remark = $row['remark'];
                    $picname = $row['picname'];
                    $photo = $row['photo'];
                    $crt_time = $row['crt_time'];
                    $crt_user = $row['crt_user'];
                    $mail_cnt = $row['mail_cnt'];
                    $mail_note = $row['mail_note'];
                    
                    $pic = GetPic($picname, $photo, $id, $conn);

                    $merged_results[] = array(
                        "is_edited" => $is_edited,
                        "is_checked" => $is_checked,
                        "id" => $id,
                        "date_receive" => $date_receive,
                        "customer" => $customer,
                        "email_customer" => $email_customer,
                        "email" => $email,
                        "description" => $description,
                        "quantity" => $quantity,
                        "supplier" => $supplier,
                        "kilo" => $kilo,
                        "cuft" => $cuft,
                        "taiwan_pay" => $taiwan_pay,
                        "courier_pay" => $courier_pay,
                        "courier_money" => $courier_money,
                        "remark" => $remark,
                        "picname" => $picname,
                        "photo" => $photo,
                        "crt_time" => $crt_time,
                        "crt_user" => $crt_user,
                        "mail_note" => $mail_note,
                        "mail_cnt" => $mail_cnt,

                        "pic" => $pic,
                    
                    );
                }
            
            }
            //while ($row = mysqli_fetch_assoc($result1))
            //    $merged_results[] = $row;


            echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
            break;
        }

        if (!empty($_GET['page'])) {
            $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
            if (false === $page) {
                $page = 1;
            }
        }

        if (!empty($_GET['size'])) {
            $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
            if (false === $size) {
                $size = 10;
            }

            $offset = ($page - 1) * $size;

            $sql = $sql . " LIMIT " . $offset . "," . $size;
        }

        // run SQL statement
        $result = mysqli_query($conn, $sql);

        // die if SQL statement failed
        if (!$result) {
            http_response_code(404);
            die(mysqli_error($conn));
        }

        if (!$id) echo '[';
        for ($i = 0; $i < mysqli_num_rows($result); $i++) {
            echo ($i > 0 ? ',' : '') . json_encode(mysqli_fetch_object($result), JSON_UNESCAPED_SLASHES);
        }
        if (!$id) echo ']';
        elseif ($method == 'POST')
            echo json_encode($result);
        else
            echo mysqli_affected_rows($conn);
        break;

    case 'POST':
        $shipping_mark = stripslashes($_POST["shipping_mark"]);
        $estimate_weight = stripslashes($_POST["estimate_weight"]);
        $actual_weight = stripslashes($_POST["actual_weight"]);
        $container_number = stripslashes($_POST["container_number"]);
        $seal = stripslashes($_POST["seal"]);
        $so = stripslashes($_POST["so"]);
        $ship_company = stripslashes($_POST["ship_company"]);
        $ship_boat = stripslashes($_POST["ship_boat"]);
        $neck_cabinet = stripslashes($_POST["neck_cabinet"]);
        $shipper = stripslashes($_POST["shipper"]);
        $date_sent = stripslashes($_POST["date_sent"]);
        $etd_date = stripslashes($_POST["etd_date"]);
        $ob_date = stripslashes($_POST["ob_date"]);
        $eta_date = stripslashes($_POST["eta_date"]);
        $date_arrive = stripslashes($_POST["date_arrive"]);
        $broker = stripslashes($_POST["broker"]);
        $remark = stripslashes($_POST["remark"]);
        $record = stripslashes($_POST["record"]);
        $crud = stripslashes($_POST["crud"]);
        $id = stripslashes($_POST["id"]);

        switch ($crud) {
            case 'insert':
                /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
                $sql = "insert into loading (shipping_mark, 
                									  estimate_weight, 
                									  actual_weight, 
                									  container_number, 
                									  seal,
                									  so,
                									  ship_company,
                									  ship_boat,
                									  neck_cabinet,
                                                      shipper,
                									  date_sent,
                									  etd_date,
                									  ob_date,
                									  eta_date,
                                    date_arrive,
                									  broker,
                									  remark,
                                                      crt_user) 
                							values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param(
                    "sddssssssdssssssss",
                    $shipping_mark,
                    $estimate_weight,
                    $actual_weight,
                    $container_number,
                    $seal,
                    $so,
                    $ship_company,
                    $ship_boat,
                    $neck_cabinet,
                    $shipper,
                    $date_sent,
                    $etd_date,
                    $ob_date,
                    $eta_date,
                    $date_arrive,
                    $broker,
                    $remark,
                    $user
                );
                $stmt->execute();
                $stmt->close();

                $last_id = mysqli_insert_id($conn);

                $sql = "update receive_record set batch_num = $last_id,
                                                      mdf_time = now(),
                                                      mdf_user = '$user'
                                            where id in($record)";
                $query = $conn->query($sql);

                $sql = "insert into loading_date_history (loading_id, 
                                    date_sent,
                                    etd_date,
                                    ob_date,
                                    eta_date,
                                    date_arrive) 
                              values (?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param(
                    "dssss",
                    $last_id,
                    $date_sent,
                    $etd_date,
                    $ob_date,
                    $eta_date,
                    $date_arrive
                );

                $stmt->execute();
                $stmt->close();

                if ($query) {
                    $out['message'] = "Load Successfully";
                } else {
                    $out['error'] = true;
                    $out['message'] = "Could not Load Goods";
                }

                break;

            case "update":

                /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
                $sql = "update loading set shipping_mark = ?, 
                                                          estimate_weight = ?, 
                                                          actual_weight = ?, 
                                                          container_number = ?, 
                                                          seal = ?,
                                                          so = ?,
                                                          ship_company = ?,
                                                          ship_boat = ?,
                                                          neck_cabinet = ?,
                                                          shipper = ?,
                                                          date_sent = ?,
                                                          etd_date = ?,
                                                          ob_date = ?,
                                                          eta_date = ?,
                                                          date_arrive = ?,
                                                          broker = ?,
                									      remark = ?,
                                                          mdf_time = now(),
                                                          mdf_user = ?
                                                where id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param(
                    "sddssssssdssssssssd",
                    $shipping_mark,
                    $estimate_weight,
                    $actual_weight,
                    $container_number,
                    $seal,
                    $so,
                    $ship_company,
                    $ship_boat,
                    $neck_cabinet,
                    $shipper,
                    $date_sent,
                    $etd_date,
                    $ob_date,
                    $eta_date,
                    $date_arrive,
                    $broker,
                    $remark,
                    $user,
                    $id
                );
                $stmt->execute();
                $affected_rows = mysqli_stmt_num_rows($stmt);
                $stmt->close();

                $sql = "update receive_record set batch_num = 0,
                                                      mdf_time = now(),
                                                      mdf_user = '$user'
                                            where batch_num = $id";
                $query = $conn->query($sql);

                $sql = "update receive_record set batch_num = $id,
                                                      mdf_time = now(),
                                                      mdf_user = '$user'
                                            where id in($record)";
                $query = $conn->query($sql);

                $sql = "SELECT date_sent, etd_date, ob_date, eta_date, date_arrive FROM loading_date_history  where loading_id=$id";
                $result = mysqli_query($conn, $sql);

                // die if SQL statement failed
                while ($row = mysqli_fetch_array($result)) {
                    $date_sent_ary = explode(",", $row['date_sent']);
                    $etd_date_ary = explode(",", $row['etd_date']);
                    $ob_date_ary = explode(",", $row['ob_date']);
                    $eta_date_ary = explode(",", $row['eta_date']);
                    $date_arrive_ary = explode(",", $row['date_arrive']);
                }

                if (!in_array($date_sent, $date_sent_ary)) {
                    array_push($date_sent_ary, $date_sent);
                }

                if (!in_array($etd_date, $etd_date_ary)) {
                    array_push($etd_date_ary, $etd_date);
                }

                if (!in_array($ob_date, $ob_date_ary)) {
                    array_push($ob_date_ary, $ob_date);
                }

                if (!in_array($eta_date, $eta_date_ary)) {
                    array_push($eta_date_ary, $eta_date);
                }

                if (!in_array($date_arrive, $date_arrive_ary)) {
                    array_push($date_arrive_ary, $date_arrive);
                }

                $date_sent_str = ltrim(implode(",", $date_sent_ary), ",");
                $etd_date_str = ltrim(implode(",", $etd_date_ary), ",");
                $ob_date_str = ltrim(implode(",", $ob_date_ary), ",");
                $eta_date_str = ltrim(implode(",", $eta_date_ary), ",");
                $date_arrive_str = ltrim(implode(",", $date_arrive_ary), ",");


                $sql = "update loading_date_history set date_sent = '$date_sent_str',
                                                      etd_date = '$etd_date_str',
                                                      ob_date = '$ob_date_str',
                                                      eta_date = '$eta_date_str',
                                                      date_arrive = '$date_arrive_str'
                                            where loading_id = $id";
                $query = $conn->query($sql);

                $sql = "update measure_ph set date_arrive = '$date_arrive' where id = (select measure_num from loading where id = $id)";
                $query = $conn->query($sql);

                $sql = "update loading set date_arrive = '$date_arrive' where measure_num in (select * from (select measure_num from loading where id = $id) as t)";
                $query = $conn->query($sql);

                echo $affected_rows;

                break;

            case 'del':
                $sql = "update loading set status = 'D', del_time = now(), del_user = '$user' where id in ($id)";

                $query = $conn->query($sql);

                $sql = "update receive_record set batch_num = 0, mdf_time = now(), mdf_user = '$user' where batch_num in ($id)";

                $query = $conn->query($sql);

                if ($query) {
                    $out['message'] = "Member Deleted Successfully";
                } else {
                    $out['error'] = true;
                    $out['message'] = "Could not delete Member";
                }

                break;

            case 'del_all':
                $sql = "update loading set status = 'D', del_time = now(), del_user = '$user' where id in ($id)";

                $query = $conn->query($sql);

                $sql = "update receive_record set status = 'D', del_time = now(), del_user = '$user' where batch_num in ($id)";

                $query = $conn->query($sql);

                if ($query) {
                    $out['message'] = "Member Deleted Successfully";
                } else {
                    $out['error'] = true;
                    $out['message'] = "Could not delete Member";
                }

                break;
        }

        break;
}

// Close connection
mysqli_close($conn);
