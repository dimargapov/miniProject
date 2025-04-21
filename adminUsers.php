<?php require_once 'dbConnection.php' ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Пользователи</title>
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.css">
    <style>
        table { border-collapse: collapse; width: 70%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
</head>
<body>
<h3>Пользователи</h3>
<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Имя</th>
        <th>Email</th>
        <th>Password</th>
        <th>Действие</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $select = $conn->query("SELECT * FROM admins");
    $admins = $select->fetchAll(PDO::FETCH_ASSOC);
    if (count($admins) > 0): ?>
        <?php foreach ($admins as $admin): ?>
            <tr>
                <td><?= htmlspecialchars($admin['admin_id']) ?></td>
                <td><?= htmlspecialchars($admin['admin_name']) ?></td>
                <td><?= htmlspecialchars($admin['email']) ?></td>
                <td><?= htmlspecialchars($admin['password']) ?></td>
                <td><a href='adminDelete.php?id=<?= urlencode($admin['admin_id'])?>'>Удалить администратора</a>&nbsp&nbsp&nbsp&nbsp&nbsp<a href='adminEdit.php?id=<?= urlencode($admin['admin_id'])?>'>Изменить администратора</a></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="4">Нет данных</td></tr>
    <?php endif; ?>
    </tbody>
</table>
<a href="adminAdd.php">Добавить администратора</a>
<br>
<a href="adminFrontend.php">Вернуться к админке</a>
<br>
<a href="adminMessages.php">Сообщения пользователей</a>
</body>
</html>
