<?php
// 'user' object
class LoginHistory{
 
    // database connection and table name
    private $conn;
    private $table_name = "login_history";
 
    // object properties
    public $uid;
    public $status;
    public $ip;
 
    // constructor
    public function __construct($db){
        $this->conn = $db;
    }
 
    // create new user record
    function create(){
    
        // insert query
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    uid = :uid,
                    status = :status,
                    ip = :ip,
                    login_time = now()";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->uid=htmlspecialchars(strip_tags($this->uid));
        $this->status=htmlspecialchars(strip_tags($this->status));
        $this->ip=htmlspecialchars(strip_tags($this->ip));

        // bind the values
        $stmt->bindParam(':uid', $this->uid);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':ip', $this->ip);

        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    

}