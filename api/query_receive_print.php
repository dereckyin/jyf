<?php
// required headers
 error_reporting(0);
 
 require '../vendor/autoload.php';
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

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
 
// get database connection
$database = new Database();
$db = $database->getConnection();
 
// instantiate loading object
$receive_record = new ReceiveRecord($db);
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

$date_start = (isset($_POST['date_start']) ?  $_POST['date_start'] : '');
$date_end = (isset($_POST['date_end']) ?  $_POST['date_end'] : '');
$customer = (isset($_POST['customer']) ?  $_POST['customer'] : '');
$supplier = (isset($_POST['supplier']) ?  $_POST['supplier'] : '');
// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
            http_response_code(200);
            $recode = $receive_record->Query_Receive_Query_Simple($date_start, $date_end, $customer, $supplier);
            // response in json format
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
            $sheet->setTitle("Sheet 1");

    
                
                $sheet->setCellValue('A1', '日期');
                $sheet->setCellValue('B1', '收件人');
                $sheet->setCellValue('C1', '品名');
                $sheet->setCellValue('D1', '件數');
                $sheet->setCellValue('E1', '寄貨人');
                $sheet->setCellValue('F1', '備註');
                $sheet->setCellValue('G1', '櫃號');
                $sheet->setCellValue('H1', '結關日期');
                $sheet->setCellValue('I1', '貨櫃到達日期');
                $sheet->setCellValue('J1', '丈量日期');
                $sheet->setCellValue('K1', '提貨日期');
                $sheet->setCellValue('L1', '付款日期');

                $i = 2;
                foreach($recode as $row)
                {
                    $sheet->setCellValue('A' . $i, $row['date_receive']);
                    $sheet->setCellValue('B' . $i, $row['customer']);
                    $sheet->setCellValue('C' . $i, $row['description']);
                    $sheet->setCellValue('D' . $i, $row['quantity']);
                    $sheet->setCellValue('E' . $i, $row['supplier']);
                    $sheet->setCellValue('F' . $i, $row['remark']);
                    $sheet->setCellValue('G' . $i, $row['container_number']);
                    $sheet->setCellValue('H' . $i, $row['date_sent']);
                    $sheet->setCellValue('I' . $i, $row['date_arrive']);
                    $sheet->setCellValue('J' . $i, $row['date_encode']);
                    $sheet->setCellValue('K' . $i, '');
                    $sheet->setCellValue('L' . $i, '');

                    $sheet->getStyle('A'. $i. ':' . 'L' . $i)->applyFromArray($styleArray);

                    $i++;
                }

                $sheet->getStyle('A1:' . 'L1')->getFont()->setBold(true);
                $sheet->getStyle('A1:' . 'L' . --$i)->applyFromArray($styleArray);

            

            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="file.xlsx"');

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


            exit;
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