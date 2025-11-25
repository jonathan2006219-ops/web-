<?php
require 'db.php';
require 'auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: beverage_log.php');
  exit;
}

$id = $_POST['id'] ?? null;
if (!$id) {
  header('Location: beverage_log.php');
  exit;
}

// CSRF 檢查
check_csrf($_POST['csrf_token'] ?? '');

// admin 可刪除
if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin') {
  $del = $pdo->prepare('DELETE FROM beverage_logs WHERE id = ?');
  $del->execute([$id]);
  header('Location: beverage_log.php');
  exit;
}

// 非管理員皆無刪除權限
http_response_code(403);
die('僅限管理員可刪除飲料紀錄');
?>
