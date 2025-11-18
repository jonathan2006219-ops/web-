<?php
require 'db.php';
require 'header.php';
require 'auth.php';
require_admin();

$repairs = $pdo->query('SELECT r.*, u.username, rs.student_no, rs.room_no FROM repairs r LEFT JOIN residents rs ON r.resident_id = rs.id LEFT JOIN users u ON rs.user_id = u.id ORDER BY r.created_at DESC')->fetchAll();
?>
<h3>報修管理（管理員）</h3>
<table class="table">
  <thead><tr><th>ID</th><th>地點/項目</th><th>描述</th><th>住民</th><th>狀態</th><th>建立時間</th><th>動作</th></tr></thead>
  <tbody>
  <?php foreach($repairs as $r): ?>
    <tr>
      <td><?=$r['id']?></td>
      <td><?=htmlspecialchars($r['location'].' / '.$r['item'])?></td>
      <td><?=nl2br(htmlspecialchars($r['description']))?></td>
      <td><?=htmlspecialchars($r['student_no'].' / '.$r['room_no'] . ' (' . ($r['username'] ?? '') . ')')?></td>
      <td><?=$r['status']?></td>
      <td><?=$r['created_at']?></td>
      <td><a class="btn btn-sm btn-secondary" href="repair_update.php?id=<?=$r['id']?>">編輯</a></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php require 'footer.php'; ?>
