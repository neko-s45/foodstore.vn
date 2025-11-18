<?php
require_once __DIR__.'/includes/db.php';
if(!isset($_SESSION['user'])){ header('Location: /foodstore/dangnhap.php'); exit; }
include __DIR__.'/includes/header.php';
echo "<div class='card'><h2>Tài khoản</h2><p>Email: ".htmlspecialchars($_SESSION['user']['email'])."</p></div>";
include __DIR__.'/includes/footer.php';
