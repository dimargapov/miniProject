<?php
include 'addMessage.php';
require_once 'dbConnection.php';
?>
<!DOCTYPE html>
<html lang="ru">
	<head>
		<meta charset="utf-8">  
		<title>Гостевая книга</title>
		<link rel="stylesheet" href="css/bootstrap/css/bootstrap.css">
		<link rel="stylesheet" href="css/styles.css">
	</head>
	<body>
		<div id="wrapper">
			<h1>Гостевая книга</h1>
			<div>
				<nav>
				  <ul class="pagination">
					<li class="<?= ($page <= 1) ? 'disabled' : '' ?>">
						<a href="?page=<?= max(1, $page - 1) ?>"  aria-label="Previous">
							<span aria-hidden="true">&laquo;</span>
						</a>
					</li>
                      <?php
                      $start = max(1, $page - 2);
                      $end = min($totalPages, $page + 2);

                      for ($i = $start; $i <= $end; $i++): ?>
					<li class="<?= ($i === $page) ? 'active' : ''?>">
                        <a href="?page=<?= $i ?>"><?= $i ?></a></li>
                      <?php endfor; ?>
					<li class="<?= ($page >= $totalPages) ? 'disabled' : '' ?>">
						<a href="?page=<?= min($totalPages, $page + 1) ?>" aria-label="Next">
							<span aria-hidden="true">&raquo;</span>
						</a>
					</li>
				  </ul>
				</nav>
			</div>
			<div class="note">
                <?php foreach ($messages as $msg): ?>
				<p>
					<span class="date"><?= date('d.m.Y H:i:s', strtotime($msg['created_at'])) ?></span>
					<span class="name"><?= htmlspecialchars($msg['name']) ?></span>
				</p>
				<p>
					<?= htmlspecialchars($msg['message']) ?>
				</p>
                <?php endforeach; ?>
			</div>
            <? if (!empty($_SESSION['success_message'])) {
                echo '<div class="info alert alert-info">
			' . $_SESSION['success_message'] . '</div>';
            }
            unset($_SESSION['success_message']);
    ?>
			<div id="form">
				<form action="addMessage.php" method="POST">
					<p><input class="form-control" placeholder="Ваше имя" name="name"></p>
					<p><textarea class="form-control" placeholder="Ваш отзыв" name="message"></textarea></p>
					<p><input type="submit" class="btn btn-info btn-block" value="Сохранить"></p>
				</form>
			</div>
		</div>
	</body>
</html>

