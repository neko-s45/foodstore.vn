<?php
session_start();
require_once __DIR__.'/includes/db.php';

$error = "";

// Xử lý form login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // CHECK ADMIN
    $stmt = $conn->prepare("SELECT * FROM admin WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password); // admin pass plaintext
    $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();

    if ($admin) {
        $_SESSION['admin'] = $admin['username'];
        header("Location: /foodstore/admin/sanpham.php");
        exit;
    }

    // CHECK USER
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {

        // Lưu đầy đủ thông tin user
        $_SESSION['user'] = [
            "id" => $user['id'],
            "username" => $user['username'],
            "email" => $user['email'],
            "password" => $user['password'],    // hash
            "password_plain" => $password       // password thực do user nhập
        ];

        header("Location: /foodstore/index.php");
        exit;
    }

    $error = "Sai tài khoản hoặc mật khẩu!";
}

include __DIR__.'/includes/header.php';
?>

<div class="form-box">
    <h2>Đăng nhập</h2>
    <?php if($error) echo "<p style='color:red'>$error</p>"; ?>
    <form method="post">
        <label>Username</label>
        <input type="text" name="username" required>
        <label>Password</label>
        <input type="password" name="password" required>
        <button class="btn" type="submit">Đăng nhập</button>
    </form>
    <p>Chưa có tài khoản? <a href="/foodstore/dangky.php">Đăng ký</a></p>
</div>

<?php include __DIR__.'/includes/footer.php'; ?>
