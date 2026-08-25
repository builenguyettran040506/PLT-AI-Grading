<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['defects_data'])) {
    $defects = json_decode($_POST['defects_data'], true);

    // Thiết lập Header để trình duyệt tải file xuống tự động
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Danh_Sach_Defects.csv');

    $output = fopen('php://output', 'w');
    
    // Thêm BOM (Byte Order Mark) để Excel nhận diện chuẩn định dạng UTF-8 (tránh lỗi tiếng Việt)
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Ghi hàng tiêu đề (Headers) cho file Excel
    fputcsv($output, array('DEFECT_ID', 'Title', 'Test Type', 'Area', 'Steps', 'Expected Result', 'Actual Result', 'Severity', 'Priority'));

    // Ghi từng dòng dữ liệu từ mảng giao diện gửi lên
    if (!empty($defects)) {
        foreach ($defects as $df) {
            fputcsv($output, array(
                $df['defect_code'] ?? '',
                $df['title'] ?? '',
                $df['test_type'] ?? '',
                $df['area'] ?? '',
                $df['steps'] ?? '',
                $df['expected_result'] ?? '',
                $df['actual_result'] ?? '',
                $df['severity'] ?? '',
                $df['priority'] ?? ''
            ));
        }
    }

    fclose($output);
    exit();
} else {
    // Nếu truy cập trực tiếp file mà không qua POST, đá về trang defect
    header("Location: index.php?page=defect");
    exit();
}
?>