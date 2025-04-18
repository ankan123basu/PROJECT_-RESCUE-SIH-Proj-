<?php
/**
 * Database connection simulation
 * This file provides a mock database connection for demonstration purposes
 */

// In a real application, this would contain actual database credentials
$host = 'localhost';
$dbname = 'project_rescue';
$username = 'root';
$password = '';

// Simulate a database connection
try {
    // For demonstration purposes, we'll create a mock connection
    // In a real app, this would be: $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
    class MockPDO {
        public function prepare($query) {
            return new MockPDOStatement();
        }
        
        public function lastInsertId() {
            return rand(1000, 9999);
        }
    }
    
    class MockPDOStatement {
        public function execute($params = []) {
            return true;
        }
        
        public function fetch($mode = null) {
            // Return mock data based on the prepared query (simplified)
            return [
                'id' => rand(1, 100),
                'name' => isset($params[0]) ? "Agency " . $params[0] : "Test Agency",
                'type' => ['ambulance', 'fire', 'medical', 'rescue'][rand(0, 3)],
                'phone' => '123-456-7890',
                'status' => ['available', 'busy', 'assigned'][rand(0, 2)],
                'location' => 'City Center'
            ];
        }
    }
    
    $conn = new MockPDO();
    
} catch (PDOException $e) {
    // Error handling
    die("Connection failed: " . $e->getMessage());
}
?>
