<?php
require_once __DIR__.'/includes/db.php';
include __DIR__.'/includes/header.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Đảm bảo biến giỏ hàng luôn tồn tại dưới dạng mảng
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Xử lý form
$successMsg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cập nhật số lượng
    if (isset($_POST['update'])) {
        foreach ($_POST['qty'] as $id => $q) {
            $q = max(0, intval($q));
            if ($q == 0) unset($_SESSION['cart'][$id]);
            else $_SESSION['cart'][$id] = $q;
        }
    }
    // Thanh toán
    elseif (isset($_POST['checkout'])) {
        if (!isset($_SESSION['user'])) {
            header('Location: /foodstore/dangnhap.php');
            exit;
        } else {
            $userId = $_SESSION['user']['id'];
            $cart = $_SESSION['cart'];
            if (!empty($cart)) {
                $total = 0;
                foreach ($cart as $id => $qty) {
                    $res = $conn->query("SELECT price FROM products WHERE id=".intval($id));
                    if ($res && $row = $res->fetch_assoc()) {
                        $total += $row['price'] * $qty;
                    }
                }

                // Thêm đơn hàng
                $stmt = $conn->prepare("INSERT INTO orders (user_id, total) VALUES (?, ?)");
                $stmt->bind_param("id", $userId, $total);
                $stmt->execute();
                $orderId = $stmt->insert_id;

                // Thêm order_items
                foreach ($cart as $id => $qty) {
                    $stmt2 = $conn->prepare("INSERT INTO order_items (order_id, product_id, qty) VALUES (?, ?, ?)");
                    $stmt2->bind_param("iii", $orderId, $id, $qty);
                    $stmt2->execute();
                }

                // Xóa giỏ hàng
                $_SESSION['cart'] = [];
                $successMsg = "🎉 Đặt hàng thành công! Cảm ơn bạn đã mua hàng.";
            }
        }
    }
}

// Lấy thông tin sản phẩm trong giỏ hàng
$items = [];
if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $types = str_repeat('i', count($ids));
    $sql = "SELECT * FROM products WHERE id IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param($types, ...$ids);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($r = $res->fetch_assoc()) $items[] = $r;
    }
}
?>

<div class="card" style="max-width:1000px;margin:20px auto">
  <h2>Giỏ hàng</h2>

  <?php if($successMsg): ?>
      <p style="color:green;font-weight:bold;text-align:center;"><?= $successMsg ?></p>
  <?php endif; ?>

  <?php if (!empty($items)): ?>
  <form method="post">
    <table style="width:100%;border-collapse:collapse">
      <thead>
        <tr style="text-align:left">
          <th>Sản phẩm</th>
          <th>Giá</th>
          <th>Số lượng</th>
          <th>Thành tiền</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $total = 0; 
        foreach ($items as $it):
          $qty = $_SESSION['cart'][$it['id']] ?? 1;
          $sub = $qty * $it['price'];
          $total += $sub;
        ?>
        <tr>
          <td style="padding:8px">
            <img src="/foodstore/assets/images/<?= htmlspecialchars($it['image'] ?: 'default.jpg') ?>" 
                 style="width:80px;height:60px;object-fit:cover;border-radius:6px">
            <?= htmlspecialchars($it['name']) ?>
          </td>
          <td><?= number_format($it['price']) ?> VND</td>
          <td><input type="number" name="qty[<?= $it['id'] ?>]" value="<?= $qty ?>" min="0" style="width:70px"></td>
          <td><?= number_format($sub) ?> VND</td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <p style="text-align:right;font-weight:700;margin-top:10px">
      Tổng: <?= number_format($total) ?> VND
    </p>

    <div style="display:flex;gap:12px;justify-content:flex-end">
      <button class="btn" name="update" type="submit">Cập nhật</button>
      <button class="btn secondary" name="checkout" type="submit">Đặt hàng</button>
    </div>
  </form>

  <?php else: ?>
    <p style="text-align:center;padding:20px;font-size:16px">
      Giỏ hàng của bạn đang trống.
    </p>
  <?php endif; ?>
</div>

<?php include __DIR__.'/includes/footer.php'; ?>
