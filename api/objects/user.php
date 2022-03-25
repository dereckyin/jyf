<?php
// 'user' object
class User{
 
    // database connection and table name
    private $conn;
    private $table_name = "user";
 
    // object properties
    public $id;
    public $username;
    public $email;
    public $password;
    public $status;
    public $phili;
    public $status_1;
    public $status_2;
    public $taiwan_read;
    public $phili_read;
    public $report1;
    public $report2;
    public $sea_expense;
    public $sea_expense_v2;
    public $is_admin;
 
    // constructor
    public function __construct($db){
        $this->conn = $db;
    }
 
    // create new user record
    function create(){
    
        // insert query
        $query = "INSERT INTO " . $this->table_name . "
                SET
                    username = :username,
                    email = :email,
                    password = :password,
                    status = :status,
                    phili = :phili,
                    status_1 = :status_1,
                    status_2 = :status_2,
                    taiwan_read = :taiwan_read,
                    phili_read = :phili_read,
                    report1 = :report1,
                    report2 = :report2,
                    sea_expense = :sea_expense,
                    sea_expense_v2 = :sea_expense_v2,
                    is_admin = :is_admin";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->username=htmlspecialchars(strip_tags($this->username));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->password=htmlspecialchars(strip_tags($this->password));
        $this->status = ($this->status ? $this->status : 0);
    
        // bind the values
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':phili', $this->phili);
        $stmt->bindParam(':status_1', $this->status_1);
        $stmt->bindParam(':status_2', $this->status_2);
        $stmt->bindParam(':taiwan_read', $this->taiwan_read);
        $stmt->bindParam(':phili_read', $this->phili_read);
        $stmt->bindParam(':report1', $this->report1);
        $stmt->bindParam(':report2', $this->report2);
        $stmt->bindParam(':sea_expense', $this->sea_expense);
        $stmt->bindParam(':sea_expense_v2', $this->sea_expense_v2);
        $stmt->bindParam(':is_admin', $this->is_admin);
    
        // hash the password before saving to database
        $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt->bindParam(':password', $password_hash);
    
        // execute the query, also check if query was successful
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }

    // check if given email exist in the database
    function userExists(){
    
        // query to check if email exists
        $query = "SELECT id, username, status, phili, status_1, status_2, taiwan_read, phili_read, report1, report2, sea_expense, sea_expense_v2, password
                FROM " . $this->table_name . "
                WHERE username = ?
                LIMIT 0,1";
    
        // prepare the query
        $stmt = $this->conn->prepare( $query );
    
        // sanitize
        $this->username=htmlspecialchars(strip_tags($this->username));
    
        // bind given email value
        $stmt->bindParam(1, $this->username);
    
        // execute the query
        $stmt->execute();
    
        // get number of rows
        $num = $stmt->rowCount();
    
        // if email exists, assign values to object properties for easy access and use for php sessions
        if($num>0){
    
            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // assign values to object properties
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->password = $row['password'];
            $this->status = $row['status'];
            $this->phili = $row['phili'];
            $this->status_1 = $row['status_1'];
            $this->status_2 = $row['status_2'];
            $this->taiwan_read = $row['taiwan_read'];
            $this->phili_read = $row['phili_read'];
            $this->report1 = $row['report1'];
            $this->report2 = $row['report2'];
            $this->sea_expense = $row['sea_expense'];
            $this->sea_expense_v2 = $row['sea_expense_v2'];
    
            // return true because email exists in the database
            return true;
        }
    
        // return false if email does not exist in the database
        return false;
    }

    function userCanLogin(){
        // query to check if email exists
        $query = "SELECT id, username, password, status, phili, status_1, status_2, taiwan_read, phili_read, report1, report2, is_admin, sea_expense, sea_expense_v2
                FROM " . $this->table_name . "
                WHERE username = ? 
                LIMIT 0,1";
    
        // prepare the query
        $stmt = $this->conn->prepare( $query );
    
        // sanitize
        $this->username=htmlspecialchars(strip_tags($this->username));
    
        // bind given email value
        $stmt->bindParam(1, $this->username);
    
        // execute the query
        $stmt->execute();
    
        // get number of rows
        $num = $stmt->rowCount();
    
        // if email exists, assign values to object properties for easy access and use for php sessions
        if($num>0){
    
            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // assign values to object properties
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->password = $row['password'];
            $this->status = $row['status'];
            $this->phili = $row['phili'];
            $this->status_1 = $row['status_1'];
            $this->status_2 = $row['status_2'];
            $this->taiwan_read = $row['taiwan_read'];
            $this->phili_read = $row['phili_read'];
            $this->report1 = $row['report1'];
            $this->report2 = $row['report2'];
            $this->sea_expense = $row['sea_expense'];
            $this->sea_expense_v2 = $row['sea_expense_v2'];
            $this->is_admin = $row['is_admin'];
            // return true because email exists in the database
            return true;
        }
    
        // return false if email does not exist in the database
        return false;
    }
    
    // check if given email exist in the database
    function emailExists(){
    
        // query to check if email exists
        $query = "SELECT id, username, password
                FROM " . $this->table_name . "
                WHERE email = ?
                LIMIT 0,1";
    
        // prepare the query
        $stmt = $this->conn->prepare( $query );
    
        // sanitize
        $this->email=htmlspecialchars(strip_tags($this->email));
    
        // bind given email value
        $stmt->bindParam(1, $this->email);
    
        // execute the query
        $stmt->execute();
    
        // get number of rows
        $num = $stmt->rowCount();
    
        // if email exists, assign values to object properties for easy access and use for php sessions
        if($num>0){
    
            // get record details / values
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // assign values to object properties
            $this->id = $row['id'];
            $this->username = $row['username'];
            $this->password = $row['password'];
    
            // return true because email exists in the database
            return true;
        }
    
        // return false if email does not exist in the database
        return false;
    }

    // update a user record
    public function delete(){

        $query = "delete from " . $this->table_name . "
                
                WHERE id = :id";

        // prepare the query
        $stmt = $this->conn->prepare($query);

        // bind the values from the form
        //$stmt->bindParam(':status', $a = -1);

        // unique ID of record to be edited
        $stmt->bindParam(':id', $this->id);

        // execute the query
        if($stmt->execute()){
            return true;
        }

        return false;
    }

    // update a user record
    public function updateStatus(){

        $query = "UPDATE " . $this->table_name . "
                SET
                    status = :status,
                    phili = :phili,
                    status_1 = :status_1,
                    status_2 = :status_2,
                    taiwan_read = :taiwan_read,
                    phili_read = :phili_read,
                    report1 = :report1,
                    report2 = :report2,
                    sea_expense = :sea_expense,
                    sea_expense_v2 = :sea_expense_v2,
                    is_admin = :is_admin
                WHERE id = :id";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->status=htmlspecialchars(strip_tags($this->status));
        $this->is_admin=htmlspecialchars(strip_tags($this->is_admin));
    
        // bind the values from the form
        $stmt->bindParam(':status', $this->status);
        $stmt->bindParam(':phili', $this->phili);
        $stmt->bindParam(':status_1', $this->status_1);
        $stmt->bindParam(':status_2', $this->status_2);
        $stmt->bindParam(':taiwan_read', $this->taiwan_read);
        $stmt->bindParam(':phili_read', $this->phili_read);
        $stmt->bindParam(':report1', $this->report1);
        $stmt->bindParam(':report2', $this->report2);
        $stmt->bindParam(':sea_expense', $this->sea_expense);
        $stmt->bindParam(':sea_expense_v2', $this->sea_expense_v2);
        $stmt->bindParam(':is_admin', $this->is_admin);

        // unique ID of record to be edited
        $stmt->bindParam(':id', $this->id);
    
        // execute the query
        if($stmt->execute()){
            return true;
        }else
            {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
                error_log($this->is_admin);
            }

    
        return false;
    }

    // update a user record
    public function update(){
    
        // if password needs to be updated
        $password_set=!empty($this->password) ? ", password = :password" : "";
    
        // if no posted password, do not update the password
        $query = "UPDATE " . $this->table_name . "
                SET
                    username = :username,
                    email = :email
                    {$password_set}
                WHERE id = :id";
    
        // prepare the query
        $stmt = $this->conn->prepare($query);
    
        // sanitize
        $this->username=htmlspecialchars(strip_tags($this->username));
        $this->email=htmlspecialchars(strip_tags($this->email));
    
        // bind the values from the form
        $stmt->bindParam(':username', $this->username);
        $stmt->bindParam(':email', $this->email);
    
        // hash the password before saving to database
        if(!empty($this->password)){
            $this->password=htmlspecialchars(strip_tags($this->password));
            $password_hash = password_hash($this->password, PASSWORD_BCRYPT);
            $stmt->bindParam(':password', $password_hash);
        }
    
        // unique ID of record to be edited
        $stmt->bindParam(':id', $this->id);
    
        // execute the query
        if($stmt->execute()){
            return true;
        }
    
        return false;
    }
 
}