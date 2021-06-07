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
    public $email;
    public $description;
    public $quantity;
    public $supplier;
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

    function UpdateReceiveRecordById($id){
      $query = "UPDATE " . $this->table_name . "
                SET
                    date_receive = :date_receive,
                    customer = :customer,
                    description = :description,
                    quantity = :quantity,
                    supplier = :supplier,
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
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':quantity', $this->quantity);
        $stmt->bindParam(':supplier', $this->supplier);
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
                                  id, 
                                  date_receive, 
                                  customer, 
                                  email, 
                                  description, 
                                  quantity,
                                  supplier, 
                                  picname, 
                                  kilo, 
                                  cuft, 
                                  taiwan_pay, 
                                  courier_pay,
                                  courier_money, 
                                  remark, 
                                  batch_num, 
                                  status, 
                                  crt_time, 
                                  crt_user,
                                  mdf_time, 
                                  mdf_user, 
                                  del_time, 
                                  del_user
                                  FROM " . $this->table_name . "
                                  where batch_num in (" . $ids . ")
                                  and status = ''  
                                  ORDER BY customer, date_receive  ";

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

    function CourierPayQuery($date_start, $date_end, $container_number){

        $merged_results = array();

        $query = "SELECT r.id, 
                        r.date_receive, 
                        r.customer, 
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


    function Query_Receive_Query_Simple($date_start, $date_end, $customer, $supplier){

        $merged_results = array();

        $cus_str = "";
        $sup_str = "";

        if(!empty($customer)) {
            $customer = rtrim($customer, ',');
            $cust = explode(",", $customer);
            $cus_str = "'".implode("','",array_map("trim",array_filter($cust)))."'";
        }

        if(!empty($supplier)) {
            $supplier = rtrim($supplier, ',');
            $sup = explode(",", $supplier);
            $sup_str = "'".implode("','",array_map("trim",array_filter($sup)))."'";

        }

        $query = "SELECT r.id, 
                        r.date_receive, 
                        r.customer, 
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
                        and r.date_receive <> '' ";

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

        $query = $query . " order by r.date_receive ";

        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            $merged_results[] = $row;

        // no date_receive
        $query = "SELECT r.id, 
                        r.date_receive, 
                        r.customer, 
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

        return $merged_results;
    }
}