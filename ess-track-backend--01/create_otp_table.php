<?php

require_once __DIR__ . '/bootstrap/app.php';
require_once __DIR__ . '/config/database.php';

try {
    $db = new \Database();
    $conn = $db->getConnection();

    // Create OTP table
    $sql = "CREATE TABLE IF NOT EXISTS `otp_verifications` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `phone_number` varchar(255) NOT NULL COMMENT 'Encrypted phone number',
        `otp_code` varchar(10) NOT NULL,
        `purpose` varchar(50) NOT NULL DEFAULT 'inquiry',
        `is_verified` tinyint(1) NOT NULL DEFAULT 0,
        `expires_at` timestamp NOT NULL,
        `attempts` int(11) NOT NULL DEFAULT 0,
        `last_attempt_at` timestamp NULL DEFAULT NULL,
        `locked_until` timestamp NULL DEFAULT NULL,
        `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `phone_number` (`phone_number`(50)),
        KEY `otp_code` (`otp_code`),
        KEY `expires_at` (`expires_at`),
        KEY `locked_until` (`locked_until`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    if ($conn->query($sql) === TRUE) {
        echo "OTP table created successfully!\n";
    } else {
        echo "Error creating OTP table: " . $conn->error . "\n";
    }

    $conn->close();
} catch (Exception $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}