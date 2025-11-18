<?php
session_start();
require_once __DIR__.'/includes/db.php';

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // KIỂM TRA ĐẦU VÀO
    if (strlen($username) < 3) {
        $error = "Username phải từ 3 ký tự trở lên!";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email không hợp lệ! (VD: example@gmail.com)";
    }
    elseif (strlen($password) < 6) {
        $error = "Password phải từ 6 ký tự trở lên!";
    }
    else {

        // KIỂM TRA EMAIL ĐÃ TỒN TẠI
        $check = $conn->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
        $check->bind_param("s", $email);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $error = "Email đã được dùng để đăng ký. Vui lòng dùng email khác.";
        }
        else {

            // HASH PASSWORD
            $hash = password_hash($password, PASSWORD_DEFAULT);

            // THÊM USER MỚI
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hash);

            if ($stmt->execute()) {

                // Lưu thông tin session đầy đủ
                $_SESSION['user'] = [
                    "id" => $stmt->insert_id,   // ID mới tạo
                    "username" => $username,
                    "email" => $email,
                    "password" => $hash,        // hash cho login xác thực
                    "password_plain" => $password // lưu mật khẩu thực để hiển thị
                
                ];


                header("Location: /foodstore/index.php");
                exit;
            } else {
                $error = "Lỗi không thể đăng ký! Vui lòng thử lại.";
            }
        }
    }
}

include __DIR__.'/includes/header.php';
?>

<div class="form-box">
    <h2>Đăng ký tài khoản</h2>

    <?php if ($error): ?>
        <p style="color:red; font-weight:bold;"><?= $error ?></p>
    <?php endif; ?>

    <form method="post">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Email (gmail)</label>
        <input type="email" name="email" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <button class="btn" type="submit">Đăng ký</button>
    </form>

    <p>Đã có tài khoản?  
        <a href="/foodstore/dangnhap.php">Đăng nhập</a>
    </p>
</div>

<?php include __DIR__.'/includes/footer.php'; ?>
