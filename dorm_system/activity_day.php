<?php
require 'db.php';
require 'header.php';

$activity_id = $_GET['id'] ?? null;
$activity = $pdo->prepare('SELECT * FROM activities WHERE id=?');
$activity->execute([$activity_id]);
$a = $activity->fetch();
if (!$a) { echo '<div class="alert alert-danger">活動不存在</div>'; require 'footer.php'; exit; }

$signups = $pdo->prepare('SELECT s.*, r.student_no, r.room_no, r.name FROM activity_signups s LEFT JOIN residents r ON s.resident_id = r.id WHERE s.activity_id = ? ORDER BY s.signup_at DESC');
$signups->execute([$activity_id]);
$rows = $signups->fetchAll();
?>
<h3>活動簽到：<?=htmlspecialchars($a['title'])?> (<?=htmlspecialchars($a['activity_date'])?>)</h3>
<form method="post" action="signup_process.php">
  <div class="mb-3"><label class="form-label">學號或房號</label><input class="form-control" name="student_or_room" required></div>
  <input type="hidden" name="activity_id" value="<?=htmlspecialchars($a['id'])?>">
  <button class="btn btn-success">簽到</button>
</form>

<hr>
<h5>已簽到名單</h5>
<table class="table table-sm">
  <thead><tr><th>時間</th><th>學號/房號</th><th>房號</th><th>姓名</th><th>關聯住民</th></tr></thead>
  <tbody>
  <?php foreach($rows as $r): ?>
    <tr>
      <td><?=$r['signup_at']?></td>
      <td><?=htmlspecialchars($r['student_or_room'])?></td>
      <td><?=htmlspecialchars($r['room_no'] ?? '')?></td>
      <td><?=htmlspecialchars($r['name'] ?? '')?></td>
      <td><?= $r['resident_id'] ? '已關聯' : '匿名' ?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php require 'footer.php'; ?>
