<?php
// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// files needed to connect to database
include_once 'config/database.php';
include_once 'objects/contact_us.php';
include_once 'config/conf.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// instantiate product object
$contact_us = new ContactUs($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// set product property values
$contact_us->gender = $data->gender;
$contact_us->customer = $data->customer;
$contact_us->emailinfo = $data->emailinfo;
$contact_us->telinfo = $data->telinfo;
 
// create the user
if(
    !empty($contact_us->customer) &&
    $contact_us->create()
){
 
    // set response code
    http_response_code(200);

    sendMail($data->gender, $data->customer, $data->emailinfo, $data->telinfo);
 
    // display message: user was created
    echo json_encode(array("message" => "User was created."));
}
 
// message if unable to create user
else{
 
    // set response code
    http_response_code(400);
 
    // display message: unable to create user
    echo json_encode(array("message" => "Unable to create user."));
}


function sendMail($gender, $customer,  $emailinfo, $telinfo) {
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

    $tz_object = new DateTimeZone("Asia/Taipei");
    $datetime = new DateTime();
    $datetime->setTimezone($tz_object);

    $mail->IsHTML(true);
    $mail->AddAddress("jyf_lu@hotmail.com", "jyf_lu");
    
    $mail->SetFrom("servictoryshipment@gmail.com", "servictoryshipment");
    $mail->AddReplyTo("servictoryshipment@gmail.com", "servictoryshipment");

    $mail->Subject = "�Ȥ��p����T from ���ȵ�Google�s�i";
    $content = "<p>�ٿסG" . $gender . "</p>";
    $content = $content . "<p>�m�W�G" . $customer . "</p>";
    $content = $content . "<p>�q�l�H�c�G" . $emailinfo . "</p>";
    $content = $content . "<p>�s���q�ܡG" . $telinfo . "</p>";
    $content = $content . "<p>�n�O����G" . $datetime->format('Y\-m\-d\ h:i:s') . "</p>";

    $mail->MsgHTML($content);
    if(!$mail->Send()) {
        echo "Error while sending Email.";
        var_dump($mail);
    } else {
        echo "Email sent successfully";
    }
}
?>