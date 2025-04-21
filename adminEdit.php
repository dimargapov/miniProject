<?php require_once 'dbConnection.php';

if(!isset($_GET['id'])) {
    echo 'Некорректный ID';
    exit;
}
$id = $_GET['id'];
try {
    if (!empty($_POST['name']) || !empty($_POST['email'])) {
        $name = htmlspecialchars($_POST['name']);
        $email = htmlspecialchars($_POST['age']);
        $fieldsToUpdate = [];
        $params = [];
        if (!empty($name)) {
            $fieldsToUpdate[] = "admin_name = :name";
            $params[':name'] = $name;
        }

        if (!empty($email)) {
            $fieldsToUpdate[] = "email = :email";
            $params[':email'] = $email;
        }

        if (empty($fieldsToUpdate)) {
            die("Нет данных для обновления");
        }
        $sql = "UPDATE admins SET " . implode(', ', $fieldsToUpdate) . " WHERE admin_id = :id";
        $params[":id"] = $id;
        $sql = $conn->prepare($sql);
        $sql->execute($params);
        if ($sql->rowCount() > 0) {
            echo 'Администратор изменен!';
        } else {
            echo 'Что-то пошло не так!';
        }
    }
} catch (Exception $e) {
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
<form action="adminEdit.php?id=<?= urlencode($id) ?>" method="post">
    <h3>Редактировать администратора</h3>
    <?php
    echo '<p>Имя</p>';
    inputs('name');
    echo '<p>Email</p>';
    inputs('age');
    ?>
    <input type="submit" value="Изменить" />
</form>
<a href="adminFrontend.php">Вернуться к админке</a>
</body>
</html>
