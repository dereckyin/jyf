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


$subquery = "";

    $merged_results = array();

    $key=array();

    $sql = "SELECT customer FROM  receive_record where date_receive <> '' and status = '' and batch_num = 0 " .($id ? " and id in ($id)" : '') . " GROUP BY date_receive, customer  ORDER BY date_receive;";

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

            $subquery = "SELECT 0 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, crt_time, crt_user FROM receive_record where status = '' and date_receive <> '' and batch_num = 0 and customer = ?  " .($id ? " and id in ($id)" : '') . "ORDER BY date_receive ";

            if ($stmt = mysqli_prepare($conn, $subquery)) {

                mysqli_stmt_bind_param($stmt, "s", $row['customer']);
            
                /* execute query */
                mysqli_stmt_execute($stmt);

                $result1 = mysqli_stmt_get_result($stmt);

                while($row = mysqli_fetch_assoc($result1)) {
                    $merged_results[] = $row;
                }
            }
        }
    }

    $subquery = "SELECT 0 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, crt_time, crt_user FROM receive_record where status = '' and date_receive = '' and batch_num = 0  " .($id ? " and id in ($id)" : '') . "ORDER BY customer, date_receive ";

    $result1 = mysqli_query($conn,$subquery);
    while($row = mysqli_fetch_assoc($result1))
        $merged_results[] = $row;

$excel_file = 'works_excel.xlsx';

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

$spreadsheet->setActiveSheetIndex(0);
$sheet = $spreadsheet->getActiveSheet();

$sheet->setTitle('Sheet 1');

$sheet->setCellValue('A1', '日期');
$sheet->setCellValue('B1', '收件人');
$sheet->setCellValue('C1', '貨品名稱');
$sheet->setCellValue('D1', '件數');
$sheet->setCellValue('E1', '重量');
$sheet->setCellValue('F1', '才積');
$sheet->setCellValue('G1', '寄貨人');
$sheet->setCellValue('H1', '備註');

$i = 2;
foreach($merged_results as $row)
{
    $sheet->setCellValue('A' . $i, $row['date_receive']);
    $sheet->setCellValue('B' . $i, $row['customer']);
    $sheet->setCellValue('C' . $i, $row['description']);
    $sheet->setCellValue('D' . $i, $row['quantity']);
    $sheet->setCellValue('E' . $i, $row['kilo'] > 0 ? $row['kilo'] : '');
    $sheet->setCellValue('F' . $i, $row['cuft'] > 0 ? $row['cuft'] : '');
    $sheet->setCellValue('G' . $i, $row['supplier']);
    $sheet->setCellValue('H' . $i, $row['remark']);

    $sheet->getStyle('A'. $i. ':' . 'H' . $i)->applyFromArray($styleArray);

    $i++;
}

$sheet->getStyle('A1:' . 'H1')->getFont()->setBold(true);
$sheet->getStyle('A1:' . 'H' . --$i)->applyFromArray($styleArray);
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
    header('Content-Disposition: attachment;filename="收貨明細.xlsx"');
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
