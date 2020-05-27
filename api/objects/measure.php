<?php
// 'user' object
class Measure{
 
    // database connection and table name
    private $conn;
    private $table_name = "measure";
 
    // object properties
    public $id;
    public $date_encode;
    public $date_arrive;
    public $currency_rate;
    public $remark;
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

    function Add() {
      $last_id = 0;
      $query = "INSERT INTO " . $this->table_name . "
                SET
                    date_encode = :date_encode,
                    date_arrive = :date_arrive,
                    currency_rate = :currency_rate,
                    remark = :remark,
                    crt_time = now(),
                    crt_user = :crt_user";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->date_encode=htmlspecialchars(strip_tags($this->date_encode));
        $this->date_arrive=htmlspecialchars(strip_tags($this->date_arrive));
        $this->currency_rate=htmlspecialchars(strip_tags($this->currency_rate));
        $this->remark=htmlspecialchars(strip_tags($this->remark));
    
        // bind the values
        $stmt->bindParam(':date_encode', $this->date_encode);
        $stmt->bindParam(':date_arrive', $this->date_arrive);
        $stmt->bindParam(':currency_rate', $this->currency_rate);
        $stmt->bindParam(':remark', $this->remark);
        $stmt->bindParam(':crt_user', $this->crt_user);
    
        // execute the query, also check if query was successful
        if($stmt->execute()){
            $last_id = $this->conn->lastInsertId();
        }
    
        return $last_id;
    }

    function Update() {

        $query = "UPDATE " . $this->table_name . "
                SET
                    date_encode = :date_encode,
                    date_arrive = :date_arrive,
                    currency_rate = :currency_rate,
                    remark = :remark,
                    mdf_time = now(),
                    mdf_user = :mdf_user";

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // bind the values
        $stmt->bindParam(':date_encode', $this->date_encode);
        $stmt->bindParam(':date_arrive', $this->date_arrive);
        $stmt->bindParam(':currency_rate', $this->currency_rate);
        $stmt->bindParam(':remark', $this->remark);
        $stmt->bindParam(':mdf_user', $this->mdf_user);

        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function Delete($ids) {
      $last_id = 0;
      $query = "UPDATE " . $this->table_name . "
                SET
                    status = 'D',
                    del_user = :del_user,
                    del_time = now()
                    where id in ($ids)";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
    
        // bind the values
        $stmt->bindParam(':del_user', $this->del_user);

        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    function GetMeasureRecord(){

        $merged_results = array();

        $query = "SELECT 0 as is_checked, 
                 id,                 
                 date_encode,        
                 date_arrive,        
                 currency_rate,      
                 (SELECT COUNT(*) FROM loading WHERE measure_num = measure.id AND STATUS = '') qty,
                 (SELECT GROUP_CONCAT(container_number) FROM loading WHERE measure_num = measure.id AND STATUS = '') container,
                 remark,             
                 status              
                 FROM " . $this->table_name . "        
                 where status = ''   
                 ORDER BY date_encode ";

        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            $merged_results[] = $row;
    
        return $merged_results;
    }

    function GetMeasureRecordById($id){

        $merged_results = array();

        $query = "SELECT 0 as is_checked, 
                 id,                 
                 date_encode,        
                 date_arrive,        
                 currency_rate,      
                 (SELECT COUNT(*) FROM loading WHERE measure_num = measure.id AND STATUS = '') qty,
                 (SELECT GROUP_CONCAT(container_number) FROM loading WHERE measure_num = measure.id AND STATUS = '') container,
                 (SELECT GROUP_CONCAT(id) FROM loading WHERE measure_num = measure.id AND STATUS = '') batch_num,
                 remark,             
                 status              
                 FROM " . $this->table_name . "        
                 where status = ''   
                 and id = :id
                 ORDER BY date_encode ";

        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            $merged_results[] = $row;
    
        return $merged_results;
    }
 
}