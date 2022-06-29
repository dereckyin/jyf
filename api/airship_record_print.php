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

 
// get jwt
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
$keyword = (isset($_POST['keyword']) ? $_POST['keyword'] : "");
$start_date = (isset($_POST['start_date']) ? $_POST['start_date'] : "");
$end_date = (isset($_POST['end_date']) ? $_POST['end_date'] : "");
$date_type = (isset($_GET['date_type']) ? $_GET['date_type'] : "");
$page = (isset($_POST['page']) ? $_POST['page'] : 1);
$size  = (isset($_POST['size']) ? $_POST['size'] : 25);

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
                        date_receive, 
                        customer, 
                        address, 
                        description, 
                        quantity, 
                        kilo, 
                        supplier,
                        flight,
                        flight_date,
                        currency,
                        amount,
                        amount_php,
                        total,
                        total_php,
                        pay_date,
                        pay_status,
                        payee,
                        date_arrive,
                        receiver,
                        remark,
                        ss.`status` from airship_records ss 
                        where 1=1  ";


if($date_type == "")
{
        if($start_date!='') {
            $query = $query . " and ss.date_arrive >= '$start_date' ";
            $query_cnt = $query_cnt . " and ss.date_arrive >= '$start_date' ";
        }

        if($end_date!='') {
            $query = $query . " and ss.date_arrive <= '$end_date" . "T23:59:59' ";
            $query_cnt = $query_cnt . " and ss.date_arrive <= '$end_date" . "T23:59:59' ";
        }
}

if($date_type == "r")
{
        if($start_date!='') {
            $query = $query . " and ss.date_receive >= '$start_date' ";
            $query_cnt = $query_cnt . " and ss.date_receive >= '$start_date' ";
        }

        if($end_date!='') {
            $query = $query . " and ss.date_receive <= '$end_date" . "' ";
            $query_cnt = $query_cnt . " and ss.date_receive <= '$end_date" . "' ";
        }
}

if($date_type == "p")
{
        if($start_date!='') {
            $query = $query . " and ss.pay_date >= '$start_date' ";
            $query_cnt = $query_cnt . " and ss.pay_date >= '$start_date' ";
        }

        if($end_date!='') {
            $query = $query . " and ss.pay_date <= '$end_date" . "' ";
            $query_cnt = $query_cnt . " and ss.pay_date <= '$end_date" . "' ";
        }
}

        if (!empty($_POST['page'])) {
            $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
            if (false === $page) {
                $page = 1;
            }
        }

        // order 
        $query = $query . " order by ss.date_receive  ";

        if (!empty($_POST['size'])) {
            $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
            if (false === $size) {
                $size = 10;
            }

            $offset = ($page - 1) * $size;

            $query = $query . " LIMIT " . $offset . "," . $size;
        }


        $stmt = $db->prepare($query);
        $stmt->execute();
    
        $merged_results = [];
        
        $items = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $date_receive = $row['date_receive'];
            $customer = $row['customer'];
            $address = $row['address'];
            $description = $row['description'];
            $quantity = $row['quantity'];
            $kilo = $row['kilo'];
            $supplier = $row['supplier'];
            $flight = $row['flight'];
            $flight_date = $row['flight_date'];
            $currency = $row['currency'];
            $total = $row['total'];
            $total_php = $row['total_php'];
            $amount_php = $row['amount_php'];
            $amount = $row['amount'];
            $pay_date = $row['pay_date'];
            $pay_status = $row['pay_status'];
            $payee = $row['payee'];
            $date_arrive = $row['date_arrive'];
            $receiver = $row['receiver'];
            $remark = $row['remark'];
            $status = $row['status'];

            $items = GetSalesDetail($id, $db, 'n');
            $items_php = GetSalesDetail($id, $db, 'p');
           
            $merged_results[] = array( 
                "is_edited" => 1,
                "id" => $id,
                "date_receive" => $date_receive,
                "customer" => $customer,
                "address" => $address,
                "description" => $description,
                "quantity" => $quantity,
                "kilo" => $kilo,
                "supplier" => $supplier,
                "flight" => $flight,
                "flight_date" => $flight_date,
                "currency" => $currency,
                "total" => $total,
                "total_php" => $total_php,
                "pay_date" => $pay_date,
                "pay_status" => $pay_status,
                "amount" => $amount,
                "amount_php" => $amount_php,
                "payee" => $payee,
                "date_arrive" => $date_arrive,
                "receiver" => $receiver,
                "remark" => $remark,
                "status" => $status,
                "items" => $items,
                "items_php" => $items_php,
               
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


            $sheet->setCellValue('A1', '收件日期Date Received');
            $sheet->setCellValue('B1', '客戶名Customer');
            $sheet->setCellValue('C1', '地址Address');
            $sheet->setCellValue('D1', '貨品名稱Description');
            $sheet->setCellValue('E1', '件數Quantity');
            $sheet->setCellValue('F1', '重量Kilo');
            $sheet->setCellValue('G1', '寄貨人Supplier');
            $sheet->setCellValue('H1', '班機與日期Flight and Date');
            $sheet->setCellValue('I1', '收費金額Amount');
            $sheet->setCellValue('J1', '付款日期Date Paid');
            $sheet->setCellValue('K1', '付款狀態Payment Status');
            $sheet->setCellValue('L1', '台幣金額Amount in NTD');
            $sheet->setCellValue('M1', '明細Details');
            $sheet->setCellValue('N1', '菲幣金額Amount in PHP');
            $sheet->setCellValue('O1', '明細Details');
            $sheet->setCellValue('P1', '抵達客人住址時間Time Delivery Arrived');
            $sheet->setCellValue('Q1', '簽收人Person Receive Delivery');
            $sheet->setCellValue('R1', '補充說明Notes');

            $i = 2;

            foreach ($merged_results as $measure)
            {
                
                    $sheet->setCellValue('A' . $i, $measure["date_receive"]);
                    $sheet->setCellValue('B' . $i, $measure["customer"]);
                    $sheet->setCellValue('C' . $i, $measure["address"]);
                    $sheet->setCellValue('D' . $i, $measure["description"]);
                    $sheet->setCellValue('E' . $i, $measure["quantity"]);
                    $sheet->setCellValue('F' . $i, $measure["kilo"]);
                    $sheet->setCellValue('G' . $i, $measure["supplier"]);
                    $sheet->setCellValue('H' . $i, $measure["flight"] . "\n" . $measure["flight_date"]);
                    $sheet->getStyle('H' . $i)->getAlignment()->setWrapText(true);
                    $sheet->setCellValue('I' . $i, $measure["total"] . ' ' . $measure["currency"]);
                    $sheet->setCellValue('J' . $i, $measure["pay_date"]);
                    $sheet->setCellValue('K' . $i, $measure["pay_status"] == 't' ? 'Taiwan Paid' : ($measure["pay_status"] == 'p' ? 'Philippines Paid' : ""));
                    $sheet->setCellValue('L' . $i, $measure["amount"]);
                    $sheet->setCellValue('M' . $i, RecordToString($measure["items"]));
                    $sheet->getStyle('M' . $i)->getAlignment()->setWrapText(true);
                    $sheet->setCellValue('N' . $i, $measure["amount_php"]);
                    $sheet->setCellValue('O' . $i, RecordToString($measure["items_php"]));
                    $sheet->getStyle('O' . $i)->getAlignment()->setWrapText(true);
                    $sheet->setCellValue('P' . $i, str_replace("T"," ",$measure["date_arrive"]));
                    $sheet->setCellValue('Q' . $i, $measure["receiver"]);
                    $sheet->setCellValue('R' . $i, $measure["remark"]);
                  
              
                    $sheet->getStyle('A'. $i. ':' . 'R' . $i)->applyFromArray($styleArray);
                    $i++;
            }
            
            
            $sheet->getStyle('A1:' . 'R1')->getFont()->setBold(true);
            $sheet->getStyle('A1:' . 'R' . --$i)->applyFromArray($styleArray);
            

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


function RecordToString($array)
{
    // join array into string
    $ret = "";
    foreach($array as $rec)
    {
        $ret .= $rec["title"] . "：" . $rec["qty"] . "*" . $rec["price"] . "\n";
    }
    return $ret;
}

function GetSalesDetail($sales_id, $db, $type){
    $query = "
            SELECT 0 as is_checked, id, title, qty, price 
                FROM airship_records_detail
            WHERE  airship_id = " . $sales_id . "
            AND `status` <> -1 AND type = '" . $type . "'
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $is_checked = $row['is_checked'];
        $id = $row['id'];
        $title = $row['title'] == "" ? "" : $row['title'];
        $qty = $row['qty'] == "" ? "" : $row['qty'];
        $price = $row['price'] == "" ? "" : $row['price'];
   
       
        $merged_results[] = array(
            "is_checked" => $is_checked,
            "id" => $id,
            "title" => $title,
            "qty" => $qty,
            "price" => $price,
           
        );
    }

    return $merged_results;
}


?>