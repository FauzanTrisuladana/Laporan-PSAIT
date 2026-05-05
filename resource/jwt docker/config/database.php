<?php
// used to get mysql database connection
class DatabaseService{

    private $db_host = "10.33.35.96";
    private $db_port = 3306;
    private $db_name = "sait_db";
    private $db_user = "fauzan";
    private $db_password = "fauzan123";
    private $connection;

    public function getConnection(){

        $this->connection = null;

        try{
            $this->connection = new PDO("mysql:host=" . $this->db_host . ";port=" . $this->db_port . ";dbname=" . $this->db_name, $this->db_user, $this->db_password);
        }catch(PDOException $exception){
            echo "Connection failed: " . $exception->getMessage();
        }

        return $this->connection;
    }
}
?>