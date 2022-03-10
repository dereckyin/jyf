<?php

error_reporting(E_ERROR | E_PARSE);

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
          if(!$decoded->data->is_admin)
          {
            http_response_code(401);
     
            echo json_encode(array("message" => "Access denied."));
            die();
          }
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

      include_once 'config/database.php';
      include_once 'objects/user.php';

      $method = $_SERVER['REQUEST_METHOD'];

      //$user = $decoded->data->username;

      switch ($method) {
          case 'GET':
            $id = stripslashes((isset($_GET['id']) ?  $_GET['id'] : ""));
            $page = stripslashes((isset($_GET['page']) ?  $_GET['page'] : ""));
            $size = stripslashes((isset($_GET['size']) ?  $_GET['size'] : ""));
            $keyword = stripslashes((isset($_GET['keyword']) ?  $_GET['keyword'] : ""));

            $sql = "SELECT 0 as is_checked, id, username, email, status, phili, status_1, status_2, taiwan_read, phili_read, report1, sea_expense, sea_expense_v2, is_admin, (SELECT login_time FROM login_history WHERE login_history.uid = user.id ORDER BY login_time desc LIMIT 1) login_time  FROM user where status <> -1 ".($id ? " and id=$id" : '');

            if(!empty($_GET['page'])) {
                $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);
                if(false === $page) {
                    $page = 1;
                }
            }

            $sql = $sql . " ORDER BY username ";

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
            // get database connection
            $database = new Database();
            $db = $database->getConnection();
             
            // instantiate product object
            $user = new User($db);

            $username = stripslashes(isset($_POST['username']) ?  $_POST['username'] : "");
            $email = stripslashes(isset($_POST['email']) ?  $_POST['email'] : "");
            $password = stripslashes(isset($_POST['password']) ?  $_POST['password'] : "" );
            $status = stripslashes(isset($_POST['status']) ?  $_POST['status'] : 0 );
            $phili = stripslashes(isset($_POST['phili']) ?  $_POST['phili'] : 0 );
            $status_1 = stripslashes(isset($_POST['status_1']) ?  $_POST['status_1'] : 0 );
            $status_2 = stripslashes(isset($_POST['status_2']) ?  $_POST['status_2'] : 0 );
            $taiwan_read = stripslashes(isset($_POST['taiwan_read']) ?  $_POST['taiwan_read'] : 0 );
            $phili_read = stripslashes(isset($_POST['phili_read']) ?  $_POST['phili_read'] : 0 );
            $report1 = stripslashes(isset($_POST['report1']) ?  $_POST['report1'] : 0 );
            $sea_expense = stripslashes(isset($_POST['sea_expense']) ?  $_POST['sea_expense'] : 0 );
            $sea_expense_v2 = stripslashes(isset($_POST['sea_expense_v2']) ?  $_POST['sea_expense_v2'] : 0 );
            $is_admin = stripslashes(isset($_POST['is_admin']) ?  $_POST['is_admin'] : "");

            if($is_admin == "null")
                $is_admin = "0";

            $crud = stripslashes($_POST["crud"]);
            $id = stripslashes($_POST["id"]);

            switch ($crud) 
            {
              case 'insert':
            /* Bind parameters. Types: s = string, i = integer, d = double,  b = blob */
                $user->username = $username;
                $user->email = $email;
                $user->password = $password;
                $user->status = $status;
                $user->phili = $phili;
                $user->status_1 = $status_1;
                $user->status_2 = $status_2;
                $user->taiwan_read = $taiwan_read;
                $user->phili_read = $phili_read;
                $user->report1 = $report1;
                $user->sea_expense = $sea_expense;
                $user->sea_expense_v2 = $sea_expense_v2;
                $user->is_admin = $is_admin;

                $user->create();

                break;

            case "update":
                    $user->status = $status;
                    $user->phili = $phili;
                    $user->status_1 = $status_1;
                    $user->status_2 = $status_2;
                    $user->taiwan_read = $taiwan_read;
                    $user->phili_read = $phili_read;
                    $user->report1 = $report1;
                    $user->sea_expense = $sea_expense;
                    $user->sea_expense_v2 = $sea_expense_v2;
                    $user->is_admin = $is_admin;
                    $user->id = $id;

                    $user->updateStatus();

                break;

            case 'del':
                $ids = explode(",", $id);
                foreach($ids as $item) {
                    $user->id = trim($item);
                    $user->delete();
                }

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
