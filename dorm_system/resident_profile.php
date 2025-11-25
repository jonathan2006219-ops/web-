<?php
require 'db.php';
require 'header.php';
require 'auth.php';
require_login();

$user_id = current_user_id();

// 取得現有 residents 資料
$stmt = $pdo->prepare('SELECT * FROM residents WHERE user_id = ? LIMIT 1');
$stmt->execute([$user_id]);
$res = $stmt->fetch();

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $student_no = trim($_POST['student_no'] ?? '');
  $room_no = trim($_POST['room_no'] ?? '');
  $name = trim($_POST['name'] ?? '');

  if ($student_no === '') $errors[] = '請填寫學號/工號';
  if ($room_no === '') $errors[] = '請填寫房號';

  if (empty($errors)) {
    if ($res) {
      $upd = $pdo->prepare('UPDATE residents SET student_no = ?, room_no = ?, name = ? WHERE id = ?');
      $upd->execute([$student_no, $room_no, $name, $res['id']]);
    } else {
      $ins = $pdo->prepare('INSERT INTO residents (user_id, student_no, room_no, name) VALUES (?, ?, ?, ?)');
      $ins->execute([$user_id, $student_no, $room_no, $name]);
    }
    header('Location: resident_profile.php?saved=1');
    exit;
  }
}
?>
<h3>我的基本資料</h3>
<?php if (!empty($errors)): ?><div class="alert alert-danger"><?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?></div><?php endif; ?>
<?php if (!empty($_GET['saved'])): ?><div class="alert alert-success">已儲存基本資料。</div><?php endif; ?>
<form method="post">
  <div class="mb-3"><label class="form-label">學號/工號</label><input name="student_no" class="form-control" value="<?=htmlspecialchars($res['student_no'] ?? '')?>" required></div>
  <div class="mb-3"><label class="form-label">房號</label><input name="room_no" class="form-control" value="<?=htmlspecialchars($res['room_no'] ?? '')?>" required></div>
  <div class="mb-3"><label class="form-label">姓名（選填）</label><input name="name" class="form-control" value="<?=htmlspecialchars($res['name'] ?? '')?>"></div>
  <button class="btn btn-primary">儲存</button>
  <a class="btn btn-secondary" href="logout.php">登出</a>
</form>
<?php require 'footer.php'; ?>
