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
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require_once '../vendor/autoload.php';

$conf = new Conf();

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

switch ($method) {
    case 'GET':

        //$record = stripslashes($_POST["record"]);
        $record = stripslashes($_GET["record"]);
        $recordArray = explode(',', $record);

        foreach($recordArray as $recid) {
            $detail = GetRecordDetail($recid, $conn);
            if($detail[0]['email'] != '')
                $status = SendMail($detail);
            else
                $status = 'No email address';
                
            if($status == '')
                UpdateRecordCnt($recid, $user, $conn);
        }

        if ($status == '') {
            http_response_code(200);
            echo json_encode(array("message" => ""));
        } else {
            http_response_code(200);
            echo json_encode(array("message" => $status));
        }
        break;
}

// Close connection
mysqli_close($conn);

function UpdateRecordCnt($record, $user, $conn) {
    $sql = "update receive_record set mail_cnt = mail_cnt + 1,
                                                      mdf_time = now(),
                                                      mdf_user = '$user'
                                            where id in($record)";
    $query = $conn->query($sql);
}

function SendMail($detail) {
    $content = '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8"/>
    </head>
    <body>
    
    
    <div style="height: 138px; width: 865px; background-color: rgb(19,153,72); padding-top: 22px; padding-left: 45px;">
    
        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 32px; font-weight: 600; text-align: left; color: white; vertical-align: top; padding-top: 8px; width: 610px;">
                    中亞菲國際貿易有限公司<br>
                    Feliix Inc.
                </td>
                <td style="text-align: right;">
                    <img src="https://webmatrix.myvnc.com/images/banner_logo.png" style="width: 253px; height: 120px;">
                </td>
            </tr>
            </tbody>
        </table>
    
    </div>
    
    
    <div style="width: 766px; padding: 25px 70px 20px 70px; border: 2px solid rgb(230,230,230); border-top: none; color: black;">
    
        <table style="width: 100%;">
            <tbody>
            <tr>
                <td style="font-size: 20px; padding: 20px 0 20px 5px;">Dear ';

    $content .= $detail[0]['customer'];

    $content .= ',
    </td>
</tr>

<tr>
    <td style="font-size: 20px; padding: 0 0 20px 5px; text-align: justify;">
        Your shipment is currently on a ship headed to Philippines. No matter how careful we manage the
        delivery
        route and your shipment, there are still some factors that might affect the actual date of your
        shipment
        arriving at Caloocan, such as the state of sea, port congestion, port productivity, and the
        inspection
        of Philippines Customs. When the time is close to the ETA Manila Port that we provided, please feel free to call our Caloocan office to get the latest update for your
        shipment.
    </td>
</tr>

<tr>
    <td style="font-size: 20px; padding: 0 0 25px 5px;">
        Please check your shipment details below.
    </td>
</tr>

<tr>
    <td style="font-size: 20px; padding: 0 0 3px 5px; color: rgb(237,5,15); font-weight: 600; letter-spacing: 1px;">
        DELIVER INFORMATION
    </td>
</tr>

</tbody>

</table>


<table style="width: 100%">
<tbody>
<tr>
    <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
        <eng style="font-size: 16px;">
            Date of Goods Arriving at Taiwan Office
        </eng>
        <br>
        彰化收到貨物日期
    </td>
    <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';

    $content .= $detail[0]['date_receive'];

    $content .= '</td>
    </tr>

    <tr>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
            <eng style="font-size: 16px;">
                Cut Off Date
            </eng>
            <br>
            結關日

        </td>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';

    $content .= $detail[0]['date_sent'];

    $content .= '</td>
    </tr>

    <tr>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
            <eng style="font-size: 16px;">
                ETA Manila Port
            </eng>
            <br>
            預計到達馬尼拉港日期

        </td>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';

    $content .= $detail[0]['eta_date'];

    $content .= '</td>
    </tr>

    <tr>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
            <eng style="font-size: 16px;">
                Tentative Date of Pick-up
            </eng>
            <br>
            預計可提貨日期

        </td>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';

    $content .= $detail[0]['eta_date_6'];

    $content .= '<span style="color: red; display: block; margin-top: 5px; text-align: justify;">Notes: the cut off date is the date that our container was gated-in to the carrier company. The ETA Manila port is far away from the cut off date usually because the carrier company kept changing the departure date of vessel due to vessel availability, port congestion, or weather condition. As a result, the tentative date of pick-up will be affected.</span>
    </td>
</tr>

<tr>
    <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
        <eng style="font-size: 16px;">
            Description of Goods
        </eng>
        <br>
        貨品名稱
    </td>
    <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';

    $content .= $detail[0]['description'];

    $content .= '</td>
    </tr>

    <tr>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
            <eng style="font-size: 16px;">
                Quantity
            </eng>
            <br>
            件數
        </td>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';

    $content .= $detail[0]['quantity'];

    $content .= '</td>
    </tr>

    <tr>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
            <eng style="font-size: 16px;">
                Supplier
            </eng>
            <br>
            寄貨人
        </td>
        <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';

    $content .= $detail[0]['supplier'];

    // if($detail[0]['mail_note'] != ''){
    //     $content .= '</td>
    //     </tr>

    //     <tr>
    //         <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
    //             <eng style="font-size: 16px;">
    //                 Notes
    //             </eng>
    //             <br>
    //             補充說明
    //         </td>
    //         <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; width: 440px; font-size: 16px;">';
        
    //         $content .= $detail[0]['mail_note'];
    // }

    $content .= '</td>
        </tr>
        </tbody>
    </table>';

    if(count($detail[0]['pic']) > 0)
    {
        $content .= '<table style="margin-top: 20px; width: 100%; text-align: center;">
        <tbody>
        <tr>
            <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px; font-size: 14px; width: 280px; font-weight: 600;">
                <eng style="font-size: 16px;">
                    Photo of Goods
                </eng>
                貨品照片
            </td>
        </tr>';

        foreach ($detail[0]['pic'] as $pic)
        {
            $content .= '<tr>
            <td style="background-color: #F0F0F0; border: 2px solid #FFFFFF; padding: 8px;">
                <img src="';

            if($pic['type'] == 'FILE')
                $content .= 'https://webmatrix.myvnc.com/img/' . $pic['gcp_name'];

            if($pic['type'] == 'RECEIVE')
                $content .= 'https://storage.googleapis.com/feliiximg/' . $pic['gcp_name'];
            
            $content .= '">
            </td>
        </tr>';
        }

        $content .= '</tbody>
        </table>';
    }

    // tail
    $content .= '<hr style="margin-top: 45px;">

    <table style="width: 100%;">
        <tbody>
        <tr>
            <td style="font-size: 16px; font-weight: 600; padding: 10px 0 0 5px;">
                中亞菲國際貿易有限公司<br>
                Feliix Inc.
            </td>
        </tr>
        <tr>
            <td style="font-size: 15px; padding: 3px 0 3px 20px;">
                • Calooncan Office: (+63) 02-8363-5116, (+63) 02-8334-1716 <span
                    style="margin-left: 6px">Miss Lailani Ong</span>
            </td>
        </tr>
        <tr>
            <td style="font-size: 15px; padding: 0 0 3px 20px;">
                • Taiwan Office: (+886) 04-728-9301 <span style="margin-left: 6px">Miss Amy</span>
            </td>
        </tr>
        </tbody>

    </table>

</div>


</body>
</html>';

    $conf = new Conf();
    
    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    $mail->SMTPDebug  = 0;
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = "ssl";
    $mail->Port       = 465;
    $mail->SMTPKeepAlive = true;
    $mail->Host       = $conf::$mail_Host;
    $mail->Username   = $conf::$mail_Username;
    $mail->Password   = $conf::$mail_Password;

    $mail->IsHTML(true);
    //$mail->AddAddress('dereckyin@gmail.com', 'dereckyin');
    //$mail->AddAddress('dennis@feliix.com', 'dennis');
    $email_arr = explode (";", $detail[0]['email']);

    foreach ($email_arr as $email) {
        $mail->AddAddress($email, $detail[0]['customer']);
    }
    
 
    $mail->SetFrom("servictoryshipment@gmail.com", "Feliix Shipping");
    $mail->AddReplyTo("feliixshipment@gmail.com", "Feliix Shipping");
   
    $mail->Subject = "[Feliix Shipping] Hello, your shipment is on the way";
   

    $mail->MsgHTML($content);
    if(!$mail->Send()) {
        return $mail->ErrorInfo;
        //var_dump($mail);
    } else {
        return "";
    }
}

function GetRecordDetail($id, $conn){
    $merged_results = array();

    $sql = "SELECT rc.id, customer, date_receive, lo.date_sent, lo.eta_date, email, email_customer, description, quantity, supplier, 
    mail_note, rc.picname, rc.photo  FROM receive_record rc
    LEFT JOIN loading lo ON lo.id = rc.batch_num WHERE rc.id = ?";
    if ($stmt = mysqli_prepare($conn, $sql)) {

        mysqli_stmt_bind_param($stmt, "i", $id);
    
        /* execute query */
        mysqli_stmt_execute($stmt);

        $result1 = mysqli_stmt_get_result($stmt);

        while($row = mysqli_fetch_assoc($result1)) {
            $id = $row['id'];
            $customer = $row['customer'];
            $date_receive = $row['date_receive'];
            $date_sent = $row['date_sent'];
            $eta_date = $row['eta_date'];
            $email = $row['email'];
            $email_customer = $row['email_customer'];
            $description = $row['description'];
            $quantity = $row['quantity'];
            $supplier = $row['supplier'];
            $mail_note = $row['mail_note'];

            $picname = $row['picname'];
            $photo = $row['photo'];

            $pic = GetPic($picname, $photo, $id, $conn);

            array_push($merged_results, array(
                "id" => $id,
                "customer" => $customer,
                "date_receive" => FormatDate($date_receive, 0),
                "date_sent" => FormatDate($date_sent, 0),
                "eta_date" => FormatDate($eta_date, 0),
                "eta_date_6" => FormatDate($eta_date, 6),
                "email" => $email,
                "email_customer" => $email_customer,
                "description" => $description,
                "quantity" => $quantity,
                "supplier" => $supplier,
                "mail_note" => $mail_note,

                "pic" => $pic,
            ));
        }
    }


    return $merged_results;
}

function FormatDate($date_receive, $add)
{
    $welcome = new DateTime($date_receive);
    $hello = $welcome->modify($add . ' day');
    return $hello->format('Y/m/d (D)');
}

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
