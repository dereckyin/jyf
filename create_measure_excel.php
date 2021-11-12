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


function GetShipper($shipper_id){
    $shipper = "";
    if($shipper_id == 0)
        $shipper = "";
    if($shipper_id == 1)
        $shipper = "盛盛";
    if($shipper_id == 2)
        $shipper = "中亞菲";
    if($shipper_id == 3)
        $shipper = "心心";
    return $shipper;
}

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

$excel_header = $excel_content = $excel_file = '';
$n = "\n";
$data = array();

$id = stripslashes((isset($_POST['id']) ?  $_POST['id'] : ""));

$sql = "SELECT 0 as is_checked, rc.id, rc.date_receive, rc.customer, rc.email, rc.description, rc.quantity, rc.supplier, rc.kilo, rc.cuft, rc.taiwan_pay, rc.courier_pay, rc.courier_money, rc.remark, rc.picname, rc.crt_time, rc.crt_user, l.container_number  FROM receive_record rc 
left join loading l on l.id = rc.batch_num where rc.status = '' and  rc.batch_num in ($id) ";

$sql = $sql . " ORDER BY binary rc.customer, rc.date_receive ";

$excel_file = 'works_excel.xlsx';

$result = mysqli_query($conn,$sql);

$styleArray = array(
    'borders' => array(
        'allBorders' => array(
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            'color' => array('rgb' => '000000'),
        ),
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
$sheet->setCellValue('A1', '日期 Date Recive');
$sheet->setCellValue('B1', '收件人 Company/Customer');
$sheet->setCellValue('C1', '櫃號 Container Number');
$sheet->setCellValue('D1', '貨品名稱 Description');
$sheet->setCellValue('E1', '件數 Quantity');
$sheet->setCellValue('F1', '重量 Kilo');
$sheet->setCellValue('G1', '才積 Cuft');
$sheet->setCellValue('H1', '寄貨人 Supplier');
$sheet->setCellValue('I1', '備註 Remark');

$i = 2;
while ($row = mysqli_fetch_array($result))
{
    $sheet->setCellValue('A' . $i, $row[2]);
    $sheet->setCellValue('B' . $i, $row[3]);
    $sheet->setCellValue('C' . $i, $row[17]);
    $sheet->setCellValue('D' . $i, $row[5]);
    $sheet->setCellValue('E' . $i, $row[6]);
    $sheet->setCellValue('F' . $i, $row[8] > 0 ? $row[8] : '');
    $sheet->setCellValue('G' . $i, $row[9] > 0 ? $row[9] : '');
    $sheet->setCellValue('H' . $i, $row[7]);
    $sheet->setCellValue('I' . $i, $row[13]);

    $sheet->getStyle('A'. $i. ':' . 'I' . $i)->applyFromArray($styleArray);

    $i++;
}

$sheet->getStyle('A1:' . 'I1')->getFont()->setBold(true);
$sheet->getStyle('A1:' . 'I' . --$i)->applyFromArray($styleArray);



$sql = "SELECT lo.shipping_mark, lo.actual_weight, lo.container_number, lo.seal, lo.so, lo.ship_company, lo.ship_boat, lo.neck_cabinet, lo.shipper, lo.broker, lo.date_sent, lo.etd_date, lo.ob_date, lo.eta_date, lo.date_arrive, lo.estimate_weight  FROM loading lo where lo.id in ($id) order by crt_time desc";

$result1 = mysqli_query($conn,$sql);
while($row = mysqli_fetch_assoc($result1))
    $merged_results[] = $row;

    $page = 1;
foreach($merged_results as $row)
{
    $mark = $row['shipping_mark'];
    $es_weight = $row['estimate_weight'];
    $weight = $row['actual_weight'];
    $container = $row['container_number'];
    $seal = $row['seal'];
    $so = $row['so'];
    $company = $row['ship_company'];
    $boat = $row['ship_boat'];
    $cabinet = $row['neck_cabinet'];
    $shipper = GetShipper($row['shipper']);
    $date_sent = $row['date_sent'];
    $etd = $row['etd_date'];
    $ob = $row['ob_date'];
    $eta = $row['eta_date'];
    $cr = $row['date_arrive'];
    $broker = $row['broker'];

    
    $sheet->setCellValue('A'  . ($i + 3), '嘜頭 Shopping Mark');
    $sheet->setCellValue('B'  . ($i + 3), $mark);

    $sheet->setCellValue('A'  . ($i + 4), '空櫃重 Empty Container Weight');
    $sheet->setCellValue('B'  . ($i + 4), $es_weight);

    $sheet->setCellValue('A'  . ($i + 5), '櫃重 Actual Weight');
    $sheet->setCellValue('B'  . ($i + 5), $weight);

    $sheet->setCellValue('A'  . ($i + 6), '櫃號 Container Number');
    $sheet->setCellValue('B'  . ($i + 6), $container);

    $sheet->setCellValue('A'  . ($i + 7), '封條 Seal');
    $sheet->setCellValue('B'  . ($i + 7), $seal);

    $sheet->setCellValue('A'  . ($i + 8), 'S/O');
    $sheet->setCellValue('B'  . ($i + 8), $so);

    $sheet->setCellValue('A'  . ($i + 9), '船公司 Shipping Line Company');
    $sheet->setCellValue('B'  . ($i + 9), $company);

    $sheet->setCellValue('A'  . ($i + 10), '船名航次 Shipping Line Boat');
    $sheet->setCellValue('B'  . ($i + 10), $boat);

    $sheet->setCellValue('A'  . ($i + 11), '領櫃 Neck Cabinet');
    $sheet->setCellValue('B'  . ($i + 11), $cabinet);

    $sheet->setCellValue('A'  . ($i + 12), '結關 Date Sent');
    $sheet->setCellValue('B'  . ($i + 12), $date_sent);

    $sheet->setCellValue('A'  . ($i + 13), 'ETD');
    $sheet->setCellValue('B'  . ($i + 13), $etd);

    $sheet->setCellValue('A'  . ($i + 14), 'O/B');
    $sheet->setCellValue('B'  . ($i + 14), $ob);

    $sheet->setCellValue('A'  . ($i + 15), 'ETA');
    $sheet->setCellValue('B'  . ($i + 15), $eta);

    $sheet->setCellValue('A'  . ($i + 16), 'C/R');
    $sheet->setCellValue('B'  . ($i + 16), $cr);

    $sheet->setCellValue('A'  . ($i + 17), '領櫃人 Broker');
    $sheet->setCellValue('B'  . ($i + 17), $broker);

    $sheet->setCellValue('A'  . ($i + 18), '出貨人 Shipper');
    $sheet->setCellValue('B'  . ($i + 18), $shipper);

    $sheet->getStyle('A' . ($i + 3) . ':' . 'A' . ($i + 18))->getFont()->setBold(true);
    $sheet->getStyle('A' . ($i + 3) . ':' . 'B' . ($i + 18))->applyFromArray($styleArray);

    $i = $i + 18;

    $page++;
}
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
    header('Content-Disposition: attachment;filename="丈量明細.xlsx"');
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
