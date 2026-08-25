<?php
// Gọi kết nối Database
require_once 'includes/database.php';

// Mật khẩu muốn đặt lại cho tất cả tài khoản
$password = '123456';

// Tạo mã băm chuẩn xác từ PHP của máy bạn
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    // Cập nhật lại toàn bộ mật khẩu trong bảng users
    $stmt = $pdo->prepare("UPDATE users SET password_hash = :hash");
    $stmt->execute([':hash' => $hash]);
    
    echo "<h2 style='color: green;'>Đã sửa lỗi mật khẩu thành công!</h2>";
    echo "<p>Tất cả tài khoản (admin, qa, tester1) đều đã được đặt lại mật khẩu chuẩn là: <b>123456</b></p>";
    echo "<a href='login.php'>Bấm vào đây để quay lại trang đăng nhập</a>";
} catch (Exception $e) {
    echo "Lỗi: " . $e->getMessage();
}
?>