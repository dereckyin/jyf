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
$type = (isset($_POST['type']) ?  $_POST['type'] : '');
// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
            http_response_code(200);
            $date_start = str_replace('-', '/', $date_start);
            $date_end = str_replace('-', '/', $date_end);
            // if decode succeed, show user details
            try {
        
                // decode jwt
                $decoded = JWT::decode($jwt, $key, array('HS256'));

                // response in json format
                http_response_code(200);
                    
                $merged_results = array();

                $query = "select mp.id,
                            sum(IF(abs(charge - (md.kilo * md.kilo_price)) > abs(charge - (md.cuft * md.cuft_price)), 0, charge)) charge_kilo,
                            sum(IF(abs(charge - (md.kilo * md.kilo_price)) <= abs(charge - (md.cuft * md.cuft_price)), 0, charge)) charge_cuft,
                            sum(if(md.payment_status = 'C', md.charge, 0)) charge,
                            sum(md.charge) - sum(if(md.payment_status = 'C', md.charge, 0)) ar,
                            mp.remark, mp.notes
                        from measure_ph mp 
                            left join measure_detail md on mp.id = md.measure_id 
                            left join loading l on mp.id = l.measure_num 
                            left join loading_date_history ldh on l.id = ldh.loading_id 
                        where mp.status <> -1
                            group by
                            mp.id";
            

                $stmt = $db->prepare( $query );
                $stmt->execute();


                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $items = [];
                    $items = GetLoadingDetail($db, $row["id"]);

                    $id = $row["id"];
                    $charge_kilo = $row["charge_kilo"];
                    $charge_cuft = $row["charge_cuft"];
                    $charge = $row["charge"];
                    $ar = $row["ar"];
                    $remark = $row["remark"];

                    $merged_results[] = array( 
                        "is_checked" => 0,
                        "id" => $id,
                        "charge_kilo" => $charge_kilo,
                        "charge_cuft" => $charge_cuft,
                        "loading" => $items,
                        "charge" => $charge,
                        "ar" => $ar,
                        "remark" => $remark,
                        "notes" => $row["notes"]
                    );
                    
                }

                if($date_end != '' && $date_start != '' && $type == "1")
                {
                    $merged_results = array_filter($merged_results, function($a) use ($date_end, $date_start) {
                        $in_value = false;

                        foreach($a['loading'] as $item)
                        {
                            if($item['eta_date'] <= $date_end && $item['eta_date'] >= $date_start)
                            {
                                $in_value = true;
                            }
                        
                        }

                        return $in_value;
                    });
                }

                if($date_end != '' && $date_start != '' && $type == "2")
                {
                    $merged_results = array_filter($merged_results, function($a) use ($date_end, $date_start) {
                        $in_value = false;

                        foreach($a['loading'] as $item)
                        {
                            if($item['date_arrive'] <= $date_end && $item['date_arrive'] >= $date_start)
                            {
                                $in_value = true;
                            }
                        
                        }

                        return $in_value;
                    });
                }

                if($type == '1')
                    usort($merged_results, "compare_eta");
                else
                    usort($merged_results, "compare_arrive");
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

    $container_total = 0;
    $ar_total = 0;
    $charge_total = 0;
    $total_total = 0;
                
                $sheet->setCellValue('A1', 'Date Sent 結關日期');
                $sheet->setCellValue('B1', 'Date C/R 到倉日期');
                $sheet->setCellValue('C1', 'Container Number 櫃號');
                $sheet->setCellValue('D1', 'A/R (By Kilo) 應收帳款(根據重量)');
                $sheet->setCellValue('E1', 'A/R (By Cuft) 應收帳款(根據材積)');
                $sheet->setCellValue('F1', 'A/R 應收帳款');
                $sheet->setCellValue('G1', 'Amount Received 已收金額');
                $sheet->setCellValue('H1', 'Remaining A/R 未收金額');
                $sheet->setCellValue('I1', 'Remarks 備註');

                $i = 2;

                foreach ($merged_results as $measure)
                {
                    if(count($measure["loading"]) > 1)
                        $j = $i;

                        $container_total += count($measure["loading"]);

                    foreach($measure["loading"] as $rec)
                    {
                        $sheet->setCellValue('A' . $i, $rec["eta_date"]);
                        $sheet->setCellValue('B' . $i, $rec["date_arrive"]);
                        $sheet->setCellValue('C' . $i, $rec["container_number"]);
                        $sheet->setCellValue('D' . $i, '₱ ' . number_format((float)$measure["charge_kilo"], 2, '.', ''));
                        $sheet->setCellValue('E' . $i, '₱ ' . number_format((float)$measure["charge_cuft"], 2, '.', ''));
                        $sheet->setCellValue('F' . $i, '₱ ' . number_format((float)$measure["charge_kilo"] + (float)$measure["charge_cuft"], 2, '.', ''));
                        $sheet->setCellValue('G' . $i, '₱ ' . number_format((float)$measure["charge"], 2, '.', ''));
                        $sheet->setCellValue('H' . $i, '₱ ' . number_format((float)$measure["ar"], 2, '.', ''));
                        $sheet->setCellValue('I' . $i, $measure["notes"]);

                        $sheet->getStyle('A'. $i. ':' . 'I' . $i)->applyFromArray($styleArray);
                        $i++;
                    }

                    if(count($measure["loading"]) > 1)
                    {
                        $sheet->mergeCells('D' . $j . ':D' . ($i -1));
                        $sheet->mergeCells('E' . $j . ':E' . ($i -1));
                        $sheet->mergeCells('F' . $j . ':F' . ($i -1));
                        $sheet->mergeCells('G' . $j . ':G' . ($i -1));
                        $sheet->mergeCells('H' . $j . ':H' . ($i -1));
                    }

                    $ar_total += $measure["ar"];
                    $charge_total += $measure["charge"];
                    $total_total += $measure["charge_kilo"] + $measure["charge_cuft"];

                }

                $i = $i + 1;
                $sheet->setCellValue('A' . $i, "Total:");

                $sheet->setCellValue('C' . $i, $container_total);
       
                $sheet->setCellValue('F' . $i, '₱ ' . number_format((float)$total_total, 2, '.', ''));
                $sheet->setCellValue('G' . $i, '₱ ' . number_format((float)$charge_total, 2, '.', ''));
                $sheet->setCellValue('H' . $i, '₱ ' . number_format((float)$ar_total, 2, '.', ''));

                $sheet->getStyle('A' . $i . ':' . 'H' . $i)->getFont()->setBold(true);
                //$sheet->getStyle('A'. $i. ':' . 'J' . $i)->applyFromArray($styleArray);

                $i = $i + 2;

                $sheet->getStyle('A1:' . 'H1')->getFont()->setBold(true);
                $sheet->getStyle('A1:' . 'H' . --$i)->applyFromArray($styleArray);

            

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