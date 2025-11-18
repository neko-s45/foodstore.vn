<?php
require_once __DIR__.'/includes/db.php';
if(!isset($_SESSION['user'])) { header('Location: /foodstore/dangnhap.php'); exit; }
if(empty($_SESSION['cart'])) { header('Location: /foodstore/giohang.php'); exit; }

$userId = $_SESSION['user']['id'];

// tạo bảng nếu chưa có
$conn->query("CREATE TABLE IF NOT EXISTS orders (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, fullname VARCHAR(200), phone VARCHAR(50), address TEXT, total DECIMAL(12,0), created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)");
$conn->query("CREATE TABLE IF NOT EXISTS order_items (id INT AUTO_INCREMENT PRIMARY KEY, order_id INT, product_id INT, qty INT, price DECIMAL(12,0))");

// lấy sản phẩm từ cart
$ids = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0,count($ids),'?'));
$types = str_repeat('i', count($ids));
$sql = "SELECT * FROM products WHERE id IN ($placeholders)";
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$ids);
$stmt->execute();
$res = $stmt->get_result();
$products = [];
$total = 0;
while($r = $res->fetch_assoc()){
  $q = $_SESSION['cart'][$r['id']];
  $products[] = ['id'=>$r['id'],'qty'=>$q,'price'=>$r['price']];
  $total += $q * $r['price'];
}

// Lưu đơn
$fullname = 'Khách hàng';
$phone = '';
$address = '';
$stmt = $conn->prepare("INSERT INTO orders (user_id, fullname, phone, address, total) VALUES (?,?,?,?,?)");
$stmt->bind_param('isssd', $userId, $fullname, $phone, $address, $total);
$stmt->execute();
$orderId = $conn->insert_id;

// lưu order_items
$stmt2 = $conn->prepare("INSERT INTO order_items (order_id, product_id, qty, price) VALUES (?,?,?,?)");
foreach($products as $p){
  $stmt2->bind_param('iiid', $orderId, $p['id'], $p['qty'], $p['price']);
  $stmt2->execute();
}

// xóa cart
$_SESSION['cart'] = [];

header('Location: /foodstore/giohang.php');
exit;
