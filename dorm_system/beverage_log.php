<?php
require 'db.php';
require 'header.php';
require 'auth.php';

// 處理新增紀錄（CSRF 驗證）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  check_csrf($_POST['csrf_token'] ?? '');
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

// 過濾與分頁
$params = [];
$where = '';
$limit = 10;
$page = max(1, intval($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;
if (!empty($_GET['mine'])) {
  // 只看自己
  if (empty($_SESSION['user_id'])) {
    // 未登入則不返回任何資料
    $where = 'WHERE 1=0';
  } else {
    $where = 'WHERE r.user_id = ?';
    $params[] = current_user_id();
  }
}

$sql = 'SELECT b.*, r.student_no, r.room_no FROM beverage_logs b LEFT JOIN residents r ON b.resident_id = r.id ' . $where . ' ORDER BY b.created_at DESC LIMIT ' . intval($limit) . ' OFFSET ' . intval($offset);
$logs = $pdo->prepare($sql);
$logs->execute($params);
$logs = $logs->fetchAll();
?>
<h3>飲料機吃錢紀錄</h3>
<form method="post" class="mb-4">
  <input type="hidden" name="csrf_token" value="<?=htmlspecialchars(csrf_token())?>">
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
      <td>
        <?php if (!empty($_SESSION['role']) && $_SESSION['role']==='admin'): ?>
        <!-- 管理員可刪除 -->
        <form method="post" action="beverage_delete.php" style="display:inline-block" onsubmit="return confirm('確定要刪除此筆紀錄嗎？');">
          <input type="hidden" name="id" value="<?=$l['id']?>">
          <input type="hidden" name="csrf_token" value="<?=htmlspecialchars(csrf_token())?>">
          <button class="btn btn-sm btn-danger">刪除</button>
        </form>
        <?php endif; ?>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php require 'footer.php'; ?>
