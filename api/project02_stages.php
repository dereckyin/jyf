<?php
error_reporting(E_ERROR | E_PARSE);

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$jwt = (isset($_COOKIE['jwt']) ?  $_COOKIE['jwt'] : null);
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
if ( !isset( $jwt ) ) {
    http_response_code(401);

    echo json_encode(array("message" => "Access denied."));
    die();
}
else
{
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $user_id = $decoded->data->id;
    }
        // if decode fails, it means jwt is invalid
    catch (Exception $e){

        http_response_code(401);

        echo json_encode(array("message" => "Access denied."));
        die();
    }
}

include_once 'config/database.php';
$database = new Database_Feliix();
$db = $database->getConnection();

$page = (isset($_GET['page']) ?  $_GET['page'] : "");
$size = (isset($_GET['size']) ?  $_GET['size'] : "");

$pid = (isset($_GET['pid']) ?  $_GET['pid'] : 0);

$merged_results = array();



$query = "SELECT pm.id, `sequence`, pst.id project_stage_id, pst.`stage`, pm.stage_title title, (CASE `stages_status_id` WHEN '1' THEN 'Ongoing' WHEN '2' THEN 'Pending' WHEN '3' THEN 'Close' END ) as `stages_status`, `stages_status_id`, DATE_FORMAT(pm.created_at, '%Y-%m-%d') start, user.username, DATE_FORMAT(pm.created_at, '%Y-%m-%d %T') created_at, 0 replies, 0 post, '' recent FROM project_stages pm LEFT JOIN project_stage pst ON pm.stage_id = pst.id LEFT JOIN user ON pm.create_id = user.id where pm.status <> -1 ";

if($pid != 0)
{
    $query = $query . " and pm.project_id = " . $pid . " ";
}
else {
    # code...
    $query = $query . " and 1 = 0 ";
}


if(!empty($_GET['page'])) {
    $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
    if(false === $page) {
        $page = 1;
    }
}

$query = $query . " order by pm.`sequence` ";

if(!empty($_GET['size'])) {
    $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
    if(false === $size) {
        $size = 10;
    }

    $offset = ($page - 1) * $size;

    $query = $query . " LIMIT " . $offset . "," . $size;
}


$stmt = $db->prepare( $query );
$stmt->execute();



while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $id = $row['id'];
    $sequence = $row['sequence'];
    $project_stage_id = $row['project_stage_id'];
    $stage = $row['stage'];
    $stages_status = $row['stages_status'];

    $stages_status_id = $row['stages_status_id'];
    $start = $row['start'];
    $username = $row['username'];
    $created_at = $row['created_at'];
    $replies = $row['replies'];

    $title = $row['title'];

    $post = $row['post'];
    $recent = GetRecentPost($row['id'], $db);

    $order = GetOrderInfo($row['id'], $db);
    


    $inquiry = GetInquiryInfo($row['id'], $db);

    $schedule = GetScheduleInfo($row['id'], $db);
    

    $merged_results[] = array(
        "id" => $id,
        "sequence" => $sequence,
        "project_stage_id" => $project_stage_id,
        "stage" => $stage,
        "title" => $title,
        "stages_status" => $stages_status,
        "stages_status_id" => $stages_status_id,
        "start" => $start,
        "username" => $username,
        "created_at" => $created_at,
        "replies" => $replies,
        "post" => $post,
        "recent" => $recent,

        "order" => $order,
        "inquiry" => $inquiry,
        "schedule" => $schedule
    );
}

echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);


function GetRecentPost($stage_id, $db){
    $query = "SELECT u.username, pm.created_at FROM project_stage_client pm left join user u on u.id = pm.create_id  where stage_id = " . $stage_id . " and pm.status <> -1  
    UNION all
    SELECT  u.username, pc.created_at FROM project_stage_client_task pc left join user u on u.id = pc.create_id  where pc.stage_id = " . $stage_id . "  and pc.status <> -1 
    UNION ALL
    SELECT  u.username, pt.created_at FROM project_stage_client_task_comment pt left join user u on u.id = pt.create_id LEFT JOIN project_stage_client_task pc ON pt.task_id = pc.id where pc.stage_id = " . $stage_id . "  and pc.status <> -1 
    UNION ALL
    SELECT  u.username, pm.created_at FROM project_other_task pm left join user u on u.id = pm.create_id  where stage_id = " . $stage_id . "  and pm.status <> -1 
    UNION ALL
    SELECT  u.username, pm.created_at FROM project_other_task_message pm left join user u on u.id = pm.create_id LEFT JOIN project_other_task pc ON pm.task_id = pc.id where stage_id = " . $stage_id . "  and pm.status <> -1 
    UNION all
    SELECT  u.username, pm.created_at FROM project_other_task_message_reply pm left join user u on u.id = pm.create_id  LEFT JOIN project_other_task_message ps ON pm.message_id = ps.id  LEFT JOIN project_other_task pc ON ps.task_id = pc.id where stage_id = " . $stage_id . "  and pm.status <> -1 
    UNION ALL
    SELECT  u.username, pm.created_at FROM project_other_task_r pm left join user u on u.id = pm.create_id  where stage_id = " . $stage_id . "  and pm.status <> -1 
    UNION ALL
    SELECT  u.username, pm.created_at FROM project_other_task_message_r pm left join user u on u.id = pm.create_id LEFT JOIN project_other_task_r pc ON pm.task_id = pc.id where stage_id = " . $stage_id . "  and pm.status <> -1 
    UNION all
    SELECT  u.username, pm.created_at FROM project_other_task_message_reply_r pm left join user u on u.id = pm.create_id  LEFT JOIN project_other_task_message_r ps ON pm.message_id = ps.id  LEFT JOIN project_other_task_r pc ON ps.task_id = pc.id where stage_id = " . $stage_id . "  and pm.status <> -1 
    ";

    // prepare the query
    $stmt = $db->prepare($query);
    $stmt->execute();

    $merged_results = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $merged_results[] = $row;
    }

    $str = "";

    if(count($merged_results) > 0)
    {
        usort($merged_results, function ($item1, $item2) {
            return $item2['created_at'] <=> $item1['created_at'];
        });
    
        foreach ($merged_results as $arr)
        {
            $str = $arr['created_at'] . " " . $arr['username'];
            break;
        }
    }

    return $str;
}

function GetOrderInfo($task_id, $db)
{
    $sql = "select id, od_name, order_type, serial_name
            from od_main
            where task_id in (select id from project_other_task where stage_id = " . $task_id . ")";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $_result = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $_result[] = array(
            "id" => $row['id'],
            "od_name" => $row['od_name'],
            "order_type" => $row['order_type'],
            "serial_name" => $row['serial_name'],
        );
    }

    return $_result;
}

function GetInquiryInfo($task_id, $db)
{
    $sql = "select id, iq_name, order_type, serial_name
            from iq_main
            where task_id in (select id from project_other_task where stage_id = " . $task_id . ")";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $_result = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $_result[] = array(
            "id" => $row['id'],
            "iq_name" => $row['iq_name'],
            "order_type" => $row['order_type'],
            "serial_name" => $row['serial_name'],
        );
    }

    return $_result;
}


function GetScheduleInfo($task_id, $db)
{
    // get recent 2 months
    $sql = "select id, title, date_format(start_time, '%Y-%m-%d') start_time, date_format(end_time, '%Y-%m-%d') end_time, is_enabled
            from work_calendar_main
            where related_stage_id = " . $task_id . "
            and start_time >= DATE_ADD(CURDATE(), INTERVAL -7 MONTH)
            and is_enabled = true 
            ";

    $stmt = $db->prepare($sql);
    $stmt->execute();

    $_result = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $_result[] = array(
            "id" => $row['id'],
            "title" => $row['title'],
            "start_time" => $row['start_time'],
            "end_time" => $row['end_time'],
            "is_enabled" => $row['is_enabled'],
        );
    }

    return $_result;
}
