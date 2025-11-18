<?php
require_once __DIR__.'/includes/db.php';
include __DIR__.'/includes/header.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $conn->prepare("SELECT * FROM products WHERE id=? LIMIT 1");
$stmt->bind_param('i',$id);
$stmt->execute();
$res = $stmt->get_result();
if($res->num_rows==0){
  echo "<div class='card'><p>Sản phẩm không tồn tại</p></div>";
  include __DIR__.'/includes/footer.php'; exit;
}
$p = $res->fetch_assoc();
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])){
  $qty = max(1, intval($_POST['qty']));
  if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
  if(isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id] += $qty;
  else $_SESSION['cart'][$id] = $qty;
  header('Location: /foodstore/giohang.php'); exit;
}
$img = '/foodstore/assets/images/'.($p['image']?:'default.jpg');
?>
<div class="card" style="display:flex;gap:18px;flex-wrap:wrap">
  <div style="flex:1;min-width:260px">
    <img src="<?= $img ?>" style="width:100%;height:320px;object-fit:cover;border-radius:8px">
  </div>
  <div style="flex:1;min-width:260px">
    <h2><?= htmlspecialchars($p['name']) ?></h2>
    <p class="price"><?= number_format($p['price']) ?> VND</p>
    <p><?= nl2br(htmlspecialchars($p['description'])) ?></p>
    <form method="post">
      <label>Số lượng:</label><br>
      <input type="number" name="qty" value="1" min="1" style="width:80px;padding:6px;margin-top:6px"><br><br>
      <button class="btn" name="add_to_cart" type="submit">Thêm vào giỏ</button>
    </form>
  </div>
</div>
<?php include __DIR__.'/includes/footer.php'; ?>
