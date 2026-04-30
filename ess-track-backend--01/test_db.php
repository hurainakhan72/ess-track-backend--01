<?php

require_once __DIR__ . '/bootstrap/app.php';
require_once __DIR__ . '/config/database.php';

try {
    $db = new \Database();
    $conn = $db->getConnection();
    echo "Database connected successfully!\n";
    echo "Host: " . $conn->host_info . "\n";
    echo "Charset: " . $conn->character_set_name() . "\n";

    // Check if inquiries table exists
    $result = $conn->query("SHOW TABLES LIKE 'inquiries'");
    if ($result->num_rows > 0) {
        echo "Inquiries table exists!\n";

        // Check table structure
        $result = $conn->query("DESCRIBE inquiries");
        echo "Table structure:\n";
        while ($row = $result->fetch_assoc()) {
            echo "- " . $row['Field'] . " (" . $row['Type'] . ")\n";
        }
    } else {
        echo "Inquiries table does not exist!\n";
    }

    $conn->close();
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
}