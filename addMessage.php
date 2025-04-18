<?php
session_start();
require_once 'dbConnection.php';
if (!empty($_POST['name']) && !empty($_POST['message'])) {
    $name = htmlspecialchars(trim($_POST['name']));
    $message = htmlspecialchars(trim($_POST['message']));
        $stmt = $conn->prepare("INSERT INTO messages (name, message) VALUES (?, ?)");
        $stmt->execute([$name, $message]);
        $_SESSION['success_message'] = 'Запись успешно сохранена!';
        header("Location: /miniProject/index.php ");
        exit;
}
$limit = 5;
$page = !empty($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;
$totalData = $conn->query("SELECT COUNT(*) FROM messages");
$totalRows = $totalData->fetchColumn();
$totalPages = ceil($totalRows / $limit);

$data = $conn->prepare("SELECT * FROM messages ORDER BY created_at DESC LIMIT ?, ? ");
$data->bindParam(1, $offset, PDO::PARAM_INT);
$data->bindParam(2, $limit, PDO::PARAM_INT);
$data->execute();
$messages = $data->fetchAll(PDO::FETCH_ASSOC);




