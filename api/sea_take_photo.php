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


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require_once '../vendor/autoload.php';

$conf = new Conf();

use Google\Cloud\Storage\StorageClient;

require_once "db.php";


header('Access-Control-Allow-Origin: *');

$method = $_SERVER['REQUEST_METHOD'];

$user = $decoded->data->username;

switch ($method) {

    case 'POST':
        $date_receive = stripslashes($_POST["date_receive"]);
        $customer = stripslashes($_POST["customer"]);
        $email = stripslashes($_POST["email"]);
        $description = stripslashes($_POST["description"]);
        $quantity = stripslashes($_POST["quantity"]);
        $supplier = stripslashes($_POST["supplier"]);
        $kilo = stripslashes($_POST["kilo"]);
        $cuft = stripslashes($_POST["cuft"]);
        $taiwan_pay = stripslashes($_POST["taiwan_pay"]);
        $courier_pay = stripslashes($_POST["courier_pay"]);
        $courier_money = stripslashes($_POST["courier_money"]);
        $remark = stripslashes($_POST["remark"]);
        $crud = stripslashes($_POST["crud"]);
        $id = stripslashes($_POST["id"]);

        $file_count = stripslashes($_POST["file_count"]);

        $taiwan_pay = ($taiwan_pay ? $taiwan_pay : 0);
        $courier_pay = ($courier_pay ? $courier_pay : 0);

        $date_receive = trim($date_receive);
        $customer = trim($customer);

        switch ($crud) {
            case 'insert':
                $filename = "RECEIVE";

                /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
                $sql = "insert into receive_record (date_receive, 
                									  customer, 
                									  email, 
                									  description, 
                									  quantity,
                									  supplier,
                									  photo,
                									  kilo,
                									  cuft,
                									  taiwan_pay,
                									  courier_pay,
                									  courier_money,
                									  remark,
                                                      crt_user) 
                							values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param(
                    "sssssssddiiiss",
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
                    $user
                );
                $stmt->execute();
                $stmt->close();

                $last_id = mysqli_insert_id($conn);


                $batch_id = $last_id;
                $batch_type = "RECEIVE";

                try {
                    $total = $file_count;
                    // Loop through each file
                    for ($i = 0; $i < $total; $i++) {

                        if (isset($_POST['files' . $i])) {
                            $img = !empty($_POST['files' . $i]) ? $_POST['files' . $i] : "";
                            $img = str_replace('data:image/png;base64,', '', $img);
                            $img = str_replace('data:image/jpeg;base64,', '', $img);
                            $img = str_replace(' ', '+', $img);
                            if ($img != "")
                                $fileData = base64_decode($img);

                            if (isset($fileData)) {
                                $key = "myKey";
                                $time = time();
                                $hash = hash_hmac('sha256', $time . rand(1, 65536), $key);
                                $ext = "jpg";
                                $filename = $time . $hash . "." . $ext;

                                file_put_contents($conf::$upload_path . $filename, $fileData);
                            }

                            $image_name = $filename;
                            $valid_extensions = array("jpg", "jpeg", "png", "gif", "pdf", "docx", "doc", "xls", "xlsx", "ppt", "pptx", "zip", "rar", "7z", "txt", "dwg", "skp", "psd", "evo");
                            $extension = pathinfo($image_name, PATHINFO_EXTENSION);
                            if (in_array(strtolower($extension), $valid_extensions)) {
                                //$upload_path = 'img/' . time() . '.' . $extension;

                                $storage = new StorageClient([
                                    'projectId' => 'predictive-fx-284008',
                                    'keyFilePath' => $conf::$gcp_key
                                ]);

                                $bucket = $storage->bucket('feliiximg');

                                $upload_name = time() . '_' . pathinfo($image_name, PATHINFO_FILENAME) . '.' . $extension;

                                $file_size = filesize($conf::$upload_path . $filename);
                                $size = 0;

                                $obj = $bucket->upload(
                                    fopen($conf::$upload_path . $filename, 'r'),
                                    ['name' => $upload_name]
                                );

                                $info = $obj->info();
                                $size = $info['size'];

                                if ($size == $file_size && $file_size != 0 && $size != 0) {
                                    $query = "INSERT INTO gcp_storage_file
                                    SET
                                        batch_id = ?,
                                        batch_type = ?,
                                        filename = ?,
                                        gcp_name = ?,

                                        create_id = ?,
                                        created_at = now()";

                                    // prepare the query
                                    $stmt = $conn->prepare($query);

                                    // bind the values
                                    $stmt->bind_param(
                                        "isssi",
                                        $batch_id,
                                        $batch_type,
                                        $filename,
                                        $upload_name,
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


                                    $message = 'Uploaded';
                                    $code = 0;
                                    $upload_id = $last_id;
                                    $image = $image_name;

                                    unlink($conf::$upload_path . $filename);
                                } else {
                                    $message = 'There is an error while uploading file';
                                    mysqli_rollback($conn);
                                    http_response_code(501);
                                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                                    die();
                                }
                            } else {
                                $message = 'Only Images or Office files allowed to upload';
                                mysqli_rollback($conn);
                                http_response_code(501);
                                echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                                die();
                            }
                        }
                    }
                } catch (Exception $e) {
                    mysqli_rollback($conn);
                    http_response_code(501);
                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Error uploading, Please use laptop to upload again."));
                    die();
                }


                http_response_code(200);
                echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));

                break;

                case 'insert_lib':
                    $filename = "LIBRARY";
    
                    /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
                    $sql = "insert into receive_library (date_receive, 
                                                          customer, 
                                                          email, 
                                                          description, 
                                                          quantity,
                                                          supplier,
                                                          photo,
                                                          kilo,
                                                          cuft,
                                                          taiwan_pay,
                                                          courier_pay,
                                                          courier_money,
                                                          remark,
                                                          crt_user) 
                                                values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param(
                        "sssssssddiiiss",
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
                        $user
                    );
                    $stmt->execute();
                    $stmt->close();
    
                    $last_id = mysqli_insert_id($conn);
    
    
                    $batch_id = $last_id;
                    $batch_type = "LIBRARY";
    
                    try {
                        $total = $file_count;
                        // Loop through each file
                        for ($i = 0; $i < $total; $i++) {
    
                            if (isset($_POST['files' . $i])) {
                                $img = !empty($_POST['files' . $i]) ? $_POST['files' . $i] : "";
                                $img = str_replace('data:image/png;base64,', '', $img);
                                $img = str_replace('data:image/jpeg;base64,', '', $img);
                                $img = str_replace(' ', '+', $img);
                                if ($img != "")
                                    $fileData = base64_decode($img);
    
                                if (isset($fileData)) {
                                    $key = "myKey";
                                    $time = time();
                                    $hash = hash_hmac('sha256', $time . rand(1, 65536), $key);
                                    $ext = "jpg";
                                    $filename = $time . $hash . "." . $ext;
    
                                    file_put_contents($conf::$upload_path . $filename, $fileData);
                                }
    
                                $image_name = $filename;
                                $valid_extensions = array("jpg", "jpeg", "png", "gif", "pdf", "docx", "doc", "xls", "xlsx", "ppt", "pptx", "zip", "rar", "7z", "txt", "dwg", "skp", "psd", "evo");
                                $extension = pathinfo($image_name, PATHINFO_EXTENSION);
                                if (in_array(strtolower($extension), $valid_extensions)) {
                                    //$upload_path = 'img/' . time() . '.' . $extension;
    
                                    $storage = new StorageClient([
                                        'projectId' => 'predictive-fx-284008',
                                        'keyFilePath' => $conf::$gcp_key
                                    ]);
    
                                    $bucket = $storage->bucket('feliiximg');
    
                                    $upload_name = time() . '_' . pathinfo($image_name, PATHINFO_FILENAME) . '.' . $extension;
    
                                    $file_size = filesize($conf::$upload_path . $filename);
                                    $size = 0;
    
                                    $obj = $bucket->upload(
                                        fopen($conf::$upload_path . $filename, 'r'),
                                        ['name' => $upload_name]
                                    );
    
                                    $info = $obj->info();
                                    $size = $info['size'];
    
                                    if ($size == $file_size && $file_size != 0 && $size != 0) {
                                        $query = "INSERT INTO gcp_storage_file
                                        SET
                                            batch_id = ?,
                                            batch_type = ?,
                                            filename = ?,
                                            gcp_name = ?,
                                            batch_id_org = ?,
    
                                            create_id = ?,
                                            created_at = now()";
    
                                        // prepare the query
                                        $stmt = $conn->prepare($query);
    
                                        // bind the values
                                        $stmt->bind_param(
                                            "isssii",
                                            $batch_id,
                                            $batch_type,
                                            $filename,
                                            $upload_name,
                                            $batch_id,
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
    
    
                                        $message = 'Uploaded';
                                        $code = 0;
                                        $upload_id = $last_id;
                                        $image = $image_name;
    
                                        unlink($conf::$upload_path . $filename);
                                    } else {
                                        $message = 'There is an error while uploading file';
                                        mysqli_rollback($conn);
                                        http_response_code(501);
                                        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                                        die();
                                    }
                                } else {
                                    $message = 'Only Images or Office files allowed to upload';
                                    mysqli_rollback($conn);
                                    http_response_code(501);
                                    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $message));
                                    die();
                                }
                            }
                        }
                    } catch (Exception $e) {
                        mysqli_rollback($conn);
                        http_response_code(501);
                        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " Error uploading, Please use laptop to upload again."));
                        die();
                    }
    
    
                    http_response_code(200);
                    echo json_encode(array("message" => "Success at " . date("Y-m-d") . " " . date("h:i:sa")));
    
                    break;
        }

        break;
}

// Close connection
mysqli_close($conn);
