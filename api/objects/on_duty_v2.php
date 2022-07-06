<?php
class OnDuty
{
    // database connection and table name
    private $conn;
    private $table_name = "on_duty_v2";

    // object properties
    public $id;
    public $username;
    public $duty_date;
    public $duty_type;
    public $location;
    public $remark;
    public $duty_time;
    public $explain;
    public $lat;
    public $lng;
    public $pic_url;
    public $pic_time;
    public $pic_lat;
    public $pic_lng;
    public $pic_server_time;
    public $pic_server_lat;
    public $pic_server_lng;
    public $created_at;
    public $updated_at;
    public $status;

    // constructor
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // create new user record
    function create()
    {

        $last_id = 0;
        // insert query
        //$query = "INSERT INTO " . $this->table_name . "
        //        (uid, duty_date, duty_type, location, remark, duty_time, `explain`, pic_url, pic_time, pic_lat, pic_lng, pic_server_time, pic_server_lat, pic_server_lng, created_at)
        //        VALUES (" . $this->uid . ", '" . $this->duty_date . "', '" . $this->duty_type . "', '" . $this->location . "', '" . $this->remark . "', '"
        //    . $this->duty_time . "', '" . $this->explain . "', '" . $this->pic_url . "', '" . $this->pic_time . "', " . $this->pic_lat . ", " . $this->pic_lng . ", '" . $this->pic_server_time . "', " . $this->pic_server_lat . ", " . $this->pic_server_lng . ", now())";

        //$query = "INSERT INTO " . $this->table_name . "
        //        (uid, duty_date, duty_type, location, remark, duty_time, `explain`, pos_lat, pos_lng, pic_url, pic_time, pic_lat, pic_lng, pic_server_time, pic_server_lat, pic_server_lng, created_at)
        //        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?, now())";

        $query = "INSERT INTO " . $this->table_name . "
                (username, duty_date, duty_type, location, remark, duty_time, `explain`, pos_lat, pos_lng, pic_url, pic_time, pic_lat, pic_lng, pic_server_time, pic_server_lat, pic_server_lng, created_at) 
                VALUES (:username, :duty_date, :duty_type, :location, :remark, :duty_time, :exp, :pos_lat, :pos_lng, :pic_url, :pic_time, :pic_lat, :pic_lng, :pic_server_time, :pic_server_lat, :pic_server_lng, now())";

        // prepare the query
        $stmt = $this->conn->prepare($query);


/*
            $stmt->bindParam("issssssddssddsdd", $this->uid,
                $this->duty_date,
                $this->duty_type,
                $this->location,
                $this->remark,
                $this->duty_time,
                $this->explain,
                $this->lat,
                $this->lng,
                $this->pic_url,
                $this->pic_time,
                $this->pic_lat,
                $this->pic_lng,
                $this->pic_server_time,
                $this->pic_server_lat,
                $this->pic_server_lng);
*/
            // sanitize
            $this->username = htmlspecialchars(strip_tags($this->username));
            $this->duty_date = htmlspecialchars(strip_tags($this->duty_date));
            $this->duty_type = htmlspecialchars(strip_tags($this->duty_type));

            $this->location = htmlspecialchars(strip_tags($this->location));
            $this->remark = htmlspecialchars(strip_tags($this->remark));
            $this->duty_time = htmlspecialchars(strip_tags($this->duty_time));
            $this->explain = htmlspecialchars(strip_tags($this->explain));

            $this->lat = (float) $this->lat;
            $this->lng = (float) $this->lng;

            $this->pic_url = htmlspecialchars(strip_tags($this->pic_url));
            $this->pic_time = htmlspecialchars(strip_tags($this->pic_time));
            $this->pic_lat = (float) $this->pic_lat;
            $this->pic_lng = (float) $this->pic_lng;
            $this->pic_server_time = htmlspecialchars(strip_tags($this->pic_server_time));
            $this->pic_server_lat = (float) $this->pic_server_lat;
            $this->pic_server_lng = (float) $this->pic_server_lng;


            // bind the values
            $stmt->bindParam(':username', $this->username);
            $stmt->bindParam(':duty_date', $this->duty_date);
            $stmt->bindParam(':duty_type', $this->duty_type);

            $stmt->bindParam(':location', $this->location);
            $stmt->bindParam(':remark', $this->remark);
            $stmt->bindParam(':duty_time', $this->duty_time);

            $stmt->bindParam(':exp', $this->explain);

            $stmt->bindParam(':pos_lat', $this->lat);
            $stmt->bindParam(':pos_lng', $this->lng);


            $stmt->bindParam(':pic_url', $this->pic_url);
            $stmt->bindParam(':pic_time', $this->pic_time);

            $stmt->bindParam(':pic_lat', $this->pic_lat);
            $stmt->bindParam(':pic_lng', $this->pic_lng);
            $stmt->bindParam(':pic_server_time', $this->pic_server_time);

            $stmt->bindParam(':pic_server_lat', $this->pic_server_lat);
            $stmt->bindParam(':pic_server_lng', $this->pic_server_lng);

        try {
            // execute the query, also check if query was successful
            if ($stmt->execute()) {
                $last_id = $this->conn->lastInsertId();
            }
            else
            {
                $arr = $stmt->errorInfo();
                error_log($arr[2]);
            }
        }
        catch (Exception $e)
        {
            error_log($e->getMessage());
        }


        return $last_id;

    }
}