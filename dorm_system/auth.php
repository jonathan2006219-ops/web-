<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function require_login() {
  if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
  }
}

function require_admin() {
  require_login();
  if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('此功能僅限管理員');
  }
}

function current_user_id() { return $_SESSION['user_id'] ?? null; }
function current_user_role() { return $_SESSION['role'] ?? null; }
function current_username() { return $_SESSION['username'] ?? null; }

// CSRF helpers
function csrf_token() {
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf_token'];
}

function check_csrf($token) {
  if (empty($token) || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
    http_response_code(400);
    die('CSRF token invalid');
  }
}
?>