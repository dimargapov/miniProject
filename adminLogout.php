<?php
session_start();
session_destroy();
header("Location: adminFrontend.php");
exit;