<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['testcases_data'])) {
    $testcases = json_decode($_POST['testcases_data'], true);

    // Thiết lập Header để trình duyệt tải file CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=Test_Cases_Hien_Thi.csv');

    $output = fopen('php://output', 'w');
    // Fix lỗi tiếng Việt khi mở bằng Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Ghi tiêu đề cột
    fputcsv($output, array('Mã TC', 'Tiêu đề', 'Thời gian', 'Test Type', 'Phân hệ', 'Các bước thực hiện', 'Kết quả mong đợi', 'Priority', 'Trạng thái'));

    // Ghi từng dòng dữ liệu từ giao diện gửi lên
    if (!empty($testcases)) {
        foreach ($testcases as $tc) {
            fputcsv($output, array(
                $tc['tc_code'],
                $tc['title'],
                $tc['estimation'],
                $tc['test_type'],
                $tc['area'],
                $tc['procedure_steps'],
                $tc['expected_results'],
                $tc['priority'],
                $tc['result_status']
            ));
        }
    }

    fclose($output);
    exit();
}
?>