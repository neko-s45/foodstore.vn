<?php
require_once __DIR__.'/includes/db.php';
include __DIR__.'/includes/header.php';
$type = $_GET['loai'] ?? 'all';
if($type === 'doan') $res = $conn->query("SELECT * FROM products WHERE type='doan' ORDER BY id DESC");
elseif($type === 'douong') $res = $conn->query("SELECT * FROM products WHERE type='douong' ORDER BY id DESC");
else $res = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<div class="section">
  <h2>Thực đơn</h2>
  <div class="products-grid">
    <?php while($r = $res->fetch_assoc()): $img = '/foodstore/assets/images/'.($r['image']?:'default.jpg'); ?>
      <div class="product-card card">
        <img src="<?= $img ?>">
        <h3><?= htmlspecialchars($r['name']) ?></h3>
        <p class="price"><?= number_format($r['price']) ?> VND</p>
        <a class="btn" href="/foodstore/chitietmon.php?id=<?= $r['id'] ?>">Xem chi tiết</a>
      </div>
    <?php endwhile; ?>
  </div>
</div>
<?php include __DIR__.'/includes/footer.php'; ?>
