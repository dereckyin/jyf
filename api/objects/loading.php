<?php
// 'user' object
class Loading{
 
    // database connection and table name
    private $conn;
    private $table_name = "loading";
 
    // object properties
    public $id;
    public $shipping_mark;
    public $estimate_weight;
    public $actual_weight;
    public $container_number;
    public $seal;
    public $so;
    public $ship_company;
    public $ship_boat;
    public $neck_cabinet;
    public $shipper;
    public $date_sent;
    public $etd_date;
    public $ob_date;
    public $eta_date;
    public $broker;
    public $remark;
 
    // constructor
    public function __construct($db){
        $this->conn = $db;
    }

    function GetMeasureLoading(){

        $merged_results = array();

        $query = "SELECT 0 as is_checked, 
                                  id, 
                                  shipping_mark, 
                                  estimate_weight, 
                                  actual_weight, 
                                  container_number, 
                                  seal, 
                                  so, 
                                  ship_company, 
                                  ship_boat, 
                                  neck_cabinet, 
                                  shipper,
                                  date_sent, 
                                  etd_date, 
                                  ob_date, 
                                  eta_date, 
                                  broker, 
                                  remark 
                                  FROM " . $this->table_name . "
                                  where measure_num = 0 
                                  and status = ''  
                                  ORDER BY crt_time desc  ";

        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            $merged_results[] = $row;
    
        return $merged_results;
    }

    function SetLoadingMeasure($measure_id, $ids){
      $query = "UPDATE " . $this->table_name . "
                SET
                    measure_num = :measure_id
                    
                WHERE id in ($ids)";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);

        // bind the values from the form
        $stmt->bindParam(':measure_id', $measure_id);
    
        // execute the query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    function RemoveLoadingMeasure($ids) {
      $query = "UPDATE " . $this->table_name . "
                SET
                    measure_num = 0
                WHERE measure_num in ($ids) 
                AND status = '' ";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);

        // execute the query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    
    function GetUniqeCabinets($key){

        $merged_results = array();

        $query = "SELECT distinct container_number FROM loading WHERE container_number <> '' and status = '' ";

        if($key != "")
            $query = $query . " and container_number like '%$key%' ";

        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            $merged_results[] = $row;
    
        return $merged_results;
    }

 
}