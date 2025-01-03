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

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
// get jwt
$date_start = (isset($_POST['date_start']) ?  $_POST['date_start'] : '');
$date_end = (isset($_POST['date_end']) ?  $_POST['date_end'] : '');
$pay_start = (isset($_POST['pay_start']) ?  $_POST['pay_start'] : '');
$pay_end = (isset($_POST['pay_end']) ?  $_POST['pay_end'] : '');
$flight_start = (isset($_POST['flight_start']) ?  $_POST['flight_start'] : '');
$flight_end = (isset($_POST['flight_end']) ?  $_POST['flight_end'] : '');
$arrive_start = (isset($_POST['arrive_start']) ?  $_POST['arrive_start'] : '');
$arrive_end = (isset($_POST['arrive_end']) ?  $_POST['arrive_end'] : '');

$customer = (isset($_POST['customer']) ?  $_POST['customer'] : '');
//$customer = urldecode($customer);
$supplier = (isset($_POST['supplier']) ?  $_POST['supplier'] : '');
//$supplier = urldecode($supplier);

$description = (isset($_POST['description']) ?  $_POST['description'] : '');
$remark = (isset($_POST['remark']) ?  $_POST['remark'] : '');
$sort = (isset($_POST['sort']) ?  $_POST['sort'] : '');

// if jwt is not empty
if($jwt){ 
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
            http_response_code(200);

            $merged_results = array();

            
            $cus_str = "";
            $sup_str = "";
    
            $c_prefix = "%";
            if(!empty($customer)) {
                $customer = rtrim($customer, '||');
          
                $cust = explode("||", $customer);

                if($customer != "" && strpos($customer, '||') !== false) {
                    $c_prefix = "";
                }
    
                foreach ($cust as &$value) {
                    $value = addslashes(trim($value));
                    $cus_str .= " r.customer like '" . $c_prefix . $value . "%' ESCAPE '|' or ";
                }
    
                $cus_str = rtrim($cus_str, 'or ');
      
            }
    
            $p_prefix = "%";
            if(!empty($supplier)) {
                $supplier = rtrim($supplier, '||');
        
                $sup = explode("||", $supplier);

                if($supplier != "" && strpos($supplier, '||') !== false) {
                    $p_prefix = "";
                }
    
                foreach ($sup as &$value) {
                    $value = addslashes(trim($value));
                    $sup_str .= " r.supplier like '" . $p_prefix . $value . "%'  ESCAPE '|' or ";
    
                }
    
                $sup_str = rtrim($sup_str, 'or ');
           
    
            }

            $query = "SELECT r.id, 
            date_receive, 
            mode,
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
            ratio,
            total_php,
            pay_date,
            pay_status,
            payee,
            date_arrive,
            receiver,
            remark,
            sn,
            r.`status` from airship_records r 
            where 1=1  ";


if(!empty($date_start)) {
    $date_start = str_replace('/', '-', $date_start);

    $query = $query . " and r.date_receive >= '$date_start' ";
    // $query_cnt = $query_cnt . " and r.date_receive >= '$date_start' ";
}

if(!empty($date_end)) {
    $date_end = str_replace('/', '-', $date_end);

    $query = $query . " and r.date_receive <= '$date_end' ";
    // $query_cnt = $query_cnt . " and r.date_receive <= '$date_end' ";
}

if(!empty($pay_start)) {
    $pay_start = str_replace('/', '-', $pay_start);

    $query = $query . " and r.pay_date >= '$pay_start' ";
    // $query_cnt = $query_cnt . " and r.pay_date >= '$pay_start' ";
}

if(!empty($pay_end)) {
    $pay_end = str_replace('/', '-', $pay_end);

    $query = $query . " and r.pay_date <= '$pay_end' ";
    // $query_cnt = $query_cnt . " and r.pay_date <= '$pay_end' ";
}

if(!empty($flight_start)) {
    $flight_start = str_replace('/', '-', $flight_start);

    $query = $query . " and r.flight_date >= '$flight_start' ";
    // $query_cnt = $query_cnt . " and r.flight_date >= '$flight_start' ";
}

if(!empty($flight_end)) {
    $flight_end = str_replace('/', '-', $flight_end);

    $query = $query . " and r.flight_date <= '$flight_end' ";
    // $query_cnt = $query_cnt . " and r.flight_date <= '$flight_end' ";
}

if(!empty($arrive_start)) {
    $arrive_start = str_replace('/', '-', $arrive_start);

    $query = $query . " and r.date_arrive >= '$arrive_start' ";
    // $query_cnt = $query_cnt . " and r.date_arrive >= '$arrive_start' ";
}

if(!empty($arrive_end)) {
    $arrive_end = str_replace('/', '-', $arrive_end);

    $arrive_end = $arrive_end . "T23:59:59 ";

    $query = $query . " and r.date_arrive <= '$arrive_end' ";
    // $query_cnt = $query_cnt . " and r.date_arrive <= '$arrive_end' ";
}



if(!empty($description)) {
    $query = $query . " and r.description like '%$description%' ";
    // $query_cnt = $query_cnt . " and r.description like '%$description%' ";
}

if(!empty($remark)) {
    $query = $query . " and r.remark like '%$remark%' ";
    // $query_cnt = $query_cnt . " and r.remark like '%$remark%' ";
}

if(!empty($sup_str)) {
    $query = $query . " and ($sup_str) ";
    // $query_cnt = $query_cnt . " and ($sup_str) ";
}

if(!empty($cus_str)) {
    $query = $query . " and ($cus_str) ";
    // $query_cnt = $query_cnt . " and ($cus_str) ";
}

if($sort == 'd') 
    $query = $query . " order by r.date_receive desc ";
else
    $query = $query . " order by r.date_receive ";



        $stmt = $db->prepare($query);
        $stmt->execute();
    
        $merged_results = [];
        
        $items = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $id = $row['id'];
            $date_receive = $row['date_receive'];
            $mode = $row['mode'];
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
                "mode" => $mode,
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
            $sheet->setCellValue('B1', '模式Mode');
            $sheet->setCellValue('C1', '客戶名Customer');
            $sheet->setCellValue('D1', '地址Address');
            $sheet->setCellValue('E1', '貨品名稱Description');
            $sheet->setCellValue('F1', '件數Quantity');
            $sheet->setCellValue('G1', '重量Kilo');
            $sheet->setCellValue('H1', '寄貨人Supplier');
            $sheet->setCellValue('I1', '班機與日期Flight and Date');
            $sheet->setCellValue('J1', '收費金額Amount');
            $sheet->setCellValue('K1', '付款日期Date Paid');
            $sheet->setCellValue('L1', '付款狀態Payment Status');
            $sheet->setCellValue('M1', '台幣金額Amount in NTD');
            $sheet->setCellValue('N1', '明細Details');
            $sheet->setCellValue('O1', '菲幣金額Amount in PHP');
            $sheet->setCellValue('P1', '明細Details');
            $sheet->setCellValue('Q1', '抵達客人住址時間Time Delivery Arrived');
            $sheet->setCellValue('R1', '簽收人Person Receive Delivery');
            $sheet->setCellValue('S1', '補充說明Notes');

            $i = 2;

            foreach ($merged_results as $measure)
            {
                
                    $sheet->setCellValue('A' . $i, $measure["date_receive"]);
                    $sheet->setCellValue('B' . $i, $measure["mode"] == 'exp' ? '快遞' : '空運');
                    $sheet->setCellValue('C' . $i, $measure["customer"]);
                    $sheet->setCellValue('D' . $i, $measure["address"]);
                    $sheet->setCellValue('E' . $i, $measure["description"]);
                    $sheet->setCellValue('F' . $i, $measure["quantity"]);
                    $sheet->setCellValue('G' . $i, $measure["kilo"]);
                    $sheet->setCellValue('H' . $i, $measure["supplier"]);
                    $sheet->setCellValue('I' . $i, $measure["flight"] . "\n" . $measure["flight_date"]);
                    $sheet->getStyle('I' . $i)->getAlignment()->setWrapText(true);
                    $sheet->setCellValue('J' . $i, $measure["total"] . ' ' . $measure["currency"]);
                    $sheet->setCellValue('K' . $i, $measure["pay_date"]);
                    $sheet->setCellValue('L' . $i, $measure["pay_status"] == 't' ? 'Taiwan Paid' : ($measure["pay_status"] == 'p' ? 'Philippines Paid' : ""));
                    $sheet->setCellValue('M' . $i, $measure["amount"]);
                    $sheet->setCellValue('N' . $i, RecordToString($measure["items"]));
                    $sheet->getStyle('N' . $i)->getAlignment()->setWrapText(true);
                    $sheet->setCellValue('O' . $i, $measure["amount_php"]);
                    $sheet->setCellValue('P' . $i, RecordToString($measure["items_php"]));
                    $sheet->getStyle('P' . $i)->getAlignment()->setWrapText(true);
                    $sheet->setCellValue('Q' . $i, str_replace("T"," ",$measure["date_arrive"]));
                    $sheet->setCellValue('R' . $i, $measure["receiver"]);
                    $sheet->setCellValue('S' . $i, $measure["remark"]);
                  
              
                    $sheet->getStyle('A'. $i. ':' . 'S' . $i)->applyFromArray($styleArray);
                    $i++;
            }
            
            
            $sheet->getStyle('A1:' . 'S1')->getFont()->setBold(true);
            $sheet->getStyle('A1:' . 'S' . --$i)->applyFromArray($styleArray);
            

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