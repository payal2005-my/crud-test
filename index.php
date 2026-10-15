<?php
session_start();
require_once __DIR__ . '/db.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$users = $pdo->query(
    'SELECT id, name, email, phone, created_at
     FROM users ORDER BY id DESC'
)->fetchAll();

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cantech User Manager</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1000px; margin: 40px auto; padding: 0 16px; color: #222; }
        h1 { color: #174ea6; }
        form, .panel { background: #f6f8fa; padding: 18px; border-radius: 8px; margin-bottom: 24px; }
        input { padding: 10px; margin: 5px 5px 5px 0; max-width: 100%; box-sizing: border-box; }
        button, .button { padding: 9px 12px; border: 0; border-radius: 5px; cursor: pointer; text-decoration: none; background: #174ea6; color: white; }
        .delete { background: #b42318; }
        table { border-collapse: collapse; width: 100%; }
        th, td { text-align: left; border-bottom: 1px solid #ddd; padding: 10px; }
        .actions { display: flex; gap: 8px; align-items: center; flex-wrap: wrap; }
        .actions form { padding: 0; margin: 0; background: none; }
    </style>
</head>
<body>
    <h1>Cantech User Manager</h1>
    <p>PHP + MySQL CRUD application</p>

    <h2>Add a user</h2>
    <form method="post" action="create.php">
        <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
        <input name="name" placeholder="Full name" maxlength="100" required>
        <input name="email" type="email" placeholder="Email address" maxlength="150" required>
        <input name="phone" placeholder="Phone (optional)" maxlength="20">
        <button type="submit">Add User</button>
    </form>

    <h2>Users (<?= count($users) ?>)</h2>

    <?php if (!$users): ?>
        <p>No users yet. Add your first user above.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= (int) $user['id'] ?></td>
                    <td><?= e($user['name']) ?></td>
                    <td><?= e($user['email']) ?></td>
                    <td><?= e($user['phone']) ?></td>
                    <td>
                        <div class="actions">
                            <a class="button" href="edit.php?id=<?= (int) $user['id'] ?>">Edit</a>
                            <form method="post" action="delete.php"
                                  onsubmit="return confirm('Delete this user?');">
                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION['csrf_token']) ?>">
                                <input type="hidden" name="id" value="<?= (int) $user['id'] ?>">
                                <button class="delete" type="submit">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>
