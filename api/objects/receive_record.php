<?php
// 'user' object
class ReceiveRecord{
 
    // database connection and table name
    private $conn;
    private $table_name = "receive_record";
 
    // object properties
    public $id;
    public $date_receive;
    public $customer;
    public $email_customer;
    public $email;
    public $description;
    public $quantity;
    public $supplier;
    public $mail_note;
    public $picname;
    public $kilo;
    public $cuft;
    public $taiwan_pay;
    public $courier_pay;
    public $courier_money;
    public $remark;
    public $batch_num;
    public $status;
    public $crt_time;
    public $crt_user;
    public $mdf_time;
    public $mdf_user;
    public $del_time;
    public $del_user;

    // constructor
    public function __construct($db){
        $this->conn = $db;
    }

    function getLongestCommonPrefixes($strings) {
        $groups = [];
    
    
        foreach ($strings as $string) {
            $foundGroup = false;
    
            foreach ($groups as &$group) {
    
                $commonPrefix = $this->findLongestCommonPrefixForTwoStrings($group, $string);
                if (!empty($commonPrefix) && $commonPrefix === $group) {
                    $foundGroup = true;
                    break;
                }
                if(!empty($commonPrefix) && $commonPrefix === $string) {
                    $foundGroup = true;
                    $group = $string;
                    break;
                }
            }
    
            if (!$foundGroup) {
                $groups[] = $string;
            }
        }
    
        return $groups;
    }
    
    
    function findLongestCommonPrefixForTwoStrings($str1, $str2) {
        $minLength = min(mb_strlen($str1, 'UTF-8'), mb_strlen($str2, 'UTF-8'));
        $prefix = '';
    
        for ($i = 0; $i < $minLength; $i++) {
            $char1 = mb_substr($str1, $i, 1, 'UTF-8');
            $char2 = mb_substr($str2, $i, 1, 'UTF-8');
            
            if ($char1 === $char2) {
                $prefix .= $char1;
            } else {
                break;
            }
        }
    
        return $prefix;
    }



    function UpdateReceiveRecordById($id){
      $query = "UPDATE " . $this->table_name . "
                SET
                    date_receive = :date_receive,
                    customer = :customer,
                    email_customer = :email_customer,
                    description = :description,
                    quantity = :quantity,
                    supplier = :supplier,
                    email = :email,
                    mail_note = :mail_note,
                    kilo = :kilo,
                    cuft = :cuft,
                    taiwan_pay = :taiwan_pay,
                    courier_money = :courier_money,
                    remark = :remark,
                    mdf_time = now(),
                    mdf_user = :mdf_user
                    where id = :id";

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // bind the values
        $stmt->bindParam(':date_receive', $this->date_receive);
        $stmt->bindParam(':customer', $this->customer);
        $stmt->bindParam(':email_customer', $this->email_customer);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':supplier', $this->supplier);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':mail_note', $this->mail_note);
        $stmt->bindParam(':kilo', $this->kilo);
        $stmt->bindParam(':cuft', $this->cuft);
        $stmt->bindParam(':taiwan_pay', $this->taiwan_pay);
        $stmt->bindParam(':courier_money', $this->courier_money);
        $stmt->bindParam(':remark', $this->remark);
        $stmt->bindParam(':mdf_user', $this->mdf_user);
        $stmt->bindParam(':id', $id);

        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function GetReceiveRecordByBatchNumber($ids){

        $merged_results = array();

        $query = "SELECT 0 as is_checked, 
                                    rr.id, 
                                    0 group_id,
                                    date_receive, 
                                    customer, 
                                    email_customer, 
                                    email, 
                                    description, 
                                    quantity,
                                    supplier, 
                                    rr.picname, 
                                    kilo, 
                                    cuft, 
                                    '' kilo_price,
                                    '' cuft_price,
                                    taiwan_pay, 
                                    courier_pay,
                                    courier_money, 
                                    rr.remark, 
                                    batch_num, 
                                    mail_note,
                                    ld.date_arrive,
                                    CONCAT(ld.container_number) container,
                                    rr.status, 
                                    rr.crt_time, 
                                    rr.crt_user,
                                    rr.mdf_time, 
                                    rr.mdf_user, 
                                    rr.del_time, 
                                    rr.del_user
                       
                                  FROM " . $this->table_name . " rr
                                  left join loading ld on rr.batch_num = ld.id
                                  where rr.batch_num in (" . $ids . ")
                                  and rr.status = ''  
                                  and ld.status = ''
                                  ORDER BY BINARY rr.customer, rr.date_receive  ";

        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            $merged_results[] = $row;
    
        return $merged_results;
    }

    function TaiwanPayQuery($date_start, $date_end, $container_number){

        $merged_results = array();

        $query = "SELECT r.id, 
                        r.date_receive, 
                        r.customer, 
                        r.email_customer, 
                        r.description, 
                        r.quantity, 
                        r.supplier, 
                        r.remark 
                        FROM receive_record r LEFT JOIN loading l 
                        ON r.batch_num = l.id where taiwan_pay=1 
                        and r.status = '' 
                        and r.date_receive <> '' ";

        if(!empty($date_start)) {
            $query = $query . " and r.date_receive >= '$date_start' ";
        }

        if(!empty($date_end)) {
            $query = $query . " and r.date_receive <= '$date_end' ";
        }

        if(!empty($container_number)) {
            $container_number = rtrim($container_number, ',');
            $container = explode(",", $container_number);
            $container_str = "'".implode("','",array_map("trim",array_filter($container)))."'";

            $query = $query . " and l.container_number in($container_str) ";
        }

        $query = $query . " order by r.date_receive ";

        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            $merged_results[] = $row;

        
        
        // no date_receive
        $query = "SELECT r.id, 
            r.date_receive, 
            r.customer, 
            r.email_customer, 
            r.description, 
            r.quantity, 
            r.supplier, 
            r.remark 
            FROM receive_record r LEFT JOIN loading l 
            ON r.batch_num = l.id where taiwan_pay=1 
            and r.status = '' 
            and r.date_receive = '' ";

        if(!empty($date_start)) {
        $query = $query . " and r.date_receive >= '$date_start' ";
        }

        if(!empty($date_end)) {
        $query = $query . " and r.date_receive <= '$date_end' ";
        }

        if(!empty($container_number)) {
        $container_number = rtrim($container_number, ',');
        $container = explode(",", $container_number);
        $container_str = "'".implode("','",array_map("trim",array_filter($container)))."'";

        $query = $query . " and l.container_number in($container_str) ";
        }

        $query = $query . " order by r.id ";

        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            $merged_results[] = $row;

        return $merged_results;
    }

    
    function TaiwanPayQueryDetail($date_start, $date_end, $container_number){

        $merged_results = array();

        $query = "SELECT 1 is_edited,
                        r.id, 
                        r.date_receive, 
                        r.customer, 
                        r.email_customer, 
                        r.description, 
                        r.quantity, 
                        r.supplier, 
                        r.remark,
                        r.kilo,
                        r.cuft,
                        coalesce(tp.ar_php, '') ar_php,
                        coalesce(tp.ar, '') ar,
                        coalesce(tp.amount, '') amount,
                        coalesce(tp.payment_date, '') payment_date,
                        coalesce(tp.note, '') note,
                        coalesce(tp.rate, '') rate,
                        coalesce(tp.status, '') status,
                        l.date_arrive
                        FROM receive_record r LEFT JOIN loading l 
                        ON r.batch_num = l.id
                        left join taiwan_pay_record tp on tp.record_id = r.id
                        where taiwan_pay=1 
                        and r.status = '' 
                        and r.date_receive <> '' ";

        if(!empty($date_start)) {
            $query = $query . " and r.date_receive >= '$date_start' ";
        }

        if(!empty($date_end)) {
            $query = $query . " and r.date_receive <= '$date_end' ";
        }

        if(!empty($container_number)) {
            $container_number = rtrim($container_number, ',');
            $container = explode(",", $container_number);
            $container_str = "'".implode("','",array_map("trim",array_filter($container)))."'";

            $query = $query . " and l.container_number in($container_str) ";
        }

        $query = $query . " order by r.date_receive ";

        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            $merged_results[] = $row;

        
        
        // no date_receive
        $query = "SELECT 1 is_edited,
             r.id, 
            r.date_receive, 
            r.customer, 
            r.email_customer, 
            r.description, 
            r.quantity, 
            r.supplier, 
            r.remark,
            r.kilo, 
            r.cuft,
            coalesce(tp.ar_php, '') ar_php,
            coalesce(tp.ar, '') ar,
            coalesce(tp.amount, '') amount,
            coalesce(tp.payment_date, '') payment_date,
            coalesce(tp.note, '') note
            FROM receive_record r LEFT JOIN loading l 
            ON r.batch_num = l.id 
            left join taiwan_pay_record tp on tp.record_id = r.id
            where taiwan_pay=1 
            and r.status = '' 
            and r.date_receive = '' ";

        if(!empty($date_start)) {
        $query = $query . " and r.date_receive >= '$date_start' ";
        }

        if(!empty($date_end)) {
        $query = $query . " and r.date_receive <= '$date_end' ";
        }

        if(!empty($container_number)) {
        $container_number = rtrim($container_number, ',');
        $container = explode(",", $container_number);
        $container_str = "'".implode("','",array_map("trim",array_filter($container)))."'";

        $query = $query . " and l.container_number in($container_str) ";
        }

        $query = $query . " order by r.id ";

        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            $merged_results[] = $row;

        return $merged_results;
    }

    function CourierPayQuery($date_start, $date_end, $container_number){

        $merged_results = array();

        $query = "SELECT r.id, 
                        r.date_receive, 
                        r.customer, 
                        r.email_customer, 
                        r.description, 
                        r.quantity, 
                        r.supplier, 
                        r.remark,
                        r.courier_money 
                        FROM receive_record r LEFT JOIN loading l 
                        ON r.batch_num = l.id where r.courier_money <> 0 
                        and r.status = '' 
                        and r.date_receive <> '' ";

        if(!empty($date_start)) {
            $query = $query . " and r.date_receive >= '$date_start' ";
        }

        if(!empty($date_end)) {
            $query = $query . " and r.date_receive <= '$date_end' ";
        }

        if(!empty($container_number)) {
            $container_number = rtrim($container_number, ',');
            $container = explode(",", $container_number);
            $container_str = "'".implode("','",array_map("trim",array_filter($container)))."'";

            $query = $query . " and l.container_number in($container_str) ";
        }

        $query = $query . " order by r.date_receive ";

        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            $merged_results[] = $row;

        // no date_receive
        $query = "SELECT r.id, 
                        r.date_receive, 
                        r.customer, 
                        r.email_customer, 
                        r.description, 
                        r.quantity, 
                        r.supplier, 
                        r.remark,
                        r.courier_money 
                        FROM receive_record r LEFT JOIN loading l 
                        ON r.batch_num = l.id where r.courier_money <> 0 
                        and r.status = '' 
                        and r.date_receive = '' ";

        if(!empty($date_start)) {
            $query = $query . " and r.date_receive >= '$date_start' ";
        }

        if(!empty($date_end)) {
            $query = $query . " and r.date_receive <= '$date_end' ";
        }

        if(!empty($container_number)) {
            $container_number = rtrim($container_number, ',');
            $container = explode(",", $container_number);
            $container_str = "'".implode("','",array_map("trim",array_filter($container)))."'";

            $query = $query . " and l.container_number in($container_str) ";
        }

        $query = $query . " order by r.id ";

        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            $merged_results[] = $row;

        return $merged_results;

    }

    function Query_Receive_Query($date_start, $date_end, $customer, $supplier){

        $key=array();
        $merged_results = array();

        $sql = "SELECT r.customer 
                FROM  receive_record r 
                LEFT JOIN loading l
                ON r.batch_num = l.id
                LEFT JOIN measure m
                on l.measure_num = m.id
                where r.status = '' 
                and r.date_receive <> '' ";

        if(!empty($date_start)) {
            $sql = $sql . " and r.date_receive >= '$date_start' ";
        }

        if(!empty($date_end)) {
            $sql = $sql . " and r.date_receive <= '$date_end' ";
        }

        /*
        // supplier
        $sup_array = array();
        $sup_str = "";
        if(!empty($supplier)) {
            $sup_array = json_decode($supplier, true);
            foreach ($sup_array as &$value) {
                $sup_str = $sup_str . "'" . $value['name'] . "',";
            }
        }
        if($sup_str != '')
            $sup_str = mb_substr($sup_str, 0, -1);

        // customer
        $cus_str = "";
        if(!empty($customer)) {
            $sup_array = json_decode($customer, true);
            foreach ($sup_array as &$value) {
                $cus_str = $cus_str . "'" . $value['name'] . "',";
            }
        }
        if($cus_str != '')
            $cus_str = mb_substr($cus_str, 0, -1);

        if(!empty($sup_str)) {
            $sql = $sql . " and r.supplier in($sup_str) ";
        }

        if(!empty($cus_str)) {
            $sql = $sql . " and r.customer in($cus_str) ";
        }
        */

        $cus_str = "";
        $sup_str = "";

        if(!empty($customer)) {
            $customer = rtrim($customer, ',');
            $cust = explode(",", $customer);
            $cus_str = "'".implode("','",array_map("trim",array_filter($cust)))."'";

            $sql = $sql . " and r.customer in($cus_str) ";
        }

        if(!empty($supplier)) {
            $supplier = rtrim($supplier, ',');
            $sup = explode(",", $supplier);
            $sup_str = "'".implode("','",array_map("trim",array_filter($sup)))."'";

            $sql = $sql . " and r.supplier in($sup_str) ";
        }

        $sql = $sql . "  GROUP BY r.date_receive, r.customer ";

        $sql = $sql . " order by  r.date_receive,  r.customer  ";


        $data = $this->conn->query($sql)->fetchAll();

        /* fetch data */
        foreach  ($data as $row){
            if (isset($row)){

                if (in_array(strtolower($row['customer']),$key))
                {
                    continue;
                }
                else
                {
                    array_push($key, strtolower($row['customer']));
                }

                   $query = "SELECT r.id, 
                    r.date_receive, 
                    r.customer, 
                    r.email_customer, 
                    r.description, 
                    r.quantity, 
                    r.supplier, 
                    r.remark, 
                    l.container_number,
                    l.date_sent,
                    l.date_arrive,
                    m.date_encode,
                    l.eta_date,
                    COALESCE(ld.eta_date, '') eta_date_his
                    FROM receive_record r LEFT JOIN loading l 
                    ON r.batch_num = l.id 
                    LEFT JOIN measure m on l.measure_num = m.id
                    LEFT JOIN loading_date_history ld ON l.id = ld.loading_id 
                    where r.status = ''
                    and r.date_receive <> '' 
                    and r.customer = :customer ";

                    if(!empty($date_start)) {
                        $query = $query . " and r.date_receive >= '$date_start' ";
                    }

                    if(!empty($date_end)) {
                        $query = $query . " and r.date_receive <= '$date_end' ";
                    }

                    if(!empty($sup_str)) {
                        $query = $query . " and r.supplier in($sup_str) ";
                    }

                    if(!empty($cus_str)) {
                        $query = $query . " and r.customer in($cus_str) ";
                    }

                    $query = $query . " order by r.customer, r.date_receive ";

                    $stmt = $this->conn->prepare( $query );
                    $stmt->bindParam(':customer', $row['customer']);
                    $stmt->execute();

                    while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                        $merged_results[] = $row;
                }
            }

        $query = "SELECT r.id, 
                        r.date_receive, 
                        r.customer, 
                        r.email_customer, 
                        r.description, 
                        r.quantity, 
                        r.supplier, 
                        r.remark, 
                        l.container_number,
                        l.date_sent,
                        l.date_arrive,
                        m.date_encode,
                        l.eta_date,
                        COALESCE(ld.eta_date, '') eta_date_his 
                        FROM receive_record r LEFT JOIN loading l 
                        ON r.batch_num = l.id
                        LEFT JOIN measure m on l.measure_num = m.id
                        LEFT JOIN loading_date_history ld ON l.id = ld.loading_id 
                        where r.status = '' 
                        and r.date_receive = '' ";

        if(!empty($date_start)) {
            $query = $query . " and r.date_receive >= '$date_start' ";
        }

        if(!empty($date_end)) {
            $query = $query . " and r.date_receive <= '$date_end' ";
        }

        if(!empty($sup_str)) {
            $query = $query . " and r.supplier in($sup_str) ";
        }

        if(!empty($cus_str)) {
            $query = $query . " and r.customer in($cus_str) ";
        }

        $query = $query . " order by r.id ";

        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            $merged_results[] = $row;

        return $merged_results;
    }


    function Query_Receive_Query_Simple($date_start, $date_end, $customer, $supplier, $description, $remark, $sort){

        $merged_results = array();

        $cus_str = "";
        $sup_str = "";

        $c_prefix = "%";
        // $customer string contains '||', then it is a prefix search
        if($customer != "" && strpos($customer, '||') !== false) {
            $c_prefix = "";
        }

        $p_prefix = "%";
        if($supplier != "" && strpos($supplier, '||') !== false) {
            $p_prefix = "";
        }

        if(!empty($customer)) {
            $customer = rtrim($customer, '||');
      
            $cust = explode("||", $customer);

            // trim each element in the array
            $cust = array_map('trim', $cust);

            $cust_arry = $this->getLongestCommonPrefixes($cust);

            foreach ($cust_arry as &$value) {
                $value = addslashes(trim($value));
                $cus_str .= " r.customer like '" . $c_prefix . $value . "%' ESCAPE '|' or ";
            }

            $cus_str = rtrim($cus_str, 'or ');
  
        }

        if(!empty($supplier)) {
            $supplier = rtrim($supplier, '||');
    
            $sup = explode("||", $supplier);

            // trim each element in the array
            $sup = array_map('trim', $sup);

            $sup_arry = $this->getLongestCommonPrefixes($sup);

            foreach ($sup_arry as &$value) {
                $value = addslashes(trim($value));
                $sup_str .= " r.supplier like '" . $p_prefix . $value . "%'  ESCAPE '|' or ";

            }

            $sup_str = rtrim($sup_str, 'or ');
       

        }


        $query = "SELECT r.id, 
                        r.date_receive, 
                        r.customer, 
                        r.email_customer, 
                        r.description, 
                        r.quantity, 
                        r.supplier, 
                        r.remark, 
                        l.container_number,
                        l.date_sent,
                        l.date_arrive,
                        m.date_encode,
                        l.eta_date,
                        r.real_pick_time,
                        r.real_payment_time,
                        COALESCE(ld.eta_date, '') eta_date_his,
                        COALESCE(ld.date_arrive, '') date_arrive_his,
                        (select GROUP_CONCAT(encode, '') from measure_detail md left join measure_record_detail mrd on md.id = mrd.detail_id where mrd.record_id = r.id) as dr,
                        '' pic, r.photo, r.picname
                        FROM receive_record r LEFT JOIN loading l 
                        ON r.batch_num = l.id
                        LEFT JOIN measure_ph m on l.measure_num = m.id
                        LEFT JOIN loading_date_history ld ON l.id = ld.loading_id 
                        where r.status = '' 
                        and r.date_receive <> '' ";

        if(!empty($date_start)) {
            $query = $query . " and r.date_receive >= '$date_start' ";
        }

        if(!empty($date_end)) {
            $query = $query . " and r.date_receive <= '$date_end' ";
        }

        if(!empty($description)) {
            $query = $query . " and r.description like '%$description%' ";
        }

        if(!empty($remark)) {
            $query = $query . " and r.remark like '%$remark%' ";
        }

        if(!empty($sup_str)) {
            $query = $query . " and ($sup_str) ";
        }

        if(!empty($cus_str)) {
            $query = $query . " and ($cus_str) ";
        }

        if($sort == 'd') 
            $query = $query . " order by r.date_receive desc ";
        else
            $query = $query . " order by r.date_receive ";

        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            $merged_results[] = $row;

        // no date_receive
        $query = "SELECT r.id, 
                        r.date_receive, 
                        r.customer, 
                        r.email_customer, 
                        r.description, 
                        r.quantity, 
                        r.supplier, 
                        r.remark, 
                        l.container_number,
                        l.date_sent,
                        l.date_arrive,
                        m.date_encode,
                        l.eta_date,
                        r.real_pick_time,
                        r.real_payment_time,
                        COALESCE(ld.eta_date, '') eta_date_his,
                        COALESCE(ld.date_arrive, '') date_arrive_his,
                        (select GROUP_CONCAT(encode, '') from measure_detail md left join measure_record_detail mrd on md.id = mrd.detail_id where mrd.record_id = r.id) as dr,
                        '' pic, r.photo, r.picname
                        FROM receive_record r LEFT JOIN loading l 
                        ON r.batch_num = l.id
                        LEFT JOIN measure_ph m on l.measure_num = m.id
                        LEFT JOIN loading_date_history ld ON l.id = ld.loading_id 
                        where r.status = '' 
                        and r.date_receive = '' ";

        if(!empty($date_start)) {
            $query = $query . " and r.date_receive >= '$date_start' ";
        }

        if(!empty($date_end)) {
            $query = $query . " and r.date_receive <= '$date_end' ";
        }

        if(!empty($description)) {
            $query = $query . " and r.description like '%$description%' ";
        }

        if(!empty($remark)) {
            $query = $query . " and r.remark like '%$remark%' ";
        }

        if(!empty($sup_str)) {
            $query = $query . " and ($sup_str) ";
        }

        if(!empty($cus_str)) {
            $query = $query . " and ($cus_str) ";
        }

        $query = $query . " order by r.id ";

        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            $merged_results[] = $row;
        
        // $filteredData = array();
        
        // if (!empty($cust)) {
        //     foreach ($merged_results as $data) {
        //         $remove = false;
                
        //         // loop $cust and check if $customer starts with any of them
        //         foreach ($cust as $cs) {
        //             if (strpos($data['customer'], $cs) === 0) {
        //                 $remove = true;
        //                 break;
        //             }
        //         }
                
        //         if ($remove) {
        //             // remove customer
        //         } else {
        //             $filteredData[] = $data;
        //         }
        //     }
        // } else {
        //     $filteredData = $merged_results;
        // }
        
        // $filteredSup = array();
        
        // if (!empty($sup)) {
        //     foreach ($filteredData as $data) {
        //         $remove = false;
                
        //         // loop $sup and check if $supplier starts with any of them
        //         foreach ($sup as $sp) {
        //             if (strpos($data['supplier'], $sp) === 0) {
        //                 $remove = true;
        //                 break;
        //             }
        //         }
                
        //         if ($remove) {
        //             // remove supplier
        //         } else {
        //             $filteredSup[] = $data;
        //         }
        //     }
        // } else {
        //     $filteredSup = $filteredData;
        // }


        // iterate through the array of objects
        for($i =0; $i < count($merged_results); $i++) {
            $pic = $this->GetPic($merged_results[$i]['picname'], $merged_results[$i]['photo'], $merged_results[$i]['id'], $this->conn);
            
            $merged_results[$i]['pic'] = $pic;
        }

        return $merged_results;
    }

    function GetUniqeCustomers(){
      $merged_results = array();
      $query = "select distinct customer name from " . $this->table_name . " where status = '' UNION
                SELECT distinct customer name from contactor where status = '' ";

        // prepare the query
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            $merged_results[] = $row;

        return $merged_results;
    }

    function GetUniqeSuppliers(){
      $merged_results = array();
      $query = "select distinct supplier name from " . $this->table_name . " where status = '' UNION
                SELECT distinct supplier name from contactor where status = '' ";

        // prepare the query
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            $merged_results[] = $row;

        return $merged_results;
    }

    function GetUniqeCustomersByKeyowrd($keyword){
      $merged_results = array();

      if($keyword != "")
        $query = "select distinct customer name from " . $this->table_name . " where status = '' and customer like '%$keyword%' UNION
                SELECT distinct customer name from contactor where status = '' and customer like '%$keyword%' ";
      else
          $query = "select distinct customer name from " . $this->table_name . " where status = '' UNION
                SELECT distinct customer name from contactor where status = '' ";

        // prepare the query
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            $merged_results[] = $row;

        usort($merged_results, function ($item1, $item2) {
            return $item1['name'] <=> $item2['name'];
        });
        
        return $merged_results;
    }

    function GetUniqeSuppliersByKeyowrd($keyword){
      $merged_results = array();

        if($keyword != "")
            $query = "select distinct supplier name from " . $this->table_name . " where status = '' and supplier like '%$keyword%' UNION
                SELECT distinct supplier name from contactor where status = '' and supplier like '%$keyword%' ";
        else
            $query = "select distinct supplier name from " . $this->table_name . " where status = ''  UNION
                SELECT distinct supplier name from contactor where status = ''  ";

        // prepare the query
        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            $merged_results[] = $row;

        usort($merged_results, function ($item1, $item2) {
            return $item1['name'] <=> $item2['name'];
        });

        return $merged_results;
    }

    
function GetPic($picname, $photo, $id){
    $merged_results = array();

    if($picname != "")
    {
        array_push($merged_results, array(
            "pid" => $id,
            "batch_id" => $id,
            "is_checked" => true,
            "customer" => "",
            "date_receive" => "",
            "type" => "FILE",
            "quantity" => "",
            "remark" => "",
            "supplier" => "",
            "gcp_name" => $picname,
        ));
        
    }

    if($photo == 'RECEIVE')
    {
        $sql = "SELECT id, gcp_name FROM gcp_storage_file WHERE batch_id = $id AND batch_type = 'RECEIVE' AND STATUS <> -1";
        $stmt = $this->conn->prepare($sql);

        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $filename = $row['gcp_name'];
            $pid = $row['id'];
            array_push($merged_results, array(
                "pid" => $pid,
                "batch_id" => $id,
                "is_checked" => true,
                "customer" => "",
                "date_receive" => "",
                "type" => "RECEIVE",
                "quantity" => "",
                "remark" => "",
                "supplier" => "",
                "gcp_name" => $filename,
            ));
        }
    }

        return $merged_results;
    }

}