<?php
session_start();

// 1. KIỂM SOÁT THỜI GIAN PHIÊN DÙNG THỬ (GUEST)
if (isset($_SESSION['role']) && $_SESSION['role'] === 'guest') {
    if (isset($_SESSION['trial_start'])) {
        if (time() - $_SESSION['trial_start'] >= 1800) { // 30 phút = 1800s
            session_unset(); session_destroy(); session_start();
            $_SESSION['toast_msg'] = "Đã hết phiên dùng thử. Vui lòng đăng ký để sử dụng tiếp!";
            $_SESSION['toast_type'] = "error";
            header("Location: landing.php"); 
            exit();
        }
    } else {
        $_SESSION['trial_start'] = time();
    }
}

// 2. KIỂM TRA ĐĂNG NHẬP
$isRealUser = isset($_SESSION['user']);
$isGuest = (isset($_SESSION['role']) && $_SESSION['role'] === 'guest');

if (!$isRealUser && !$isGuest) {
    header("Location: login.php");
    exit();
}

// 3. ĐỊNH TUYẾN TRANG (ROUTER)
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
$valid_pages = ['dashboard', 'testcase', 'testcase_add', 'testcase_edit', 'testcase_view', 'defect', 'defect_add', 'defect_edit', 'ai', 'reports', 'settings', 'profile'];
if (!in_array($page, $valid_pages)) { 
    $page = 'dashboard'; 
}

// 4. RENDER GIAO DIỆN CHUẨN
include "includes/header.php";
?>
<!-- Bao bọc toàn bộ trang -->
<div class="wrapper">
    <!-- Sidebar cố định bên trái -->
    <?php include "includes/sidebar.php"; ?>
    
    <!-- Khu vực nội dung chính bên phải -->
    <main class="main-content">
        <?php
        $pageFile = "pages/" . $page . ".php";
        if (file_exists($pageFile)) {
            include $pageFile;
        } else {
            echo "<div class='p-8 text-center text-red-500 font-bold'>Lỗi 404: Không tìm thấy nội dung!</div>";
        }
        ?>
    </main>
</div>

<?php include "includes/footer.php"; ?>