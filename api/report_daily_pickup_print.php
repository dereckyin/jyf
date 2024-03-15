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


// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
            http_response_code(200);
            $date_start = str_replace('/', '-', $date_start);
            $date_end = str_replace('/', '-', $date_end);
            // if decode succeed, show user details
            try {
        
                // decode jwt
                $decoded = JWT::decode($jwt, $key, array('HS256'));

                // response in json format
                http_response_code(200);
                    
                $merged_results = array();

                $query = "select distinct pick_date, measure_detail.customer, encode, 0 customer_count, 0 encode_count  FROM pick_group 
                LEFT JOIN measure_detail ON pick_group.measure_detail_id = measure_detail.id 
                left join measure_record_detail on measure_record_detail.detail_id = measure_detail.id
                left join receive_record on receive_record.id = measure_record_detail.record_id
                WHERE  pick_group.status = 0 and receive_record.pick_date >= '$date_start' and receive_record.pick_date <= '$date_end'
                order by pick_date, measure_detail.customer";
            

                $stmt = $db->prepare( $query );
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $pick_date = '';
        $customer = '';

        $encode_count = 0;
        $customer_count = 0;

        $pick_date_index = 0;
        $customer_index = 0;

        $pre_pick_date = '';
        $pre_customer = '';

        for($i = 0; $i < count($result); $i++) {
            $pick_date = $result[$i]['pick_date'];
            $customer = $result[$i]['customer'];

            if($pre_pick_date != $pick_date) {
                $pre_pick_date = $pick_date;
                $pre_customer = $customer;
                $encode_count = 0;
                $customer_count = 0;
                $pick_date_index = $i;
                $customer_index = $i;
            }

            if($pre_customer != $customer) {
                $pre_customer = $customer;
                $customer_count = 0;
                $customer_index = $i;
            }

            $encode_count++;
            $customer_count++;

            $result[$pick_date_index]['encode_count'] = $encode_count;
            $result[$customer_index]['customer_count'] = $customer_count;
        }
            }
            catch (Exception $e){
    
                // set response code
                http_response_code(401);
            
                // show error message
                echo json_encode(array(
                    "message" => "Access denied.",
                    "error" => $e->getMessage()
                ));
            }

            $right_style = array(
                'alignment' => array(
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                )
            );
            
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

                
                $sheet->setCellValue('A1', 'Date 日期');
                $sheet->setCellValue('B1', 'Name of Customer 客戶名稱');
                $sheet->setCellValue('C1', 'DR # 單號');
            
                $i = 2;

                $a = 0;
                $b = 0;

                foreach($result as $rec)
                {
                    if($rec['encode_count'] != 0)
                    {
                        if($i > 2)
                        {
                            $sheet->mergeCells('A' . $a . ':A' . ($i -1));
                            $sheet->getStyle('A' . $a . ':A' . ($i -1))->applyFromArray($right_style);
                        }
                        $a = $i;
                    }

                    if($rec['customer_count'] != 0)
                    {
                        if($i > 2)
                        {
                            $sheet->mergeCells('B' . $b . ':B' . ($i -1));
                            $sheet->getStyle('B' . $b . ':B' . ($i -1))->applyFromArray($right_style);
                        }
                        $b = $i;
                    }
                
                    $sheet->setCellValue('A' . $i, $rec["pick_date"]);
                    $sheet->getColumnDimension('A')->setWidth(20);
                    $sheet->setCellValue('B' . $i, $rec["customer"]);
                    $sheet->getColumnDimension('B')->setWidth(120);
                    $sheet->setCellValue('C' . $i, $rec["encode"]);
                    $sheet->getColumnDimension('C')->setWidth(20);

                    $sheet->getStyle('A'. $i. ':' . 'C' . $i)->applyFromArray($styleArray);
                    $i++;
                }

                $sheet->getStyle('A' . $i . ':' . 'C' . $i)->getFont()->setBold(true);
                //$sheet->getStyle('A'. $i. ':' . 'J' . $i)->applyFromArray($styleArray);

                $i = $i + 2;

                $sheet->getStyle('A1:' . 'C1')->getFont()->setBold(true);
                $sheet->getStyle('A1:' . 'C' . --$i)->applyFromArray($styleArray);

            

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



function compare_eta($a, $b)
{
    $last_a = '';
    $last_b = '';

    foreach($a['loading'] as $item)
    {
        if($item['eta_date'] > $last_a)
        {
            $last_a = $item['eta_date'];
        }
    }

    foreach($b['loading'] as $item)
    {
        if($item['eta_date'] > $last_b)
        {
            $last_b = $item['eta_date'];
        }
    }

   return ($last_a > $last_b);
}

function compare_arrive($a, $b)
{
    $last_a = '';
    $last_b = '';

    foreach($a['loading'] as $item)
    {
        if($item['date_arrive'] > $last_a)
        {
            $last_a = $item['date_arrive'];
        }
    }

    foreach($b['loading'] as $item)
    {
        if($item['date_arrive'] > $last_b)
        {
            $last_b = $item['date_arrive'];
        }
    }

   return ($last_a > $last_b);

}

function filter_eta($a, $b)
{
    $last_a = '';
    $last_b = '';

    foreach($a['loading'] as $item)
    {
        if($item['eta_date'] > $last_a)
        {
            $last_a = $item['eta_date'];
        }
    }

    foreach($b['loading'] as $item)
    {
        if($item['eta_date'] > $last_b)
        {
            $last_b = $item['eta_date'];
        }
    }

   return ($last_a > $last_b);
}

function GetLoadingDetail($conn, $id){
    $sql = "select 
    mp.id, 
    l.container_number,
    CONCAT_WS(',', l.eta_date, IFNULL(ldh.eta_date, '')) eta_date,
    CONCAT_WS(',', l.date_arrive, IFNULL(ldh.date_arrive, '')) date_arrive
from measure_ph mp 
    left join loading l on mp.id = l.measure_num 
    left join loading_date_history ldh on l.id = ldh.loading_id  
where mp.id =  ($id)";
    
            
    $stmt = $conn->prepare( $sql );
    $stmt->execute();

    $merged_results = [];

    // die if SQL statement failed
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $loading_id = $row['id'];
        $container_number = $row['container_number'];
        $eta_date_ary = explode(",", rtrim($row['eta_date'], ','));
        $date_arrive_ary = explode(",", rtrim($row['date_arrive'], ','));

        $merged_results[] = array( 
            "loading_id" => $loading_id,
            "container_number" => $container_number,
            "eta_date" => end($eta_date_ary),
            "date_arrive" => end($date_arrive_ary),
        );
    }

    return $merged_results;

}

?>