<?php
/**
 * Database Connection Class
 */
class Database {
    private $host = "localhost";
    private $username = "root";
    private $password = "";
    private $database = "gate_pass_system";
    private $conn;
    
    /**
     * Constructor - establishes database connection
     */
    public function __construct() {
        $this->conn = new mysqli($this->host, $this->username, $this->password, $this->database);
        
        if ($this->conn->connect_error) {
            die("Connection failed: " . $this->conn->connect_error);
        }
    }
    
    /**
     * Get the database connection
     * @return mysqli The database connection
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Close the database connection
     */
    public function closeConnection() {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
?>