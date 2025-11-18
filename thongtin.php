<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// nếu chưa đăng nhập => ẩn hoàn toàn
if (!isset($_SESSION['user'])) return;

$user = $_SESSION['user'];
?>

<style>
#overlay-info {
    position: fixed;
    top: 0;
    right: -350px;
    width: 350px;
    height: 100vh;
    background: white;
    box-shadow: -3px 0 8px rgba(0,0,0,0.3);
    transition: 0.35s;
    padding: 20px;
    z-index: 9999;
}
#overlay-info.open {
    right: 0;
}
.close-btn {
    background: red;
    color: #fff;
    padding: 5px 10px;
    float: right;
    cursor: pointer;
    border-radius: 4px;
}
.user-row {
    font-size: 18px;
    margin: 15px 0;
    font-weight: 500;
}
</style>

<div id="overlay-info">
    <span class="close-btn" onclick="closeOverlay()">X</span>
    <h2>Thông tin tài khoản</h2>

    <div class="user-row">👤 Username: <b><?= htmlspecialchars($user['username']) ?></b></div>
    <div class="user-row">📧 Email: <b><?= htmlspecialchars($user['email']) ?></b></div>
    <div class="user-row">🔑 Password: <b><?= htmlspecialchars($user['password_plain'] ?? $user['password']) ?></b></div>
</div>

<script>
function openOverlay() {
    document.getElementById("overlay-info").classList.add("open");
}

function closeOverlay() {
    document.getElementById("overlay-info").classList.remove("open");
}
</script>
