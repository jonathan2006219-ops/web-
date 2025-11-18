<?php
require 'db.php';

$activity_id = $_POST['activity_id'] ?? null;
$student_or_room = trim($_POST['student_or_room'] ?? '');
if (!$activity_id || !$student_or_room) { header('Location: activity_day.php?id=' . urlencode($activity_id)); exit; }

$stmt = $pdo->prepare('SELECT * FROM residents WHERE student_no = ? OR room_no = ? LIMIT 1');
$stmt->execute([$student_or_room, $student_or_room]);
$res = $stmt->fetch();
$resident_id = $res ? $res['id'] : null;

$check = $pdo->prepare('SELECT COUNT(*) FROM activity_signups WHERE activity_id = ? AND student_or_room = ?');
$check->execute([$activity_id, $student_or_room]);
if ($check->fetchColumn() > 0) {
  header('Location: activity_day.php?id=' . urlencode($activity_id));
  exit;
}

$ins = $pdo->prepare('INSERT INTO activity_signups (activity_id, resident_id, student_or_room) VALUES (?, ?, ?)');
$ins->execute([$activity_id, $resident_id, $student_or_room]);

header('Location: activity_day.php?id=' . urlencode($activity_id));
exit;
?>