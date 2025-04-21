<?php
require_once 'dbConnection.php';
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo 'Некорректный ID';
}
$id = $_GET['id'];
session_start();
$admin_id = $_SESSION['session_id'];
$sql = "UPDATE messages SET status = 'approved', moderated_at = NOW(), moderated_by = :admin_id WHERE id = :id";
$approve = $conn->prepare($sql);
$approve->execute([':id' => $id, ':admin_id' => $admin_id]);
header("Location: adminMessages.php");
exit;