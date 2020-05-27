<?php
// 'user' object
class MeasureHistory{
 
    // database connection and table name
    private $conn;
    private $table_name = "measure_history";
 
    // object properties
    public $id;
    public $measure_id;
    public $customer;
    public $kilo;
    public $cuft;
    public $price_kilo;
    public $price_cuft;

    // constructor
    public function __construct($db){
        $this->conn = $db;
    }

    function Add() {
      $last_id = 0;
      $query = "INSERT INTO " . $this->table_name . "
                SET
                    measure_id = :measure_id,
                    customer = :customer,
                    kilo = :kilo,
                    cuft = :cuft,
                    price_kilo = :price_kilo,
                    price_cuft = :price_cuft";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);

        // bind the values
        $stmt->bindParam(':measure_id', $this->measure_id);
        $stmt->bindParam(':customer', $this->customer);
        $stmt->bindParam(':kilo', $this->kilo);
        $stmt->bindParam(':cuft', $this->cuft);
        $stmt->bindParam(':price_kilo', $this->price_kilo);
        $stmt->bindParam(':price_cuft', $this->price_cuft);
    
        // execute the query, also check if query was successful
        if($stmt->execute()){
            $last_id = $this->conn->insert_id;
        }
    
        return $last_id;
    }

    function DeleteHistory($ids) {
      $last_id = 0;
      $query = "DELETE FROM " . $this->table_name . "
                WHERE
                    measure_id IN ($ids)";
        // prepare the query
        $stmt = $this->conn->prepare($query);

            if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    function GetById($id){

        $merged_results = array();

        $query = "SELECT measure_id, customer, kilo, cuft, price_kilo, price_cuft
                 FROM " . $this->table_name . "        
                 where measure_id = :id
                 ORDER BY customer ";

        $stmt = $this->conn->prepare( $query );
        $stmt->bindParam(':id', $id);
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            $merged_results[] = $row;
    
        return $merged_results;
    }
 
}