<?php require_once 'dbConnection.php';
session_start();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Сообщения пользователей</title>
    <link rel="stylesheet" href="css/bootstrap/css/bootstrap.css">
    <style>
        table { border-collapse: collapse; width: 70%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f0f0f0; }
        .pagination {
            margin-top: 20px;
            text-align: center;
            font-family: Arial, sans-serif;
        }
        .pagination a,
        .pagination strong {
            display: inline-block;
            margin: 0 5px;
            padding: 6px 12px;
            text-decoration: none;
            border: 1px solid #007bff;
            border-radius: 4px;
            color: #007bff;
            transition: background-color 0.3s, color 0.3s;
        }
        .pagination a:hover {
            background-color: #007bff;
            color: white;
        }
        .pagination strong {
            background-color: #007bff;
            color: white;
            cursor: default;
            border-color: #007bff;
        }
        .filter {
            margin-left: 500px;
            margin-top: -80px;
        }
    </style>
</head>
<body>
<div class="messages">
<h3>Сообщения пользователей</h3>
<?php
$selectedStatuses = $_GET['status'] ?? [];

$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$messagesPerPage = 5;
$offset = ($page - 1) * $messagesPerPage;

if (empty($selectedStatuses)) {
    $countSql = "SELECT COUNT(*) FROM messages";
    $totalMessages = $conn->query($countSql)->fetchColumn();
} else {
    $allowedStatuses = ['pending', 'approved', 'rejected', 'updated'];
    $filteredStatuses = array_intersect($selectedStatuses, $allowedStatuses);
    if (count($filteredStatuses) > 0) {
        $placeholders = implode(',', array_fill(0, count($filteredStatuses), '?'));
        $countSql = "SELECT COUNT(*) FROM messages WHERE status IN ($placeholders)";
        $stmtCount = $conn->prepare($countSql);
        $stmtCount->execute($filteredStatuses);
        $totalMessages = $stmtCount->fetchColumn();
    } else {
        $totalMessages = 0;
    }
}

$totalPages = ceil($totalMessages / $messagesPerPage);

if ($page > $totalPages && $totalPages > 0) {
    $page = $totalPages;
    $offset = ($page - 1) * $messagesPerPage;
}

if (empty($selectedStatuses)) {
    $sql = "SELECT m.*, a.admin_name, a.email
            FROM messages m
            LEFT JOIN admins a ON m.moderated_by = a.admin_id
            ORDER BY CASE WHEN m.status = 'pending' THEN 0 ELSE 1 END, m.moderated_at DESC
            LIMIT :limit OFFSET :offset";

    $stmt = $conn->prepare($sql);
    $stmt->bindValue(':limit', $messagesPerPage, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $unapprovedMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    if (count($filteredStatuses) > 0) {
        $placeholders = implode(',', array_fill(0, count($filteredStatuses), '?'));
        $sql = "SELECT m.*, a.admin_name, a.email
                FROM messages m
                LEFT JOIN admins a ON m.moderated_by = a.admin_id
                WHERE m.status IN ($placeholders)
                ORDER BY CASE WHEN m.status = 'pending' THEN 0 ELSE 1 END, m.moderated_at DESC
                LIMIT $messagesPerPage OFFSET $offset";

        $stmt = $conn->prepare($sql);
        $i = 1;
        foreach ($filteredStatuses as $status) {
            $stmt->bindValue($i, $status, PDO::PARAM_STR);
            $i++;
        }
        $stmt->execute();
        $unapprovedMessages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $unapprovedMessages = [];
    }
}
?>
<table>
    <thead>
    <tr>
        <th>Имя</th>
        <th>Сообщение</th>
        <th>Дата</th>
        <th>Действие</th>
        <th>Статус</th>
    </tr>
    </thead>
    <tbody>
    <?php if(count($unapprovedMessages) > 0): ?>
        <?php foreach ($unapprovedMessages as $unapprovedMessage): ?>
            <tr>
                <td><?= htmlspecialchars($unapprovedMessage['name']) ?></td>
                <td><?= htmlspecialchars($unapprovedMessage['message']) ?></td>
                <td><?= date('d.m.Y H:i', strtotime($unapprovedMessage['created_at'])) ?></td>
                <td><a href='messageApprove.php?id=<?= urlencode($unapprovedMessage['id'])?>'>Одобрить</a>&nbsp&nbsp&nbsp&nbsp&nbsp<a href='messageDelete.php?id=<?= urlencode($unapprovedMessage['id'])?>'>Отклонить</a>&nbsp&nbsp&nbsp&nbsp&nbsp<a href='messageEdit.php?id=<?= urlencode($unapprovedMessage['id'])?>'>Изменить</a></td>
                <td><?= $unapprovedMessage['status']; if (!empty($unapprovedMessage['moderated_at']) && !empty($unapprovedMessage['admin_name'])) {
                    echo " by ". $unapprovedMessage['admin_name'] . "(" . $unapprovedMessage['email'] . ")" . " on " . date('d.m.Y H:i', strtotime($unapprovedMessage['moderated_at']));
                    }?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="4">Нет данных</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>
<?php
$queryParams = $_GET;
?>

<div class="pagination">
    <?php if ($page > 1): ?>
        <?php
        $queryParams['page'] = $page - 1;
        ?>
        <a href="?<?= htmlspecialchars(http_build_query($queryParams)) ?>">&laquo; Назад</a>
    <?php endif; ?>

    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
        <?php
        $queryParams['page'] = $p;
        ?>
        <?php if ($p == $page): ?>
            <strong><?= $p ?></strong>
        <?php else: ?>
            <a href="?<?= htmlspecialchars(http_build_query($queryParams)) ?>"><?= $p ?></a>
        <?php endif; ?>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <?php
        $queryParams['page'] = $page + 1;
        ?>
        <a href="?<?= htmlspecialchars(http_build_query($queryParams)) ?>">Вперёд &raquo;</a>
    <?php endif; ?>
</div>
<br>
<a href="adminFrontend.php">Вернуться к админке</a>
<br>
<a href="adminUsers.php">Администраторы</a>
<form class='filter' method="get" action="">
    <label><input type="checkbox" name="status[]" value="pending" <?= in_array('pending', $_GET['status'] ?? []) ? 'checked' : '' ?>> Pending</label>
    <label><input type="checkbox" name="status[]" value="approved" <?= in_array('approved', $_GET['status'] ?? []) ? 'checked' : '' ?>> Approved</label>
    <label><input type="checkbox" name="status[]" value="rejected" <?= in_array('rejected', $_GET['status'] ?? []) ? 'checked' : '' ?>> Rejected</label>
    <label><input type="checkbox" name="status[]" value="updated" <?= in_array('updated', $_GET['status'] ?? []) ? 'checked' : '' ?>> Updated</label>
    <button type="submit">Фильтровать</button>
</form>
</body>
</html>
