<?php
require 'db.php';
require 'header.php';
require 'auth.php';
require_admin();

$repairs = $pdo->query('SELECT r.*, u.username, rs.student_no, rs.room_no FROM repairs r LEFT JOIN residents rs ON r.resident_id = rs.id LEFT JOIN users u ON rs.user_id = u.id ORDER BY r.created_at DESC')->fetchAll();
?>
<h3>報修管理（管理員）</h3>
<a class="btn btn-primary mb-3" href="repair_create.php">新增報修</a>
<table class="table">
  <thead><tr><th>ID</th><th>地點/項目</th><th>描述</th><th>住民</th><th>狀態</th><th>建立時間</th><th>動作</th></tr></thead>
  <tbody>
  <?php foreach($repairs as $r): ?>
    <tr>
      <td><?=$r['id']?></td>
      <td><?=htmlspecialchars($r['location'].' / '.$r['item'])?></td>
      <td><?=nl2br(htmlspecialchars($r['description']))?></td>
      <td><?=htmlspecialchars($r['student_no'].' / '.$r['room_no'] . ' (' . ($r['username'] ?? '') . ')')?></td>
      <td>
        <!-- inline 狀態更新表單 -->
        <form method="post" action="repair_update.php" style="display:inline-block">
          <input type="hidden" name="id" value="<?= $r['id'] ?>">
          <input type="hidden" name="csrf_token" value="<?=htmlspecialchars(csrf_token())?>">
          <select name="status" class="form-select form-select-sm" style="display:inline-block;width:auto;">
            <option <?= $r['status']=='待處理' ? 'selected' : '' ?>>待處理</option>
            <option <?= $r['status']=='處理中' ? 'selected' : '' ?>>處理中</option>
            <option <?= $r['status']=='已完成' ? 'selected' : '' ?>>已完成</option>
          </select>
          <button class="btn btn-sm btn-primary">更新</button>
        </form>
      </td>
      <td><?=$r['created_at']?></td>
      <td>
        <a class="btn btn-sm btn-secondary" href="repair_update.php?id=<?=$r['id']?>">編輯</a>
        <!-- 刪除表單 -->
        <form method="post" action="repair_delete.php" style="display:inline-block" onsubmit="return confirm('確定要刪除此筆報修嗎？');">
          <input type="hidden" name="id" value="<?=$r['id']?>">
          <input type="hidden" name="csrf_token" value="<?=htmlspecialchars(csrf_token())?>">
          <button class="btn btn-sm btn-danger">刪除</button>
        </form>
      </td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php require 'footer.php'; ?>
