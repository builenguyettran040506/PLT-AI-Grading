<?php
// Kết nối database tùy theo cấu hình của bạn (ví dụ dùng file config hoặc kết nối trực tiếp)
// Nếu project có sẵn file kết nối (như db.php hay config.php), bạn hãy require vào đây.
// Ví dụ: require_once 'includes/db.php'; 

$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'ai_test_management';

$conn = mysqli_connect($host, $username, $password, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");

// Thiết lập header để trình duyệt hiểu đây là file tải về định dạng CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Danh_Sach_Test_Case.csv');

$output = fopen('php://output', 'w');

// Thêm BOM UTF-8 để mở bằng Excel không bị lỗi font tiếng Việt
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Ghi tiêu đề các cột
fputcsv($output, array('Mã TC', 'Tiêu đề', 'Thời gian', 'Loại kiểm thử', 'Phân hệ', 'Các bước thực hiện', 'Kết quả mong đợi', 'Priority', 'Trạng thái'));
// Truy vấn lấy dữ liệu từ bảng test_cases
$query = "SELECT tc_code, title, estimation, area, procedure_steps, expected_results, result_status FROM test_cases ORDER BY id DESC";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, $row);
}

fclose($output);
exit();
?>