<?php
require 'db.php';
require 'header.php';

$activities = $pdo->query('SELECT a.*, u.username FROM activities a LEFT JOIN users u ON a.created_by = u.id ORDER BY activity_date DESC')->fetchAll();
?>
<h3>活動列表</h3>
<table class="table">
  <thead><tr><th>ID</th><th>名稱</th><th>日期</th><th>建立者</th><th>簽到頁</th></tr></thead>
  <tbody>
  <?php foreach($activities as $a): ?>
    <tr>
      <td><?=$a['id']?></td>
      <td><?=htmlspecialchars($a['title'])?></td>
      <td><?=$a['activity_date']?></td>
      <td><?=htmlspecialchars($a['username'])?></td>
      <td><a class="btn btn-sm btn-success" href="activity_day.php?id=<?=$a['id']?>">簽到頁</a></td>
    </tr>
  <?php endforeach; ?>
  </tbody>
</table>
<?php require 'footer.php'; ?>
