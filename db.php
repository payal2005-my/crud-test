<?php
// db.php

require_once __DIR__ . '/config.php';

$host = app_env('DB_HOST');
$port = app_env('DB_PORT', '3306');
$dbname = app_env('DB_NAME', 'cantech_crud');
$username = app_env('DB_USER');
$password = app_env('DB_PASSWORD');

if (!$host || !$username || $password === null) {
    error_log('Database configuration is incomplete.');
    http_response_code(503);
    exit('Database configuration is incomplete.');
}

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
