<?php

namespace App\Models;

class Inquiry {
    private $conn;
    private $table_name = "inquiries";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function checkSpam($phone) {
        if (!$this->conn) {
            return $this->countPhoneInStorage($phone) >= 3;
        }

        $query = "SELECT COUNT(*) AS total FROM " . $this->table_name . " WHERE phone_number = ?";
        $stmt = $this->conn->prepare($query);
        if (!$stmt) {
            return $this->countPhoneInStorage($phone) >= 3;
        }

        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return isset($result['total']) && $result['total'] >= 3;
    }

    public function create($data) {
        if ($this->conn) {
            $query = "INSERT INTO " . $this->table_name . " 
                      (first_name, last_name, email, phone_number, vehicle_type, message, interested_package) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";

            $stmt = $this->conn->prepare($query);
            if ($stmt) {
                $pkg = $data['interested_package'] ?? 'Not Sure';
                $stmt->bind_param("sssssss", 
                    $data['first_name'], $data['last_name'], $data['email'], 
                    $data['phone'], $data['vehicleType'], $data['message'], $pkg
                );

                if ($stmt->execute()) {
                    return $this->conn->insert_id;
                }
            }
        }

        $fallbackData = [
            'first_name' => $data['first_name'] ?? '',
            'last_name' => $data['last_name'] ?? '',
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? '',
            'vehicle_type' => $data['vehicleType'] ?? '',
            'message' => $data['message'] ?? '',
            'interested_package' => $data['interested_package'] ?? 'Not Sure',
            'saved_at' => date('Y-m-d H:i:s'),
            'source' => 'file-fallback'
        ];

        if ($this->saveToStorage($fallbackData)) {
            return time();
        }

        return false;
    }

    private function getStoragePath() {
        return __DIR__ . '/../../storage/inquiries.json';
    }

    private function loadStoredInquiries() {
        $path = $this->getStoragePath();
        if (!file_exists($path)) {
            return [];
        }

        $json = file_get_contents($path);
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }

    private function saveToStorage($data) {
        $path = $this->getStoragePath();
        $items = $this->loadStoredInquiries();
        $items[] = $data;

        return file_put_contents($path, json_encode($items, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
    }

    private function countPhoneInStorage($phone) {
        $items = $this->loadStoredInquiries();
        $count = 0;
        foreach ($items as $item) {
            if (isset($item['phone']) && $item['phone'] === $phone) {
                $count++;
            }
        }
        return $count;
    }
}
