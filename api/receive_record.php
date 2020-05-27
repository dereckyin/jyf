<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'config/conf.inc';
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

$conf = new Conf();

function sendMail($email, $date, $customer,  $desc, $amount, $supplier, $pic) {
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
    if($pic != '')
        $content = $content . "<a href='https://webmatrix.myvnc.com/img/" . $pic . "'>" . "照片(picture)" . "</a>";
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

      require_once "db.php";

      $method = $_SERVER['REQUEST_METHOD'];

    $user = $decoded->data->username;

      switch ($method) {
          case 'GET':
            $id = mysqli_real_escape_string($conn, (isset($_GET['id']) ?  $_GET['id'] : ""));
            $page = mysqli_real_escape_string($conn, (isset($_GET['page']) ?  $_GET['page'] : ""));
            $size = mysqli_real_escape_string($conn, (isset($_GET['size']) ?  $_GET['size'] : ""));
/*
            $sql = "( SELECT 0 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, crt_time, crt_user  FROM receive_record where batch_num = 0 and date_receive <> '' and status = '' ".($id ? " and id=$id" : ''); 

            $sql = $sql . " ORDER BY date_receive, customer) ";

            $sql = $sql . " union ";

            $sql = $sql . " (SELECT 0 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, crt_time, crt_user  FROM receive_record where batch_num = 0 and date_receive = '' and status = '' ".($id ? " and id=$id" : ''); 

            $sql = $sql . " ORDER BY customer)";


            if(!empty($_GET['page'])) {
                $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
                if(false === $page) {
                    $page = 1;
                }
            }

            if(!empty($_GET['size'])) {
                $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
                if(false === $size) {
                    $size = 10;
                }

                $offset = ($page - 1) * $size;

                $sql = $sql . " LIMIT " . $offset . "," . $size;
            }
*/
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

                    if (in_array($row['customer'],$key))
                    {
                        continue;
                    }
                    else
                    {
                        array_push($key, $row['customer']);
                    }

                        $subquery = "SELECT 0 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, crt_time, crt_user FROM receive_record where batch_num = 0 and date_receive <> '' and status = ''  and customer = '" . $row['customer']. "' ORDER BY date_receive  ";

                            $result1 = mysqli_query($conn,$subquery);

                    while($row = mysqli_fetch_assoc($result1))
                        $merged_results[] = $row;



                }
            }

            $subquery = "SELECT 0 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, crt_time, crt_user  FROM receive_record where batch_num = 0 and date_receive = '' and status = ''  ORDER BY customer";

              $result1 = mysqli_query($conn,$subquery);
              while($row = mysqli_fetch_assoc($result1))
                  $merged_results[] = $row;

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
            $date_receive = mysqli_real_escape_string($conn, $_POST["date_receive"]);
            $customer = mysqli_real_escape_string($conn, $_POST["customer"]);
            $email = mysqli_real_escape_string($conn, $_POST["email"]);
            $description = mysqli_real_escape_string($conn, $_POST["description"]);
            $quantity = mysqli_real_escape_string($conn, $_POST["quantity"]);
            $supplier = mysqli_real_escape_string($conn, $_POST["supplier"]);
            $kilo = mysqli_real_escape_string($conn, $_POST["kilo"]);
            $cuft = mysqli_real_escape_string($conn, $_POST["cuft"]);
            $taiwan_pay = mysqli_real_escape_string($conn, $_POST["taiwan_pay"]);
            $courier_pay = mysqli_real_escape_string($conn, $_POST["courier_pay"]);
            $courier_money = mysqli_real_escape_string($conn, $_POST["courier_money"]);
            $remark = stripslashes($_POST["remark"]);
            $crud = mysqli_real_escape_string($conn, $_POST["crud"]);
            $id = mysqli_real_escape_string($conn, $_POST["id"]);

            $taiwan_pay = ($taiwan_pay ? $taiwan_pay : 0);
            $courier_pay = ($courier_pay ? $courier_pay : 0);

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
                    // if (move_uploaded_file($_FILES["file"]["tmp_name"], "/var/www/html/img/" . $filename)) {
                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $conf::$upload_path . $filename)) {
                        echo "done";
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
                									  kilo,
                									  cuft,
                									  taiwan_pay,
                									  courier_pay,
                									  courier_money,
                									  remark,
                                                      crt_user) 
                							values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql); 
                $stmt->bind_param("sssssssddiiiss",
                					trim($date_receive), 
                					trim($customer), 
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

                $last_id = mysqli_insert_id($conn);

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
                    // if (move_uploaded_file($_FILES["file"]["tmp_name"], "/var/www/html/img/" . $filename)) {
                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $conf::$upload_path . $filename)) {
                        echo "done";
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
                                                      kilo,
                                                      cuft,
                                                      taiwan_pay,
                                                      courier_pay,
                                                      courier_money,
                                                      remark,
                                                      crt_user) 
                                            values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql); 
                $stmt->bind_param("sssssssddiiiss",
                                    trim($date_receive), 
                                    trim($customer), 
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
                    sendMail($email, $date_receive, $customer, $description, $quantity, $supplier, $filename);

                $last_id = mysqli_insert_id($conn);

                echo $last_id;

                break;

            case "update":
                $filename = "";

                if(isset($_FILES['file']['name'])) {
                    $key = "myKey";
                    $time = time();
                    $hash = hash_hmac('sha256', $time, $key);
                    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                    $filename = $time . $hash . "." . $ext;
                    // if (move_uploaded_file($_FILES["file"]["tmp_name"], "/var/www/html/img/" . $filename)) {
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
                    $stmt->bind_param("sssssssddiiissd",
                                        trim($date_receive), 
                                        trim($customer), 
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
                    $stmt->bind_param("ssssssddiiissd",
                                        trim($date_receive), 
                                        trim($customer),  
                                        $email, 
                                        $description, 
                                        $quantity, 
                                        $supplier,
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

                

                echo $affected_rows;

                break;

              case "update_mail":
                $filename = "";

                if(isset($_FILES['file']['name'])) {
                    $key = "myKey";
                    $time = time();
                    $hash = hash_hmac('sha256', $time, $key);
                    $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                    $filename = $time . $hash . "." . $ext;
                    // if (move_uploaded_file($_FILES["file"]["tmp_name"], "/var/www/html/img/" . $filename)) {
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
                    $stmt->bind_param("sssssssddiiissd",
                                        trim($date_receive), 
                                        trim($customer), 
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
                    $stmt->bind_param("ssssssddiiissd",
                                        trim($date_receive), 
                                        trim($customer), 
                                        $email, 
                                        $description, 
                                        $quantity, 
                                        $supplier,
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

                if($email != "")
                    sendMail($email, $date_receive, $customer, $description, $quantity, $supplier, $filename);

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
