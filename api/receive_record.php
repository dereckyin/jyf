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

require_once "db.php";

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

function insertContactor($customer, $supplier, $user, $conn) {
    $needToInsert = 0;

    $customer =trim($customer);
    $supplier = trim($supplier);

    $hadData = 0;
   if(trim($customer) != '')
    {
        $sql = "select customer from contactor where customer = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {

            mysqli_stmt_bind_param($stmt, "s", $customer);
        
            /* execute query */
            mysqli_stmt_execute($stmt);

            $result1 = mysqli_stmt_get_result($stmt);

            while($row = mysqli_fetch_assoc($result1)) {
                $hadData = 1;
            }

            if(!$hadData)
                $needToInsert = 1;
        }
    }

    $hadData = 0;
    if(trim($supplier) != '')
    {
        $sql = "select supplier from contactor where supplier = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {

            mysqli_stmt_bind_param($stmt, "s", $supplier);
        
            /* execute query */
            mysqli_stmt_execute($stmt);

            $result1 = mysqli_stmt_get_result($stmt);

            while($row = mysqli_fetch_assoc($result1)) {
                $hadData = 1;
            }
        }

        if(!$hadData)
            $needToInsert = 1;
    }


    if($needToInsert == 1)
    {
        $sql = "insert into contactor (customer, supplier, crt_user) 
                                    values (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $customer, $supplier, $user);
        $stmt->execute();

        $last_id = mysqli_insert_id($conn);
    }

}

function sendMail($email, $date, $customer,  $desc, $amount, $supplier, $pic_mail_array) {
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

    $mail->IsHTML(true);
    $mail->AddAddress($email, $customer);
    //$mail->Subject = "=?utf-8?B?" . base64_encode("信件標題") . "?=";
    $mail->SetFrom("servictoryshipment@gmail.com", "servictoryshipment");
    $mail->AddReplyTo("servictoryshipment@gmail.com", "servictoryshipment");
    // $mail->AddCC("tryhelpbuy@gmail.com", "tryhelpbuy");
    $mail->Subject = "SERVICTORY ADVISORY";
    $content = "<p>ADVISORY ONLY !!!</p>";
    $content = $content . "<p>Greetings! Pls be advised that our taiwan office have already received your goods sent on :</p>";
    $content = $content . "<p>" . $date . " " . $customer . "</p>";
    $content = $content . "<p>" . $desc . " " . $amount . " " . $supplier . "</p>";
    for($i=0; $i<count($pic_mail_array); $i++)
        $content = $content . "<a href='" . $pic_mail_array[$i] . "'>" . "照片(picture)" . "</a>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>Note:first in first out, priority will be given to early senders</p>";
    $content = $content . "<p>We will send another advisory for the</p>";
    $content = $content . "<p>Tentative of this goods. Thank you</p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p> </p>";
    $content = $content . "<p>-SERVICTORY</p>";

    $mail->MsgHTML($content);
    if(!$mail->Send()) {
        echo "Error while sending Email.";
        var_dump($mail);
    } else {
        echo "Email sent successfully";
    }
}

      header('Access-Control-Allow-Origin: *');  

      $method = $_SERVER['REQUEST_METHOD'];

    $user = $decoded->data->username;
    $create_id = $decoded->data->id;

      switch ($method) {
          case 'GET':
            $id = stripslashes((isset($_GET['id']) ?  $_GET['id'] : ""));
            $page = stripslashes((isset($_GET['page']) ?  $_GET['page'] : ""));
            $size = stripslashes((isset($_GET['size']) ?  $_GET['size'] : ""));

              $subquery = "";

              $merged_results = array();

              $key=array();

            $sql = "SELECT customer FROM  receive_record where batch_num = 0 and date_receive <> '' and status = ''  GROUP BY date_receive, customer  ORDER BY date_receive;";

            // $sql = "CALL createReceiveList(); ";
            // run SQL statement
            $result = mysqli_query($conn,$sql);

            /* fetch data */
            while ($row = mysqli_fetch_array($result)){
                if (isset($row)){

                    if (in_array(strtolower($row['customer']),$key))
                    {
                        continue;
                    }
                    else
                    {
                        array_push($key, strtolower($row['customer']));
                    }

                        $subquery = "SELECT 0 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, photo, crt_time, crt_user FROM receive_record where batch_num = 0 and date_receive <> '' and status = ''  and customer = ? ORDER BY date_receive  ";

                        if ($stmt = mysqli_prepare($conn, $subquery)) {

                            mysqli_stmt_bind_param($stmt, "s", $row['customer']);
                        
                            /* execute query */
                            mysqli_stmt_execute($stmt);

                            $result1 = mysqli_stmt_get_result($stmt);

                            while($row = mysqli_fetch_assoc($result1)) {
                                $is_checked = $row['is_checked'];
                                $id = $row['id'];
                                $date_receive = $row['date_receive'];
                                $customer = $row['customer'];
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
                                
                                $pic = GetPic($picname, $photo, $id, $conn);

                                $merged_results[] = array(
                                    "is_checked" => $is_checked,
                                    "id" => $id,
                                    "date_receive" => $date_receive,
                                    "customer" => $customer,
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

                                    "pic" => $pic,
                                
                                );
                                
                            }
                        }
                    //         $result1 = mysqli_query($conn,$subquery);

                    //         if($result1 != null)
                    //         {
                    // while($row = mysqli_fetch_assoc($result1))
                    //     $merged_results[] = $row;
                // }



                }
            }

            $subquery = "SELECT 0 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, photo, crt_time, crt_user  FROM receive_record where batch_num = 0 and date_receive = '' and status = ''  ORDER BY id";

            $result1 = mysqli_query($conn,$subquery);
            if($result1 != null)
            {
                while($row = mysqli_fetch_assoc($result1))
                {
                    $is_checked = $row['is_checked'];
                    $id = $row['id'];
                    $date_receive = $row['date_receive'];
                    $customer = $row['customer'];
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
                    
                    $pic = GetPic($picname, $photo, $id, $conn);

                    $merged_results[] = array(
                        "is_checked" => $is_checked,
                        "id" => $id,
                        "date_receive" => $date_receive,
                        "customer" => $customer,
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

                        "pic" => $pic,
                    
                    );
                }
            
            }

            // die if SQL statement failed
            if (!$merged_results) {
                  http_response_code(404);
                  die(mysqli_error($conn));
            }

             echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
            //if (!$id) echo '[';
            //for ($i=0 ; $i<mysqli_num_rows($merged_results) ; $i++) {
            //      echo ($i>0?',':'').json_encode(mysqli_fetch_object($merged_results), JSON_UNESCAPED_SLASHES);
            //}
            //if (!$id) echo ']';
            //elseif ($method == 'POST')
            //      echo json_encode($result);
            //else
            //      echo mysqli_affected_rows($conn);
            break;

          case 'POST':
            $date_receive = (isset($_POST['date_receive']) ?  $_POST['date_receive'] : '');
            $customer = (isset($_POST['customer']) ?  $_POST['customer'] : '');
            $email = (isset($_POST['email']) ?  $_POST['email'] : '');
            $description = (isset($_POST['description']) ?  $_POST['description'] : '');
            $quantity = (isset($_POST['quantity']) ?  $_POST['quantity'] : '');
            $supplier = (isset($_POST['supplier']) ?  $_POST['supplier'] : '');
            $kilo = (isset($_POST['kilo']) ?  $_POST['kilo'] : '');
            $cuft = (isset($_POST['cuft']) ?  $_POST['cuft'] : '');
            $taiwan_pay = (isset($_POST['taiwan_pay']) ?  $_POST['taiwan_pay'] : 0);
            $courier_pay = (isset($_POST['courier_pay']) ?  $_POST['courier_pay'] : 0);
            $courier_money = (isset($_POST['courier_money']) ?  $_POST['courier_money'] : '');
            $remark = (isset($_POST['remark']) ?  $_POST['remark'] : '');
            $photo = (isset($_POST['photo']) ?  $_POST['photo'] : '');
            $crud = (isset($_POST['crud']) ?  $_POST['crud'] : '');
            $id = (isset($_POST['id']) ?  $_POST['id'] : 0);

            $pic = (isset($_POST['pic']) ?  $_POST['pic'] : '[]');

            $pic_array = json_decode($pic,true);

            $taiwan_pay = ($taiwan_pay ? $taiwan_pay : 0);
            $courier_pay = ($courier_pay ? $courier_pay : 0);

            $date_receive = trim($date_receive);
            $customer = trim($customer);

            switch ($crud) 
            {
              case 'insert':
                $filename = "";

                if(isset($_FILES['file']['name'])) {
                    $key = "myKey";
                    $time = time();
                    $hash = hash_hmac('sha256', $time, $key);
                    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                    $filename = $time . $hash . "." . $ext;
                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $conf::$upload_path . $filename)) {
                        echo "done";
                    }
                }

            $library = "";
            if($photo != "")
            {
                $library = "RECEIVE";
            }

          	/* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
                $sql = "insert into receive_record (date_receive, 
                									  customer, 
                									  email, 
                									  description, 
                									  quantity,
                									  supplier,
                									  picname,
                									  photo,
                									  kilo,
                									  cuft,
                									  taiwan_pay,
                									  courier_pay,
                									  courier_money,
                									  remark,
                                                      crt_user) 
                							values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql); 
                $stmt->bind_param("ssssssssddiiiss",
                					$date_receive, 
                					$customer, 
                					$email, 
                					$description, 
                					$quantity, 
                					$supplier,
                					$filename,
                					$library,
                					$kilo, 
                					$cuft, 
                					$taiwan_pay, 
                					$courier_pay, 
                					$courier_money, 
                					$remark,
                                    $user);
                $stmt->execute();
                $stmt->close();

                $last_id = mysqli_insert_id($conn);

                // update library
                if($photo != "")
                {
                    $batch_id = $last_id;
                    $query = "update gcp_storage_file
                        SET
                            batch_id = ?,
                            batch_type = ?,
        
                            create_id = ?,
                            created_at = now()
                        where id in (?) and batch_type = 'LIBRARY'
                    ";

                    // prepare the query
                    $stmt = $conn->prepare($query);

                    // bind the values
                    $stmt->bind_param(
                        "isis",
                        $batch_id,
                        $library,
                        $create_id,
                        $photo
                    );

                    try {
                        // execute the query, also check if query was successful
                        if ($stmt->execute()) {
                            $last_id = mysqli_insert_id($conn);
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

                insertContactor($customer, $supplier, $user, $conn);

                echo $last_id;

                break;

            case 'insert_mail':

                $filename = "";

                if(isset($_FILES['file']['name'])) {
                    $key = "myKey";
                    $time = time();
                    $hash = hash_hmac('sha256', $time, $key);
                    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                    $filename = $time . $hash . "." . $ext;
                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $conf::$upload_path . $filename)) {
                        echo "done";
                    }
                }

                $pic_mail_array = array();

                $library = "";
                if($photo != "")
                {
                    $library = "RECEIVE";
                    $sql = "SELECT gcp_name FROM  gcp_storage_file where id in (" . $photo . ")";

                    $result = mysqli_query($conn,$sql);

                    /* fetch data */
                    while ($row = mysqli_fetch_array($result)){
                        if (isset($row)){
                            array_push($pic_mail_array, 'https://storage.googleapis.com/feliiximg/' . $row['gcp_name']);
                        }
                    }
                }

            /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
                $sql = "insert into receive_record (date_receive, 
                                                      customer, 
                                                      email, 
                                                      description, 
                                                      quantity,
                                                      supplier,
                                                      picname,
                                                      photo,
                                                      kilo,
                                                      cuft,
                                                      taiwan_pay,
                                                      courier_pay,
                                                      courier_money,
                                                      remark,
                                                      crt_user) 
                                            values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql); 
                $stmt->bind_param("ssssssssddiiiss",
                                    $date_receive, 
                                    $customer, 
                                    $email, 
                                    $description, 
                                    $quantity, 
                                    $supplier,
                                    $filename,
                                    $kilo, 
                                    $cuft, 
                                    $taiwan_pay, 
                                    $courier_pay, 
                                    $courier_money, 
                                    $remark,
                                    $user);
                $stmt->execute();
                $stmt->close();

                
                

                if($email != "")
                    sendMail($email, $date_receive, $customer, $description, $quantity, $supplier, $pic_mail_array);

                $last_id = mysqli_insert_id($conn);

                // update library
                if($photo != "")
                {
                    $batch_id = $last_id;
                    $query = "update gcp_storage_file
                        SET
                            batch_id = ?,
                            batch_type = ?,
        
                            create_id = ?,
                            created_at = now()
                        where id in (?) and batch_type = 'LIBRARY'
                    ";

                    // prepare the query
                    $stmt = $conn->prepare($query);

                    // bind the values
                    $stmt->bind_param(
                        "isis",
                        $batch_id,
                        $library,
                        $create_id,
                        $photo
                    );

                    try {
                        // execute the query, also check if query was successful
                        if ($stmt->execute()) {
                            $last_id = mysqli_insert_id($conn);
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

                insertContactor($customer, $supplier, $user, $conn);

                echo $last_id;

                break;

            case "update":
                $filename = "";
                $pic_name = "";
                $library = "";
                $photo = "";
                $stringarray = array();

                for($i=0 ; $i < count($pic_array) ; $i++)
                {
                    if($pic_array[$i]['type'] == 'FILE'){
                        if($pic_array[$i]['is_checked'] == true)
                            $pic_name = $pic_array[$i]['gcp_name'];
                        else
                            $pic_name = "";
                    }

                    if($pic_array[$i]['type'] == 'RECEIVE'){
                        if($pic_array[$i]['is_checked'] == true)
                            array_push($stringarray,$pic_array[$i]['pid']);
                    }
                }

                $photo = implode(",",$stringarray);
                if($photo != "")
                {
                    $library = "RECEIVE";
                }


                if(isset($_FILES['file']['name'])) {
                    $key = "myKey";
                    $time = time();
                    $hash = hash_hmac('sha256', $time, $key);
                    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                    $filename = $time . $hash . "." . $ext;
                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $conf::$upload_path . $filename)) {
                        echo "done";
                    }
                }

                if($filename != "")
                    {
                    /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
                    $sql = "update receive_record set date_receive = ?, 
                                                          customer = ?, 
                                                          email = ?, 
                                                          description = ?, 
                                                          quantity = ?,
                                                          supplier = ?,
                                                          picname = ?,
                                                          photo = ?,
                                                          kilo = ?,
                                                          cuft = ?,
                                                          taiwan_pay = ?,
                                                          courier_pay = ?,
                                                          courier_money = ?,
                                                          remark = ?,
                                                          mdf_time = now(),
                                                          mdf_user = ?
                                                where id = ?";
                    $stmt = $conn->prepare($sql); 
                    $stmt->bind_param("ssssssssddiiissd",
                                        $date_receive, 
                                        $customer, 
                                        $email, 
                                        $description, 
                                        $quantity, 
                                        $supplier,
                                        $filename,
                                        $library,
                                        $kilo, 
                                        $cuft, 
                                        $taiwan_pay, 
                                        $courier_pay, 
                                        $courier_money, 
                                        $remark,
                                        $user,
                                        $id);
                    $stmt->execute();
                    $affected_rows = mysqli_stmt_num_rows($stmt);
                    $stmt->close();
                }
                else
                {
                    /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
                    $sql = "update receive_record set date_receive = ?, 
                                                          customer = ?, 
                                                          email = ?, 
                                                          description = ?, 
                                                          quantity = ?,
                                                          supplier = ?,
                                                          picname = ?,
                                                          photo = ?,
                                                          kilo = ?,
                                                          cuft = ?,
                                                          taiwan_pay = ?,
                                                          courier_pay = ?,
                                                          courier_money = ?,
                                                          remark = ?,
                                                          mdf_time = now(),
                                                          mdf_user = ?
                                                where id = ?";
                    $stmt = $conn->prepare($sql); 
                    $stmt->bind_param("ssssssssddiiissd",
                                        $date_receive, 
                                        $customer,  
                                        $email, 
                                        $description, 
                                        $quantity, 
                                        $supplier,
                                        $pic_name,
                                        $library,
                                        $kilo, 
                                        $cuft, 
                                        $taiwan_pay, 
                                        $courier_pay, 
                                        $courier_money, 
                                        $remark,
                                        $user,
                                        $id);
                    $stmt->execute();
                    $affected_rows = mysqli_stmt_num_rows($stmt);
                    $stmt->close();
                }

                $library = "LIBRARY";
                $batch_id = $id;
                $query = "update gcp_storage_file
                    SET
                        batch_id = batch_id_org,
                        batch_type = ?
    
                    where batch_id = ? and batch_type = 'RECEIVE'
                ";

                // prepare the query
                $stmt = $conn->prepare($query);

                // bind the values
                $stmt->bind_param(
                    "si",
                    $library,
                    $batch_id
                );

                try {
                    // execute the query, also check if query was successful
                    if ($stmt->execute()) {
                        $last_id = mysqli_insert_id($conn);
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

                // update library
                if($photo != "")
                {
                    $library = "RECEIVE";

                    $batch_id = $id;
                    $query = "update gcp_storage_file
                        SET
                            batch_id = ?,
                            batch_type = ?,
        
                            create_id = ?,
                            created_at = now()
                        where id in (" . $photo . ") and batch_type = 'LIBRARY'
                    ";

                    // prepare the query
                    $stmt = $conn->prepare($query);

                    // bind the values
                    $stmt->bind_param(
                        "isi",
                        $batch_id,
                        $library,
                        $create_id
                    );

                    try {
                        // execute the query, also check if query was successful
                        if ($stmt->execute()) {
                            $last_id = mysqli_insert_id($conn);
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

                

                echo $affected_rows;

                break;

              case "update_mail":
                $filename = "";
                $pic_name = "";
                $library = "";
                $photo = "";
                $stringarray = array();

                $pic_mail_array = array();

                for($i=0 ; $i < count($pic_array) ; $i++)
                {
                    if($pic_array[$i]['type'] == 'FILE'){
                        if($pic_array[$i]['is_checked'] == true)
                        {
                            $pic_name = $pic_array[$i]['gcp_name'];
                            array_push($pic_mail_array, 'https://webmatrix.myvnc.com/img/' . $pic_name);
                        }
                        else
                            $pic_name = "";
                    }

                    if($pic_array[$i]['type'] == 'RECEIVE'){
                        if($pic_array[$i]['is_checked'] == true)
                        {
                            array_push($stringarray,$pic_array[$i]['pid']);
                            array_push($pic_mail_array, 'https://storage.googleapis.com/feliiximg/' . $pic_array[$i]['gcp_name']);
                        }
                    }
                }

                $photo = implode(",",$stringarray);
                if($photo != "")
                {
                    $library = "RECEIVE";
                }

                if(isset($_FILES['file']['name'])) {
                    $key = "myKey";
                    $time = time();
                    $hash = hash_hmac('sha256', $time, $key);
                    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                    $filename = $time . $hash . "." . $ext;
                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $conf::$upload_path . $filename)) {
                        echo "done";
                    }
                }

                if($filename != "")
                    {
                    /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
                    $sql = "update receive_record set date_receive = ?, 
                                                          customer = ?, 
                                                          email = ?, 
                                                          description = ?, 
                                                          quantity = ?,
                                                          supplier = ?,
                                                          picname = ?,
                                                          photo = ?,
                                                          kilo = ?,
                                                          cuft = ?,
                                                          taiwan_pay = ?,
                                                          courier_pay = ?,
                                                          courier_money = ?,
                                                          remark = ?,
                                                          mdf_time = now(),
                                                          mdf_user = ?
                                                where id = ?";
                    $stmt = $conn->prepare($sql); 
                    $stmt->bind_param("ssssssssddiiissd",
                                        $date_receive, 
                                        $customer, 
                                        $email, 
                                        $description, 
                                        $quantity, 
                                        $supplier,
                                        $filename,
                                        $library,
                                        $kilo, 
                                        $cuft, 
                                        $taiwan_pay, 
                                        $courier_pay, 
                                        $courier_money, 
                                        $remark,
                                        $user,
                                        $id);
                    $stmt->execute();
                    $affected_rows = mysqli_stmt_num_rows($stmt);
                    $stmt->close();
                }
                else
                {
                    /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
                    $sql = "update receive_record set date_receive = ?, 
                                                          customer = ?, 
                                                          email = ?, 
                                                          description = ?, 
                                                          quantity = ?,
                                                          supplier = ?,
                                                          picname = ?,
                                                          photo = ?,
                                                          kilo = ?,
                                                          cuft = ?,
                                                          taiwan_pay = ?,
                                                          courier_pay = ?,
                                                          courier_money = ?,
                                                          remark = ?,
                                                          mdf_time = now(),
                                                          mdf_user = ?
                                                where id = ?";
                    $stmt = $conn->prepare($sql); 
                    $stmt->bind_param("ssssssssddiiissd",
                                        $date_receive, 
                                        $customer, 
                                        $email, 
                                        $description, 
                                        $quantity, 
                                        $supplier,
                                        $pic_name,
                                        $library,
                                        $kilo, 
                                        $cuft, 
                                        $taiwan_pay, 
                                        $courier_pay, 
                                        $courier_money, 
                                        $remark,
                                        $user,
                                        $id);
                    $stmt->execute();
                    $affected_rows = mysqli_stmt_num_rows($stmt);
                    $stmt->close();
                }

                
                $library = "LIBRARY";
                $batch_id = $id;
                $query = "update gcp_storage_file
                    SET
                        batch_id = batch_id_org,
                        batch_type = ?
    
                    where batch_id = ? and batch_type = 'RECEIVE'
                ";

                // prepare the query
                $stmt = $conn->prepare($query);

                // bind the values
                $stmt->bind_param(
                    "si",
                    $library,
                    $batch_id
                );

                try {
                    // execute the query, also check if query was successful
                    if ($stmt->execute()) {
                        $last_id = mysqli_insert_id($conn);
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

                // update library
                if($photo != "")
                {
                    $library = "RECEIVE";

                    $batch_id = $id;
                    $query = "update gcp_storage_file
                        SET
                            batch_id = ?,
                            batch_type = ?,
        
                            create_id = ?,
                            created_at = now()
                        where id in (" . $photo . ") and batch_type = 'LIBRARY'
                    ";

                    // prepare the query
                    $stmt = $conn->prepare($query);

                    // bind the values
                    $stmt->bind_param(
                        "isi",
                        $batch_id,
                        $library,
                        $create_id
                    );

                    try {
                        // execute the query, also check if query was successful
                        if ($stmt->execute()) {
                            $last_id = mysqli_insert_id($conn);
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

                if($email != "")
                    sendMail($email, $date_receive, $customer, $description, $quantity, $supplier, $pic_mail_array);

                echo $affected_rows;

                break;

              case 'del':
                $sql = "update receive_record set status = 'D', del_time = now(), del_user = '$user' where id in ($id)";
                
                $query = $conn->query($sql);
     
                if($query){
                    $out['message'] = "Member Deleted Successfully";
                }
                else{
                    $out['error'] = true;
                    $out['message'] = "Could not delete Member";
                }
               
                break;
            }

            break;
      }

      // Close connection
      mysqli_close($conn);


?>
