<?php
session_start();
require_once __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (
    !isset($_SESSION['csrf_token'], $_POST['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    http_response_code(403);
    exit('Invalid request. Please return to the home page and try again.');
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if (
    $name === '' ||
    strlen($name) > 100 ||
    strlen($email) > 150 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL) ||
    strlen($phone) > 20
) {
    http_response_code(422);
    exit('Please enter a valid name, email, and phone number.');
}

try {
    $stmt = $pdo->prepare(
        'INSERT INTO users (name, email, phone)
         VALUES (:name, :email, :phone)'
    );

    $stmt->execute([
        'name' => $name,
        'email' => $email,
        'phone' => $phone !== '' ? $phone : null,
    ]);

    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        http_response_code(409);
        exit('That email address is already registered.');
    }

    error_log('Unable to create user: ' . $e->getMessage());
    http_response_code(500);
    exit('Unable to create the user.');
}
