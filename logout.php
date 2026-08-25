<?php
// Khởi tạo session để có thể hủy nó
session_start();

// Xóa tất cả các biến session
$_SESSION = array();

// Hủy toàn bộ session
session_destroy();

// Chuyển hướng người dùng về trang đăng nhập (Thay đổi 'login.php' thành tên trang đăng nhập thực tế của bạn)
header("Location: login.php");
exit;
?>