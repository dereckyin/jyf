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

include_once 'config/conf.php';

use \Firebase\JWT\JWT;
 
// files needed to connect to database
include_once 'config/database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
 
// get database connection
$database = new Database();
$db = $database->getConnection();

 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);

$apply_start = (isset($_POST['apply_start']) ?  $_POST['apply_start'] : '');
$apply_end = (isset($_POST['apply_end']) ?  $_POST['apply_end'] : '');
$apply_name = (isset($_POST['apply_name']) ?  $_POST['apply_name'] : '');

$apply_start = str_replace('-', '/', $apply_start);
$apply_end = str_replace('-', '/', $apply_end);

$conf = new Conf();

// if jwt is not empty
if($jwt){
 
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        // response in json format
            http_response_code(200);

            $merged_results = array();

            $sql = "SELECT username, duty_date, duty_type, location, DATE_FORMAT(created_at, '%Y/%m/%d %h:%i %p') duty_time, `explain`, pic_url, remark, pos_lat, pos_lng  FROM on_duty_v2 a left join staff_list_sea b on a.username = b.staff  
            WHERE b.punch = 1 ";

            if(!empty($apply_start)) {
                $sql = $sql . " and duty_date >= '$apply_start' ";
            }

            if(!empty($apply_end)) {
                $sql = $sql . " and duty_date <= '$apply_end' ";
            }

            if(!empty($apply_name)) {
                $sql = $sql . " and username = '$apply_name' ";
            }

            $sql = $sql . " ORDER BY username, duty_date, duty_type  ";

            $stmt = $db->prepare( $sql );
            $stmt->execute();

            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                $merged_results[] = $row;

            $old_name = "";
          
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
            

            $merged = array();
            $page = 0;

            foreach($merged_results as $row)
            {
                if($row['username'] != $old_name)
                {
                    if($old_name != '')
                    {
                        if($page == 0)
                        {
                            SetFirstPage($sheet, $merged, $conf, $styleArray);
                            $sheet->setTitle($old_name);
                            $page++;
                        }
                        else
                        {
                            $spreadsheet->createSheet();
                            $spreadsheet->setActiveSheetIndex($page);
                            $sheet = $spreadsheet->getActiveSheet();
                            $sheet->setTitle($old_name);
                            SetFirstPage($sheet, $merged, $conf, $styleArray);
                            $page++;
                        }
                        $merged = array();
                        array_push($merged, $row);
                        $old_name = $row['username'];
                    }

                    if($old_name == '')
                    {
                        $old_name = $row['username'];
                        array_push($merged, $row);
                    }
                }
                else
                {
                    array_push($merged, $row);
                }
            }

            if(count($merged) > 0)
            {
                $spreadsheet->createSheet();
                $spreadsheet->setActiveSheetIndex($page);
                $sheet = $spreadsheet->getActiveSheet();
                $sheet->setTitle($row['username']);
                SetFirstPage($sheet, $merged, $conf, $styleArray);
            }

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

function GetLocation($loc)
{
    $location = "";
    switch ($loc) {
        case "A":
            $location = "Antel Office";
            break;
        case "M":
            $location = "Main Office";
            break;
        case "T":
            $location = "Taiwan Office";
            break;
        case "B":
            $location = "Shangri-La Store";
            break;
        case "C":
            $location = "Caloocan Warehouse";
            break;
        case "D":
            $location = "Installation";
            break;
        case "E":
            $location = "Client Meeting";
            break;
            case "F":
                $location = "Others";
                break;
    }

    return $location;
}

function GetDutyType($loc)
{
    $location = "";
    switch ($loc) {
        case "A":
            $location = "On Duty";
            break;
        case "B":
            $location = "Off Duty";
            break;
   
    }

    return $location;
}

function SetFirstPage($sheet, $merged_results, $conf, $styleArray)
{
    $sheet->setCellValue('A1', 'Name');
    $sheet->setCellValue('B1', 'Date');
    $sheet->setCellValue('C1', 'Type');
    $sheet->setCellValue('D1', 'Time');
    $sheet->setCellValue('E1', 'Photo');
    $sheet->setCellValue('F1', 'Remarks');


    $i = 2;
    foreach($merged_results as $row)
    {
        $sheet->setCellValue('A' . $i, $row['username']);
        $sheet->setCellValue('B' . $i, $row['duty_date']);
        $sheet->setCellValue('C' . $i, GetDutyType($row['duty_type']));
        $sheet->setCellValue('D' . $i, $row['duty_time']);

        if($row['pic_url'] != '')
        {
            $link = $conf::$mail_ip . 'img/' . $row['pic_url'];
            $sheet->setCellValue('E' . $i, 'Photo');
            $sheet->getCellByColumnAndRow(5,$i)->getHyperlink()->setUrl($link);
        }
        else
            $sheet->setCellValue('E' . $i, '');

        $sheet->setCellValue('F' . $i, $row['remark']);

        $sheet->getStyle('A'. $i. ':' . 'F' . $i)->applyFromArray($styleArray);

        $i++;
    }

    $sheet->getStyle('A1:' . 'F1')->getFont()->setBold(true);
    $sheet->getStyle('A1:' . 'F' . --$i)->applyFromArray($styleArray);
}

?>