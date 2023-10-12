<?php
ob_start();
error_reporting(E_ALL);

include_once 'api/config/database.php';
include_once 'api/config/conf.php';

require 'vendor/autoload.php';

include_once 'api/config/core.php';
include_once 'api/libs/php-jwt-master/src/BeforeValidException.php';
include_once 'api/libs/php-jwt-master/src/ExpiredException.php';
include_once 'api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'api/libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

use PhpOffice\PhpWord\IOFactory;

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

$database = new Database();
$db = $database->getConnection();

$full = (isset($_POST['full']) ?  $_POST['full'] : "");
$id = (isset($_POST['id']) ?  $_POST['id'] : 0);
$schedule_Name = (isset($_POST['schedule_Name']) ?  $_POST['schedule_Name'] : "");
$date_use = (isset($_POST['date_use']) ?  $_POST['date_use'] : "");
$car_use = (isset($_POST['car_use']) ?  $_POST['car_use'] : "");
$driver = (isset($_POST['driver']) ?  $_POST['driver'] : "");
$helper = (isset($_POST['helper']) ?  $_POST['helper'] : "");
$time_out = (isset($_POST['time_out']) ?  $_POST['time_out'] : "");
$time_in = (isset($_POST['time_in']) ?  $_POST['time_in'] : "");
$notes = (isset($_POST['notes']) ?  $_POST['notes'] : "");
$items = (isset($_POST['items']) ?  $_POST['items'] : []);
$status = (isset($_POST['status']) ?  $_POST['status'] : 0);

$check_date_use = (isset($_POST['check_date_use']) ?  $_POST['check_date_use'] : "");
$check_car_use = (isset($_POST['check_car_use']) ?  $_POST['check_car_use'] : "");
$check_driver = (isset($_POST['check_driver']) ?  $_POST['check_driver'] : "");
$check_time_out = (isset($_POST['check_time_out']) ?  $_POST['check_time_out'] : "");
$check_time_in = (isset($_POST['check_time_in']) ?  $_POST['check_time_in'] : "");
$creator = (isset($_POST['creator']) ?  $_POST['creator'] : "");


$items_detail = json_decode($items, true);

// $check1 = GetCheck($db, $id, "1");
// $check2 = GetCheck($db, $id, "2");

// Creating the new document...
$phpWord = new PhpOffice\PhpWord\PhpWord();

/* Note: any element you append to a document must reside inside of a Section. */

// Adding an empty Section to the document...
$section = $phpWord->addSection();
// Adding Text element to the Section having font styled by default...

if($full == '1' && $status > 0)
{

    $check_dateString = "";

    $check_tout = "";
    if($check_date_use != "" && $check_time_out != "")
    {
        $check_dateString = new DateTime($check_date_use . " " . $check_time_out);
        $check_tout = date('h:i A', strtotime($check_date_use . " " . $check_time_out));
    }

    $check_tin = "";
    if($check_date_use != "" && $check_time_in != "")
    {
        $check_dateString = new DateTime($check_date_use . " " . $check_time_in);
        $check_tin = date('h:i A', strtotime($check_date_use . " " . $check_time_in));
    }


    $table2 = $section->addTable('table2', [
        'borderSize' => 6, 
        'borderColor' => 'F73605', 
        'afterSpacing' => 0, 
        'Spacing'=> 0, 
        'cellMargin'=> 0
    ]);

    $table2->addRow();
    $cell = $table2->addCell(10500, ['borderSize' => 6]);
    $cell->getStyle()->setGridSpan(2);
    $cell->addText("Request Review", array('bold' => true));

    $table2->addRow();
    $table2->addCell(2000, ['borderSize' => 6])->addText("Date:", array('bold' => true));
    $table2->addCell(8500, ['borderSize' => 6])->addText($check_date_use);

    $table2->addRow();
    $table2->addCell(2000, ['borderSize' => 6])->addText("Time:", array('bold' => true));
    $TextRun = $table2->addCell(8500, ['borderSize' => 6])->addTextRun();
    $TextRun->addText($check_tout);
    $TextRun->addText(" to ");
    $TextRun->addText($check_tin);

    $table2->addRow();
    $table2->addCell(2000, ['borderSize' => 6])->addText("Assigned Car:", array('bold' => true));
    $table2->addCell(8500, ['borderSize' => 6])->addText($check_car_use);
    
    $table2->addRow();
    $table2->addCell(2000, ['borderSize' => 6])->addText("Assigned Driver:", array('bold' => true));
    $table2->addCell(8500, ['borderSize' => 6])->addText($check_driver);
       
    
    $section->addText("");
    $section->addText("");
}

$dateString = "";

$tout = "";
if($date_use != "" && $time_out != "")
{
    $dateString = new DateTime($date_use . " " . $time_out);
    $tout = date('h:i A', strtotime($date_use . " " . $time_out));
}

$tin = "";
if($date_use != "" && $time_in != "")
{
    $dateString = new DateTime($date_use . " " . $time_in);
    $tin = date('h:i A', strtotime($date_use . " " . $time_in));
}

$table = $section->addTable('table', [
    'borderSize' => 6, 
    'borderColor' => 'F73605', 
    'afterSpacing' => 0, 
    'Spacing'=> 0, 
    'cellMargin'=> 0
]);

$table->addRow();
$table->addCell(10500, null, 2, ['borderSize' => 6])->addText("Content of Request", array('bold' => true));

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Schedule Name:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($schedule_Name);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Creator:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($creator);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Date Use:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($date_use);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Car Use:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($car_use);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Driver:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($driver);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Helper:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($helper);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Time Out:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($tout);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Time In:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($tin);


$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Note/s:", array('bold' => true));
$cell = $table->addCell(8500, ['borderSize' => 6]);
addMultiLineText($cell, $notes);

$section->addText("");

$table1 = $section->addTable('table1', [
    'borderSize' => 6, 
    'borderColor' => 'F73605', 
    'afterSpacing' => 0, 
    'Spacing'=> 0, 
    'cellMargin'=> 0,
    'textAlign' => 'center'
]);

$table1->addRow();
$table1->addCell(2600, ['borderSize' => 6])->addText("Schedule", ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table1->addCell(2600, ['borderSize' => 6])->addText("Company",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table1->addCell(2600, ['borderSize' => 6])->addText("Address",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table1->addCell(2600, ['borderSize' => 6])->addText("Purpose",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);


foreach($items_detail as $row)
{
    $schedule = $row['schedule'];
    $company = $row['company'];
    $address = $row['address'];
    $purpose = $row['purpose'];

    $table1->addRow();
    $table1->addCell(2600, ['borderSize' => 6])->addText($schedule, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(2600, ['borderSize' => 6])->addText($company, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(2600, ['borderSize' => 6])->addText($address, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(2600, ['borderSize' => 6])->addText($purpose, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

}

// Adding Text element with font customized using explicitly created font style object...
$fontStyle = new \PhpOffice\PhpWord\Style\Font();
$fontStyle->setBold(true);
$fontStyle->setName('Tahoma');
$fontStyle->setSize(13);
// $myTextElement = $section->addText('"Believe you can and you\'re halfway there." (Theodor Roosevelt)');
//$myTextElement->setFontStyle($fontStyle);

ob_end_clean();
// Saving the document as OOXML file...
$objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007', $download = true);

$products_to_bring_files = "";

if(trim($products_to_bring_files) != "")
{
    $attachment = explode(",", $products_to_bring_files);

    $conf = new Conf();

    $path = $conf::$upload_path . "tmp/";

    if (!file_exists($path)) {
        mkdir($path, 0777, true);
    }
    else
    {
        $files = glob($path . "*"); // get all file names
        foreach($files as $file){ // iterate files
            if(is_file($file)) {
                unlink($file); // delete file
            }
        }
    }

    foreach ($attachment as &$value) {
        grab_image($value, $path . $value);
    }
    // $arr is now array(2, 4, 6, 8)
    unset($value); // break the reference with the last element

    $objWriter->save($path . "schedule.docx");

    $time = microtime(true);
    $zipname = $path . $time . 'schedule.zip';
    $zip = new ZipArchive();
    
    // touch($zipname); 
    //$zip->open($zipname, ZipArchive::CREATE);
    if ($zip->open($zipname, (ZipArchive::CREATE)) !== true)
        die("Failed to create archive\n");

    if ($handle = opendir($path)) {
    while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != "..") {
            $r = $zip->addFile($path . $entry, basename($entry));
        }
    }
    closedir($handle);
    }

    $zip->close();

    header('Content-Type: application/zip');
    header("Content-Disposition: attachment; filename='schedule.zip'");
    header('Content-Length: ' . filesize($zipname));
    header("Content-Transfer-Encoding: Binary");
    while (ob_get_level()) {
        ob_end_clean();
      }
    readfile($zipname);
    exit;
}
else
{
    header("Content-Disposition: attachment; filename=schedule.docx");

    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');
    // If you're serving to IE over SSL, then the following may be needed
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
    header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header('Pragma: public'); // HTTP/1.0

    $objWriter->save('php://output');
}

    function getService($type){
        $leave_type = '';
    
        if($type =="1")
            $leave_type = "innova";
        if($type =="2")
            $leave_type = "avanza gold";
        if($type =="3")
            $leave_type = "avanza gray";
        if($type =="4")
            $leave_type = "L3001";
        if($type =="5")
            $leave_type = "L3002";
        if($type =="6")
            $leave_type = "Grab";
        
        return $leave_type;
    }

    function getDriver($type){
        $leave_type = '';
    
        if($type =="1")
            $leave_type = "MG";
        if($type =="2")
            $leave_type = "AY";
        if($type =="3")
            $leave_type = "EV";
        if($type =="4")
            $leave_type = "JB";
        if($type =="5")
            $leave_type = "MA";
        if($type =="6")
            $leave_type = "Other";
    
        return $leave_type;
    }

    function getRequest($type){
        $leave_type = '';
    
        if($type =="0")
            $leave_type = "No";
        if($type =="1")
            $leave_type = "Yes";
        
        return $leave_type;
    }

    function addMultiLineText($cell, $text)
    {
        // break from line breaks
        $strArr = explode("\n", $text);

        // add text line together
        foreach ($strArr as $v) {
            $cell->addText($v);
        }
       
    }

    function grab_image($image_url,$image_file){

        /*
        $storage = new StorageClient([
            'projectId' => 'predictive-fx-284008',
            'keyFilePath' => $key
        ]);

        $bucket = $storage->bucket('feliiximg');

        $object = $bucket->object($image_url);
        $object->downloadToFile($image_file);
        */

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://storage.googleapis.com/calendarfile/' . $image_url);
        //Create a new file where you want to save
        $fp = fopen($image_file, 'w');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_exec ($ch);
        curl_close ($ch);
        fclose($fp);
    }

    function GetCheck($db, $sid, $kind)
    {
        $result = array();

        $query = "SELECT * from car_calendar_check 
                where `feliix` = 0 and `status` <> -1 and kind = '" . $kind . "' and sid = " . $sid . " order by id desc limit 1";

        $stmt = $db->prepare($query);
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = $row;
        }

        return $result;
    }

?>
