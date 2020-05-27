<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// required to encode json web token
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
 
// files needed to connect to database
include_once 'config/database.php';
include_once 'objects/receive_record.php';
include_once 'objects/measure_history.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// instantiate loading object
$receive_record = new ReceiveRecord($db);
$measure_history = new MeasureHistory($db);

// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$id = (isset($_GET['id']) ?  $_GET['id'] : "");
$ids = (isset($_GET['ids']) ?  $_GET['ids'] : "");
// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
        if(!empty($ids))
        {
            http_response_code(200);
            $recode = $receive_record->GetReceiveRecordByBatchNumber($ids);
            $price_record = $measure_history->GetById($id);

            $cust = "";
            $sDate = "";
            $sDesc = "";
            $sQty = "";
   
            $sCourier = "";
            $sSupplier = "";
            $sRemark = "";

            foreach ($recode as $row) {
                if($cust != $row['customer'])
                {
                    if($cust != "")
                    {
                        // add record
                        $kilo = 0.0;
                        $cuft = 0.0;
                        $pKilo = 0.0;
                        $pCuft = 0.0;

                        for ($x = 0; $x < sizeof($price_record); $x++) {
                            if($price_record[$x]['customer'] == $cust)
                            {
                                $kilo = $price_record[$x]['kilo'];
                                $cuft = $price_record[$x]['cuft'];
                                $pKilo = $price_record[$x]['price_kilo'];
                                $pCuft = $price_record[$x]['price_cuft'];
                                break;
                            }
                        }

                        $new = ["date_receive" => rtrim($sDate, "<br>"), "customer" => $cust, "description" => rtrim($sDesc, "<br>"), "quantity" => rtrim($sQty, "<br>"), "kilo" => $kilo, "cuft" => $cuft, "price_kilo" => $pKilo, "price_cuft" => $pCuft, "courier_money" => rtrim($sCourier, "<br>"), "supplier" => rtrim($sSupplier, "<br>"), "remark" => rtrim($sRemark, "<br>")];
                        $merged_results[] = $new;

                        // clear values
                        $sDate = "";
                        $sDesc = "";
                        $sQty = "";
      
                        $sCourier = "";
                        $sSupplier = "";
                        $sRemark = "";
                    }

                    if(!empty($row['date_receive']))
                        $sDate = $sDate . $row['date_receive'] . "<br>";
                    else
                        $sDate = $sDate . "&nbsp<br>";

                    if(!empty($row['description']))
                        $sDesc = $sDesc . $row['description'] . "<br>";
                    else
                        $sDesc = $sDesc . "&nbsp<br>";

                    if(!empty($row['quantity']))
                        $sQty = $sQty . $row['quantity']. "<br>";
                    else
                        $sQty = $sQty . "&nbsp<br>";

                    if(!empty($row['courier_money']))
                        $sCourier = $sCourier . $row['courier_money']. "<br>";
                    else
                        $sCourier = $sCourier . "&nbsp<br>";

                    if(!empty($row['supplier']))
                        $sSupplier = $sSupplier . $row['supplier']. "<br>";
                    else
                        $sSupplier = $sSupplier . "&nbsp<br>";

                    if(!empty($row['remark']))
                        $sRemark = $sRemark . $row['remark']. "<br>";
                    else
                        $sRemark = $sRemark . "&nbsp<br>";

                    $cust = $row['customer'];
                }
                else
                {
                    if(!empty($row['date_receive']))
                        $sDate = $sDate . $row['date_receive']. "<br>";
                    else
                        $sDate = $sDate . "&nbsp<br>";

                    if(!empty($row['description']))
                        $sDesc = $sDesc . $row['description']. "<br>";
                    else
                        $sDesc = $sDesc ."&nbsp<br>";

                    if(!empty($row['quantity']))
                        $sQty = $sQty . $row['quantity']. "<br>";
                    else
                        $sQty = $sQty . "&nbsp<br>";

                    if(!empty($row['courier_money']))
                        $sCourier = $sCourier .$row['courier_money']. "<br>";
                    else
                        $sCourier = $sCourier ."&nbsp<br>";

                    if(!empty($row['supplier']))
                        $sSupplier = $sSupplier . $row['supplier']. "<br>";
                    else
                        $sSupplier = $sSupplier . "&nbsp<br>";

                    if(!empty($row['remark']))
                        $sRemark = $sRemark . $row['remark']. "<br>";
                    else
                        $sRemark = $sRemark . "&nbsp<br>";
                }
            }

            if($cust != "") {
                // add record
                $kilo = 0.0;
                $cuft = 0.0;
                $pKilo = 0.0;
                $pCuft = 0.0;
                for ($x = 0; $x < sizeof($price_record); $x++) {
                    if($price_record[$x]['customer'] == $cust)
                    {
                        $kilo = $price_record[$x]['kilo'];
                        $cuft = $price_record[$x]['cuft'];
                        $pKilo = $price_record[$x]['price_kilo'];
                        $pCuft = $price_record[$x]['price_cuft'];
                        break;
                    }
                }

                $new = ["date_receive" => rtrim($sDate, "<br>"), "customer" => $cust, "description" => rtrim($sDesc, "<br>"), "quantity" => rtrim($sQty, "<br>"), "kilo" => $kilo, "cuft" => $cuft, "price_kilo" => $pKilo, "price_cuft" => $pCuft, "courier_money" => rtrim($sCourier, "<br>"), "supplier" => rtrim($sSupplier, "<br>"), "remark" => rtrim($sRemark, "<br>")];
                $merged_results[] = $new;
            }
            
            // response in json format
            echo json_encode(
                $merged_results);
        }
        else
        {
            $merged_results = [];
            http_response_code(200);
            json_encode(
                $merged_results);
         
        }
    }
 
    // if decode fails, it means jwt is invalid
    catch (Exception $e){
    
        // set response code
        http_response_code(401);
    
        // show error message
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}
// show error message if jwt is empty
else{
 
    // set response code
    http_response_code(401);
 
    // tell the user access denied
    echo json_encode(array("message" => "Access denied."));
}

?>