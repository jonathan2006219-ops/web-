<?php
require 'db.php';
require 'header.php';
require 'auth.php';
require_login();

// find resident id
$user_id = current_user_id();
$stmt = $pdo->prepare('SELECT id FROM residents WHERE user_id = ? LIMIT 1');
$stmt->execute([$user_id]);
$res = $stmt->fetch();
$resident_id = $res ? $res['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $location = trim($_POST['location']);
  $item = trim($_POST['item']);
  $description = trim($_POST['description']);
  if (!$location || !$item) { $err = '請填寫地點與項目'; }
  else {
    $ins = $pdo->prepare('INSERT INTO repairs (resident_id, location, item, description) VALUES (?, ?, ?, ?)');
    $ins->execute([$resident_id, $location, $item, $description]);
    $ok = '報修已送出';
  }
}
?>
<h3>提交報修</h3>
<?php if (!empty($err)) echo '<div class="alert alert-danger">'.htmlspecialchars($err).'</div>'; ?>
<?php if (!empty($ok)) echo '<div class="alert alert-success">'.htmlspecialchars($ok).'</div>'; ?>
<form method="post">
  <div class="mb-3"><label class="form-label">地點</label><input name="location" class="form-control"></div>
  <div class="mb-3"><label class="form-label">項目</label><input name="item" class="form-control"></div>
  <div class="mb-3"><label class="form-label">問題描述</label><textarea name="description" class="form-control"></textarea></div>
  <button class="btn btn-primary">送出</button>
</form>
<?php require 'footer.php'; ?>
