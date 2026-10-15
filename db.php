<?php
// db.php

require_once __DIR__ . '/config.php';

// Replace these placeholders with your NEW PaaS MySQL details.
$host = '192.168.15.92';
$port = '3306';
$dbname = 'cantech_crud';
$username = 'cantech_app';
$password = 'CRUD@test2005';

try {
    $pdo = new PDO(
        "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    error_log('Database connection failed: ' . $e->getMessage());
    http_response_code(503);
    exit('Database is temporarily unavailable. Please try again later.');
}

