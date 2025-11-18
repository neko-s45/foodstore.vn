<?php
session_start();

// Xóa tất cả session
$_SESSION = [];
session_destroy();

// Redirect về trang chính
header("Location: /foodstore/index.php");
exit;
