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

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
 
 include_once 'config/conf.php';
// get database connection
$database = new Database();
$db = $database->getConnection();

$mail_ip= "https://storage.googleapis.com/feliiximg/";
$files = array();
$explode_row = array();
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

$start_date = (isset($_POST['start_date']) ?  $_POST['start_date'] : '');
$end_date = (isset($_POST['end_date']) ?  $_POST['end_date'] : '');

$start_date = str_replace('-', '/', $start_date);
$end_date = str_replace('-', '/', $end_date);

$category = (isset($_POST['category']) ?  $_POST['category'] : '');
$sub_category = (isset($_POST['sub_category']) ?  $_POST['sub_category'] : '');

if($category == "All")
{
    $category = "";
    $sub_category = "";
}

$account = (isset($_POST['account']) ?  $_POST['account'] : 0);
$keyword = (isset($_POST['keyword']) ?  $_POST['keyword'] : '');
$select_date_type = 1;
// if jwt is not empty
if($jwt){ 
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
            http_response_code(200);

            $merged_results = array();

            $sql = "SELECT account, created_at, category, sub_category, project_name, related_account, details, pic_url, payee, paid_date, cash_in, cash_out, remarks, staff_name, company_name from gcash_expense_recorder_sea_2 where is_enabled = true";
            $sql1 = "";
            $sql2 = "";
            $sql3 = "";
            
            if($account!=0) {
                $sql1 = $sql1 . " and account = '$account' ";
            }else{
                $sql1 = $sql1 . " and account !=0 ";
            }
            
            if($select_date_type == 0){
                if($start_date != '') {
                    $sql1 = $sql1 . " and created_at >= '$start_date' ";
                }
    
                if($end_date != '') {
                    $sql1 = $sql1 . " and created_at < date_add('$end_date', interval 1 day)";
                }
            }
            
            if($select_date_type == 1){
                if($start_date != '') {
                    $sql1 = $sql1 . " and paid_date >= '$start_date' ";
                }
    
                if($end_date != '') {
                    $sql1 = $sql1 . " and paid_date < date_add('$end_date', interval 1 day)";
                }
            }
            
            if($category != '') {
                $sql1 = $sql1 . " and category = '$category' ";
            }
            
            if($sub_category != '') {
                $sql1 = $sql1 . " and sub_category = '$sub_category' ";
            }
            
            if($keyword!= '') {
                $sql2 = "or remarks like '%$keyword%' and is_enabled = true".$sql1;
                $sql3 = "or payee like '%$keyword%' and is_enabled = true".$sql1;
                $sql1 = $sql1 . " and details like '%$keyword%'";
            }

            $sql = $sql .$sql1 . $sql2. $sql3 . " order by paid_date  ";

            $stmt = $db->prepare( $sql );
            $stmt->execute();

            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                $merged_results[] = $row;
          
            // response in json format
            $styleArray = array(
                'borders' => array(
                    'allBorders' => array(
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => array('rgb' => '000000'),
                    ),
                ),

                'alignment' => array(
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP,
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


            // $sheet->setCellValue('A1', 'Account');
            $sheet->setCellValue('A1', 'Date');
            $sheet->setCellValue('B1', 'Category');
            //$sheet->setCellValue('C1', 'Sub Category');
            // $sheet->setCellValue('E1', 'Project Name');
            // $sheet->setCellValue('F1', 'Related Account');
            $sheet->setCellValue('C1', 'Details');
            $sheet->setCellValue('G1', 'Files');
            $sheet->setCellValue('D1', 'Staff Name');
            //$sheet->setCellValue('F1', 'Company/Customer Name');
            $sheet->setCellValue('E1', 'Cash in');
            $sheet->setCellValue('F1', 'Cash out');
       


            $i = 2;
            foreach($merged_results as $row)
            {
                // $sheet->setCellValue('A' . $i, getAccount($row['account']));
                $sheet->setCellValue('A' . $i, getFormatDate($row['paid_date']));
                $sheet->setCellValue('B' . $i, $row['category']);
                //$sheet->setCellValue('C' . $i, $row['sub_category']);
                // $sheet->setCellValue('E' . $i, $row['project_name']);
                // $sheet->setCellValue('F' . $i, $row['related_account']);

                $sheet->getStyle('C' . $i)->getAlignment()->setWrapText(true);
                $sheet->getStyle('C' . $i)->getAlignment()->setWrapText(true);
                $sheet->getColumnDimension('C')->setWidth(50);

                $detail = str_replace("<br>", "\n", $row['details']);
                // $detail = preg_replace('/<a.*?<\/a>/', '', $detail);
                $detail = strip_tags($detail);
                $sheet->setCellValue('C' . $i, $detail);

                if($row['pic_url'] != '')
                {
                    $explode_row = explode(",",$row['pic_url']);
                    $aph = 'G';
                    foreach($explode_row as $pic_urls){
                        //$sheet->getActiveSheet()->unmergeCells('F'.$i:'F'.$i);
                        $link = $mail_ip . $pic_urls;
                        $sheet->setCellValue($aph.'1', 'Files');
                        $sheet->setCellValue($aph.$i, 'File');
                        $sheet->getCell($aph.$i)->getHyperlink()->setUrl($link);
                        $aph++;
                        //array_push($files ,$link);
                    }
                    //$sheet->fromArray($files, NULL, 'F' . $i);
                }
                else
                    $sheet->setCellValue('G' . $i, '');

                
                $sheet->setCellValue('D' . $i, $row['staff_name']);
                //$sheet->setCellValue('F' . $i, $row['company_name']);
                $sheet->setCellValue('E' . $i, $row['cash_in']);
                $sheet->setCellValue('F' . $i, $row['cash_out']);
      
                                      
                $sheet->getStyle('A'. $i. ':' . 'Z' . $i)->applyFromArray($styleArray);

                $i++;
            }

            $sheet->getStyle('A1:' . 'Z1')->getFont()->setBold(true);
            $sheet->getStyle('A1:' . 'Z' . --$i)->applyFromArray($styleArray);

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

function getAccount($loc)
{
    $account = "";
    switch ($loc) {
        case 1:
            $account = "Office Petty Cash";
            break;
        case 2:
            $account = "Security Bank";
            break;
        case 3:
            $account = "Online Transactions";
            break;
   
    }

    return $account;
}

function getFormatDate($date){
    return substr($date,0,10);
}
?>