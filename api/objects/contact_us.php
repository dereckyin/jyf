<?php
// 'user' object
class ContactUs{
 
    // database connection and table name
    private $conn;
    private $table_name = "contact_us";
 
    // object properties
    public $id;
    public $gender;
    public $customer;
    public $emailinfo;
    public $telinfo;
    public $crt_time;
    public $status;

    // constructor
    public function __construct($db){
        $this->conn = $db;
    }
 
    // create new user record
    function create(){
    
        // insert query
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    gender = :gender,
                    customer = :customer,
                    emailinfo = :emailinfo,
                    telinfo = :telinfo,
                    crt_time = now()";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->gender=htmlspecialchars(strip_tags($this->gender));
        $this->customer=htmlspecialchars(strip_tags($this->customer));
        $this->emailinfo=htmlspecialchars(strip_tags($this->emailinfo));
        $this->telinfo=htmlspecialchars(strip_tags($this->telinfo));
    
        // bind the values
        $stmt->bindParam(':gender', $this->gender);
        $stmt->bindParam(':customer', $this->customer);
        $stmt->bindParam(':emailinfo', $this->emailinfo);
        $stmt->bindParam(':telinfo', $this->telinfo);
    

        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    function get(){
        $merged_results = [];
        // insert query
        $query = "select * from " . $this->table_name . "
                where 
                    status <> 'D'";
    
        // prepare the query
        $stmt = $this->conn->prepare( $query );
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC))
            $merged_results[] = $row;
    
        return $merged_results;
    }

    function delete($ids){
    
        // insert query
         $query = "UPDATE " . $this->table_name . "
                SET
                    status = 'D'
                    where id in (" . $ids . ")";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // bind the values
        // $stmt->bindParam(':ids', $ids);
    
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }
}