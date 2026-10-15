<?php
session_start();
require_once __DIR__ . '/db.php';

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (
        !isset($_SESSION['csrf_token'], $_POST['csrf_token']) ||
        !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        http_response_code(403);
        exit('Invalid request. Please return to the home page and try again.');
    }

    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (
        !$id || $id < 1 ||
        $name === '' || strlen($name) > 100 ||
        strlen($email) > 150 ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($phone) > 20
    ) {
        http_response_code(422);
        exit('Please check the form fields and try again.');
    }

    try {
        $stmt = $pdo->prepare(
            'UPDATE users SET name = :name, email = :email, phone = :phone
             WHERE id = :id'
        );

        $stmt->execute([
            'name' => $name,
            'email' => $email,
            'phone' => $phone !== '' ? $phone : null,
            'id' => $id,
        ]);

        header('Location: index.php');
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            http_response_code(409);
            exit('That email address is already registered to another user.');
        }

        error_log('Unable to update user: ' . $e->getMessage());
        http_response_code(500);
        exit('Unable to update the user.');
    }
}

$id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit('Invalid user ID.');
}

$stmt = $pdo->prepare('SELECT id, name, email, phone FROM users WHERE id = ?');
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(404);
    exit('User not found.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit User - Cantech</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; padding: 0 16px; }
        input { display: block; width: 100%; box-sizing: border-box; padding: 10px; margin: 12px 0; }
        button, a { padding: 10px 14px; border: 0; border-radius: 5px; background: #174ea6; color: white; text-decoration: none; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Edit User</h1>
    <form method="post" action="edit.php">
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
        <label>Name
            <input name="name" maxlength="100" required value="<?= e($user['name']) ?>">
        </label>
        <label>Email
            <input type="email" name="email" maxlength="150" required value="<?= e($user['email']) ?>">
        </label>
        <label>Phone
            <input name="phone" maxlength="20" value="<?= e($user['phone']) ?>">
        </label>
        <button type="submit">Save Changes</button>
        <a href="index.php">Cancel</a>
    </form>
</body>
</html>
