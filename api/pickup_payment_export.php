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
$payment = (isset($_POST['payment']) ?  $_POST['payment'] : []);
$record = (isset($_POST['record']) ?  $_POST['record'] : []);

$payments = json_decode($payment);
$goods = json_decode($record);

if(IsExist($id, $conn))
{
    $query = "update pickup_payment_export set 
        exp_dr = ?, 
        exp_date = ?, 
        exp_sold_to = ?, 
        exp_quantity = ?, 
        exp_unit = ?, 
        exp_discription = ?, 
        exp_amount = ?, 
        payment = ?, 
        record = ?, 
        crt_user = ?, 
        crt_time = now()
        where measure_detail_id = ?";
        // prepare the query
        $stmt = $conn->prepare($query);

        $stmt->bind_param(
        "ssssssssssi",
        $exp_dr,
        $exp_date,
        $exp_sold_to,
        $exp_quantity,
        $exp_unit,
        $exp_discription,
        $exp_amount,
        $payment,
        $record,
        $user,
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
    exp_date = ?, 
    exp_sold_to = ?, 
    exp_quantity = ?, 
    exp_unit = ?, 
    exp_discription = ?, 
    exp_amount = ?, 
    payment = ?, 
    record = ?, 
    crt_user = ?, 
    crt_time = now()
  ";
    // prepare the query
    $stmt = $conn->prepare($query);

    $stmt->bind_param(
    "issssssssss",
    $id,
    $exp_dr,
    $exp_date,
    $exp_sold_to,
    $exp_quantity,
    $exp_unit,
    $exp_discription,
    $exp_amount,
    $payment,
    $record,
    $user
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

// Adding an empty Section to the document...
$section = $phpWord->addSection(["paperSize" => "Letter", 'marginLeft' => 600, 'marginRight' => 600, 'marginTop' => 600, 'marginBottom' => 600]);


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

$table = $section->addTable('table', [
    'border-style' => 'none',
    'border-size' => 0,
]);


$table->addRow();
$table->addCell(7500)->addText(htmlspecialchars("FELIIX INC."), array('name' => 'Bodoni MT Black', 'bold' => true, 'size' => 20), array('align' => 'left'));
$table->addCell(3000)->addText(htmlspecialchars("PAYMENT RECEIPT"), array('name' => 'Bodoni MT', 'bold' => true, 'size' => 12), array('align' => 'right'));

$table->addRow();
$cell = $table->addCell(7500, array('lineHeight' => '1.5'));
$cell->addText(htmlspecialchars(""), array('name' => 'Calibri', 'size' => 10), array('align' => 'left'));
$cell->addText(htmlspecialchars("664 7th St. bet.7th & 8th Ave.,"), array('name' => 'Calibri', 'size' => 10), array('align' => 'left'));
$cell->addText(htmlspecialchars("Brgy. 103 Zone 9, Dist. II, Caloocan City"), array('name' => 'Calibri', 'size' => 10), array('align' => 'left'));
$cell->addText(htmlspecialchars("Tel. Nos. 8334-1716 * 8363-5116"), array('name' => 'Calibri', 'size' => 10), array('align' => 'left'));
$cell->addText(htmlspecialchars("Office Hours: Mon-Fri 8:30am to 5:00pm Lunch Break: 12nn to 1pm"), array('name' => 'Calibri', 'size' => 10), array('align' => 'left'));

$cell = $table->addCell(3000);
$cell->addText(htmlspecialchars(""), array('name' => 'Bodoni MT', 'size' => 20), array('align' => 'right'));
$cell->addText(htmlspecialchars("No.  " . $exp_dr), array('name' => 'Bodoni MT', 'size' => 20), array('align' => 'right'));

$table->addRow();
$table->addCell(7500)->addText(htmlspecialchars(""), array('name' => 'Calibri', 'size' => 12), array('align' => 'left'));
$table->addCell(3000)->addText(htmlspecialchars(""), array('name' => 'Calibri', 'size' => 12), array('align' => 'right'));

$section->addTextBreak(1);

$table2 = $section->addTable('table2', [
    'border-style' => 'none',
    'border-size' => 0,
    'cellMargin' => 100,
]);

$table2->addRow();
$table2->addCell(1000)->addText(htmlspecialchars("SOLD TO: "), array('name' => 'Calibri', 'size' => 12), array('align' => 'left'));
$table2->addCell(6500)->addText(htmlspecialchars($exp_sold_to), array('name' => 'Calibri', 'size' => 12, 'underline' => 'single'), array('align' => 'left'));
$table2->addCell(800)->addText(htmlspecialchars("DATE: "), array('name' => 'Calibri', 'size' => 12), array('align' => 'right'));
$table2->addCell(2200)->addText(htmlspecialchars($exp_date), array('name' => 'Calibri', 'size' => 12, 'underline' => 'single'), array('align' => 'right'));

$section->addTextBreak(1);

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
];

$styleCellCenterConent =
[
    'align' => 'center',
 
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
    'size' => 12
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
    'size' => 12
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
    'bold' => true
];


$table3 = $section->addTable('table3', [
    'borderSize' => 6,
    'borderColor' => '999999', 
    'afterSpacing' => 0, 
    'Spacing'=> 0, 
    'cellMargin'=> 0
]);

$styleTable = array('borderSize' => 6, 'borderColor' => '999999');
$cellRowSpan = array('vMerge' => 'restart', 'valign' => 'center');
$cellRowContinue = array('vMerge' => 'continue');
$cellColSpan = array('gridSpan' => 2, 'valign' => 'center', 'borderSize' => 6, 'borderColor' => '999999');
$cellHCentered = array('align' => 'center');
$cellVCentered = array('valign' => 'center');


$table3->addRow();
$table3->addCell(500, $styleCellCenter)->addText(htmlspecialchars("Quantity"), $styleHeadCenterUnderlineBold, array('align' => 'center'));
$table3->addCell(500, $styleCellCenter)->addText(htmlspecialchars("Unit"), $styleHeadCenterUnderlineBold, array('align' => 'center'));
$table3->addCell(4500, $cellColSpan)->addText(htmlspecialchars("Description"), $styleHeadCenterUnderlineBold, array('align' => 'center'));


$table3->addRow();
$cell = $table3->addCell(500, $styleCellCenter);
// Quantity
$strArr = explode("\n", $exp_quantity);
foreach ($strArr as $v) {
    $cell->addText(htmlspecialchars($v), array('name' => 'Calibri', 'size' => 12), array('align' => 'center'));
}

$cell = $table3->addCell(500, $styleCellCenter);
// Unit
$strArr = explode("\n", $exp_unit);
foreach ($strArr as $v) {
    $cell->addText(htmlspecialchars($v), array('name' => 'Calibri', 'size' => 12), array('align' => 'center'));
}

$cell = $table3->addCell(4500, $styleCellHeadCenter);
// Description
$strArr = explode("\n", $exp_discription);
foreach ($strArr as $v) {
    $cell->addText(htmlspecialchars($v), array('name' => 'Calibri', 'size' => 12), array('align' => 'center'));
}

$cell = $table3->addCell(4500, $styleCellTailCenter);
// Amount
$strArr = explode("\n", $exp_amount);
foreach ($strArr as $v) {
    $cell->addText(htmlspecialchars($v), array('name' => 'Calibri', 'size' => 12), array('align' => 'center'));
}

$section->addTextBreak(1);


$table4 = $section->addTable('table5', [
    'borderSize' => 6,
    'borderColor' => '999999', 
    'afterSpacing' => 0, 
    'Spacing'=> 0, 
    'cellMargin'=> 0
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
    $table4->addCell(10500, array('gridSpan' => count($real_payment), 'valign' => 'center', 'borderSize' => 6, 'borderColor' => '999999'))->addText(htmlspecialchars("Details of Received Payment"), $styleHeadCenterUnderlineBold, array('align' => 'center'));
    $table4->addRow();
    foreach ($real_payment as $payment) {
        $cell = $table4->addCell(10500/count($real_payment), $styleCellCenter);
        $cell->addText(htmlspecialchars($payment['kind']), array('name' => 'Calibri', 'size' => 12), array('align' => 'center'));
        $cell->addText(htmlspecialchars($payment['receive_date']), array('name' => 'Calibri', 'size' => 12), array('align' => 'center'));
        $cell->addText(htmlspecialchars($payment['amount']), array('name' => 'Calibri', 'size' => 12), array('align' => 'center'));
        $cell->addText(htmlspecialchars($payment['remark']), array('name' => 'Calibri', 'size' => 12), array('align' => 'center'));
    }
}
else
{
    $table4->addRow();
    $table4->addCell(10500, array('gridSpan' => 1, 'valign' => 'center', 'borderSize' => 6, 'borderColor' => '999999'))->addText(htmlspecialchars("Details of Received Payment"), array('name' => 'Calibri', 'size' => 12, 'bold' => true), array('align' => 'center'));
    $table4->addRow();
   
    $cell = $table4->addCell(10500, $styleCellCenter);
    $cell->addText("", array('name' => 'Calibri', 'size' => 12), array('align' => 'center'));
    $cell->addText("", array('name' => 'Calibri', 'size' => 12), array('align' => 'center'));
    $cell->addText("", array('name' => 'Calibri', 'size' => 12), array('align' => 'center'));
    $cell->addText("", array('name' => 'Calibri', 'size' => 12), array('align' => 'center'));
    
}

$section->addTextBreak(1);


$table5 = $section->addTable('table5', [
    'borderSize' => 6,
    'borderColor' => '999999', 
    'afterSpacing' => 0, 
    'Spacing'=> 0, 
    'cellMargin'=> 0
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
];

$table5->addRow();
$table5->addCell(10500, array('gridSpan' => 5, 'valign' => 'center', 'borderSize' => 6, 'borderColor' => '999999'))->addText(htmlspecialchars("Details of Goods"), array('name' => 'Calibri', 'size' => 12, 'bold' => true), array('align' => 'center'));

foreach ($real_goods as $good) {
    $table5->addRow();
    $table5->addCell(2100, $styleHeadLeftUnderline)->addText(htmlspecialchars($good['date_receive']), array('name' => 'Calibri', 'size' => 12), array('align' => 'left'));
    $table5->addCell(2100, $styleLeftUnderline)->addText(htmlspecialchars($good['customer']), array('name' => 'Calibri', 'size' => 12), array('align' => 'left'));
    $table5->addCell(2100, $styleLeftUnderline)->addText(htmlspecialchars($good['description']), array('name' => 'Calibri', 'size' => 12), array('align' => 'left'));
    $table5->addCell(2100, $styleLeftUnderline)->addText(htmlspecialchars($good['quantity']), array('name' => 'Calibri', 'size' => 12), array('align' => 'left'));
    $table5->addCell(2100, $styleTailLeftUnderline)->addText(htmlspecialchars($good['supplier']), array('name' => 'Calibri', 'size' => 12), array('align' => 'left'));
   
}

if(count($real_goods) == 0)
{
    $table5->addRow();
    $table5->addCell(2100, $styleHeadLeftUnderline)->addText("", array('name' => 'Calibri', 'size' => 12), array('align' => 'left'));
    $table5->addCell(2100, $styleLeftUnderline)->addText("", array('name' => 'Calibri', 'size' => 12), array('align' => 'left'));
    $table5->addCell(2100, $styleLeftUnderline)->addText("", array('name' => 'Calibri', 'size' => 12), array('align' => 'left'));
    $table5->addCell(2100, $styleLeftUnderline)->addText("", array('name' => 'Calibri', 'size' => 12), array('align' => 'left'));
    $table5->addCell(2100, $styleTailLeftUnderline)->addText("", array('name' => 'Calibri', 'size' => 12), array('align' => 'left'));
}

$section->addTextBreak(1);

$table6 = $section->addTable('table6', [
    'borderSize' => 0,
    'borderColor' => 'ffffff', 
    'afterSpacing' => 0, 
    'Spacing'=> 0, 
    'cellMargin'=> 0
]);


$table6->addRow();
$table6->addCell(10500, array('gridSpan' => 5, 'valign' => 'center', 'borderSize' => 0, 'borderColor' => 'ffffff'))->addText(htmlspecialchars("Present upon pick up:"), array('name' => 'Calibri', 'size' => 12), array('align' => 'left'));

$table6->addRow();
$table6->addCell(10500, array('gridSpan' => 5, 'valign' => 'center', 'borderSize' => 0, 'borderColor' => 'ffffff'))->addText(htmlspecialchars(""), array('name' => 'Calibri', 'size' => 12), array('align' => 'left'));

$table6->addRow();
$cell = $table6->addCell(2100);
$cell->addText(htmlspecialchars("●  TEXT"), array('name' => 'Calibri', 'size' => 10), array('align' => 'left'));
$cell->addText(htmlspecialchars("●  FAX"), array('name' => 'Calibri', 'size' => 10), array('align' => 'left'));
$cell->addText(htmlspecialchars("●  EMAIL"), array('name' => 'Calibri', 'size' => 10), array('align' => 'left'));

$cell = $table6->addCell(2100);
$cell->addText(htmlspecialchars("●  VIBER"), array('name' => 'Calibri', 'size' => 10), array('align' => 'left'));
$cell->addText(htmlspecialchars("●  SKYPE"), array('name' => 'Calibri', 'size' => 10), array('align' => 'left'));

$cell = $table6->addCell(2100);
$cell->addText(htmlspecialchars("●  MESSENGER"), array('name' => 'Calibri', 'size' => 10), array('align' => 'left'));
$cell->addText(htmlspecialchars("●  OTHER"), array('name' => 'Calibri', 'size' => 10), array('align' => 'left'));

$cell = $table6->addCell(2100);
$cell->addText(htmlspecialchars(""), array('name' => 'Calibri', 'size' => 10), array('align' => 'left'));

$cell = $table6->addCell(2100);
$cell->addText(htmlspecialchars(""), array('name' => 'Calibri', 'size' => 10), array('align' => 'left'));
$cell->addText(htmlspecialchars(""), array('name' => 'Calibri', 'size' => 10), array('align' => 'left'));
$cell->addText(htmlspecialchars("STAMP HERE"), array('name' => 'Calibri', 'size' => 12), array('align' => 'left'));


$section->addTextBreak(1);

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
        case "":
            $type = "Advance Payment";
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
