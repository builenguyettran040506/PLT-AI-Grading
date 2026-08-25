<?php
// Gọi tệp kết nối CSDL
$db_path = file_exists('includes/database.php') ? 'includes/database.php' : '../includes/database.php';
require_once $db_path;

// Lấy ID từ URL
$tc_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($tc_id === 0) {
    echo "<script>alert('Không tìm thấy mã Test Case!'); window.location.href='index.php?page=testcase';</script>";
    exit;
}

// Xử lý khi Submit Form cập nhật
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $title = trim($_POST['title'] ?? '');
        $estimation = trim($_POST['estimation'] ?? '');
        $area = trim($_POST['area'] ?? '');
        $procedure = trim($_POST['procedure_steps'] ?? '');
        $expected = trim($_POST['expected_results'] ?? '');
        $priority_id = intval($_POST['priority_id'] ?? 2);
        $result_status = trim($_POST['result_status'] ?? 'Untested');

        // Cập nhật vào CSDL
        $sql = "UPDATE test_cases SET title=?, estimation=?, area=?, procedure_steps=?, expected_results=?, priority_id=?, result_status=? WHERE id=?";
        $pdo->prepare($sql)->execute([$title, $estimation, $area, $procedure, $expected, $priority_id, $result_status, $tc_id]);

        echo "<script>alert('Cập nhật Test Case thành công!'); window.location.href='index.php?page=testcase';</script>";
        exit;
    } catch (Exception $e) {
        $error_msg = "Lỗi khi cập nhật Test Case: " . $e->getMessage();
    }
}

// Lấy dữ liệu Test Case hiện tại để đổ vào Form
try {
    $stmt = $pdo->prepare("SELECT * FROM test_cases WHERE id = ?");
    $stmt->execute([$tc_id]);
    $tc = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tc) {
        echo "<script>alert('Test Case không tồn tại hoặc đã bị xóa!'); window.location.href='index.php?page=testcase';</script>";
        exit;
    }
    
    // Khai báo mảng Priority chuẩn theo hệ thống
    $priorities = [
        ['id' => 1, 'name' => 'Low'],
        ['id' => 2, 'name' => 'Medium'],
        ['id' => 3, 'name' => 'High'],
        ['id' => 4, 'name' => 'Critical']
    ];
    
} catch (PDOException $e) {
    die("<div class='p-4 bg-red-50 text-red-600'>Lỗi truy vấn: " . $e->getMessage() . "</div>");
}
?>

<!-- Header nội dung -->
<header class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="index.php?page=testcase" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-bold text-slate-800">Chỉnh sửa kịch bản</h1>
                <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-indigo-50 text-indigo-600 border border-indigo-200">
                    <?= htmlspecialchars($tc['tc_code'] ?? 'TC_N/A') ?>
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-0.5">Cập nhật thông tin chi tiết cho Test Case</p>
        </div>
    </div>
</header>

<!-- Khung Form Nhập Liệu -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
    
    <?php if (isset($error_msg)): ?>
        <div class="p-3 mb-6 bg-red-50 text-red-600 rounded-lg text-sm border border-red-100">
            <i class="fa-solid fa-triangle-exclamation mr-1"></i> <?= $error_msg ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="index.php?page=testcase_edit&id=<?= $tc_id ?>">
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Tiêu đề -->
            <div class="md:col-span-2">
                <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Tiêu đề kịch bản (Title) <span class="text-red-500">*</span></label>
                <input type="text" name="title" required value="<?= htmlspecialchars($tc['title'] ?? '') ?>" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-800">
            </div>

            <!-- Khu vực & Thời gian -->
            <div>
                <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Khu vực (Area)</label>
                <input type="text" name="area" value="<?= htmlspecialchars($tc['area'] ?? '') ?>" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-800">
            </div>
            
            <div>
                <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Thời gian dự kiến (Estimation)</label>
                <input type="text" name="estimation" value="<?= htmlspecialchars($tc['estimation'] ?? '') ?>" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-800">
            </div>

            <!-- Priority & Status -->
            <div>
                <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Mức độ ưu tiên (Priority)</label>
                <select name="priority_id" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-800 bg-white">
                    <?php $currentPrio = $tc['priority_id'] ?? 2; ?>
                    <?php foreach ($priorities as $pri): ?>
                        <option value="<?= $pri['id'] ?>" <?= ($currentPrio == $pri['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($pri['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Trạng thái (Result Status)</label>
                <select name="result_status" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-800 bg-white">
                    <?php $currentStatus = $tc['result_status'] ?? 'Untested'; ?>
                    <option value="Untested" <?= ($currentStatus === 'Untested') ? 'selected' : '' ?>>Untested</option>
                    <option value="Passed" <?= ($currentStatus === 'Passed') ? 'selected' : '' ?>>Passed</option>
                    <option value="Failed" <?= ($currentStatus === 'Failed') ? 'selected' : '' ?>>Failed</option>
                </select>
            </div>
        </div>

        <hr class="border-slate-100 mb-6">

        <!-- Các bước thực hiện -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-2">
                <label class="block text-[14px] font-bold text-slate-800">Các bước thực hiện (Procedure Steps) <span class="text-red-500">*</span></label>
                <span class="text-[11px] font-medium text-emerald-600 bg-emerald-50 px-2 py-1 rounded border border-emerald-100">
                    <i class="fa-solid fa-check mr-1"></i> Bắt buộc bắt đầu bằng "Access the website"
                </span>
            </div>
            <textarea name="procedure_steps" rows="5" required class="w-full border border-slate-300 rounded-lg px-4 py-3 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-800 leading-relaxed"><?= htmlspecialchars($tc['procedure_steps'] ?? '') ?></textarea>
        </div>

        <!-- Kết quả mong đợi -->
        <div class="mb-8">
            <label class="block text-[14px] font-bold text-slate-800 mb-2">Kết quả mong đợi (Expected Results) <span class="text-red-500">*</span></label>
            <textarea name="expected_results" rows="3" required class="w-full border border-slate-300 rounded-lg px-4 py-3 text-[13px] focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-800 bg-emerald-50/20 leading-relaxed"><?= htmlspecialchars($tc['expected_results'] ?? '') ?></textarea>
        </div>

        <!-- Các nút thao tác -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="index.php?page=testcase" class="px-5 py-2.5 text-[13px] font-medium text-slate-600 hover:bg-slate-100 rounded-lg transition">Hủy bỏ</a>
            <button type="submit" class="px-6 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-[13px] font-medium rounded-lg shadow-sm shadow-amber-500/30 transition flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi
            </button>
        </div>
    </form>
</div>