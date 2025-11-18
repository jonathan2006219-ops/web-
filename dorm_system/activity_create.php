<?php
require 'db.php';
require 'header.php';
require 'auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $title = trim($_POST['title']);
  $description = trim($_POST['description']);
  $date = $_POST['activity_date'];
  if (!$title || !$date) $err = '請輸入活動名稱與日期';
  else {
    $stmt = $pdo->prepare('INSERT INTO activities (title, description, activity_date, created_by) VALUES (?, ?, ?, ?)');
    $stmt->execute([$title, $description, $date, current_user_id()]);
    $ok = '活動建立成功';
  }
}
?>
<h3>建立活動</h3>
<?php if (!empty($err)) echo '<div class="alert alert-danger">'.htmlspecialchars($err).'</div>'; ?>
<?php if (!empty($ok)) echo '<div class="alert alert-success">'.htmlspecialchars($ok).'</div>'; ?>
<form method="post">
  <div class="mb-3"><label class="form-label">活動名稱</label><input name="title" class="form-control"></div>
  <div class="mb-3"><label class="form-label">活動說明</label><textarea name="description" class="form-control"></textarea></div>
  <div class="mb-3"><label class="form-label">活動日期</label><input type="date" name="activity_date" class="form-control"></div>
  <button class="btn btn-primary">建立</button>
</form>
<?php require 'footer.php'; ?>
