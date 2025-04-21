<?php
require_once 'dbConnection.php';
//Adding
try {
    if(!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['password'])) {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $password = password_hash($password, PASSWORD_DEFAULT);
        $sql = $conn -> prepare("INSERT INTO admins (admin_name, email, password) VALUES (:name, :email, :password)");
        $sql -> execute([':name' => $name, ':email' => $email, ':password' => $password]);
        if ($sql->rowCount() > 0) {
            echo 'Админ добавлен!';
        } else {
            echo 'Что-то пошло не так!';
        }
    }
}
catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}
    function inputs($inputName) {
    $value = isset($_POST[$inputName]) ? htmlspecialchars($_POST[$inputName]) : '';
    echo '<input type="text" name="' . $inputName . '" value="' . $value . '" />';
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
<form action="adminAdd.php" method="post">
    <h3>Добавить администратора</h3>
    <?php
    echo '<p>Имя</p>';
    inputs('name');
    echo '<p>Email</p>';
    inputs('email');
    echo '<p>Пароль</p>';
    inputs('password');
    ?>
    <input type="submit" value="Добавить" />
</form>
<a href="adminFrontend.php">Вернуться к админке</a>
</body>
</html>

