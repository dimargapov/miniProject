<?php
$dsn = 'mysql:host=localhost;dbname=so_sta1';
$username = 'sta1';
$password = 'dt0fIDWv9X';

try {
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Подключение успешно!";
} catch (PDOException $e) {
    echo "Ошибка подключения: " . $e->getMessage();
}