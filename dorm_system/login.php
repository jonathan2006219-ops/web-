<?php
require 'db.php';
require 'header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username']);
  $password = $_POST['password'];

  $stmt = $pdo->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
  $stmt->execute([$username]);
  $user = $stmt->fetch();
  if ($user && password_verify($password, $user['password_hash'])) {
    if (session_status() === PHP_SESSION_NONE) session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    header('Location: index.php');
    exit;
  } else {
    $err = '帳號或密碼錯誤';
  }
}
?>
<h3>登入</h3>
<?php if (!empty($err)) echo '<div class="alert alert-danger">'.htmlspecialchars($err).'</div>'; ?>
<form method="post">
  <div class="mb-3"><label class="form-label">帳號</label><input name="username" class="form-control"></div>
  <div class="mb-3"><label class="form-label">密碼</label><input type="password" name="password" class="form-control"></div>
  <button class="btn btn-primary">登入</button>
</form>
<?php require 'footer.php'; ?>
