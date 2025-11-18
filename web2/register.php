<?php
require 'db.php';
require 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = $_POST['password'];
  $name = trim($_POST['name']);
  $student_no = trim($_POST['student_no'] ?? '');
  $room_no = trim($_POST['room_no'] ?? '');
  $role = in_array($_POST['role'] ?? 'resident', ['resident','admin']) ? $_POST['role'] : 'resident';

  if (!$username || !$password) {
    $err = '請輸入帳號與密碼';
  } else {
    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare('INSERT INTO users (username, password_hash, name, role) VALUES (?, ?, ?, ?)');
    try {
      $stmt->execute([$username, $hash, $name, $role]);
      $user_id = $pdo->lastInsertId();
      if ($role === 'resident') {
        $ins = $pdo->prepare('INSERT INTO residents (user_id, student_no, room_no, name) VALUES (?, ?, ?, ?)');
        $ins->execute([$user_id, $student_no, $room_no, $name]);
      }
      echo '<div class="alert alert-success">註冊成功，請 <a href="login.php">登入</a></div>';
    } catch (Exception $e) {
      $err = '帳號已存在或資料庫錯誤';
    }
  }
}
?>
<h3>註冊</h3>
<?php if (!empty($err)) echo '<div class="alert alert-danger">'.htmlspecialchars($err).'</div>'; ?>
<form method="post">
  <div class="mb-3"><label class="form-label">帳號 (username)</label><input name="username" class="form-control"></div>
  <div class="mb-3"><label class="form-label">密碼</label><input type="password" name="password" class="form-control"></div>
  <div class="mb-3"><label class="form-label">姓名</label><input name="name" class="form-control"></div>
  <div class="mb-3"><label class="form-label">學號（住民）</label><input name="student_no" class="form-control"></div>
  <div class="mb-3"><label class="form-label">房號（住民）</label><input name="room_no" class="form-control"></div>
  <div class="mb-3"><label class="form-label">角色</label>
    <select name="role" class="form-select">
      <option value="resident">住民</option>
      <option value="admin">管理員</option>
    </select>
  </div>
  <button class="btn btn-primary">註冊</button>
</form>
<?php require 'footer.php'; ?>
