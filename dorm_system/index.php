<?php
require 'db.php';
require 'header.php';
require 'auth.php';

// 判斷是否登入
if (!empty($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        // 管理員導向報修管理頁面
        header('Location: repair_list.php');
        exit;
    } else {
        // 住民留在本頁（不要導向回自己以免造成重導循環）
        // 若有專屬的住民頁面，可改為 header('Location: resident_page.php');
        // 這裡改為不做導向，讓後續頁面內容正常顯示。
        // (保持程式繼續執行)
    }
}
?>

<h3>歡迎使用宿舍管理系統</h3>
<p>請登入或註冊以使用系統功能：</p>
<a class="btn btn-primary" href="login.php">登入</a>
<a class="btn btn-secondary" href="register.php">註冊</a>

<?php require 'footer.php'; ?>
