<?php
require 'db.php';
require 'header.php';
require 'auth.php';
require_admin();

$id = $_GET['id'] ?? null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'];
  $status = $_POST['status'];
  $stmt = $pdo->prepare('UPDATE repairs SET status=?, updated_at=NOW() WHERE id=?');
  $stmt->execute([$status, $id]);
  header('Location: repair_list.php');
  exit;
}
$repair = $pdo->prepare('SELECT * FROM repairs WHERE id=?');
$repair->execute([$id]);
$r = $repair->fetch();
?>
<h3>更新報修狀態</h3>
<form method="post">
  <input type="hidden" name="id" value="<?=htmlspecialchars($r['id'])?>">
  <div class="mb-3">
    <label class="form-label">狀態</label>
    <select name="status" class="form-select">
      <option <?= $r['status']=='待處理' ? 'selected' : '' ?>>待處理</option>
      <option <?= $r['status']=='處理中' ? 'selected' : '' ?>>處理中</option>
      <option <?= $r['status']=='已完成' ? 'selected' : '' ?>>已完成</option>
    </select>
  </div>
  <button class="btn btn-primary">更新</button>
</form>
<?php require 'footer.php'; ?>
