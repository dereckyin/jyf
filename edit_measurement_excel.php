<?php
error_reporting(E_ALL);

require_once "api/db.php";

require 'vendor/autoload.php';

include_once 'api/config/core.php';
include_once 'api/libs/php-jwt-master/src/BeforeValidException.php';
include_once 'api/libs/php-jwt-master/src/ExpiredException.php';
include_once 'api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'api/libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

try {
    $jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
    // decode jwt
    $decoded = JWT::decode($jwt, $key, array('HS256'));

    //if(passport_decrypt( base64_decode($uid)) !== $decoded->data->username )
    //    header( 'location:index.php' );
}
    // if decode fails, it means jwt is invalid
catch (Exception $e){

    die();
}



function GetMeasureByBatchNumber($id, $db){
    $query = "
            SELECT 0 as is_checked, id, date_encode, date_arrive, currency_rate, remark
                FROM measure_ph
            WHERE  id = " . $id;

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $is_checked = $row['is_checked'];
        $id = $row['id'];
        $date_encode = $row['date_encode'];
        $date_arrive = $row['date_arrive'];
        $currency_rate = $row['currency_rate'];
        $remark = $row['remark'];

        $record = GetMeasureDetail($row['id'], $db);

        $merged_results = array(
            "is_checked" => $is_checked,
            "id" => $id,
            "date_encode" => $date_encode,
            "date_arrive" => $date_arrive,
            "currency_rate" => $currency_rate,
            "remark" => $remark,
            "record" => $record,
        );
    }

    return $merged_results;
}

function GetMeasureDetail($id, $db){
    $query = "
            SELECT 0 as is_checked, id, kilo, cuft, kilo_price, cuft_price, charge
                FROM measure_detail
            WHERE  measure_id = " . $id . "
            AND `status` <> -1 
    ";

    // prepare the query
    $result = mysqli_query($db,$query);

    $merged_results = [];

    while ($row = mysqli_fetch_array($result)) {
        $is_checked = $row['is_checked'];
        $id = $row['id'];
        $kilo = $row['kilo'] == 0 ? "" : $row['kilo'];
        $cuft = $row['cuft'] == 0 ? "" : $row['cuft'];
        $kilo_price = $row['kilo_price'] == 0 ? "" : $row['kilo_price'];
        $cuft_price = $row['cuft_price'] == 0 ? "" : $row['cuft_price'];
        $charge = $row['charge'] == 0 ? "" : $row['charge'];

        $record = GetMeasureDetailRecord($row['id'], $db);


        $merged_results[] = array(
            "is_checked" => $is_checked,
            "order" => $id,
            "group_id" => 0,
            "kilo" => $kilo,
            "cuft" => $cuft,
            "kilo_price" => $kilo_price,
            "cuft_price" => $cuft_price,
            "charge" => $charge,
           "record" => $record,
        );
    }

    return $merged_results;
}

function GetMeasureDetailRecord($id, $db){
    $query = "SELECT rc.id, rc.date_receive, rc.customer, rc.description, rc.quantity, rc.supplier, rc.remark, cp.customer cust, rc.kilo, rc.cuft, rc.taiwan_pay, rc.courier_pay
                FROM measure_record_detail rd
                    left JOIN receive_record rc ON
                    rd.record_id = rc.id
                    left join contactor_ph cp on rd.cust = cp.id
                WHERE rd.detail_id = " . $id . "
            AND rd.`status` <> -1 ";

    // prepare the query
    $result = mysqli_query($db,$query);

    $merged_results = [];

    while ($row = mysqli_fetch_array($result)) {
    
        $id = $row['id'];
        $date_receive = $row['date_receive'];
        $customer = $row['customer'];
        $description = $row['description'];
        $quantity = $row['quantity'];
        $supplier = $row['supplier'];
        $remark = $row['remark'];
        $cust = $row['cust'];
        $kilo = $row['kilo'];
        $cuft = $row['cuft'];
        $taiwan_pay = $row['taiwan_pay'];
        $courier_pay = $row['courier_pay'];

        

        $merged_results[] = array(
            "id" => $id,
            "date_receive" => $date_receive,
            "customer" => $customer,
            "description" => $description,
            "quantity" => $quantity,
            "supplier" => $supplier,
            "remark" => $remark,
            "cust" => $cust,
            "kilo" => $kilo,
            "cuft" => $cuft,
            "taiwan_pay" => $taiwan_pay,
            "courier_pay" => $courier_pay,
        );
    }

    return $merged_results;
}


$excel_header = $excel_content = $excel_file = '';
$n = "\n";
$result = array();

$id = stripslashes((isset($_POST['id']) ?  $_POST['id'] : ""));
$qty = stripslashes((isset($_POST['qty']) ?  $_POST['qty'] : ""));
$container = stripslashes((isset($_POST['container']) ?  $_POST['container'] : ""));
$date_cr = stripslashes((isset($_POST['date_cr']) ?  $_POST['date_cr'] : ""));
$date_encode = stripslashes((isset($_POST['date_encode']) ?  $_POST['date_encode'] : ""));
$currency_rate = stripslashes((isset($_POST['currency_rate']) ?  $_POST['currency_rate'] : ""));
$remark = stripslashes((isset($_POST['remark']) ?  $_POST['remark'] : ""));

$result = GetMeasureDetail($id, $conn);


$styleArray = array(
    'borders' => array(
        'allBorders' => array(
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => array('rgb' => '000000'),
        ),
    ),

    'alignment' => array(
        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
    ),
);

$spreadsheet = new Spreadsheet();

$spreadsheet->getProperties()->setCreator('PhpOffice')
        ->setLastModifiedBy('PhpOffice')
        ->setTitle('Office 2007 XLSX Test Document')
        ->setSubject('Office 2007 XLSX Test Document')
        ->setDescription('PhpOffice')
        ->setKeywords('PhpOffice')
        ->setCategory('PhpOffice');

$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', '日期 Date Receive');
$sheet->setCellValue('B1', '收件人 Company/customer');
$sheet->setCellValue('C1', '收件人(菲) Company/customer(PH)');
$sheet->setCellValue('D1', '貨品名稱 Description');
$sheet->setCellValue('E1', '件數 Quantity');
$sheet->setCellValue('F1', '測量重量 Measure Kilo');
$sheet->setCellValue('G1', '測量才積 Measure Cuft');
$sheet->setCellValue('H1', '重量單價 Price per Kilo');
$sheet->setCellValue('I1', '才積單價 Price per Cuft');
$sheet->setCellValue('J1', '收費金額 Amount');
$sheet->setCellValue('K1', '寄貨人 Supplier');
$sheet->setCellValue('L1', '備註 Remark');
$sheet->setCellValue('M1', '重量 Kilo');
$sheet->setCellValue('N1', '才積 Cuft');
$sheet->setCellValue('O1', '台灣付運費 Taiwan Pay');
$sheet->setCellValue('P1', '代墊 Courier/payment');

$i = 2;

foreach ($result as $measure)
{
    if(count($measure["record"]) > 1)
        $j = $i;

    foreach($measure["record"] as $rec)
    {
        $sheet->setCellValue('A' . $i, $rec["date_receive"]);
        $sheet->setCellValue('B' . $i, $rec["customer"]);
        $sheet->setCellValue('C' . $i, $rec["cust"]);
        $sheet->setCellValue('D' . $i, $rec["description"]);
        $sheet->setCellValue('E' . $i, $rec["quantity"]);
        $sheet->setCellValue('F' . $i, $measure["kilo"]);
        $sheet->setCellValue('G' . $i, $measure["cuft"]);
        
        $kilo_price = "";
        if($measure["kilo"] > 45)
            $kilo_price = 45;
        if($measure["kilo"] >= 300)
            $kilo_price = 42;
        if($measure["kilo"] >= 1000)
            $kilo_price = 40;
        if($measure["kilo"] >= 3000)
            $kilo_price = 38.5;
        $sheet->setCellValue('H' . $i, $measure["kilo"] == "" ? "" : $kilo_price);

        $cuft_price = "";
        if($measure["cuft"] > 4.5)
            $cuft_price = 450;
        if($measure["cuft"] >= 30)
            $cuft_price = 430;
        if($measure["cuft"] >= 100)
            $cuft_price = 410;
        if($measure["cuft"] >= 300)
            $cuft_price = 395;
        $sheet->setCellValue('I' . $i, $measure["cuft"] == "" ? "" : $cuft_price);
        $sheet->setCellValue('J' . $i, $measure["charge"]);
        $sheet->setCellValue('K' . $i, $rec["supplier"]);
        $sheet->setCellValue('L' . $i, $rec["remark"]);
        $sheet->setCellValue('M' . $i, $rec["kilo"]);
        $sheet->setCellValue('N' . $i, $rec["cuft"]);
        $sheet->setCellValue('O' . $i, $rec["taiwan_pay"] == 1 ? "Yes" : "");
        $sheet->setCellValue('P' . $i, $rec["courier_pay"] == 1 ? "Yes" : "");

        $sheet->getStyle('A'. $i. ':' . 'P' . $i)->applyFromArray($styleArray);
        $i++;
    }

    if(count($measure["record"]) > 1)
    {
        $sheet->mergeCells('F' . $j . ':F' . ($i -1));
        $sheet->mergeCells('G' . $j . ':G' . ($i -1));
        $sheet->mergeCells('H' . $j . ':H' . ($i -1));
        $sheet->mergeCells('I' . $j . ':I' . ($i -1));
        $sheet->mergeCells('J' . $j . ':J' . ($i -1));
    }

}

$sheet->getStyle('A1:' . 'P1')->getFont()->setBold(true);
$sheet->getStyle('A1:' . 'P' . --$i)->applyFromArray($styleArray);

$sheet->setCellValue('A'  . ($i + 3), 'Qty of Containers 貨櫃數量');
$sheet->setCellValue('B'  . ($i + 3), $qty);

$sheet->setCellValue('A'  . ($i + 4), 'Container Number 櫃號');
$sheet->setCellValue('B'  . ($i + 4), $container);

$sheet->setCellValue('A'  . ($i + 5), 'Date C/R (Date Container arrived Manila) 貨櫃到倉日期');
$sheet->setCellValue('B'  . ($i + 5), $date_cr);

$sheet->setCellValue('A'  . ($i + 6), 'Currency Rate 匯率');
$sheet->setCellValue('B'  . ($i + 6), $currency_rate);

$sheet->setCellValue('A'  . ($i + 7), 'Remark 備註');
$sheet->setCellValue('B'  . ($i + 7), $remark);

$sheet->getStyle('A' . ($i + 3) . ':' . 'A' . ($i + 7))->getFont()->setBold(true);
$sheet->getStyle('A' . ($i + 3) . ':' . 'B' . ($i + 7))->applyFromArray($styleArray);

/*
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('A1', '分區', PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('B1', '學校名稱', PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('C1', '年級', PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('D1', '科別', PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('E1', '班級', PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('F1', '作者姓名', PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('G1', '作品標題', PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('H1', '閱讀書目', PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('I1', '指導老師', PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('J1', '投稿日期', PHPExcel_Cell_DataType::TYPE_STRING);
    $objPHPExcel->getActiveSheet()->setCellValueExplicit('K1', '狀態', PHPExcel_Cell_DataType::TYPE_STRING);

	$result = mysqli_query($conn,$sql);
   
        $i = 2;
    while ($row = mysqli_fetch_array($result))
    {
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('A' . $i, $row[0], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('B' . $i, $row[1], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('C' . $i, $row[1], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('D' . $i, $row[1], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('E' . $i, $row[1], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('F' . $i, $row[1], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('G' . $i, $row[1], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('H' . $i, $row[1], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('I' . $i, $row[1], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('J' . $i, $row[1], PHPExcel_Cell_DataType::TYPE_STRING);
        $objPHPExcel->getActiveSheet()->setCellValueExplicit('K' . $i, $row[1], PHPExcel_Cell_DataType::TYPE_STRING);
        $i++;
        
    }
*/
    $spreadsheet->setActiveSheetIndex(0);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="measurement.xlsx"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');
    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');


    //$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    //$objWriter->save('php://output');
    // Close connection
    mysqli_close($conn);

    exit;
?>
