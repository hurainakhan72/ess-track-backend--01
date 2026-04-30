<?php

namespace App\Validators;

class InquiryValidator {
    public static function validate($data) {
        $errors = [];

        $first_name = trim($data['first_name'] ?? '');
        $last_name = trim($data['last_name'] ?? '');
        $email = trim($data['email'] ?? '');
        $phone = trim($data['phone'] ?? '');
        $vehicleType = trim($data['vehicleType'] ?? '');
        $interestedPackage = trim($data['interested_package'] ?? '');
        $message = trim($data['message'] ?? '');

        if (strlen($first_name) < 2) {
            $errors['first_name'] = 'First name must be at least 2 characters';
        }
        if (strlen($last_name) < 1) {
            $errors['last_name'] = 'Last name is required';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address';
        }

        $cleanPhone = preg_replace('/[\s\-\(\)]/', '', $phone);
        if (!preg_match('/^(\+92|0)\d{10}$/', $cleanPhone) && !preg_match('/^\d{11}$/', $cleanPhone)) {
            $errors['phone'] = 'Please enter a valid 11-digit phone number (e.g. 03001234567)';
        }
        if (strlen($vehicleType) < 1) {
            $errors['vehicleType'] = 'Please select a vehicle type';
        }
        if (strlen($interestedPackage) < 1) {
            $errors['interested_package'] = 'Please select an interested package';
        }
        if (strlen($message) < 10) {
            $errors['message'] = 'Message must be at least 10 characters';
        }

        return ['errors' => $errors, 'cleanPhone' => $cleanPhone];
    }

    public static function validatePhone($phone) {
        $errors = [];
        $cleanPhone = preg_replace('/[\s\-\(\)]/', '', $phone);

        if (!preg_match('/^(\+92|0)\d{10}$/', $cleanPhone) && !preg_match('/^\d{11}$/', $cleanPhone)) {
            $errors['phone'] = 'Please enter a valid 11-digit phone number (e.g. 03001234567)';
        }

        return ['errors' => $errors, 'cleanPhone' => $cleanPhone];
    }
