<?php

ob_start();
error_reporting(E_ALL);

 error_reporting(0);

 include_once 'config/database.php';
include_once 'config/conf.php';

require '../vendor/autoload.php';

include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PhpOffice\PhpWord\IOFactory;

require '../PHPMailer-master/src/Exception.php';
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../vendor/autoload.php';

include_once 'config/core.php';
include_once 'config/conf.php';
include_once 'config/database.php';

function get_car_schedule_word($id, $full, $check_date_use, $check_car_use, $check_driver, $check_time_out, $check_time_in)
{
    
$database = new Database();
$db = $database->getConnection();


$sql = "select * from car_calendar_main where id = " . $id;
$stmt = $db->prepare($sql);
$stmt->execute();

$row = $stmt->fetch(PDO::FETCH_ASSOC);

$schedule_Name = $row['schedule_Name'];
$date_use = $row['date_use'];
$car_use =  $row['car_use'];
$driver = $row['driver'];
$helper = $row['helper'];
$time_out = $row['time_out'];
$time_in = $row['time_in'];
$notes = $row['notes'];
$items = $row['items'];
$status = $row['status'];
$creator = $row['created_by'];
$items = $row['items'];

if($check_date_use == "")
    $check_date_use = $date_use;

if($check_car_use == "")
    $check_car_use = $car_use;

if($check_driver == "")
    $check_driver = $driver;

if($check_time_out == "")
    $check_time_out = $time_out;

if($check_time_in == "")
    $check_time_in = $time_in;


$items_detail = json_decode($items, true);


// Creating the new document...
$phpWord = new PhpOffice\PhpWord\PhpWord();

/* Note: any element you append to a document must reside inside of a Section. */

// Adding an empty Section to the document...
$section = $phpWord->addSection();
// Adding Text element to the Section having font styled by default...

if($full == '1' && $status > 0)
{

    $check_dateString = date('Y-m-d', strtotime($check_date_use));

    $check_tout = "";
    if($check_date_use != "" && $check_time_out != "")
    {
        //$check_dateString = new DateTime($check_date_use . " " . $check_time_out);
        if(strlen($check_time_out) < 10)
        {
            $check_tout_str = new DateTime($check_date_use . " " . $check_time_out);
            $check_tout = date('h:i A', strtotime($check_tout_str));
        }
        else
            $check_tout = date('h:i A', strtotime($check_time_out));
    }
    

    $check_tin = "";
    if($check_date_use != "" && $check_time_in != "")
    {
        //$check_dateString = new DateTime($check_date_use . " " . $check_time_in);
        if(strlen($check_time_in) < 10)
        {
            $check_tin_str = new DateTime($check_date_use . " " . $check_time_in);
            $check_tin = date('h:i A', strtotime($check_tin_str));
        }
        else
            $check_tin = date('h:i A', strtotime($check_time_in));
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
    $cell->addText("Request Review", array('bold' => true, 'size' => 12), array('align' => 'center', 'valign' => 'center'));

    $table2->addRow();
    $table2->addCell(2000, ['borderSize' => 6])->addText("Date:", array('bold' => true));
    $table2->addCell(8500, ['borderSize' => 6])->addText($check_dateString);

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

$dateString = date('Y-m-d', strtotime($date_use));

$tout = "";
if($date_use != "" && $time_out != "")
{
    //$dateString = new DateTime($date_use . " " . $time_out);
    $tout = date('h:i A', strtotime( $time_out));
}

$tin = "";
if($date_use != "" && $time_in != "")
{
    //$dateString = new DateTime($date_use . " " . $time_in);
    $tin = date('h:i A', strtotime($time_in));
}

$table = $section->addTable('table', [
    'borderSize' => 6, 
    'borderColor' => 'F73605', 
    'afterSpacing' => 0, 
    'Spacing'=> 0, 
    'cellMargin'=> 0
]);

$table->addRow();
$cell = $table->addCell(10500, ['borderSize' => 6]);
$cell->getStyle()->setGridSpan(2);
$cell->addText("Content of Request", array('bold' => true, 'size' => 12), array('align' => 'center', 'valign' => 'center'));

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Schedule Name:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($schedule_Name);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Creator:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($creator);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Date Use:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($dateString);

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

    $objWriter->save($path . "schedule" . $id . ".docx");

    return $path . "schedule" . $id . ".docx";


}


function get_car_schedule_word_feliix($id, $full, 
                                    $schedule_Name, $date_use, $car_use, $driver, $helper, $time_out, $time_in, $notes, $items, $status, $creator,
                                    $check_date_use, $check_car_use, $check_driver, $check_time_out, $check_time_in)
{
    
$database = new Database_Feliix();
$db = $database->getConnection();


$sql = "select DAYNAME(start_time) weekday, DATE_FORMAT(start_time,'%d %M %Y') start_time, title, sales_executive, 
        project_in_charge, project_relevant, installer_needed, installer_needed_other, things_to_bring, installer_needed_location, things_to_bring_location, 
        products_to_bring, service, driver, driver_other,
        back_up_driver, back_up_driver_other, photoshoot_request, notes, products_to_bring_files,
        coalesce(pm.project_name, '') project_name, coalesce(pst.stage, '') stage_name, coalesce(`sequence`, '') sequence, main.status
        from work_calendar_main main 
        left join project_main pm on pm.id = main.related_project_id
        left join project_stages ps on ps.id = main.related_stage_id
        LEFT JOIN project_stage pst ON ps.stage_id = pst.id
        where  main.id = " . $id;

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    $weekday = '';
    $start_time = '';
    $title = '';
    $sales_executive = '';
    $project_in_charge = '';
    $project_relevant = '';
    $installer_needed = '';
    $installer_needed_other = '';
    $things_to_bring = '';
    $installer_needed_location = '';
    $things_to_bring_location = '';
    $products_to_bring = '';
    $service = '';
    $driver = '';
    $driver_other = '';
    $back_up_driver = '';
    $back_up_driver_other = '';
    $photoshoot_request = '';
    $notes = '';

    $products_to_bring_files = '';

    $location = '';
    $agenda = '';
    $appoint_time = '';
    $end_time = '';

    $project_name = '';
    $stage_name = '';
    $sequence = '';

    $onrecord = 0;

    $details = array();

while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
{
    $onrecord = $onrecord + 1;

    $weekday = $row['weekday'];
    $start_time = $row['start_time'];
    $title = $row['title'];
    $sales_executive = $row['sales_executive'];
    $project_in_charge = $row['project_in_charge'];
    $project_relevant = $row['project_relevant'];
    $installer_needed = $row['installer_needed'];
    $installer_needed_other = $row['installer_needed_other'];
    $things_to_bring = $row['things_to_bring'];
    $installer_needed_location = $row['installer_needed_location'];
    $things_to_bring_location = $row['things_to_bring_location'];
    $products_to_bring = $row['products_to_bring'];
    $service = $row['service'];
    $driver = $row['driver'];
    $driver_other = $row['driver_other'];
    $back_up_driver = $row['back_up_driver'];
    $back_up_driver_other = $row['back_up_driver_other'];
    $photoshoot_request = $row['photoshoot_request'];
    $notes = $row['notes'];

    $products_to_bring_files = $row['products_to_bring_files'];
    $details = GetDetails($id, $db);


    // $location = $row['location'];
    // $agenda = $row['agenda'];
    // $appoint_time = $row['appoint_time'];
    // $end_time = $row['end_time'];

    $project_name = $row['project_name'];
    $stage_name = $row['stage_name'];
    $sequence = $row['sequence'];

    $status = $row['status'];

    break;

}

// Creating the new document...
$phpWord = new PhpOffice\PhpWord\PhpWord();

/* Note: any element you append to a document must reside inside of a Section. */

// Adding an empty Section to the document...
$section = $phpWord->addSection();
// Adding Text element to the Section having font styled by default...

if($full == '1')
{

    $check_dateString = date('Y-m-d', strtotime($check_date_use));

    $check_tout = "";
    if($check_date_use != "" && $check_time_out != "")
    {
        //$check_dateString = new DateTime($check_date_use . " " . $check_time_out);
        if(strlen($check_time_out) < 10)
        {
            $check_tout_str = new DateTime($check_date_use . " " . $check_time_out);
            $check_tout = date('h:i A', strtotime($check_tout_str));
        }
        else
            $check_tout = date('h:i A', strtotime($check_time_out));
    }
    

    $check_tin = "";
    if($check_date_use != "" && $check_time_in != "")
    {
        //$check_dateString = new DateTime($check_date_use . " " . $check_time_in);
        if(strlen($check_time_in) < 10)
        {
            $check_tin_str = new DateTime($check_date_use . " " . $check_time_in);
            $check_tin = date('h:i A', strtotime($check_tin_str));
        }
        else
            $check_tin = date('h:i A', strtotime($check_time_in));
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
    $cell->addText("Request Review", array('bold' => true, 'size' => 12), array('align' => 'center', 'valign' => 'center'));

    $table2->addRow();
    $table2->addCell(2000, ['borderSize' => 6])->addText("Date:", array('bold' => true));
    $table2->addCell(8500, ['borderSize' => 6])->addText($check_dateString);

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

$dateString = date('Y-m-d', strtotime($date_use));

$tout = "";
if($date_use != "" && $time_out != "")
{
    //$dateString = new DateTime($date_use . " " . $time_out);
    $tout = date('h:i A', strtotime( $time_out));
}

$tin = "";
if($date_use != "" && $time_in != "")
{
    //$dateString = new DateTime($date_use . " " . $time_in);
    $tin = date('h:i A', strtotime($time_in));
}

$table = $section->addTable('table', [
    'borderSize' => 6, 
    'borderColor' => 'F73605', 
    'afterSpacing' => 0, 
    'Spacing'=> 0, 
    'cellMargin'=> 0
]);

$related_project = $project_name . " - " . $sequence . ": " . $stage_name;

if($related_project == " - : ") 
{
    $related_project = "";
}

$table->addRow();
$cell = $table->addCell(10500, ['borderSize' => 6]);
$cell->getStyle()->setGridSpan(2);
$cell->addText("Content of Request", array('bold' => true, 'size' => 12), array('align' => 'center', 'valign' => 'center'));

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Project:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($title);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Date:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($dateString);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Related Project:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($related_project);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Sales Executive:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($sales_executive);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Project_in_charge:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($project_in_charge);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Relevant Persons:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($project_relevant);

// CONCAT installer_needed and installer_needed_other and remove duplicate
//$installer_needed_other = str_replace(" ", "", $installer_needed_other);
//$installer_needed = str_replace(" ", "", $installer_needed);
$installer_needed_other_array = explode(",", $installer_needed_other);
$installer_needed_array = explode(",", $installer_needed);
$installer_needed_array = array_merge($installer_needed_array, $installer_needed_other_array);
$installer_needed_array = array_unique($installer_needed_array);

$merged_installer = trim(implode(", ", $installer_needed_array), ", ");

$merged_installer = str_replace("  ", " ", $merged_installer);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Installer needed:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($merged_installer);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Things to Bring:", array('bold' => true));
if($things_to_bring_location != "")
{
    $cell = $table->addCell(8500, ['borderSize' => 6]);
    addMultiLineText($cell, "From " . $things_to_bring_location . "\n" . $things_to_bring);
}
else
{
    $cell = $table->addCell(8500, ['borderSize' => 6]);
    addMultiLineText($cell, $things_to_bring);
}

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Products to Bring:", array('bold' => true));
if($installer_needed_location != "")
{
    $cell = $table->addCell(8500, ['borderSize' => 6]);
    addMultiLineText($cell, "From " . $installer_needed_location . "\n" . $products_to_bring);
}
else
{
    $cell = $table->addCell(8500, ['borderSize' => 6]);
    addMultiLineText($cell, $products_to_bring);
}

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Service:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText($service);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Driver:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText(getDriver($driver) . ' ' . $driver_other);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Back-up Driver:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText(getDriver($back_up_driver) . ' ' . $back_up_driver_other);

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Photoshoot Request:", array('bold' => true));
$table->addCell(8500, ['borderSize' => 6])->addText(getRequest($photoshoot_request));

$table->addRow();
$table->addCell(2000, ['borderSize' => 6])->addText("Notes:", array('bold' => true));
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
$table1->addCell(2600, ['borderSize' => 6])->addText("Location", ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table1->addCell(2600, ['borderSize' => 6])->addText("Agenda",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table1->addCell(2600, ['borderSize' => 6])->addText("Appoint Time",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
$table1->addCell(2600, ['borderSize' => 6])->addText("End Time",  ['bold' => true], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);


    // $table1->addRow();
    // $table1->addCell(2600, ['borderSize' => 6])->addText($location,  [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    // $table1->addCell(2600, ['borderSize' => 6])->addText($agenda, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    // $table1->addCell(2600, ['borderSize' => 6])->addText($appoint_time, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    // $table1->addCell(2600, ['borderSize' => 6])->addText($end_time, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

foreach ($details as &$value)
{
    $location = $value['location'];
    $agenda = $value['agenda'];
    $appoint_time = $value['appoint_time'];
    $end_time = $value['end_time'];

    $table1->addRow();
    $table1->addCell(2600, ['borderSize' => 6])->addText($location, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(2600, ['borderSize' => 6])->addText($agenda, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(2600, ['borderSize' => 6])->addText($appoint_time, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);
    $table1->addCell(2600, ['borderSize' => 6])->addText($end_time, [], ['align' => \PhpOffice\PhpWord\Style\Cell::VALIGN_CENTER]);

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

    $objWriter->save($path . "schedule" . $id . ".docx");

    return $path . "schedule" . $id . ".docx";


}



function send_car_approval_mail_1($id, $sender, $date_check, $service_check, $driver_check, $tout, $tin, $att)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $result = GetMain($id);

    $to = $result[0]['created_by'] . "," . $sender;
    $project = $result[0]['schedule_Name'];
    $creator = $result[0]['created_by'];
    $date = date('Y-m-d', strtotime($result[0]['date_use']));
    $time = date('h:i A', strtotime($result[0]['time_out'])) . " to " . date('h:i A', strtotime($result[0]['time_in']));
    $service = $result[0]['car_use'];

    $time_check = date('h:i A', strtotime($tout)) . " to " . date('h:i A', strtotime($tin));

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByName($to);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }


    $notifior = GetCarCheckers();
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }

    $mail->addAttachment($att);

    $mail->Subject = "[Car Schedule] Your request of car schedule has been approved";
    $content =  "<p>Dear all,</p>";
    $content = $content . "<p>Your request of car schedule has been approved. Below is the details:</p>";
    $content = $content . "<p>Approved Result</p>";
    $content = $content . "<p>Date: " . $date_check . "</p>";
    $content = $content . "<p>Time: " . $time_check . "</p>";
    $content = $content . "<p>Assigned Car: " . $service_check . "</p>";
    $content = $content . "<p>Assigned Driver: " . $driver_check . "</p>";
    $content = $content . "<p>------------------------------------------------------------------------------</p>";
    $content = $content . "<p>Content of Request</p>";
    $content = $content . "<p>Schedule Name: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Date Use: " . $date . "</p>";
    $content = $content . "<p>Time: " . $time . "</p>";
    $content = $content . "<p>Car Use: " . $service . "</p>";

    $content = $content . "<p></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}

function send_car_request_mail_2($id, $sender, $date_check, $service_check, $driver_check, $tout, $tin, $att)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $result = GetMain($id);

    $to = $result[0]['created_by'] . "," . $sender;
    $project = $result[0]['schedule_Name'];
    $creator = $result[0]['created_by'];
    $date = date('Y-m-d', strtotime($result[0]['date_use']));
    $time = date('h:i A', strtotime($result[0]['time_out'])) . " to " . date('h:i A', strtotime($result[0]['time_in']));
    $service = $result[0]['car_use'];

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByName($to);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }


    $notifior = GetCarChecker1();
    $checker1 = "";
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
        $checker1 .= $list["username"] . ", ";
    }

    $checker1 = rtrim($checker1, ", ");

    $mail->addAttachment($att);

    $mail->Subject = "[Car Schedule] A request of car schedule is waiting for your approval";
    $content =  "<p>Dear " . $checker1 . ",</p>";
    $content = $content . "<p>A request of car schedule is waiting for your approval. Below is the details:</p>";
    $content = $content . "<p>Schedule Name: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Date Use: " . $date . "</p>";
    $content = $content . "<p>Time: " . $time . "</p>";
    $content = $content . "<p>Car Use: " . $service . "</p>";

    $content = $content . "<p></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}

function send_car_reject_mail_3($id, $sender, $date_check, $service_check, $driver_check, $tout, $tin, $att, $reason)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $result = GetMain($id);

    $requestor = $result[0]['requestor'];

    $to = $result[0]['created_by'] . "," . $requestor;
    $project = $result[0]['schedule_Name'];
    $creator = $result[0]['created_by'];
    

    $date = date('Y-m-d', strtotime($result[0]['date_use']));
    $time = date('h:i A', strtotime($result[0]['time_out'])) . " to " . date('h:i A', strtotime($result[0]['time_in']));
    $service = $result[0]['car_use'];

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByName($to);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }


    $notifior = GetCarChecker1();
    $checker1 = "";
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
        $checker1 .= $list["username"] . ", ";
    }

    $checker1 = rtrim($checker1, ", ");

    $mail->addAttachment($att);

    $mail->Subject = "[Car Schedule] Your request of car schedule was rejected";
    $content =  "<p>Dear all,</p>";
    $content = $content . "<p>Your request of car schedule was rejected. Reason is ". $reason . ". Below is the details:</p>";
    $content = $content . "<p>Schedule Name: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Date Use: " . $date . "</p>";
    $content = $content . "<p>Time: " . $time . "</p>";
    $content = $content . "<p>Car Use: " . $service . "</p>";

    $content = $content . "<p></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}

function send_car_under_review_mail_4($id, $sender, $date_check, $service_check, $driver_check, $tout, $tin, $att, $reason)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $result = GetMain($id);

    $requestor = $result[0]['requestor'];

    $to = $result[0]['created_by'] . "," . $requestor;
    $project = $result[0]['schedule_Name'];
    $creator = $result[0]['created_by'];
    $date = date('Y-m-d', strtotime($result[0]['date_use']));
    $time = date('h:i A', strtotime($result[0]['time_out'])) . " to " . date('h:i A', strtotime($result[0]['time_in']));
    $service = $result[0]['car_use'];
    
    $time_check = date('h:i A', strtotime($tout)) . " to " . date('h:i A', strtotime($tin));

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByName($to);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }


    $notifior = GetCarCheckers();
    $checker1 = "";
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
        $checker1 .= $list["username"] . ", ";
    }

    $checker1 = rtrim($checker1, ", ");

    $mail->addAttachment($att);

    $mail->Subject = "[Car Schedule] Your approved request of car schedule was changed into Under Review";
    $content =  "<p>Dear all,</p>";
    $content = $content . "<p>Your approved request of car schedule was changed into Under Review. Reason is ". $reason . ". Below is the details:</p>";
    $content = $content . "<p>Old Approved Result</p>";
    $content = $content . "<p>Date: " . $date_check . "</p>";
    $content = $content . "<p>Time: " . $time_check . "</p>";
    $content = $content . "<p>Assigned Car: " . $service_check . "</p>";
    $content = $content . "<p>Assigned Driver: " . $driver_check . "</p>";
    $content = $content . "<p>------------------------------------------------------------------------------</p>";
    $content = $content . "<p>Content of Request</p>";
    $content = $content . "<p>Schedule Name: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Date Use: " . $date . "</p>";
    $content = $content . "<p>Time: " . $time . "</p>";
    $content = $content . "<p>Car Use: " . $service . "</p>";

    $content = $content . "<p></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function send_car_reject_mail_5($id, $sender, $date_check, $service_check, $driver_check, $tout, $tin, $att, $reason)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $result = GetMain($id);

    $requestor = $result[0]['requestor'];

    $to = $result[0]['created_by'] . "," . $requestor;
    $project = $result[0]['schedule_Name'];
    $creator = $result[0]['created_by'];
    $date = date('Y-m-d', strtotime($result[0]['date_use']));
    $time = date('h:i A', strtotime($result[0]['time_out'])) . " to " . date('h:i A', strtotime($result[0]['time_in']));
    $service = $result[0]['car_use'];

    $mail = SetupMail($mail, $conf);

    $time_check = date('h:i A', strtotime($tout)) . " to " . date('h:i A', strtotime($tin));

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByName($to);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }


    $notifior = GetCarCheckers();
    $checker1 = "";
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
        $checker1 .= $list["username"] . ", ";
    }

    $checker1 = rtrim($checker1, ", ");

    $mail->addAttachment($att);

    $mail->Subject = "[Car Schedule] Your approved request of car schedule was changed into Rejected";
    $content =  "<p>Dear all,</p>";
    $content = $content . "<p>Your approved request of car schedule was changed into Rejected. Reason is ". $reason . ". Below is the details:</p>";
    $content = $content . "<p>Old Approved Result</p>";
    $content = $content . "<p>Date: " . $date_check . "</p>";
    $content = $content . "<p>Time: " . $time_check . "</p>";
    $content = $content . "<p>Assigned Car: " . $service_check . "</p>";
    $content = $content . "<p>Assigned Driver: " . $driver_check . "</p>";
    $content = $content . "<p>------------------------------------------------------------------------------</p>";
    $content = $content . "<p>Content of Request</p>";
    $content = $content . "<p>Schedule Name: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Date Use: " . $date . "</p>";
    $content = $content . "<p>Time: " . $time . "</p>";
    $content = $content . "<p>Car Use: " . $service . "</p>";

    $content = $content . "<p></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function send_car_edit_mail_6($id, $sender, $date_check, $service_check, $driver_check, $tout, $tin, $att)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $result = GetMain($id);

    $requestor = $result[0]['requestor'];

    $to = $result[0]['created_by'] . "," . $requestor;
    $project = $result[0]['schedule_Name'];
    $creator = $result[0]['created_by'];
    $date = date('Y-m-d', strtotime($result[0]['date_use']));
    $time = date('h:i A', strtotime($result[0]['time_out'])) . " to " . date('h:i A', strtotime($result[0]['time_in']));
    $service = $result[0]['car_use'];

    $time_check = date('h:i A', strtotime($tout)) . " to " . date('h:i A', strtotime($tin));

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByName($to);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }


    $notifior = GetCarCheckers();
    $checker1 = "";
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
        $checker1 .= $list["username"] . ", ";
    }

    $checker1 = rtrim($checker1, ", ");

    $mail->addAttachment($att);

    $mail->Subject = "[Car Schedule] Your approved request of car schedule has been revised and approved again";
    $content =  "<p>Dear all,</p>";
    $content = $content . "<p>Your approved request of car schedule has been revised and approved again. Below is the details:</p>";
    $content = $content . "<p>New Approved Result</p>";
    $content = $content . "<p>Date: " . $date_check . "</p>";
    $content = $content . "<p>Time: " . $time_check . "</p>";
    $content = $content . "<p>Assigned Car: " . $service_check . "</p>";
    $content = $content . "<p>Assigned Driver: " . $driver_check . "</p>";
    $content = $content . "<p>------------------------------------------------------------------------------</p>";
    $content = $content . "<p>Content of Request</p>";
    $content = $content . "<p>Schedule Name: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Date Use: " . $date . "</p>";
    $content = $content . "<p>Time: " . $time . "</p>";
    $content = $content . "<p>Car Use: " . $service . "</p>";

    $content = $content . "<p></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function send_car_approved_withdraw_mail_7($id, $sender, $date_check, $service_check, $driver_check, $tout, $tin, $att)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $result = GetMain($id);

    
    $requestor = $result[0]['requestor'];
    $to = $result[0]['created_by'] . "," . $sender . "," . $requestor;

    $project = $result[0]['schedule_Name'];
    $creator = $result[0]['created_by'];
    $date = date('Y-m-d', strtotime($result[0]['date_use']));
    $time = date('h:i A', strtotime($result[0]['time_out'])) . " to " . date('h:i A', strtotime($result[0]['time_in']));
    $service = $result[0]['car_use'];

    $time_check = date('h:i A', strtotime($tout)) . " to " . date('h:i A', strtotime($tin));

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByName($to);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }


    $notifior = GetCarCheckers();
    $checker1 = "";
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
        $checker1 .= $list["username"] . ", ";
    }

    $checker1 = rtrim($checker1, ", ");

    $mail->addAttachment($att);

    $mail->Subject = "[Car Schedule] An approved request of car schedule was withdrawn by " . $sender;
    $content =  "<p>Dear all,</p>";
    $content = $content . "<p>An approved request of car schedule was withdrawn by " . $sender . ". Below is the details:</p>";
    $content = $content . "<p>Previous Approved Result</p>";
    $content = $content . "<p>Date: " . $date_check . "</p>";
    $content = $content . "<p>Time: " . $time_check . "</p>";
    $content = $content . "<p>Assigned Car: " . $service_check . "</p>";
    $content = $content . "<p>Assigned Driver: " . $driver_check . "</p>";
    $content = $content . "<p>------------------------------------------------------------------------------</p>";
    $content = $content . "<p>Content of Request</p>";
    $content = $content . "<p>Schedule Name: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Date Use: " . $date . "</p>";
    $content = $content . "<p>Time: " . $time . "</p>";
    $content = $content . "<p>Car Use: " . $service . "</p>";

    $content = $content . "<p></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function send_car_withdraw_mail_8($id, $sender, $date_check, $service_check, $driver_check, $tout, $tin, $att)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $result = GetMain($id);

    $requestor = $result[0]['requestor'];
    $to = $result[0]['created_by'] . "," . $sender . "," . $requestor;

    $project = $result[0]['schedule_Name'];
    $creator = $result[0]['created_by'];
    $date = date('Y-m-d', strtotime($result[0]['date_use']));
    $time = date('h:i A', strtotime($result[0]['time_out'])) . " to " . date('h:i A', strtotime($result[0]['time_in']));
    $service = $result[0]['car_use'];

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByName($to);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }


    $notifior = GetCarChecker1();
    $checker1 = "";
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
        $checker1 .= $list["username"] . ", ";
    }

    $checker1 = rtrim($checker1, ", ");

    $mail->addAttachment($att);

    $mail->Subject = "[Car Schedule] A request of car schedule was withdrawn by " . $sender;
    $content =  "<p>Dear " . $checker1 . ",</p>";
    $content = $content . "<p>A request of car schedule was withdrawn by " . $sender . ". Below is the details:</p>";
    $content = $content . "<p>Content of Request</p>";
    $content = $content . "<p>Schedule Name: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Date Use: " . $date . "</p>";
    $content = $content . "<p>Time: " . $time . "</p>";
    $content = $content . "<p>Car Use: " . $service . "</p>";

    $content = $content . "<p></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function send_car_approved_delete_mail_9($id, $sender, $date_check, $service_check, $driver_check, $tout, $tin, $att)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $result = GetMain($id);

    
    $requestor = $result[0]['requestor'];
    $to = $result[0]['created_by'] . "," . $sender . "," . $requestor;

    $project = $result[0]['schedule_Name'];
    $creator = $result[0]['created_by'];
    $date = date('Y-m-d', strtotime($result[0]['date_use']));
    $time = date('h:i A', strtotime($result[0]['time_out'])) . " to " . date('h:i A', strtotime($result[0]['time_in']));
    $service = $result[0]['car_use'];

    $time_check = date('h:i A', strtotime($tout)) . " to " . date('h:i A', strtotime($tin));

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByName($to);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }


    $notifior = GetCarCheckers();
    $checker1 = "";
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
        $checker1 .= $list["username"] . ", ";
    }

    $checker1 = rtrim($checker1, ", ");

    $mail->addAttachment($att);

    $mail->Subject = "[Car Schedule] An approved request of car schedule was withdrawn and deleted by " . $sender;
    $content =  "<p>Dear all,</p>";
    $content = $content . "<p>An approved request of car schedule was withdrawn and deleted by " . $sender . ". Below is the details:</p>";
    $content = $content . "<p>Previous Approved Result</p>";
    $content = $content . "<p>Date: " . $date_check . "</p>";
    $content = $content . "<p>Time: " . $time_check . "</p>";
    $content = $content . "<p>Assigned Car: " . $service_check . "</p>";
    $content = $content . "<p>Assigned Driver: " . $driver_check . "</p>";
    $content = $content . "<p>------------------------------------------------------------------------------</p>";
    $content = $content . "<p>Content of Request</p>";
    $content = $content . "<p>Schedule Name: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Date Use: " . $date . "</p>";
    $content = $content . "<p>Time: " . $time . "</p>";
    $content = $content . "<p>Car Use: " . $service . "</p>";

    $content = $content . "<p></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function send_car_delete_mail_10($id, $sender, $date_check, $service_check, $driver_check, $tout, $tin, $att)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $result = GetMain($id);

    $requestor = $result[0]['requestor'];
    $to = $result[0]['created_by'] . "," . $sender . "," . $requestor;

    $project = $result[0]['schedule_Name'];
    $creator = $result[0]['created_by'];
    $date = date('Y-m-d', strtotime($result[0]['date_use']));
    $time = date('h:i A', strtotime($result[0]['time_out'])) . " to " . date('h:i A', strtotime($result[0]['time_in']));
    $service = $result[0]['car_use'];

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByName($to);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }


    $notifior = GetCarChecker1();
    $checker1 = "";
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
        $checker1 .= $list["username"] . ", ";
    }

    $checker1 = rtrim($checker1, ", ");

    $mail->addAttachment($att);

    $mail->Subject = "[Car Schedule] A request of car schedule was withdrawn and deleted by " . $sender;
    $content =  "<p>Dear " . $checker1 . ",</p>";
    $content = $content . "<p>A request of car schedule was withdrawn and deleted by " . $sender . ". Below is the details:</p>";
    $content = $content . "<p>Content of Request</p>";
    $content = $content . "<p>Schedule Name: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Date Use: " . $date . "</p>";
    $content = $content . "<p>Time: " . $time . "</p>";
    $content = $content . "<p>Car Use: " . $service . "</p>";

    $content = $content . "<p></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}

function send_car_approve_mail_11($id, $sender, $date_check, $service_check, $driver_check, $tout, $tin, $att)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $result = GetMain($id);

    $requestor = $result[0]['requestor'];
    $to = $result[0]['created_by'] . "," . $requestor;

    $project = $result[0]['schedule_Name'];
    $creator = $result[0]['created_by'];
    $date = date('Y-m-d', strtotime($result[0]['date_use']));
    $time = date('h:i A', strtotime($result[0]['time_out'])) . " to " . date('h:i A', strtotime($result[0]['time_in']));
    $service = $result[0]['car_use'];

    $time_check = date('h:i A', strtotime($tout)) . " to " . date('h:i A', strtotime($tin));

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByName($to);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }


    $notifior = GetCarCheckers();
    $checker1 = "";
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
        $checker1 .= $list["username"] . ", ";
    }

    $checker1 = rtrim($checker1, ", ");

    $mail->addAttachment($att);

    $mail->Subject = "[Car Schedule] Your request of car schedule has been approved";
    $content =  "<p>Dear all,</p>";
    $content = $content . "<p>Your request of car schedule has been approved. Below is the details:</p>";
    $content = $content . "<p>Approved Result</p>";
    $content = $content . "<p>Date: " . $date_check . "</p>";
    $content = $content . "<p>Time: " . $time_check . "</p>";
    $content = $content . "<p>Assigned Car: " . $service_check . "</p>";
    $content = $content . "<p>Assigned Driver: " . $driver_check . "</p>";
    $content = $content . "<p>------------------------------------------------------------------------------</p>";
    $content = $content . "<p>Content of Request</p>";
    $content = $content . "<p>Schedule Name: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Date Use: " . $date . "</p>";
    $content = $content . "<p>Time: " . $time . "</p>";
    $content = $content . "<p>Car Use: " . $service . "</p>";

    $content = $content . "<p></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}



function send_car_approval_mail_1_feliix($id, $sender, $date_check, $service_check, $driver_check, $tout, $tin, $att)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $result = GetMainFeliix($id);

    $requestor = $result[0]['requestor'];

    $to = $result[0]['created_by'] . "," . $requestor;

    $project_relevant = $result[0]['project_relevant'];

    $project = $result[0]['title'];
    $creator = $result[0]['created_by'];
    $date = date('Y-m-d', strtotime($result[0]['start_time']));
    $time = date('h:i A', strtotime($result[0]['start_time'])) . " to " . date('h:i A', strtotime($result[0]['end_time']));
    $service = $result[0]['service'];

    $time_check = date('h:i A', strtotime($tout)) . " to " . date('h:i A', strtotime($tin));

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByNameFeliix($to);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $notifior = GetCarCheckers();
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }

    $notifior = GetRelevantsByName($project_relevant);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }

    $mail->addAttachment($att);

    $mail->Subject = "[Car Schedule] Your request of car schedule has been approved";
    $content =  "<p>Dear all,</p>";
    $content = $content . "<p>Your request of car schedule has been approved. Below is the details:</p>";
    $content = $content . "<p>Approved Result</p>";
    $content = $content . "<p>Date: " . $date_check . "</p>";
    $content = $content . "<p>Time: " . $time_check . "</p>";
    $content = $content . "<p>Assigned Car: " . $service_check . "</p>";
    $content = $content . "<p>Assigned Driver: " . $driver_check . "</p>";
    $content = $content . "<p>------------------------------------------------------------------------------</p>";
    $content = $content . "<p>Content of Request</p>";
    $content = $content . "<p>Schedule Name: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Date Use: " . $date . "</p>";
    $content = $content . "<p>Time: " . $time . "</p>";
    $content = $content . "<p>Car Use: " . $service . "</p>";

    $content = $content . "<p></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function send_car_reject_mail_2_feliix($id, $sender, $date_check, $service_check, $driver_check, $tout, $tin, $att, $reason)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $result = GetMainFeliix($id);

    $requestor = $result[0]['requestor'];

    $to = $result[0]['created_by'] . "," . $requestor;

    $project_relevant = $result[0]['project_relevant'];

    $project = $result[0]['title'];
    $creator = $result[0]['created_by'];
    $date = date('Y-m-d', strtotime($result[0]['start_time']));
    $time = date('h:i A', strtotime($result[0]['start_time'])) . " to " . date('h:i A', strtotime($result[0]['end_time']));
    $service = $result[0]['service'];

    $mail = SetupMail($mail, $conf);

    $time_check = date('h:i A', strtotime($tout)) . " to " . date('h:i A', strtotime($tin));

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByNameFeliix($to);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }


    $notifior = GetCarChecker1();
    $checker1 = "";
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
        $checker1 .= $list["username"] . ", ";
    }

    $checker1 = rtrim($checker1, ", ");

    // $notifior = GetRelevantsByName($project_relevant);
    // foreach($notifior as &$list)
    // {
    //     $mail->AddCC($list["email"], $list["username"]);
    // }

    $mail->addAttachment($att);

    $mail->Subject = "[Car Schedule] Your request of car schedule was rejected";
    $content =  "<p>Dear all,</p>";
    $content = $content . "<p>Your request of car schedule was rejected. Reason is ". $reason . ". Below is the details:</p>";

    $content = $content . "<p>Schedule Name: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Date Use: " . $date . "</p>";
    $content = $content . "<p>Time: " . $time . "</p>";
    $content = $content . "<p>Car Use: " . $service . "</p>";

    $content = $content . "<p></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}

function send_car_under_review_mail_3_feliix($id, $sender, $date_check, $service_check, $driver_check, $tout, $tin, $att, $reason)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $result = GetMainFeliix($id);

    $requestor = $result[0]['requestor'];

    $to = $result[0]['created_by'] . "," . $requestor;

    $project_relevant = $result[0]['project_relevant'];

    $project = $result[0]['title'];
    $creator = $result[0]['created_by'];
    $date = date('Y-m-d', strtotime($result[0]['start_time']));
    $time = date('h:i A', strtotime($result[0]['start_time'])) . " to " . date('h:i A', strtotime($result[0]['end_time']));
    $service = $result[0]['service'];
    
    $time_check = date('h:i A', strtotime($tout)) . " to " . date('h:i A', strtotime($tin));

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByNameFeliix($to);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $notifior = GetRelevantsByName($project_relevant);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }


    $notifior = GetCarCheckers();
    $checker1 = "";
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
        $checker1 .= $list["username"] . ", ";
    }

    $checker1 = rtrim($checker1, ", ");

    $mail->addAttachment($att);

    $mail->Subject = "[Car Schedule] Your approved request of car schedule was changed into Under Review";
    $content =  "<p>Dear all,</p>";
    $content = $content . "<p>Your approved request of car schedule was changed into Under Review. Reason is ". $reason . ". Below is the details:</p>";
    $content = $content . "<p>Old Approved Result</p>";
    $content = $content . "<p>Date: " . $date_check . "</p>";
    $content = $content . "<p>Time: " . $time_check . "</p>";
    $content = $content . "<p>Assigned Car: " . $service_check . "</p>";
    $content = $content . "<p>Assigned Driver: " . $driver_check . "</p>";
    $content = $content . "<p>------------------------------------------------------------------------------</p>";
    $content = $content . "<p>Content of Request</p>";
    $content = $content . "<p>Schedule Name: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Date Use: " . $date . "</p>";
    $content = $content . "<p>Time: " . $time . "</p>";
    $content = $content . "<p>Car Use: " . $service . "</p>";

    $content = $content . "<p></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}

function send_car_reject_mail_4_feliix($id, $sender, $date_check, $service_check, $driver_check, $tout, $tin, $att, $reason)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $result = GetMainFeliix($id);

    $requestor = $result[0]['requestor'];

    $to = $result[0]['created_by'] . "," . $requestor;

    $project_relevant = $result[0]['project_relevant'];

    $project = $result[0]['title'];
    $creator = $result[0]['created_by'];
    $date = date('Y-m-d', strtotime($result[0]['start_time']));
    $time = date('h:i A', strtotime($result[0]['start_time'])) . " to " . date('h:i A', strtotime($result[0]['end_time']));
    $service = $result[0]['service'];

    $mail = SetupMail($mail, $conf);

    $time_check = date('h:i A', strtotime($tout)) . " to " . date('h:i A', strtotime($tin));

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByNameFeliix($to);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }


    $notifior = GetCarCheckers();
    $checker1 = "";
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
        $checker1 .= $list["username"] . ", ";
    }

    $checker1 = rtrim($checker1, ", ");

    $notifior = GetRelevantsByName($project_relevant);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }

    $mail->addAttachment($att);

    $mail->Subject = "[Car Schedule] Your approved request of car schedule was changed into Rejected";
    $content =  "<p>Dear all,</p>";
    $content = $content . "<p>Your approved request of car schedule was changed into Rejected. Reason is ". $reason . ". Below is the details:</p>";
    $content = $content . "<p>Old Approved Result</p>";
    $content = $content . "<p>Date: " . $date_check . "</p>";
    $content = $content . "<p>Time: " . $time_check . "</p>";
    $content = $content . "<p>Assigned Car: " . $service_check . "</p>";
    $content = $content . "<p>Assigned Driver: " . $driver_check . "</p>";
    $content = $content . "<p>------------------------------------------------------------------------------</p>";
    $content = $content . "<p>Content of Request</p>";
    $content = $content . "<p>Schedule Name: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Date Use: " . $date . "</p>";
    $content = $content . "<p>Time: " . $time . "</p>";
    $content = $content . "<p>Car Use: " . $service . "</p>";

    $content = $content . "<p></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

}


function send_car_edit_mail_5_feliix($id, $sender, $date_check, $service_check, $driver_check, $tout, $tin, $att)
{
    $conf = new Conf();

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->Mailer = "smtp";
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "ssl";
    // $mail->Port       = 465;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = $conf::$mail_host;
    // $mail->Username   = $conf::$mail_username;
    // $mail->Password   = $conf::$mail_password;

    $result = GetMainFeliix($id);

    $requestor = $result[0]['requestor'];

    $to = $result[0]['created_by'] . "," . $requestor;

    $project_relevant = $result[0]['project_relevant'];

    $project = $result[0]['title'];
    $creator = $result[0]['created_by'];
    $date = date('Y-m-d', strtotime($result[0]['start_time']));
    $time = date('h:i A', strtotime($result[0]['start_time'])) . " to " . date('h:i A', strtotime($result[0]['end_time']));
    $service = $result[0]['service'];

    $time_check = date('h:i A', strtotime($tout)) . " to " . date('h:i A', strtotime($tin));

    $mail = SetupMail($mail, $conf);

    $mail->IsHTML(true);

    $mail->SetFrom("feliix.it@gmail.com", "Feliix.System");
    $mail->AddReplyTo("feliix.it@gmail.com", "Feliix.System");

    $notifior = array();
    $notifior = GetNotifiersByNameFeliix($to);
    foreach($notifior as &$list)
    {
        $mail->AddAddress($list["email"], $list["username"]);
    }

    $notifior = GetRelevantsByName($project_relevant);
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
    }

    $notifior = GetCarCheckers();
    $checker1 = "";
    foreach($notifior as &$list)
    {
        $mail->AddCC($list["email"], $list["username"]);
        $checker1 .= $list["username"] . ", ";
    }

    $checker1 = rtrim($checker1, ", ");

    $mail->addAttachment($att);

    $mail->Subject = "[Car Schedule] Your approved request of car schedule has been revised and approved again";
    $content =  "<p>Dear all,</p>";
    $content = $content . "<p>Your approved request of car schedule has been revised and approved again. Below is the details:</p>";
    $content = $content . "<p>New Approved Result</p>";
    $content = $content . "<p>Date: " . $date_check . "</p>";
    $content = $content . "<p>Time: " . $time_check . "</p>";
    $content = $content . "<p>Assigned Car: " . $service_check . "</p>";
    $content = $content . "<p>Assigned Driver: " . $driver_check . "</p>";
    $content = $content . "<p>------------------------------------------------------------------------------</p>";
    $content = $content . "<p>Content of Request</p>";
    $content = $content . "<p>Schedule Name: " . $project . "</p>";
    $content = $content . "<p>Creator: " . $creator . "</p>";
    $content = $content . "<p>Date Use: " . $date . "</p>";
    $content = $content . "<p>Time: " . $time . "</p>";
    $content = $content . "<p>Car Use: " . $service . "</p>";

    $content = $content . "<p></p>";

    $mail->MsgHTML($content);
    if($mail->Send()) {
        logMail($creator, $content);
        return true;
//        echo "Error while sending Email.";
//        var_dump($mail);
    } else {
        logMail($creator, $mail->ErrorInfo . $content);
        return false;
//        echo "Email sent successfully";
    }

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


function GetCheck($db, $sid, $kind, $feliix)
{
    $result = array();

    $query = "SELECT * from car_calendar_check 
              where `feliix` = " . $feliix . " and `status` <> -1 and kind = '" . $kind . "' and sid = " . $sid . " order by id desc limit 1";

    $stmt = $db->prepare($query);
    $stmt->execute();

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result[] = $row;
    }

    return $result;
}

function GetMain($id)
{
    $database = new Database();
    $db = $database->getConnection();

    $database_feliix = new Database_Feliix();
    $db_feliix = $database_feliix->getConnection();

    try {

        $merged_results = array();
            
        $query = "SELECT * from car_calendar_main main 
                  where `status` <> -1 and id = " . $id;

        $stmt = $db->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $merged_results[] = $row;

            $check1 = GetCheck($db, $row['id'], "1", "0");
            $check2 = GetCheck($db, $row['id'], "2", "0");

            $merged_results[count($merged_results) - 1]['check1'] = $check1;
            $merged_results[count($merged_results) - 1]['check2'] = $check2;

            $merged_results[count($merged_results) - 1]['feliix'] = "";
        }

        // for feliix
        // $merged_results_feliix = array();
        // $merged_results_feliix = GetFeliix($db_feliix, $sdate, $edate, $db);

        // foreach ($merged_results_feliix as $key => $value) {
        //     $merged_results[] = $value;
        // }

        return $merged_results;
    } catch (Exception $e) {
        http_response_code(401);

        return $merged_results;
    }


}


function GetMainFeliix($id)
{
    $database = new Database();
    $db = $database->getConnection();

    $database_feliix = new Database_Feliix();
    $db_feliix = $database_feliix->getConnection();

    try {

        $merged_results = array();
            
        $query = "SELECT * from work_calendar_main main 
                  where `status` <> -1 and id = " . $id;

        $stmt = $db_feliix->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $merged_results[] = $row;

            $check1 = GetCheck($db, $row['id'], "1", "1");
            $check2 = GetCheck($db, $row['id'], "2", "1");

            $merged_results[count($merged_results) - 1]['check1'] = $check1;
            $merged_results[count($merged_results) - 1]['check2'] = $check2;

            $merged_results[count($merged_results) - 1]['feliix'] = "1";
        }

        // for feliix
        // $merged_results_feliix = array();
        // $merged_results_feliix = GetFeliix($db_feliix, $sdate, $edate, $db);

        // foreach ($merged_results_feliix as $key => $value) {
        //     $merged_results[] = $value;
        // }

        return $merged_results;
    } catch (Exception $e) {
        http_response_code(401);

        return $merged_results;
    }


}

function GetNotifiersByName($names)
{
    $database = new Database();
    $db = $database->getConnection();

    $myArray = explode(',', $names);
    $result = "'" . implode ( "', '", $myArray ) . "'";

    $sql = "SELECT user.id, username, email FROM user 
        WHERE user.username in (" . $result . ") and user.status = 1";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetNotifiersByNameFeliix($names)
{
    $database = new Database_Feliix();
    $db = $database->getConnection();

    $myArray = explode(',', $names);
    $result = "'" . implode ( "', '", $myArray ) . "'";

    $sql = "SELECT user.id, username, email FROM user 
        WHERE user.username in (" . $result . ") and user.status = 1";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}


function GetCarCheckers()
{
    $database = new Database();
    $db = $database->getConnection();

    $names = [];
    $result = "";

    // get car_access1, car_access2 split by comma
    $sql = "select car_access1, car_access2  from access_control ";
    $rs = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $rs[] = $row;
    }

    foreach($rs as &$list)
    {
        $arr = explode(',', $list['car_access1']);
        $result = "'" . implode ( "', '", $arr ) . "'";

        $arr = explode(',', $list['car_access2']);
        $result .= ",'" . implode ( "', '", $arr ) . "'";

    }

    if($result != "")
    {
        $sql = "SELECT user.id, username, email FROM user 
            WHERE user.username in (" . $result . ") and user.status = 1";
    
        $merged_results = array();
    
        $stmt = $db->prepare($sql);
        $stmt->execute();
    
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $merged_results[] = $row;
        }
    }

    return $merged_results;
}


function GetRelevantsByName($names)
{
    $database = new Database_Feliix();
    $db = $database->getConnection();

    $myArray = explode(',', $names);
    $result = "'" . implode ( "', '", $myArray ) . "'";

    $sql = "SELECT user.id, username, email FROM user 
        WHERE user.username in (" . $result . ") and user.status = 1";

    $merged_results = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    return $merged_results;
}

function GetCarChecker1()
{
    $database = new Database();
    $db = $database->getConnection();

    $names = [];
    $result = "";

    // get car_access1, car_access2 split by comma
    $sql = "select car_access1  from access_control ";
    $rs = array();

    $stmt = $db->prepare($sql);
    $stmt->execute();

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $rs[] = $row;
    }

    foreach($rs as &$list)
    {
        $arr = explode(',', $list['car_access1']);
        $result = "'" . implode ( "', '", $arr ) . "'";
    }


    if($result != "")
    {
        $sql = "SELECT user.id, username, email FROM user 
            WHERE user.username in (" . $result . ") and user.status = 1";
    
        $merged_results = array();
    
        $stmt = $db->prepare($sql);
        $stmt->execute();
    
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $merged_results[] = $row;
        }
    }

    return $merged_results;
}


function GetDetails($id, $db)
{
    $merged_details = array();

    $sql = "select detail.location, agenda, DATE_FORMAT(appoint_time, '%I:%i %p') appoint_time, 
            DATE_FORMAT(detail.end_time, '%I:%i %p') end_time
            from work_calendar_details detail
            where coalesce(detail.is_enabled, 1) = 1  and main_id = " . $id . " order by sort " ;

    $stmt = $db->prepare( $sql );
    $stmt->execute();

    $location = '';
    $agenda = '';
    $appoint_time = '';
    $end_time = '';

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) 
    {
        $location = $row['location'];
        $agenda = $row['agenda'];
        $appoint_time = $row['appoint_time'];
        $end_time = $row['end_time'];

        $merged_details[] = array(
            'location' => $location,
            'agenda' => $agenda,
            'appoint_time' => $appoint_time,
            'end_time' => $end_time
        );
    }

    return $merged_details;

}

function logMail($sender, $content)
{
    return;
}


function SetupMail($mail, $conf)
{
    $mail->SMTPDebug  = 0;
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = "ssl";
    $mail->Port       = 465;
    $mail->SMTPKeepAlive = true;
    $mail->Host       = $conf::$mail_host;
    $mail->Username   = $conf::$mail_username;
    $mail->Password   = $conf::$mail_password;


    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "tls";
    // $mail->Port       = 587;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = 'smtp.ethereal.email';
    // $mail->Username   = 'jermey.wilkinson@ethereal.email';
    // $mail->Password   = 'zXX3N6QwJ5AYZUjbKe';

    // $mail->SMTPDebug  = 0;
    // $mail->SMTPAuth   = true;
    // $mail->SMTPSecure = "tls";
    // $mail->Port       = 587;
    // $mail->SMTPKeepAlive = true;
    // $mail->Host       = 'smtp.ethereal.email';
    // $mail->Username   = 'calista.lubowitz@ethereal.email';
    // $mail->Password   = 'VzkRWsx6FszvrQ1ZTW';

    return $mail;

}