<?php
session_start();
require_once 'dbConnection.php';
if (!empty($_SESSION['session_id'])): ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.css">
    <style>
        table { border-collapse: collapse; width: 70%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
    </style>
    <title>Админка</title>
</head>
<body>
<div class="header">
    <h1>Админка(<?= htmlspecialchars($_SESSION['session_name'] ?? '')?>, <?= htmlspecialchars($_SESSION['session_email'] ?? '')?>)</h1>
    <p><a href="adminLogout.php">Выйти</a></p>
    <a href="adminUsers.php"><h3>Пользователи</h3></a>
    <div class="messages">
        <a href="adminMessages.php"><h3>Сообщения пользователей</h3></a>
</div>
</body>
</html>
<?php exit;
endif;

 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    if (empty($email) || empty($password)) {
        echo 'Введите email и пароль!';
    } else {
        $stmt = $conn->prepare("SELECT admin_id, admin_name, password FROM admins WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$admin) {
            echo 'Неверный email!';
        } else {
            if (password_verify($password, $admin['password'])) {
                $_SESSION['session_id'] = $admin['admin_id'];
                $_SESSION['session_name'] = $admin['admin_name'];
                $_SESSION['session_email'] = $email;
                header('Location: adminFrontend.php');
                exit;
            } else {
                echo 'Неверный пароль!';
            }
        }

    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Админка</title>
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.css">
</head>
<body>
<form method="post" action="">
    <p>
        <label>Email:<br>
            <input type="email" name="email" required>
        </label>
    </p>
    <p>
        <label>Пароль:<br>
            <input type="password" name="password" required>
        </label>
    </p>
    <p><button type="submit">Войти</button></p>
</form>
</body>
</html>

