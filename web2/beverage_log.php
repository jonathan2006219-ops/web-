<?php
require 'db.php';
require 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $student_or_room = trim($_POST['student_or_room']);
  $amount = floatval($_POST['amount']);
  $stmt = $pdo->prepare('SELECT id FROM residents WHERE student_no=? OR room_no=? LIMIT 1');
  $stmt->execute([$student_or_room, $student_or_room]);
  $res = $stmt->fetch();
  $rid = $res ? $res['id'] : null;
  $ins = $pdo->prepare('INSERT INTO beverage_logs (resident_id, description, amount) VALUES (?, ?, ?)');
  $ins->execute([$rid, $student_or_room, $amount]);
  echo '<div class="alert alert-success">已紀錄</div>';
}

$logs = $pdo->query('SELECT b.*, r.student_no, r.room_no FROM beverage_logs b LEFT JOIN residents r ON b.resident_id = r.id ORDER BY b.created_at DESC LIMIT 50')->fetchAll();
?>
<h3>飲料機吃錢紀錄</h3>
<form method="post" class="mb-4">
  <div class="row">
    <div class="col-md-6 mb-3">
      <input class="form-control" name="student_or_room" placeholder="學號或房號">
    </div>
    <div class="col-md-3 mb-3">
      <input class="form-control" name="amount" placeholder="金額 (NTD)">
    </div>
    <div class="col-md-3">
      <button class="btn btn-primary">記錄</button>
    </div>
  </div>
</form>

<table class="table table-sm">
  <thead><tr><th>時間</th><th>學號/房號</th><th>金額</th></tr></thead>
  <tbody>
  <?php foreach($logs as $l): ?>
    <tr>
      <td><?=$l['created_at']?></td>
      <td><?=htmlspecialchars($l['description'])?></td>
      <td><?=number_format($l['amount'],2)?></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php require 'footer.php'; ?>
