<?php
require 'db.php';
require 'auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: repair_list.php');
  exit;
}

$id = $_POST['id'] ?? null;
if (!$id) {
  header('Location: repair_list.php');
  exit;
}

// CSRF 檢查
check_csrf($_POST['csrf_token'] ?? '');

// 若為 admin，允許刪除任何紀錄
if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') {
  $del = $pdo->prepare('DELETE FROM repairs WHERE id = ?');
  $del->execute([$id]);
  header('Location: repair_list.php');
  exit;
}

// 否則檢查使用者是否為該報修的擁有者（透過 residents.user_id）
require_login();
$user_id = current_user_id();

$stmt = $pdo->prepare('SELECT r.id FROM repairs r JOIN residents rs ON r.resident_id = rs.id WHERE r.id = ? AND rs.user_id = ? LIMIT 1');
$stmt->execute([$id, $user_id]);
$row = $stmt->fetch();
if ($row) {
  $del = $pdo->prepare('DELETE FROM repairs WHERE id = ?');
  $del->execute([$id]);
  header('Location: index.php');
  exit;
}

// 無權限
die('無刪除權限');
?>
