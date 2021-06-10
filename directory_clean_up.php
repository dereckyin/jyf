<?php
error_reporting(E_ALL);

require_once "api/db.php";

require 'vendor/autoload.php';

include_once 'api/config/core.php';
include_once 'api/libs/php-jwt-master/src/BeforeValidException.php';
include_once 'api/libs/php-jwt-master/src/ExpiredException.php';
include_once 'api/libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'api/libs/php-jwt-master/src/JWT.php';


$sql = "DELETE FROM contactor
WHERE id NOT IN (SELECT * 
                    FROM (SELECT MIN(n.id)
                            FROM contactor n where status = ''
                        GROUP BY n.shipping_mark, n.customer, n.c_phone, n.c_email, n.supplier, n.s_phone, n.s_email, n.company_title, n.vat_number, n.address) x)";


$result = mysqli_query($conn,$sql);

    mysqli_close($conn);

    exit;
?>
