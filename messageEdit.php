<?php require_once 'dbConnection.php';

if(!isset($_GET['id'])) {
    echo 'Некорректный ID';
    exit;
}
$id = $_GET['id'];
if (!empty($_POST['name'] || !empty($_POST['message']))) {
    $name = htmlspecialchars($_POST['name']);
    $message = htmlspecialchars($_POST['message']);
    $fieldsToUpdate = [];
    $params = [];
    if (!empty($name)) {
        $fieldsToUpdate[] = "name = :name";
        $params[':name'] = $name;
    }

    if (!empty($message)) {
        $fieldsToUpdate[] = "message = :message";
        $params[':message'] = $message;
    }

    if (empty($fieldsToUpdate)) {
        die("Нет данных для обновления");
    }
    $sql = "UPDATE messages SET " . implode(', ', $fieldsToUpdate) . ", status = 'updated', moderated_at = NOW(), moderated_by = :admin_id WHERE id = :id";
    $params[":id"] = $id;
    session_start();
    $admin_id = $_SESSION['session_id'];
    $params[":admin_id"] = $admin_id;
    $sql = $conn->prepare($sql);
    $sql->execute($params);
    if ($sql->rowCount() > 0) {
        echo 'Сообщение изменено!';
    } else {
        echo 'Что-то пошло не так!';
    }
}
$stmt = $conn->prepare("SELECT name, message FROM messages WHERE id = :id");
$stmt->execute([':id' => $id]);
$messageData = $stmt->fetch();
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
<form action="messageEdit.php?id=<?= urlencode($id) ?>" method="post">
    <h3>Редактировать сообщение</h3>
    <p>
        <label>Имя:</label><br>
        <input type="text" name="name" value="<?= htmlspecialchars($messageData['name']) ?>">
    </p>
    <p>
        <label>Сообщение:</label><br>
        <textarea name="message"><?= htmlspecialchars($messageData['message']) ?></textarea>
    </p>
    <p>
        <input type="submit" value="Сохранить изменения">
    </p>
</form>
<a href="adminMessages.php">Вернуться к сообщениям</a>
<br>
<a href="adminFrontend.php">Вернуться к админке</a>
</body>
</html>