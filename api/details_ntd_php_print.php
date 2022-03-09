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

$start_date = str_replace('/', '-', $start_date);
$end_date = str_replace('/', '-', $end_date);

$keyword = (isset($_POST['keyword']) ?  $_POST['keyword'] : '');

// if jwt is not empty
if($jwt){ 
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
            http_response_code(200);

            $merged_results = array();

            $query = "SELECT ss.id, 
                        client_name, 
                        payee_name, 
                        amount, 
                        amount_php, 
                        rate, 
                        rate_yahoo, 
                        total_receive,
                        overpayment,
                        pay_date,
                        payee,
                        remark,
                        ss.`status` from details_ntd_php ss left join details_ntd_php_record sr
                        on ss.id = sr.sales_id
                        where 1=1  ";

        if($start_date!='') {
            $query = $query . " and sr.receive_date >= '$start_date' ";
        }

        if($end_date!='') {
            $query = $query . " and sr.receive_date <= '$end_date" . " 23:59:59' ";
        }

        if($keyword != '')
            $query .= " AND (ss.client_name like '%" . $keyword . "%' or ss.payee_name like '%" . $keyword . "%' or ss.remark like '%" . $keyword . "%' or ss.payee like '%" . $keyword . "%') ";

        $query .= "group by
            ss.id, 
            ss.client_name, 
            ss.payee_name, 
            ss.amount,
            ss.amount_php,
            ss.rate,
            ss.rate_yahoo,
            ss.total_receive,
            ss.overpayment,
            ss.pay_date,
            ss.payee,
            ss.remark,
            ss.`status` ";

        $stmt = $db->prepare($query);
        $stmt->execute();
    
        $merged_results = [];
        
        $items = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $client_name = $row['client_name'];
            $payee_name = $row['payee_name'];
            $amount = $row['amount'];
            $amount_php = $row['amount_php'];
            $rate = $row['rate'];
            $rate_yahoo = $row['rate_yahoo'];
            $total_receive = $row['total_receive'];
            $overpayment = $row['overpayment'];
            $pay_date = $row['pay_date'];
            $payee = $row['payee'];
            $remark = $row['remark'];
            $status = $row['status'];
 
            $items = GetSalesDetail($id, $db);
           
            $merged_results[] = array( 
                "is_edited" => 1,
                "id" => $id,
                "client_name" => $client_name,
                "payee_name" => $payee_name,
                "amount" => $amount,
                "amount_php" => $amount_php,
                "rate" => $rate,
                "rate_yahoo" => $rate_yahoo,
                "total_receive" => $total_receive,
                "overpayment" => $overpayment,
                "pay_date" => $pay_date,
                "payee" => $payee,
                "remark" => $remark,
                "details"=> $items,
                "status" => $status
            );
        }

        
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


            $sheet->setCellValue('A1', '客戶名 Customer');
            $sheet->setCellValue('B1', '廠商名稱 Payee');
            $sheet->setCellValue('C1', '台幣金額 NTD');
            $sheet->setCellValue('D1', '匯率(雅虎) Currency Rate (Yahoo)');
            $sheet->setCellValue('E1', '匯率 Currency Rate');
            $sheet->setCellValue('F1', '菲幣金額 PHP');
            $sheet->setCellValue('G1', '收款日期 Received Date');
            $sheet->setCellValue('H1', '付款方式 Payment Method');
            $sheet->setCellValue('I1', '付款細節 Payment Details');
            $sheet->setCellValue('J1', '收款金額 Received Amount');
            $sheet->setCellValue('K1', '總收款金額 Total Received Amount');
            $sheet->setCellValue('L1', '溢付金額 Overpayment');
            $sheet->setCellValue('M1', '備註 Remarks');
            $sheet->setCellValue('N1', '付款日期 Paid Date');
            $sheet->setCellValue('O1', '廠商名稱 Payee');

            $i = 2;

            foreach ($merged_results as $measure)
            {
                if(count($measure["details"]) > 1)
                    $j = $i;
            
                foreach($measure["details"] as $rec)
                {
                    $sheet->setCellValue('A' . $i, $measure["client_name"]);
                    $sheet->setCellValue('B' . $i, $measure["payee_name"]);
                    $sheet->setCellValue('C' . $i, $measure["amount"]);
                    $sheet->setCellValue('D' . $i, $measure["rate_yahoo"]);
                    $sheet->setCellValue('E' . $i, $measure["rate"]);
                    $sheet->setCellValue('F' . $i, $measure["amount_php"]);
                    $sheet->setCellValue('G' . $i, $rec["receive_date"]);
                    $sheet->setCellValue('H' . $i, $rec["payment_method"]);
                    $sheet->setCellValue('I' . $i, $rec["account_number"] . " / " . $rec["check_details"]);
                    $sheet->setCellValue('J' . $i, $rec["receive_amount"] == "" ? $rec['receive_amount'] : "");
                    $sheet->setCellValue('K' . $i, $measure["total_receive"]);
                    $sheet->setCellValue('L' . $i, $measure["overpayment"]);
                    $sheet->setCellValue('M' . $i, $measure["remark"]);
                    $sheet->setCellValue('N' . $i, $measure["pay_date"]);
                    $sheet->setCellValue('O' . $i, $measure["payee"]);
                  
              
                    $sheet->getStyle('A'. $i. ':' . 'O' . $i)->applyFromArray($styleArray);
                    $i++;
                }
            
                if(count($measure["details"]) > 1)
                {
                    $sheet->mergeCells('A' . $j . ':A' . ($i -1));
                    $sheet->mergeCells('B' . $j . ':B' . ($i -1));
                    $sheet->mergeCells('C' . $j . ':C' . ($i -1));
                    $sheet->mergeCells('D' . $j . ':D' . ($i -1));
                    $sheet->mergeCells('E' . $j . ':E' . ($i -1));
                    $sheet->mergeCells('F' . $j . ':F' . ($i -1));
     
                    $sheet->mergeCells('K' . $j . ':K' . ($i -1));
                    $sheet->mergeCells('L' . $j . ':L' . ($i -1));
                    $sheet->mergeCells('M' . $j . ':M' . ($i -1));
                    $sheet->mergeCells('N' . $j . ':N' . ($i -1));
                    $sheet->mergeCells('O' . $j . ':O' . ($i -1));
                }
            
            }
            
            $sheet->getStyle('A1:' . 'O1')->getFont()->setBold(true);
            $sheet->getStyle('A1:' . 'O' . --$i)->applyFromArray($styleArray);
            

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




function GetSalesDetail($sales_id, $db){
    $query = "
            SELECT 0 as is_checked, id, receive_date, payment_method, account_number, check_details, receive_amount
                FROM details_ntd_php_record
            WHERE  sales_id = " . $sales_id . "
            AND `status` <> -1 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $is_checked = $row['is_checked'];
        $id = $row['id'];
        $receive_date = $row['receive_date'] == "" ? "" : $row['receive_date'];
        $payment_method = $row['payment_method'] == "" ? "" : $row['payment_method'];
        $account_number = $row['account_number'] == "" ? "" : $row['account_number'];
        $check_details = $row['check_details'] == "" ? "" : $row['check_details'];
        $receive_amount = $row['receive_amount'] == "" ? "" : $row['receive_amount'];
    
       
        $merged_results[] = array(
            "is_checked" => $is_checked,
            "id" => $id,
            "receive_date" => $receive_date,
            "payment_method" => $payment_method,
            "account_number" => $account_number,
            "check_details" => $check_details,
           "receive_amount" => $receive_amount,
        );
    }

    return $merged_results;
}


?>