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
$container_number = (isset($_POST['container_number']) ?  $_POST['container_number'] : '');
// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
            http_response_code(200);

            if($date_start == '' && $date_end == '')
            {
                $date_start = date('Y');

                $this_year = date("Y/m/d",strtotime($date_start . '-01-01' . " first day of 0 year"));
                $last_year = date("Y/m/d",strtotime($date_start . '-01-01' . " first day of 1 year"));

                $date_start    = $this_year;
                $date_end      = $last_year;
            }

                $recode = $receive_record->TaiwanPayQueryDetail($date_start, $date_end, $container_number);
          
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
                $sheet->setCellValue('E1', '重量');
                $sheet->setCellValue('F1', '材積');
                $sheet->setCellValue('G1', '寄貨人');
                $sheet->setCellValue('H1', '請款金額');
                $sheet->setCellValue('I1', '付款金額');
                $sheet->setCellValue('J1', '付款日期');
                $sheet->setCellValue('K1', '備註');
                $sheet->setCellValue('L1', '請款金額(菲幣)');
                $sheet->setCellValue('M1', '請款金額(台幣)');
                $sheet->setCellValue('N1', '付款金額');
                $sheet->setCellValue('O1', '付款日期');
                $sheet->setCellValue('P1', '補充說明');

                $i = 2;
                foreach($recode as $row)
                {
                    $sheet->setCellValue('A' . $i, $row['date_receive']);
                    $sheet->setCellValue('B' . $i, $row['customer']);
                    $sheet->setCellValue('C' . $i, $row['description']);
                    $sheet->setCellValue('D' . $i, $row['quantity']);
                    $sheet->setCellValue('E' . $i, '');
                    $sheet->setCellValue('F' . $i, '');
                    $sheet->setCellValue('G' . $i, $row['supplier']);
                    $sheet->setCellValue('H' . $i, '');
                    $sheet->setCellValue('I' . $i, '');
                    $sheet->setCellValue('J' . $i, '');
                    $sheet->setCellValue('K' . $i, $row['remark']);
                    $sheet->setCellValue('L' . $i, $row['ar_php']);
                    $sheet->setCellValue('M' . $i, $row['ar']);
                    $sheet->setCellValue('N' . $i, $row['amount']);
                    $sheet->setCellValue('O' . $i, $row['payment_date']);
                    $sheet->setCellValue('P' . $i, $row['note']);

                    $sheet->getStyle('A'. $i. ':' . 'P' . $i)->applyFromArray($styleArray);

                    $i++;
                }

                $sheet->getStyle('A1:' . 'P1')->getFont()->setBold(true);
                $sheet->getStyle('A1:' . 'P' . --$i)->applyFromArray($styleArray);

           

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