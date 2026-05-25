<?php

class DatabaseService {

    private $db_host;
    private $db_name;
    private $db_user;
    private $db_password;
    private $db_port;
    private $connection;

    public function __construct() {
        $this->db_host = getenv('DB_HOST');
        $this->db_name = getenv('DB_NAME');
        $this->db_user = getenv('DB_USER');
        $this->db_password = getenv('DB_PASSWORD');
        $this->db_port = getenv('DB_PORT');
    }

    public function getConnection() {

        $this->connection = null;

        try {
            $this->connection = new PDO(
                "mysql:host=" . $this->db_host .
                ";port=" . $this->db_port .
                ";dbname=" . $this->db_name,
                $this->db_user,
                $this->db_password
            );

            $this->connection->setAttribute(
                PDO::ATTR_ERRMODE,
                PDO::ERRMODE_EXCEPTION
            );

        } catch(PDOException $exception) {

            echo "Connection failed: " . $exception->getMessage();
        }

        return $this->connection;
    }
}

?>