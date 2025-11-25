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

<?php
// 處理表單提交：可為報修（default）或建立/更新住民資料（action=create_resident）
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF 保護
    check_csrf($_POST['csrf_token'] ?? '');
    require_login(); // 確保已登入
    $user_id = current_user_id();

    $action = $_POST['action'] ?? 'create_repair';

    if ($action === 'create_resident') {
        // 建立或更新 residents
        $student_no = trim($_POST['student_no'] ?? '');
        $room_no = trim($_POST['room_no'] ?? '');
        $name = trim($_POST['name'] ?? '');

        $errors = [];
        if ($student_no === '') $errors[] = '請填寫學號/工號';
        if ($room_no === '') $errors[] = '請填寫房號';

        if (empty($errors)) {
            // 若已有 residents（以 user_id 關聯）則更新，否則插入
            $stmt = $pdo->prepare('SELECT id FROM residents WHERE user_id = ? LIMIT 1');
            $stmt->execute([$user_id]);
            $row = $stmt->fetch();
            if ($row) {
                $upd = $pdo->prepare('UPDATE residents SET student_no = ?, room_no = ?, name = ? WHERE id = ?');
                $upd->execute([$student_no, $room_no, $name, $row['id']]);
            } else {
                $ins = $pdo->prepare('INSERT INTO residents (user_id, student_no, room_no, name) VALUES (?, ?, ?, ?)');
                $ins->execute([$user_id, $student_no, $room_no, $name]);
            }
            header('Location: index.php?resident_created=1');
            exit;
        }

    } else {
        // 預設為建立報修
        $location = trim($_POST['location'] ?? '');
        $item = trim($_POST['item'] ?? '');
        $description = trim($_POST['description'] ?? '');

        // 盡量找出對應的 residents.id（若使用者有綁定住民資料）
        $stmt = $pdo->prepare('SELECT id FROM residents WHERE user_id = ? LIMIT 1');
        $stmt->execute([$user_id]);
        $res = $stmt->fetch();
        $resident_id = $res ? $res['id'] : null;

        // 簡單驗證
        $errors = [];
        if ($location === '') $errors[] = '請填寫地點';
        if ($item === '') $errors[] = '請填寫項目';

        if (empty($errors)) {
            $ins = $pdo->prepare('INSERT INTO repairs (resident_id, location, item, description, status, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
            $ins->execute([$resident_id, $location, $item, $description, '待處理']);
            header('Location: index.php?created=1');
            exit;
        }
    }
}
?>

<h3>歡迎使用宿舍管理系統</h3>

<?php if (!empty($_SESSION['user_id']) && $_SESSION['role'] !== 'admin'): ?>
    <p>您好，<?=htmlspecialchars(current_username())?> ，您可以在此提交新的報修：</p>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger"><?php foreach($errors as $e) echo htmlspecialchars($e) . '<br>'; ?></div>
    <?php endif; ?>

    <?php if (!empty($_GET['created'])): ?>
        <div class="alert alert-success">已建立報修，管理員將會處理。</div>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?=htmlspecialchars(csrf_token())?>">
        <div class="mb-3"><label class="form-label">地點</label><input name="location" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">項目</label><input name="item" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">描述</label><textarea name="description" class="form-control"></textarea></div>
        <button class="btn btn-primary">送出報修</button>
    </form>

        <hr>
        <h4>我的報修紀錄</h4>
        <?php
            // 嘗試找到該使用者對應的 residents.id
            $stmt = $pdo->prepare('SELECT id FROM residents WHERE user_id = ? LIMIT 1');
            $stmt->execute([current_user_id()]);
            $resRow = $stmt->fetch();
            if ($resRow) {
                $rid = $resRow['id'];
                $rp = $pdo->prepare('SELECT * FROM repairs WHERE resident_id = ? ORDER BY created_at DESC');
                $rp->execute([$rid]);
                $myRepairs = $rp->fetchAll();
                if ($myRepairs) {
        ?>
            <table class="table">
                <thead><tr><th>ID</th><th>地點/項目</th><th>描述</th><th>狀態</th><th>建立時間</th></tr></thead>
                <tbody>
                <?php foreach($myRepairs as $r): ?>
                    <tr>
                        <td><?=$r['id']?></td>
                        <td><?=htmlspecialchars($r['location'].' / '.$r['item'])?></td>
                        <td><?=nl2br(htmlspecialchars($r['description']))?></td>
                        <td><?=$r['status']?></td>
                        <td><?=$r['created_at']?></td>
                                <td>
                                    <!-- 住民可刪除自己的報修 -->
                                    <form method="post" action="repair_delete.php" style="display:inline-block" onsubmit="return confirm('確定要刪除此筆報修嗎？');">
                                        <input type="hidden" name="id" value="<?=$r['id']?>">
                                        <input type="hidden" name="csrf_token" value="<?=htmlspecialchars(csrf_token())?>">
                                        <button class="btn btn-sm btn-danger">刪除</button>
                                    </form>
                                </td>
                            </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php
                } else {
                    echo '<div class="alert alert-secondary">目前尚無報修紀錄。</div>';
                }
                } else {
                    // 顯示住民資料表單，讓使用者自行建立或更新
                    $student_no_val = '';
                    $room_no_val = '';
                    $name_val = '';
                    if (!empty($errors) && isset($_POST['action']) && $_POST['action']==='create_resident') {
                        $student_no_val = htmlspecialchars($_POST['student_no'] ?? '');
                        $room_no_val = htmlspecialchars($_POST['room_no'] ?? '');
                        $name_val = htmlspecialchars($_POST['name'] ?? '');
                    }
                    ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title">建立或更新住民資料</h5>
                            <?php if (!empty($errors) && isset($_GET['resident_created'])===false): ?>
                                <div class="alert alert-danger"><?php foreach($errors as $e) echo htmlspecialchars($e).'<br>'; ?></div>
                            <?php endif; ?>
                            <?php if (!empty($_GET['resident_created'])): ?>
                                <div class="alert alert-success">已建立/更新住民資料。</div>
                            <?php endif; ?>
                            <form method="post">
                                <input type="hidden" name="action" value="create_resident">
                                <input type="hidden" name="csrf_token" value="<?=htmlspecialchars(csrf_token())?>">
                                <div class="mb-3"><label class="form-label">學號/工號</label><input name="student_no" class="form-control" value="<?= $student_no_val ?>" required></div>
                                <div class="mb-3"><label class="form-label">房號</label><input name="room_no" class="form-control" value="<?= $room_no_val ?>" required></div>
                                <div class="mb-3"><label class="form-label">姓名（選填）</label><input name="name" class="form-control" value="<?= $name_val ?>"></div>
                                <button class="btn btn-primary">儲存住民資料</button>
                            </form>
                        </div>
                    </div>
                    <?php
                }
        ?>

<?php else: ?>
    <p>請登入或註冊以使用系統功能：</p>
    <a class="btn btn-primary" href="login.php">登入</a>
    <a class="btn btn-secondary" href="register.php">註冊</a>
<?php endif; ?>

<?php require 'footer.php'; ?>
