<?php require_once 'dbConnection.php';
if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo 'Некорректный ID';
}
$id = $_GET['id'];
$delete = $conn->prepare("DELETE FROM admins WHERE admin_id = :id");
$delete->execute([':id' => $id]);

if ($delete->rowCount() > 0) {
    header('Location: adminFrontend.php');
    exit;
} else {
    echo "Работник с ID $id не найден!";
}

