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

$excel_header = $excel_content = $excel_file = '';
$n = "\n";
$data = array();

$id = stripslashes((isset($_GET['id']) ?  $_GET['id'] : ""));

$sql = "SELECT 0 as is_checked, id, company, customer, address, phone, fax, mobile, email, remark, acquisition, acquisition_by, date_to_call, crt_time, crt_user  FROM contactor_ph_po where status = '' " .($id ? " and id in ($id)" : '') ;

$sql = $sql . " ORDER BY customer ";

$excel_file = 'works_excel.xlsx';

$result = mysqli_query($conn,$sql);

//$styleArray = array(
//    'borders' => array(
//        'allborders' => array(
//            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
//            'color' => array('rgb' => '000000'),
//        ),
//    ),
//);

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
$sheet->setCellValue('A1', 'Company Name');
$sheet->setCellValue('B1', 'Customer\'s Name');
$sheet->setCellValue('C1', 'Address');
$sheet->setCellValue('D1', 'Landline Number');
$sheet->setCellValue('E1', 'Fax Number');
$sheet->setCellValue('F1', 'Mobile Number');
$sheet->setCellValue('G1', 'E-mail');
$sheet->setCellValue('H1', 'Remarks');
$sheet->setCellValue('I1', 'Acquisition');
$sheet->setCellValue('J1', 'Date To Call');

$i = 2;
while ($row = mysqli_fetch_array($result))
{
    $sheet->setCellValue('A' . $i, $row[2]);
    $sheet->setCellValue('B' . $i, $row[3]);
    $sheet->setCellValue('C' . $i, $row[4]);
    $sheet->setCellValue('D' . $i, $row[5]);
    $sheet->setCellValue('E' . $i, $row[6]);
    $sheet->setCellValue('F' . $i, $row[7]);
    $sheet->setCellValue('G' . $i, $row[8]);
    $sheet->setCellValue('H' . $i, $row[9]);
    $sheet->setCellValue('I' . $i, $row[10] == "ads" ? "Newspaper Ads " . $row[11] : ($row[10] == "refer" ? "Refer By " . $row[11] : ""));
    $sheet->setCellValue('J' . $i, $row[12]);


    $sheet->getStyle('A'. $i. ':' . 'J' . $i)->applyFromArray($styleArray);

    $i++;
}

$sheet->getStyle('A1:' . 'J1')->getFont()->setBold(true);
$sheet->getStyle('A1:' . 'J' . --$i)->applyFromArray($styleArray);

//unset($styleArray);
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
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="customer.xlsx"');
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
