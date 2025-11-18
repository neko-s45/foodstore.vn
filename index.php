<?php
require_once __DIR__.'/includes/db.php';
include __DIR__.'/includes/header.php';

// nếu DB rỗng thì hướng dẫn
$resTest = $conn->query("SHOW TABLES LIKE 'products'");
if(!$resTest || $resTest->num_rows==0){
    echo "<div class='card'><p>Chưa có dữ liệu. Vui lòng truy cập <a href='cai_dat.php'>cai_dat.php</a> để tạo DB & dữ liệu mẫu.</p></div>";
    include __DIR__.'/includes/footer.php';
    exit;
}

// HOT: ưu tiên is_hot trước, sau đó mới tổng số lượng bán
$sqlHot = "
SELECT p.*, COALESCE(SUM(oi.qty),0) AS total_sold
FROM products p
LEFT JOIN order_items oi ON p.id = oi.product_id
GROUP BY p.id
ORDER BY p.is_hot DESC, total_sold DESC, p.id ASC
LIMIT 3
";
$hotRes = $conn->query($sqlHot);
$hot = [];
if($hotRes) while($r=$hotRes->fetch_assoc()) $hot[] = $r;

// fallback nếu chưa đủ 3 món
if(count($hot) < 3){
    $fallback = $conn->query("SELECT * FROM products ORDER BY id ASC LIMIT ".(3-count($hot)));
    while($r=$fallback->fetch_assoc()) $hot[] = $r;
}

// foods and drinks (order newest first)
$foods = []; $drinks = [];
$r1 = $conn->query("SELECT * FROM products WHERE type='doan' ORDER BY id DESC");
if($r1) while($r=$r1->fetch_assoc()) $foods[] = $r;
$r2 = $conn->query("SELECT * FROM products WHERE type='douong' ORDER BY id DESC");
if($r2) while($r=$r2->fetch_assoc()) $drinks[] = $r;
?>

<!-- HOME: hot + sample 3 food + 3 drink (we display full grids below too) -->
<div id="section-home" class="section">
  <h2>🔥 Hot Picks</h2>
  <div class="hot-section">
    <?php foreach($hot as $item): 
        $img = '/foodstore/assets/images/'.($item['image'] ?: 'default.jpg');
    ?>
      <div class="product-card card">
        <img src="<?= $img ?>" alt="<?= htmlspecialchars($item['name']) ?>">
        <h3><?= htmlspecialchars($item['name']) ?></h3>
        <p class="price"><?= number_format($item['price']) ?> VND</p>
        <a class="btn" href="/foodstore/chitietmon.php?id=<?= $item['id'] ?>">Xem chi tiết</a>
      </div>
    <?php endforeach; ?>
  </div>

  <h2>🍽 Món Ăn </h2>
  <div class="products-grid">
    <?php
    $cnt=0;
    foreach($foods as $f){
      if($cnt>=3) break;
      $cnt++;
      $img = '/foodstore/assets/images/'.($f['image'] ?: 'default.jpg');
    ?>
      <div class="product-card card">
        <img src="<?= $img ?>" alt="<?= htmlspecialchars($f['name']) ?>">
        <h3><?= htmlspecialchars($f['name']) ?></h3>
        <p class="price"><?= number_format($f['price']) ?> VND</p>
        <a class="btn" href="/foodstore/chitietmon.php?id=<?= $f['id'] ?>">Xem chi tiết</a>
      </div>
    <?php } ?>
  </div>

  <h2>🥤 Thức Uống </h2>
  <div class="products-grid">
    <?php
    $cnt=0;
    foreach($drinks as $d){
      if($cnt>=3) break;
      $cnt++;
      $img = '/foodstore/assets/images/'.($d['image'] ?: 'default.jpg');
    ?>
      <div class="product-card card">
        <img src="<?= $img ?>" alt="<?= htmlspecialchars($d['name']) ?>">
        <h3><?= htmlspecialchars($d['name']) ?></h3>
        <p class="price"><?= number_format($d['price']) ?> VND</p>
        <a class="btn" href="/foodstore/chitietmon.php?id=<?= $d['id'] ?>">Xem chi tiết</a>
      </div>
    <?php } ?>
  </div>
</div>

<!-- FOOD: all foods (each row 3 items) -->
<div id="section-food" class="section" style="display:none">
  <h2>🍽 Tất cả món Ăn</h2>
  <div class="products-grid">
    <?php foreach($foods as $f):
      $img = '/foodstore/assets/images/'.($f['image'] ?: 'default.jpg');
    ?>
      <div class="product-card card">
        <img src="<?= $img ?>" alt="<?= htmlspecialchars($f['name']) ?>">
        <h3><?= htmlspecialchars($f['name']) ?></h3>
        <p class="price"><?= number_format($f['price']) ?> VND</p>
        <a class="btn" href="/foodstore/chitietmon.php?id=<?= $f['id'] ?>">Xem chi tiết</a>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<!-- DRINK: all drinks -->
<div id="section-drink" class="section" style="display:none">
  <h2>🥤 Tất cả Thức Uống</h2>
  <div class="products-grid">
    <?php foreach($drinks as $d):
      $img = '/foodstore/assets/images/'.($d['image'] ?: 'default.jpg');
    ?>
      <div class="product-card card">
        <img src="<?= $img ?>" alt="<?= htmlspecialchars($d['name']) ?>">
        <h3><?= htmlspecialchars($d['name']) ?></h3>
        <p class="price"><?= number_format($d['price']) ?> VND</p>
        <a class="btn" href="/foodstore/chitietmon.php?id=<?= $d['id'] ?>">Xem chi tiết</a>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<?php include __DIR__.'/includes/footer.php'; ?>
