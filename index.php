<?php
// =======================================================
// AI TEST MANAGEMENT SYSTEM
// Author : PLT Solution Internship Project
// File   : index.php
// Chức năng: Router chính của hệ thống
// =======================================================

session_start();

// =======================================================
// Nếu chưa đăng nhập thì chuyển về login
// (Tạm thời comment để thiết kế giao diện)
// =======================================================

// if (!isset($_SESSION['user'])) {
//     header("Location: login.php");
//     exit();
// }

// =======================================================
// Khai báo trang mặc định
// =======================================================

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// =======================================================
// Danh sách các trang hợp lệ
// =======================================================

$pages = [

    'dashboard',

    'testcase',

    'testcase_add',

    'testcase_edit',

    'testcase_view',

    'defect',

    'defect_add',

    'defect_edit',

    'ai',

    'reports',

    'settings',

    'profile'

];

// =======================================================
// Nếu nhập sai URL
// =======================================================

if (!in_array($page, $pages)) {
    $page = "dashboard";
}

// =======================================================
// Header
// =======================================================

include "includes/header.php";

?>

<div class="wrapper">

    <!-- Sidebar -->
    <?php include "includes/sidebar.php"; ?>

    <!-- Nội dung -->
    <main class="main-content">

        <?php

        $pageFile = "pages/" . $page . ".php";

        if (file_exists($pageFile)) {

            include $pageFile;

        } else {

            echo "<div class='page-not-found'>";

            echo "<h2>404</h2>";

            echo "<p>Không tìm thấy trang yêu cầu.</p>";

            echo "</div>";

        }

        ?>

    </main>

</div>

<?php

include "includes/footer.php";

?>

