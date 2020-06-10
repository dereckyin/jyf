<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
   
      }
      // if decode fails, it means jwt is invalid
      catch (Exception $e){
      
          http_response_code(401);
     
        echo json_encode(array("message" => "Access denied."));
        die();
      }
}

      header('Access-Control-Allow-Origin: *');  

      require_once "db.php";

      $method = $_SERVER['REQUEST_METHOD'];

      $user = $decoded->data->username;

      switch ($method) {
          case 'GET':
            $id = mysqli_real_escape_string($conn, (isset($_GET['id']) ?  $_GET['id'] : ""));
            $page = mysqli_real_escape_string($conn, (isset($_GET['page']) ?  $_GET['page'] : ""));
            $size = mysqli_real_escape_string($conn, (isset($_GET['size']) ?  $_GET['size'] : ""));
            $loading = mysqli_real_escape_string($conn, (isset($_GET['loading']) ?  $_GET['loading'] : ""));
            $record = mysqli_real_escape_string($conn, (isset($_GET['record']) ?  $_GET['record'] : ""));
            $query = mysqli_real_escape_string($conn, (isset($_GET['query']) ?  $_GET['query'] : ""));

            if($loading == "1")
            {
              //$sql = "SELECT 0 as is_checked, lo.id, shipping_mark, estimate_weight, actual_weight, container_number, seal, so, ship_company, ship_boat, neck_cabinet, lo.date_sent, lo.etd_date, lo.ob_date, lo.eta_date, ld.date_sent date_send_his, ld.etd_date etd_date_his, ld.ob_date ob_date_his, ld.eta_date eta_date_his, broker, remark, (SELECT date_arrive FROM measure WHERE measure.id = measure_num) date_arrive, crt_time, crt_user  FROM loading lo LEFT JOIN loading_date_history ld ON lo.id = ld.loading_id where  status = '' ".($id ? " and lo.id=$id" : ''); 
              //$sql = $sql . " ORDER BY ship_company, date_sent ";

              $sql = "(SELECT 0 as is_checked, lo.id, shipping_mark, estimate_weight, actual_weight, container_number, seal, so, ship_company, ship_boat, neck_cabinet, lo.date_sent, lo.etd_date, lo.ob_date, lo.eta_date, lo.date_arrive, ld.date_sent date_send_his, ld.etd_date etd_date_his, ld.ob_date ob_date_his, ld.eta_date eta_date_his, broker, remark, (SELECT date_arrive FROM measure WHERE measure.id = measure_num) date_arrive_old, crt_time, crt_user, '9999/99/99' ords  FROM loading lo LEFT JOIN loading_date_history ld ON lo.id = ld.loading_id where  lo.STATUS = '' ".($id ? " and lo.id=$id" : ''). " AND lo.date_sent = '' ORDER BY lo.date_sent ) ";
              $sql = $sql . "UNION ";
              $sql = $sql . "(SELECT 0 as is_checked, lo.id, shipping_mark, estimate_weight, actual_weight, container_number, seal, so, ship_company, ship_boat, neck_cabinet, lo.date_sent, lo.etd_date, lo.ob_date, lo.eta_date, lo.date_arrive, ld.date_sent date_send_his, ld.etd_date etd_date_his, ld.ob_date ob_date_his, ld.eta_date eta_date_his, broker, remark, (SELECT date_arrive FROM measure WHERE measure.id = measure_num) date_arrive_old, crt_time, crt_user, lo.date_sent ords FROM loading lo LEFT JOIN loading_date_history ld ON lo.id = ld.loading_id where  lo.STATUS = '' ".($id ? " and lo.id=$id" : ''). " AND lo.date_sent <> '' ORDER BY lo.date_sent DESC ) ";
              $sql = $sql . "ORDER BY ords DESC, ship_company ";
            }

            if($record != "" && $query != "1")
            {
              //$sql = "SELECT 1 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, crt_time, crt_user  FROM receive_record where batch_num = $record and status = '' ".($id ? " and id=$id" : ''); 
              
              //$sql = $sql . " union ";

              //$sql = $sql . " ( SELECT 0 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, crt_time, crt_user  FROM receive_record where batch_num = 0 and status = '' ".($id ? " and id=$id" : '') . " ORDER BY customer, date_receive ) "; 

              //$sql = $sql . " ORDER BY customer, date_receive ";

              $subquery = "";

              $merged_results = array();

              $key=array();

            $sql = "SELECT customer FROM  receive_record where batch_num = $record and date_receive <> '' and status = ''  GROUP BY date_receive, customer  ORDER BY date_receive;";

            // $sql = "CALL createReceiveList(); ";
            // run SQL statement
            $result = mysqli_query($conn,$sql);

            /* fetch data */
            while ($row = mysqli_fetch_array($result)){
                if (isset($row)){

                    if (in_array($row['customer'],$key))
                    {
                        continue;
                    }
                    else
                    {
                        array_push($key, $row['customer']);
                    }

                        $subquery = "SELECT 1 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, crt_time, crt_user, 1 as is_edited FROM receive_record where batch_num = $record and date_receive <> '' and status = ''  and customer = '" . $row['customer']. "' ORDER BY date_receive  ";

                            $result1 = mysqli_query($conn,$subquery);

                    while($row = mysqli_fetch_assoc($result1))
                        $merged_results[] = $row;



                }
            }

            $subquery = "SELECT 1 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, crt_time, crt_user, 1 as is_edited  FROM receive_record where batch_num = $record and date_receive = '' and status = ''  ORDER BY customer";

              $result1 = mysqli_query($conn,$subquery);
              while($row = mysqli_fetch_assoc($result1))
                  $merged_results[] = $row;

              // for batchnum = 0
                $subquery = "";

              $key=array();

            $sql = "SELECT customer FROM  receive_record where batch_num = 0 and date_receive <> '' and status = ''  GROUP BY date_receive, customer  ORDER BY date_receive;";

            // $sql = "CALL createReceiveList(); ";
            // run SQL statement
            $result = mysqli_query($conn,$sql);

            /* fetch data */
            while ($row = mysqli_fetch_array($result)){
                if (isset($row)){

                    if (in_array($row['customer'],$key))
                    {
                        continue;
                    }
                    else
                    {
                        array_push($key, $row['customer']);
                    }

                        $subquery = "SELECT 0 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, crt_time, crt_user, 1 as is_edited FROM receive_record where batch_num = 0 and date_receive <> '' and status = ''  and customer = '" . $row['customer']. "' ORDER BY date_receive  ";

                            $result1 = mysqli_query($conn,$subquery);

                    while($row = mysqli_fetch_assoc($result1))
                        $merged_results[] = $row;



                }
            }

            $subquery = "SELECT 0 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, crt_time, crt_user, 1 as is_edited  FROM receive_record where batch_num = 0 and date_receive = '' and status = ''  ORDER BY customer";

              $result1 = mysqli_query($conn,$subquery);
              while($row = mysqli_fetch_assoc($result1))
                  $merged_results[] = $row;



             echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
             break;

            }

            if($record != "" && $query == "1")
            {
              //$sql = "SELECT 1 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, crt_time, crt_user  FROM receive_record where batch_num = $record and status = '' ".($id ? " and id=$id" : ''); 

              //$sql = $sql . " ORDER BY customer, date_receive ";

              $subquery = "";

              $merged_results = array();

              $key=array();

            $sql = "SELECT customer FROM  receive_record where batch_num = $record and date_receive <> '' and status = ''  GROUP BY date_receive, customer  ORDER BY date_receive;";

            // $sql = "CALL createReceiveList(); ";
            // run SQL statement
            $result = mysqli_query($conn,$sql);

            /* fetch data */
            while ($row = mysqli_fetch_array($result)){
                if (isset($row)){

                    if (in_array($row['customer'],$key))
                    {
                        continue;
                    }
                    else
                    {
                        array_push($key, $row['customer']);
                    }

                        $subquery = "SELECT 1 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, crt_time, crt_user FROM receive_record where batch_num = $record and date_receive <> '' and status = ''  and customer = '" . $row['customer']. "' ORDER BY date_receive  ";

                            $result1 = mysqli_query($conn,$subquery);

                    while($row = mysqli_fetch_assoc($result1))
                        $merged_results[] = $row;



                }
            }

            $subquery = "SELECT 1 as is_checked, id, date_receive, customer, email, description, quantity, supplier, kilo, cuft, taiwan_pay, courier_pay, courier_money, remark, picname, crt_time, crt_user  FROM receive_record where batch_num = $record and date_receive = '' and status = ''  ORDER BY customer";

              $result1 = mysqli_query($conn,$subquery);
              while($row = mysqli_fetch_assoc($result1))
                  $merged_results[] = $row;


             echo json_encode($merged_results, JSON_UNESCAPED_SLASHES);
             break;
            }

            if(!empty($_GET['page'])) {
                $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
                if(false === $page) {
                    $page = 1;
                }
            }

            if(!empty($_GET['size'])) {
                $size = filter_input(INPUT_GET, 'size', FILTER_VALIDATE_INT);
                if(false === $size) {
                    $size = 10;
                }

                $offset = ($page - 1) * $size;

                $sql = $sql . " LIMIT " . $offset . "," . $size;
            }

            // run SQL statement
            $result = mysqli_query($conn,$sql);

            // die if SQL statement failed
            if (!$result) {
                  http_response_code(404);
                  die(mysqli_error($conn));
            }

            if (!$id) echo '[';
            for ($i=0 ; $i<mysqli_num_rows($result) ; $i++) {
                  echo ($i>0?',':'').json_encode(mysqli_fetch_object($result), JSON_UNESCAPED_SLASHES);
            }
            if (!$id) echo ']';
            elseif ($method == 'POST')
                  echo json_encode($result);
            else 
                  echo mysqli_affected_rows($conn);
            break;

          case 'POST':
            $shipping_mark = mysqli_real_escape_string($conn, $_POST["shipping_mark"]);
            $estimate_weight = mysqli_real_escape_string($conn, $_POST["estimate_weight"]);
            $actual_weight = mysqli_real_escape_string($conn, $_POST["actual_weight"]);
            $container_number = mysqli_real_escape_string($conn, $_POST["container_number"]);
            $seal = mysqli_real_escape_string($conn, $_POST["seal"]);
            $so = mysqli_real_escape_string($conn, $_POST["so"]);
            $ship_company = mysqli_real_escape_string($conn, $_POST["ship_company"]);
            $ship_boat = mysqli_real_escape_string($conn, $_POST["ship_boat"]);
            $neck_cabinet = mysqli_real_escape_string($conn, $_POST["neck_cabinet"]);
            $date_sent = mysqli_real_escape_string($conn, $_POST["date_sent"]);
            $etd_date = mysqli_real_escape_string($conn, $_POST["etd_date"]);
            $ob_date = mysqli_real_escape_string($conn, $_POST["ob_date"]);
            $eta_date = mysqli_real_escape_string($conn, $_POST["eta_date"]);
            $date_arrive = mysqli_real_escape_string($conn, $_POST["date_arrive"]);
            $broker = mysqli_real_escape_string($conn, $_POST["broker"]);
            $remark = stripslashes($_POST["remark"]);
            $record = stripslashes($_POST["record"]);
            $crud = mysqli_real_escape_string($conn, $_POST["crud"]);
            $id = mysqli_real_escape_string($conn, $_POST["id"]);

            switch ($crud) 
            {
              case 'insert':
          	/* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
                $sql = "insert into loading (shipping_mark, 
                									  estimate_weight, 
                									  actual_weight, 
                									  container_number, 
                									  seal,
                									  so,
                									  ship_company,
                									  ship_boat,
                									  neck_cabinet,
                									  date_sent,
                									  etd_date,
                									  ob_date,
                									  eta_date,
                                    date_arrive,
                									  broker,
                									  remark,
                                                      crt_user) 
                							values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql); 
                $stmt->bind_param("sddssssssssssssss",
                					$shipping_mark,
                					$estimate_weight,
                					$actual_weight,
                					$container_number,
                					$seal,
                					$so,
                					$ship_company,
                					$ship_boat,
                					$neck_cabinet,
                					$date_sent,
                					$etd_date,
                					$ob_date,
                					$eta_date,
                          $date_arrive,
                					$broker,
                					$remark,
                                    $user);
                $stmt->execute();
                $stmt->close();

                $last_id = mysqli_insert_id($conn);

                $sql = "update receive_record set batch_num = $last_id,
                                                      mdf_time = now(),
                                                      mdf_user = '$user'
                                            where id in($record)";
                $query = $conn->query($sql);

                $sql = "insert into loading_date_history (loading_id, 
                                    date_sent,
                                    etd_date,
                                    ob_date,
                                    eta_date) 
                              values (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql); 
                $stmt->bind_param("dssss",
                          $last_id,
                          $date_sent,
                          $etd_date,
                          $ob_date,
                          $eta_date);

                $stmt->execute();
                $stmt->close();

                if($query){
                      $out['message'] = "Load Successfully";
                }
                else{
                      $out['error'] = true;
                      $out['message'] = "Could not Load Goods";
                }

                break;

            case "update":

                    /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
                    $sql = "update loading set shipping_mark = ?, 
                                                          estimate_weight = ?, 
                                                          actual_weight = ?, 
                                                          container_number = ?, 
                                                          seal = ?,
                                                          so = ?,
                                                          ship_company = ?,
                                                          ship_boat = ?,
                                                          neck_cabinet = ?,
                                                          date_sent = ?,
                                                          etd_date = ?,
                                                          ob_date = ?,
                                                          eta_date = ?,
                                                          date_arrive = ?,
                                                          broker = ?,
                									      remark = ?,
                                                          mdf_time = now(),
                                                          mdf_user = ?
                                                where id = ?";
                    $stmt = $conn->prepare($sql); 
                    $stmt->bind_param("sddssssssssssssssd",
                        $shipping_mark,
                        $estimate_weight,
                        $actual_weight,
                        $container_number,
                        $seal,
                        $so,
                        $ship_company,
                        $ship_boat,
                        $neck_cabinet,
                        $date_sent,
                        $etd_date,
                        $ob_date,
                        $eta_date,
                        $date_arrive,
                                        $broker,
                                        $remark,
                                        $user,
                                        $id);
                    $stmt->execute();
                    $affected_rows = mysqli_stmt_num_rows($stmt);
                    $stmt->close();

                $sql = "update receive_record set batch_num = 0,
                                                      mdf_time = now(),
                                                      mdf_user = '$user'
                                            where batch_num = $id";
                $query = $conn->query($sql);

                $sql = "update receive_record set batch_num = $id,
                                                      mdf_time = now(),
                                                      mdf_user = '$user'
                                            where id in($record)";
                $query = $conn->query($sql);

                $sql = "SELECT date_sent, etd_date, ob_date, eta_date FROM loading_date_history  where loading_id=$id"; 
                $result = mysqli_query($conn,$sql);

                // die if SQL statement failed
                while ($row = mysqli_fetch_array($result)) {
                    $date_sent_ary = explode(",", $row['date_sent']);
                    $etd_date_ary = explode(",", $row['etd_date']);
                    $ob_date_ary = explode(",", $row['ob_date']);
                    $eta_date_ary = explode(",", $row['eta_date']);
                }

                if (!in_array($date_sent, $date_sent_ary)) {
                    array_push($date_sent_ary,$date_sent);
                }

                if (!in_array($etd_date, $etd_date_ary)) {
                    array_push($etd_date_ary,$etd_date);
                }

                if (!in_array($ob_date, $ob_date_ary)) {
                    array_push($ob_date_ary,$ob_date);
                }

                if (!in_array($eta_date, $eta_date_ary)) {
                    array_push($eta_date_ary,$eta_date);
                }

                $date_sent_str = ltrim(implode(",", $date_sent_ary), ",");
                $etd_date_str = ltrim(implode(",", $etd_date_ary), ",");
                $ob_date_str = ltrim(implode(",", $ob_date_ary), ",");
                $eta_date_str = ltrim(implode(",", $eta_date_ary), ",");


                $sql = "update loading_date_history set date_sent = '$date_sent_str',
                                                      etd_date = '$etd_date_str',
                                                      ob_date = '$ob_date_str',
                                                      eta_date = '$eta_date_str'
                                            where loading_id = $id";
                $query = $conn->query($sql);


                echo $affected_rows;

                break;

              case 'del':
                $sql = "update loading set status = 'D', del_time = now(), del_user = '$user' where id in ($id)";
                
                $query = $conn->query($sql);

                $sql = "update receive_record set batch_num = 0, mdf_time = now(), mdf_user = '$user' where batch_num in ($id)";

                $query = $conn->query($sql);
     
                if($query){
                    $out['message'] = "Member Deleted Successfully";
                }
                else{
                    $out['error'] = true;
                    $out['message'] = "Could not delete Member";
                }
               
                break;

              case 'del_all':
                $sql = "update loading set status = 'D', del_time = now(), del_user = '$user' where id in ($id)";
                
                $query = $conn->query($sql);

                $sql = "update receive_record set status = 'D', del_time = now(), del_user = '$user' where batch_num in ($id)";

                $query = $conn->query($sql);
     
                if($query){
                    $out['message'] = "Member Deleted Successfully";
                }
                else{
                    $out['error'] = true;
                    $out['message'] = "Could not delete Member";
                }
               
                break;
            }

            break;
      }

      // Close connection
      mysqli_close($conn);


?>
