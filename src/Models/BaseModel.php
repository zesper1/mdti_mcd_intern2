<?php

class BaseModel {
    protected $db;

    public function __construct() {
        // We use the constants defined in config.php
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        
        try {
            $this->db = new PDO($dsn, DB_USER, DB_PASS);
            
            // Set error mode to Exceptions for easier debugging
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Set default fetch mode to Associative Array
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
            // Disable emulated prepares for better security against SQL injection
            $this->db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            
        } catch (PDOException $e) {
            // In a development environment, we show the error. 
            // In production, you'd log this to a file instead.
            die("Database Connection failed: " . $e->getMessage());
        }
    }
}