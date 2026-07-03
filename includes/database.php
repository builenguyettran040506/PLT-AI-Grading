<?php
// =======================================================
// File: includes/database.php
// Chức năng: Kết nối CSDL MySQL bằng PDO
// =======================================================

// 1. Cấu hình thông tin kết nối
$host = 'localhost';
$dbname = 'ai_test_management'; 
$username = 'root'; // Tên người dùng mặc định của XAMPP/WAMP
$password = '';     // Mật khẩu mặc định của XAMPP/WAMP (thường để rỗng)

// 2. Thực hiện kết nối
try {
    // Khởi tạo đối tượng kết nối $pdo
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Thiết lập chế độ báo lỗi: Ném ra ngoại lệ (Exception) khi có lỗi SQL
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch(PDOException $e) {
    // Nếu kết nối thất bại, dừng toàn bộ trang web và in ra lỗi
    die("<div style='background:#fee2e2; color:#991b1b; padding:15px; border-radius:8px; font-family:sans-serif;'>
            <strong>Lỗi kết nối Database!</strong><br>
            Vui lòng kiểm tra lại máy chủ MySQL (XAMPP/WAMP) đã được bật chưa.<br>
            <i>Chi tiết lỗi: " . $e->getMessage() . "</i>
         </div>");
}
?>