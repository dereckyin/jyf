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

require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PhpOffice\PhpWord\IOFactory;

use Google\Cloud\Storage\StorageClient;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';

require_once "db.php";

header('Access-Control-Allow-Origin: *');  

$method = $_SERVER['REQUEST_METHOD'];

$user = $decoded->data->username;
$user_id = $decoded->data->id;

switch ($method) {

case 'POST':

$id = (isset($_POST['id']) ?  $_POST['id'] : 0);
$exp_dr = (isset($_POST['exp_dr']) ?  $_POST['exp_dr'] : "");
$exp_date = (isset($_POST['exp_date']) ?  $_POST['exp_date'] : "");
$exp_sold_to = (isset($_POST['exp_sold_to']) ?  $_POST['exp_sold_to'] : "");
$exp_quantity = (isset($_POST['exp_quantity']) ?  $_POST['exp_quantity'] : "");
$exp_unit = (isset($_POST['exp_unit']) ?  $_POST['exp_unit'] : "");
$exp_discription = (isset($_POST['exp_discription']) ?  $_POST['exp_discription'] : "");
$exp_amount = (isset($_POST['exp_amount']) ?  $_POST['exp_amount'] : "");
$exp_discription_ext = (isset($_POST['exp_discription_ext']) ?  $_POST['exp_discription_ext'] : "");
$exp_amount_ext = (isset($_POST['exp_amount_ext']) ?  $_POST['exp_amount_ext'] : "");
$payment = (isset($_POST['payment']) ?  $_POST['payment'] : []);
$record = (isset($_POST['record']) ?  $_POST['record'] : []);
$assist_by = (isset($_POST['assist_by']) ?  $_POST['assist_by'] : "");
$adv = (isset($_POST['adv']) ?  $_POST['adv'] : "Y");

$payments = json_decode($payment);
$goods = json_decode($record);

$file_name = 'Payment_Receipt' . ($exp_dr !== '' ? '_' . $exp_dr : '') . '_' . uniqid() . '.docx';

if(IsExist($id, $conn))
{
    $query = "update pickup_payment_export set 
        exp_dr = ?, 
        assist_by = ?,
        exp_date = ?, 
        exp_sold_to = ?, 
        exp_quantity = ?, 
        exp_unit = ?, 
        exp_discription = ?, 
        exp_amount = ?, 
        payment = ?, 
        record = ?, 
        crt_user = ?, 
        crt_time = now(),
        upd_time = now(),
        upd_user = ?,
        file_export = ?,
        adv = ?
        where measure_detail_id = ?";
        // prepare the query
        $stmt = $conn->prepare($query);

        $stmt->bind_param(
        "ssssssssssssssi",
        $exp_dr,
        $assist_by,
        $exp_date,
        $exp_sold_to,
        $exp_quantity,
        $exp_unit,
        $exp_discription,
        $exp_amount,
        $payment,
        $record,
        $user,
        $user,
        $file_name,
        $adv,
        $id
        );


        try {
        // execute the query, also check if query was successful
        if (!$stmt->execute()) {
  
        http_response_code(501);
        echo json_encode("Failure3 at " . date("Y-m-d") . " " . date("h:i:sa"));
        die();
        }

        } catch (Exception $e) {
        error_log($e->getMessage());
  
        http_response_code(501);
        echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
        die();
        }
}
else
{
    $query = "insert into pickup_payment_export set 
    measure_detail_id = ?,
    exp_dr = ?, 
    assist_by = ?,
    exp_date = ?, 
    exp_sold_to = ?, 
    exp_quantity = ?, 
    exp_unit = ?, 
    exp_discription = ?, 
    exp_amount = ?, 
    payment = ?, 
    record = ?, 
    crt_user = ?, 
    crt_time = now(),
    upd_time = now(),
    upd_user = ?,
    file_export = ?,
    adv = ?
  ";
    // prepare the query
    $stmt = $conn->prepare($query);

    $stmt->bind_param(
    "issssssssssssss",
    $id,
    $exp_dr,
    $assist_by,
    $exp_date,
    $exp_sold_to,
    $exp_quantity,
    $exp_unit,
    $exp_discription,
    $exp_amount,
    $payment,
    $record,
    $user,
    $user,
    $file_name,
    $adv
    );


    try {
    // execute the query, also check if query was successful
    if (!$stmt->execute()) {
       
    http_response_code(501);
    echo json_encode("Failure3 at " . date("Y-m-d") . " " . date("h:i:sa"));
    die();
    }

    } catch (Exception $e) {
    error_log($e->getMessage());

    http_response_code(501);
    echo json_encode(array("Failure at " . date("Y-m-d") . " " . date("h:i:sa") . " " . $e->getMessage()));
    die();
    }
    
}

// Creating the new document...
$phpWord = new PhpOffice\PhpWord\PhpWord();

/* Note: any element you append to a document must reside inside of a Section. */

$phpWord->setDefaultParagraphStyle(
    array(
        'spacing' => 120,
        'lineHeight' => 1,
    )
);

// Adding an empty Section to the document...
//$section = $phpWord->addSection(["paperSize" => "A4", 'marginLeft' => 340.157480, 'marginRight' => 283.464567, 'marginTop' => 283.46456, 'marginBottom' => 283.46456]);
//$section = $phpWord->addSection(["paperSize" => "A4", 'marginLeft' => 396.850393, 'marginRight' => 226.771654, 'marginTop' => 283.46456, 'marginBottom' => 283.46456]);
$section = $phpWord->addSection(["paperSize" => "A4", 'marginLeft' => 368.503937, 'marginRight' => 255.118110, 'marginTop' => 283.46456, 'marginBottom' => 283.46456]);


$capitalCell =
[
    'align' => 'center',
];

$styleCell =
[
    'border-style' => 'none',
    'border-size' => 0,
];

$valueCell =
[
    'border-style' => 'solid solid solid none',
    'border-color' => 'black black black',
    'underline' => 'single',
    'border-size' => 0,
];

$table = $section->addTable([
    'border-style' => 'none',
    'border-size' => 0,
    'cellMargin' => 56.692913,
]);


// 1cm = 566.929134

$table->addRow(606.614173);
$table->addCell(8503.937010, ['valign' => \PhpOffice\PhpWord\SimpleType\VerticalJc::CENTER])->addText(htmlspecialchars("FELIIX INC."), array('name' => 'Bodoni MT Black', 'bold' => true, 'size' => 20, 'color' => 'black'), array('align' => 'left'));
$table->addCell(2768, ['valign' => \PhpOffice\PhpWord\SimpleType\VerticalJc::CENTER])->addText(htmlspecialchars("PAYMENT RECEIPT"), array('name' => 'Bodoni MT', 'bold' => true, 'size' => 12, 'color' => 'black'), array('align' => 'center', 'valign' => 'center'));


$table->addRow(1326.614173);
$cell = $table->addCell(8503.937010, array('space' => array('line' => 1000)));
$TextRun = $cell->addTextRun();
$TextRun->addText(htmlspecialchars('664 7'), array('name' => 'Calibri', 'size' => 14, 'color' => 'black'));
$TextRun->addText(htmlspecialchars('th'), array('name' => 'Calibri', 'size' => 14, 'superScript' => true, 'color' => 'black'));
$TextRun->addText(htmlspecialchars(' street between 7'), array('name' => 'Calibri', 'size' => 14, 'color' => 'black'));
$TextRun->addText(htmlspecialchars('th'), array('name' => 'Calibri', 'size' => 14, 'superScript' => true, 'color' => 'black'));
$TextRun->addText(htmlspecialchars(' & 8'), array('name' => 'Calibri', 'size' => 14, 'color' => 'black'));
$TextRun->addText(htmlspecialchars('th'), array('name' => 'Calibri', 'size' => 14, 'superScript' => true, 'color' => 'black'));
$TextRun->addText(htmlspecialchars(' avenue'), array('name' => 'Calibri', 'size' => 14, 'color' => 'black'));
$cell->addText(htmlspecialchars("Brgy. 103 Zone 9, Dist. II, Caloocan City"), array('name' => 'Calibri', 'size' => 14, 'color' => 'black'), array('align' => 'left', 'spaceBefore' => '56.692913'));
$cell->addText(htmlspecialchars("Tel. Nos. 8334-1716 * 8363-5116"), array('name' => 'Calibri', 'size' => 14, 'color' => 'black'), array('align' => 'left', 'spaceBefore' => '56.692913'));
$TextRun = $cell->addTextRun();
$TextRun->addText(htmlspecialchars("Office Hours: "), array('name' => 'Calibri', 'size' => 14, 'color' => 'black'), array('align' => 'left', 'spaceBefore' => '56.692913'));
$TextRun->addText(htmlspecialchars("Mon-Fri 8:30am to 5:00pm"), array('name' => 'Calibri', 'size' => 14, 'color' => 'blue'));
$TextRun = $cell->addTextRun();
$TextRun->addText(htmlspecialchars("Lunch Break: "), array('name' => 'Calibri', 'size' => 14, 'color' => 'black'), array('align' => 'left', 'spaceBefore' => '56.692913'));
$TextRun->addText(htmlspecialchars("12nn to 1pm"), array('name' => 'Calibri', 'size' => 14, 'color' => 'blue'));


$cell = $table->addCell(2768, ['valign' => \PhpOffice\PhpWord\SimpleType\VerticalJc::CENTER]);
$TextRun = $cell->addTextRun();
$TextRun->addText(htmlspecialchars("No.  "), array('name' => 'Bodoni MT', 'size' => 20, 'color' => 'black'), array('align' => 'center', 'valign' => 'center'));
$TextRun->addText(htmlspecialchars($exp_dr), array('name' => 'Bodoni MT', 'size' => 20, 'color' => 'red'), array('align' => 'center', 'valign' => 'center'));


$section->addTextBreak(1, array('name' => 'Calibri', 'size' => 12, 'color' => 'black'));

$table2 = $section->addTable([
    'border-style' => 'none',
    'border-size' => 0,
    'cellMarginLeft' => 56.692913,
    'cellMarginRight' => 56.692913,
    'color' => 'black'
]);

$table2->addRow();
$table2->addCell(1133.858268)->addText(htmlspecialchars("SOLD TO: "), array('name' => 'Calibri', 'size' => 12, 'color' => 'black'), array('align' => 'center'));
$table2->addCell(6236.220474, ['borderBottomColor' => '000000', 'borderBottomSize' => 6])->addText(htmlspecialchars($exp_sold_to), array('name' => 'Calibri', 'size' => 12, 'color' => 'black'), array('align' => 'left'));
$table2->addCell(396.850394)->addText("", array('name' => 'Calibri', 'size' => 12, 'color' => 'black'), array('align' => 'center'));
$table2->addCell(850.393701)->addText(htmlspecialchars("DATE: "), array('name' => 'Calibri', 'size' => 12, 'color' => 'black'), array('align' => 'center'));
$table2->addCell(2653.228347, ['borderBottomColor' => '000000', 'borderBottomSize' => 6])->addText(htmlspecialchars($exp_date), array('name' => 'Calibri', 'size' => 12, 'color' => 'black'), array('align' => 'left'));

$section->addTextBreak(1, array('name' => 'Calibri', 'size' => 12, 'color' => 'black'));

$styleCellLeft =
[
    'align' => 'left',
    'borderTopColor' =>'000000',
    'borderTopSize' => 6,
    'borderRightColor' =>'000000',
    'borderRightSize' => 6,
    'borderBottomColor' =>'000000',
    'borderBottomSize' => 6,
    'borderLeftColor' =>'000000',
    'borderLeftSize' => 6,
    'color' => 'black'
];

$styleCellRight =
[
    'align' => 'right',
    'borderTopColor' =>'000000',
    'borderTopSize' => 6,
    'borderRightColor' =>'000000',
    'borderRightSize' => 6,
    'borderBottomColor' =>'000000',
    'borderBottomSize' => 6,
    'borderLeftColor' =>'000000',
    'borderLeftSize' => 6,
    'color' => 'black'
];

$styleCellCenter =
[
    'align' => 'center',
    'borderTopColor' =>'000000',
    'borderTopSize' => 6,
    'borderRightColor' =>'000000',
    'borderRightSize' => 6,
    'borderBottomColor' =>'000000',
    'borderBottomSize' => 6,
    'borderLeftColor' =>'000000',
    'borderLeftSize' => 6,
    'color' => 'black'
];

$styleCellHeadCenter =
[
    'align' => 'center',
    'borderTopColor' =>'000000',
    'borderTopSize' => 6,
    'borderRightColor' =>'ffffff',
    'borderRightSize' => 0,
    'borderBottomColor' =>'000000',
    'borderBottomSize' => 6,
    'borderLeftColor' =>'000000',
    'borderLeftSize' => 6,
    'color' => 'black'
];

$styleCellTailCenter =
[
    'align' => 'center',
    'borderTopColor' =>'000000',
    'borderTopSize' => 6,
    'borderRightColor' =>'000000',
    'borderRightSize' => 6,
    'borderBottomColor' =>'000000',
    'borderBottomSize' => 6,
    'borderLeftColor' =>'ffffff',
    'borderLeftSize' => 0,
    'color' => 'black'
];

$styleCellCenterConent =
[
    'align' => 'center',
    'color' => 'black'
 
];

$styleHeadCenterUnderline =
[
    'align' => 'center',
    'borderTopColor' =>'000000',
    'borderTopSize' => 6,
    'borderRightColor' =>'ffffff',
    'borderRightSize' => 0,
    'borderBottomColor' =>'000000',
    'borderBottomSize' => 6,
    'borderLeftColor' =>'000000',
    'borderLeftSize' => 6,
    'name' => 'Calibri', 
    'size' => 12,
    'color' => 'black'
];

$styleTailCenterUnderline =
[
    'align' => 'center',
    'borderTopColor' =>'000000',
    'borderTopSize' => 6,
    'borderRightColor' =>'000000',
    'borderRightSize' => 6,
    'borderBottomColor' =>'000000',
    'borderBottomSize' => 6,
    'borderLeftColor' =>'ffffff',
    'borderLeftSize' => 0,
    'name' => 'Calibri', 
    'size' => 12,
    'color' => 'black'
];

$styleHeadCenterUnderlineBold =
[
    'align' => 'center',
    'borderTopColor' =>'000000',
    'borderTopSize' => 6,
    'borderRightColor' =>'000000',
    'borderRightSize' => 6,
    'borderBottomColor' =>'000000',
    'borderBottomSize' => 6,
    'borderLeftColor' =>'000000',
    'borderLeftSize' => 6,
    'name' => 'Calibri', 
    'size' => 12,
    'bold' => true, 
    'color' => 'black'
];


$table3 = $section->addTable( [
    'afterSpacing' => 0, 
    'Spacing' => 0,
    'cellMargin' => 56.692913,
    'color' => 'black'
]);

$styleTable = array('borderSize' => 6, 'borderColor' => '000000', 'color' => 'black');
$cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center', 'color' => 'black');
$cellRowContinue = array('vMerge' => 'continue', 'color' => 'black');
$cellColSpan = array('gridSpan' => 2, 'valign' => 'center', 'borderSize' => 6, 'borderColor' => '000000', 'color' => 'black');
$cellHCentered = array('align' => 'center', 'color' => 'black');
$cellVCentered = array('valign' => 'center', 'color' => 'black');


$table3->addRow();
$table3->addCell(1133.858268, $styleCellCenter)->addText(htmlspecialchars("Quantity"), $styleHeadCenterUnderlineBold, array('align' => 'center', 'color' => 'black'));
$table3->addCell(1133.858268, $styleCellCenter)->addText(htmlspecialchars("Unit"), $styleHeadCenterUnderlineBold, array('align' => 'center', 'color' => 'black'));
$table3->addCell(9002.834648, $cellColSpan)->addText(htmlspecialchars("Description"), $styleHeadCenterUnderlineBold, array('align' => 'center', 'color' => 'black'));


$table3->addRow();
$cell = $table3->addCell(1133.858268, $styleCellCenter);
// Quantity
$strArr = explode("\n", $exp_quantity);
foreach ($strArr as $v) {
    $cell->addText(htmlspecialchars($v), array('name' => 'Calibri', 'size' => 12, 'color' => 'black'), array('align' => 'center'));
}

$cell = $table3->addCell(1133.858268, $styleCellCenter);
// Unit
$strArr = explode("\n", $exp_unit);
foreach ($strArr as $v) {
    $cell->addText(htmlspecialchars($v), array('name' => 'Calibri', 'size' => 12, 'color' => 'black'), array('align' => 'center'));
}

$cell = $table3->addCell(4501.417324, $styleCellHeadCenter);
// Description
$strArr = explode("\n", $exp_discription_ext);
foreach ($strArr as $v) {
    $cell->addText(htmlspecialchars($v), array('name' => 'Calibri', 'size' => 12, 'color' => 'black'), array('align' => 'center'));
}

$cell = $table3->addCell(4501.417324, $styleCellTailCenter);
// Amount
$strArr = explode("\n", $exp_amount_ext);
foreach ($strArr as $v) {
    $cell->addText(htmlspecialchars($v), array('name' => 'Calibri', 'size' => 12, 'color' => 'black'), array('align' => 'center'));
}

$section->addTextBreak(1, array('name' => 'Calibri', 'size' => 12, 'color' => 'black'));


$table4 = $section->addTable([
    'afterSpacing' => 0, 
    'Spacing'=> 0, 
    'cellMargin' => 56.692913,
     'color' => 'black'
]);

$real_payment = [];

foreach ($payments as $payment) {
    if (property_exists($payment, 'is_selected') && $payment->is_selected == 1) {
        
        $kind = GetPaymentType($payment->type);
        $item = array(
            "kind" => $kind,
            "receive_date" => ($payment->type != 3 ? $payment->payment_date : $payment->issue_date),
            "amount" => $payment->amount,
            "remark" => $payment->remark,
        );

        array_push($real_payment, $item);
    }
}

if(count($real_payment) > 0)
{
    $table4->addRow();
    $table4->addCell(11270.551184, array('gridSpan' => count($real_payment), 'valign' => 'center', 'borderSize' => 6, 'borderColor' => '000000', 'color' => 'black'))->addText(htmlspecialchars("Details of Received Payment"), $styleHeadCenterUnderlineBold, array('align' => 'center', 'color' => 'black'));
    $table4->addRow();
    foreach ($real_payment as $payment) {
        $cell = $table4->addCell(11270.551184/count($real_payment), $styleCellCenter);
        $cell->addText(htmlspecialchars($payment['kind']), array('name' => 'Calibri', 'size' => 12, 'color' => 'black'), array('align' => 'center'));
        $cell->addText(htmlspecialchars($payment['receive_date']), array('name' => 'Calibri', 'size' => 12, 'color' => 'black'), array('align' => 'center'));
        $cell->addText(htmlspecialchars($payment['amount']), array('name' => 'Calibri', 'size' => 12, 'color' => 'black'), array('align' => 'center'));
        $cell->addText(htmlspecialchars($payment['remark']), array('name' => 'Calibri', 'size' => 12, 'color' => 'black'), array('align' => 'center'));
    }
}
else
{
    $table4->addRow();
    $table4->addCell(11270.551184, array('gridSpan' => 1, 'valign' => 'center', 'borderSize' => 6, 'borderColor' => '000000', 'color' => 'black'))->addText(htmlspecialchars("Details of Received Payment"), array('name' => 'Calibri', 'size' => 12, 'bold' => true, 'color' => 'black'), array('align' => 'center'));
    $table4->addRow();
   
    $cell = $table4->addCell(11270.551184, $styleCellCenter);
    $cell->addText("", array('name' => 'Calibri', 'size' => 12, 'color' => 'black'), array('align' => 'center'));
    $cell->addText("", array('name' => 'Calibri', 'size' => 12, 'color' => 'black'), array('align' => 'center'));
    $cell->addText("", array('name' => 'Calibri', 'size' => 12, 'color' => 'black'), array('align' => 'center'));
    $cell->addText("", array('name' => 'Calibri', 'size' => 12, 'color' => 'black'), array('align' => 'center'));
    
}

$section->addTextBreak(1, array('name' => 'Calibri', 'size' => 12, 'color' => 'black'));


$table5 = $section->addTable([
    'afterSpacing' => 0, 
    'Spacing'=> 0, 
    'cellMarginTop' => 56.692913,
    'cellMarginBottom' => 56.692913,
    'cellMarginLeft' => 113.385827,
    'cellMarginRight' => 113.385827,
    'color' => 'black'
]);

$real_goods = [];

foreach ($goods as $good) {
    if (property_exists($good, 'is_selected') && $good->is_selected == 1) 
    {
        $item = array(
            "date_receive" => $good->date_receive,
            "customer" => $good->customer,
            "description" => $good->description,
            "quantity" => $good->quantity,
            "supplier" => $good->supplier,
        );

        array_push($real_goods, $item);
    }
}

$styleHeadLeftUnderline =
[
    'align' => 'left',
    'borderTopColor' =>'000000',
    'borderTopSize' => 6,
    'borderRightColor' =>'ffffff',
    'borderRightSize' => 0,
    'borderBottomColor' =>'000000',
    'borderBottomSize' => 6,
    'borderLeftColor' =>'000000',
    'borderLeftSize' => 6,
    'color' => 'black'
];

$styleTailLeftUnderline =
[
    'align' => 'left',
    'borderTopColor' =>'000000',
    'borderTopSize' => 6,
    'borderRightColor' =>'000000',
    'borderRightSize' => 6,
    'borderBottomColor' =>'000000',
    'borderBottomSize' => 6,
    'borderLeftColor' =>'ffffff',
    'borderLeftSize' => 0,
 'color' => 'black'
];

$styleLeftUnderline =
[
    'align' => 'left',
    'borderTopColor' =>'000000',
    'borderTopSize' => 6,
    'borderRightColor' =>'ffffff',
    'borderRightSize' => 0,
    'borderBottomColor' =>'000000',
    'borderBottomSize' => 6,
    'borderLeftColor' =>'ffffff',
    'borderLeftSize' => 0,
    'color' => 'black'
];

$table5->addRow();
$table5->addCell(11270.551184, array('gridSpan' => 5, 'valign' => 'center', 'borderSize' => 6, 'borderColor' => '000000', 'color' => 'black'))->addText(htmlspecialchars("Details of Goods"), array('name' => 'Calibri', 'size' => 12, 'bold' => true, 'color' => 'black'), array('align' => 'center'));

foreach ($real_goods as $good) {
    $table5->addRow();
    $table5->addCell(1411.653544, $styleHeadLeftUnderline)->addText(htmlspecialchars($good['date_receive']), array('name' => 'Calibri', 'size' => 10, 'color' => 'black'), array('align' => 'left'));
    $table5->addCell(2267.716536, $styleLeftUnderline)->addText(htmlspecialchars($good['customer']), array('name' => 'Calibri', 'size' => 10, 'color' => 'black'), array('align' => 'left'));
    $table5->addCell(3826.771655, $styleLeftUnderline)->addText(htmlspecialchars($good['description']), array('name' => 'Calibri', 'size' => 10, 'color' => 'black'), array('align' => 'left'));
    $table5->addCell(1417.322835, $styleLeftUnderline)->addText(htmlspecialchars($good['quantity']), array('name' => 'Calibri', 'size' => 10, 'color' => 'black'), array('align' => 'left'));
    $table5->addCell(2347.086615, $styleTailLeftUnderline)->addText(htmlspecialchars($good['supplier']), array('name' => 'Calibri', 'size' => 10, 'color' => 'black'), array('align' => 'left'));
   
}

if(count($real_goods) == 0)
{
    $table5->addRow();
    $table5->addCell(1411.653544, $styleHeadLeftUnderline)->addText("", array('name' => 'Calibri', 'size' => 10, 'color' => 'black'), array('align' => 'left'));
    $table5->addCell(2267.716536, $styleLeftUnderline)->addText("", array('name' => 'Calibri', 'size' => 10, 'color' => 'black'), array('align' => 'left'));
    $table5->addCell(3826.771655, $styleLeftUnderline)->addText("", array('name' => 'Calibri', 'size' => 10, 'color' => 'black'), array('align' => 'left'));
    $table5->addCell(1417.322835, $styleLeftUnderline)->addText("", array('name' => 'Calibri', 'size' => 10, 'color' => 'black'), array('align' => 'left'));
    $table5->addCell(2100, $styleTailLeftUnderline)->addText("", array('name' => 'Calibri', 'size' => 10, 'color' => 'black'), array('align' => 'left'));
}

$section->addTextBreak(1, array('name' => 'Calibri', 'size' => 12, 'color' => 'black'));

$table6 = $section->addTable([
    'afterSpacing' => 0, 
    'Spacing'=> 0, 
    'cellMarginLeft' => 56.692913,
    'cellMarginRight' => 56.692913,
    'cellMarginBottom' => 56.692913, 'color' => 'black'
]);


$table6->addRow();
$table6->addCell(11270.551184, array('gridSpan' => 6, 'valign' => 'center', 'borderSize' => 0, 'borderColor' => 'ffffff', 'color' => 'black'))->addText(htmlspecialchars("Present upon pick up:"), array('name' => 'Calibri', 'size' => 12, 'color' => 'black'), array('align' => 'left'));


$table6->addRow();
$cell = $table6->addCell(1440);
$cell->addText(htmlspecialchars("●  TEXT"), array('name' => 'Calibri', 'size' => 10, 'color' => 'black'), array('align' => 'left', 'spaceAfter' => '56.692913'));
$cell->addText(htmlspecialchars("●  FAX"), array('name' => 'Calibri', 'size' => 10, 'color' => 'black'), array('align' => 'left', 'spaceAfter' => '56.692913'));
$cell->addText(htmlspecialchars("●  EMAIL"), array('name' => 'Calibri', 'size' => 10, 'color' => 'black'), array('align' => 'left'));

$cell = $table6->addCell(1440);
$cell->addText(htmlspecialchars("●  VIBER"), array('name' => 'Calibri', 'size' => 10, 'color' => 'black'), array('align' => 'left', 'spaceAfter' => '56.692913'));
$cell->addText(htmlspecialchars("●  SKYPE"), array('name' => 'Calibri', 'size' => 10, 'color' => 'black'), array('align' => 'left'));

$cell = $table6->addCell(1440);
$cell->addText(htmlspecialchars("●  MESSENGER"), array('name' => 'Calibri', 'size' => 10, 'color' => 'black'), array('align' => 'left', 'spaceAfter' => '56.692913'));
$cell->addText(htmlspecialchars("●  OTHER"), array('name' => 'Calibri', 'size' => 10, 'color' => 'black'), array('align' => 'left'));

$cell = $table6->addCell(2982.047245);
$cell->addText(htmlspecialchars(""), array('name' => 'Calibri', 'size' => 10, 'color' => 'black'), array('align' => 'left'));

$cell = $table6->addCell(289.133858);
$cell->addText(htmlspecialchars(""), array('name' => 'Calibri', 'size' => 10, 'color' => 'black'), array('align' => 'left'));
$cell->addText(htmlspecialchars(""), array('name' => 'Calibri', 'size' => 10, 'color' => 'black'), array('align' => 'left'));
$cell->addText(htmlspecialchars(""), array('name' => 'Calibri', 'size' => 10, 'color' => 'black'), array('align' => 'left'));
$cell->addText(htmlspecialchars("BY:"), array('name' => 'Calibri', 'size' => 12, 'color' => 'black'), array('align' => 'left'));

$cell = $table6->addCell(3679.370081, ['borderBottomColor' => '000000', 'borderBottomSize' => 6, 'color' => 'black']);
$TextRun = $cell->addTextRun();
$TextRun->addText(htmlspecialchars('     '), array('name' => 'Calibri', 'size' => 12, 'color' => 'black'));
if($assist_by == 'Lailani')
    $TextRun->addImage('https://storage.googleapis.com/feliiximg/1656033523_s_lailani.png', array('width' => 50, 'height' => 50));
if($assist_by == 'Ana')
    $TextRun->addImage('https://storage.googleapis.com/feliiximg/1656033523_s_ana.png', array('width' => 50, 'height' => 50));
if($assist_by == 'Merryl')
    $TextRun->addImage('https://storage.googleapis.com/feliiximg/1656033523_s_merryl.png', array('width' => 50, 'height' => 50));



if($adv == 'Y')
{
    $section->addTextBreak(1, array('name' => 'Calibri', 'size' => 12, 'color' => 'black'));

    $table7 = $section->addTable([
        'afterSpacing' => 0,
        'Spacing'=> 0,
        'cellMarginLeft' => 0,
        'cellMarginRight' => 56.692913,
        'cellMarginBottom' => 56.692913, 'color' => 'black'
    ]);

    $table7->addRow();
    $cell = $table7->addCell(11270.551184);
    $cell->addImage('https://storage.googleapis.com/feliiximg/1684119001_FeliixPromoCoupon.png', array('width' => 286, 'height' => 153));
}


$section->addTextBreak(1, array('name' => 'Calibri', 'size' => 12, 'color' => 'black'));

// Adding Text element with font customized using explicitly created font style object...
$fontStyle = new \PhpOffice\PhpWord\Style\Font();
$fontStyle->setBold(true);
$fontStyle->setName('Tahoma');
$fontStyle->setSize(13);
// $myTextElement = $section->addText('"Believe you can and you\'re halfway there." (Theodor Roosevelt)');
//$myTextElement->setFontStyle($fontStyle);

// Saving the document as OOXML file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007', $download = true);

    header("Content-Disposition: attachment; filename='Format of Payment Receipt'.docx");

    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');
    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0


    $conf = new Conf();
    $path = $conf::$upload_path . "tmp/";

    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }
    else
    {
        $files = glob($path . "*"); // get all file names
        foreach($files as $file){ // iterate files
            if(is_file($file)) {
                unlink($file); // delete file
            }
        }
    }

    $objWriter->save($path . $file_name);

    $storage = new StorageClient([
        'projectId' => 'predictive-fx-284008',
        'keyFilePath' => $conf::$gcp_key
    ]);

    $bucket = $storage->bucket('feliiximg');

    $upload_name = $file_name;

    $file_size = filesize($conf::$upload_path . "tmp/" . $file_name);
    $size = 0;

    $obj = $bucket->upload(
        fopen($conf::$upload_path . "tmp/" . $file_name, 'r'),
        ['name' => $upload_name]
    );

    $info = $obj->info();
    $size = $info['size'];

    $batch_type = "pickup_exp";

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
            $id,
            $batch_type,
            $file_name,
            $upload_name,
            $create_id
        );

        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                mysqli_insert_id($conn);
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

        unlink($conf::$upload_path . "tmp/" . $file_name);
    }


    $objWriter->save('php://output');

    break;

}
    
function formatCheckBox($value){
    $ret = '';
    if($value == 't' || $value == '1'){
        $ret = '1';
    }

    if($value == 'f' || $value == '0'){
        $ret = '';
    }

    return $ret;
}

function GetAttachment($id, $type, $db)
{
    $sql = "select h.id, 
                COALESCE(h.filename, '') filename, 
                COALESCE(h.gcp_name, '') gcp_name
            from project_a_meeting p 
            LEFT JOIN gcp_storage_file h ON h.batch_id = p.id AND h.batch_type = '" . $type . "'
            where p.id = " . $id . " and h.`status` <> -1 order by h.id";

    $items = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $is_checked = "";
    $gcp_name = "";
    $filename = "";
   

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $id = $row['id'];
        $gcp_name = $row["gcp_name"];
        $filename = $row["filename"];

        if ($filename != "")
            $items[] = array(
                'id' => $id,
                'checked' => true,
                'file' => null,
                'gcp_name' => $gcp_name,
                'name' => $filename,
            );
    }

    return $items;

}

function addMultiLineText($cell, $text)
{
    // break from line breaks
    $strArr = explode("\n", $text);

    // add text line together
    foreach ($strArr as $v) {
        $cell->addText($v);
    }
    
}

function GetPaymentType($kind)
{
    $type = "";

    switch($kind)
    {
        case "1":
            $type = "Cash";
            break;
        case "2":
            $type = "Deposit";
            break;
        case "3":
            $type = "Check";
            break;
        case "4":
            $type = "Taiwan Pay";
            break;
        case "5":
            $type = "Advance Payment";
            break;
        case "6":
            $type = "Gcash";
            break;
    }
    return $type;
}

function IsExist($id, $db)
{
    $is_exist = false;
    $sql = "select id from pickup_payment_export where measure_detail_id = " . $id;

    if ($result = $db->query($sql)) {
        while ($row = $result->fetch_row()) {
            $is_exist = true;
        }

    }
    return $is_exist;
}   

?>
