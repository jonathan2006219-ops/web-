<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!doctype html>
<html lang="zh-Hant">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>宿舍管理系統</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<nav class="navbar navbar-expand-lg navbar-light bg-light mb-4">
  <div class="container-fluid">
    <a class="navbar-brand" href="/">宿舍管理系統</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="index.php">報修</a></li>
        <li class="nav-item"><a class="nav-link" href="activity_list.php">活動管理</a></li>
        <li class="nav-item"><a class="nav-link" href="beverage_log.php">飲料機紀錄</a></li>
      </ul>
      <ul class="navbar-nav">
        <?php if (!empty($_SESSION['user_id'])): ?>
          <li class="nav-item"><span class="nav-link">歡迎，<?=htmlspecialchars($_SESSION['username'])?></span></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">登出</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">登入</a></li>
          <li class="nav-item"><a class="nav-link" href="register.php">註冊</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
<div class="container">
