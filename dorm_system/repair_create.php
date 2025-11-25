<?php
require 'db.php';
require 'header.php';
require 'auth.php';
require_admin();

// 取得住民清單供下拉選單使用
$res = $pdo->query('SELECT id, student_no, room_no, name FROM residents ORDER BY student_no')->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // CSRF
  check_csrf($_POST['csrf_token'] ?? '');
  $resident_id = $_POST['resident_id'] ?: null;
  $location = trim($_POST['location'] ?? '');
  $item = trim($_POST['item'] ?? '');
  $description = trim($_POST['description'] ?? '');
  $status = $_POST['status'] ?? '待處理';

  $stmt = $pdo->prepare('INSERT INTO repairs (resident_id, location, item, description, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
  $stmt->execute([$resident_id, $location, $item, $description, $status]);
  header('Location: repair_list.php');
  exit;
}
?>
<h3>新增報修</h3>
<form method="post">
  <input type="hidden" name="csrf_token" value="<?=htmlspecialchars(csrf_token())?>">
  <div class="mb-3">
    <label class="form-label">住民（選填）</label>
    <select name="resident_id" class="form-select">
      <option value="">-- 不指定 --</option>
      <?php foreach($res as $r): ?>
        <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['student_no'].' / '.$r['room_no'].' '.($r['name'] ?? '')) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="mb-3"><label class="form-label">地點</label><input name="location" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">項目</label><input name="item" class="form-control" required></div>
  <div class="mb-3"><label class="form-label">描述</label><textarea name="description" class="form-control"></textarea></div>
  <div class="mb-3">
    <label class="form-label">狀態</label>
    <select name="status" class="form-select">
      <option>待處理</option>
      <option>處理中</option>
      <option>已完成</option>
    </select>
  </div>
  <button class="btn btn-primary">建立</button>
</form>
<?php require 'footer.php'; ?>
