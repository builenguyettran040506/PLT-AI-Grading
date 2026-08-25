<?php
session_start();

// 1. Xóa sạch Session cũ để đảm bảo không đụng chạm đến tài khoản thật
session_unset();
session_destroy();
session_start();

// 2. Khởi tạo các biến riêng biệt cho phiên dùng thử
$_SESSION['role'] = 'guest';
$_SESSION['username'] = 'Guest (Bản trải nghiệm)';
$_SESSION['user_id'] = 'demo_' . time();

// 3. Lấy mốc thời gian thực từ Server (Giúp F5 không bị reset giờ)
$_SESSION['trial_start'] = time(); 

// 4. Thông báo chào mừng
$_SESSION['toast_msg'] = "Chào mừng! Bạn có 30 phút trải nghiệm an toàn.";
$_SESSION['toast_type'] = "success";

// Chuyển hướng vào Dashboard
header("Location: index.php?page=dashboard");
exit;
?>