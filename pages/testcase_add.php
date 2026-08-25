<?php
// Gọi tệp kết nối CSDL
$db_path = file_exists('includes/database.php') ? 'includes/database.php' : '../includes/database.php';
require_once $db_path;

// Lấy danh sách Module và Priority để đổ vào Dropdown
try {
    $modules = $pdo->query("SELECT * FROM modules")->fetchAll(PDO::FETCH_ASSOC);
    $priorities = $pdo->query("SELECT * FROM priorities")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Lỗi truy vấn: " . $e->getMessage());
}

// Xử lý khi Submit Form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();

        // 1. Lưu thông tin chung của Test Case
        $stmtTC = $pdo->prepare("INSERT INTO test_cases (tc_code, module_id, title, preconditions, priority_id, created_by) VALUES (?, ?, ?, ?, ?, ?)");
        
        // Tạo mã TC tự động (Ví dụ: TC004)
        $stmtMax = $pdo->query("SELECT tc_code FROM test_cases ORDER BY id DESC LIMIT 1");
        $maxCode = $stmtMax->fetchColumn();
        $nextNum = $maxCode ? intval(substr($maxCode, 2)) + 1 : 1;
        $tc_code = "TC" . str_pad($nextNum, 4, '0', STR_PAD_LEFT);

        $stmtTC->execute([
            $tc_code,
            $_POST['module_id'],
            $_POST['title'],
            $_POST['preconditions'],
            $_POST['priority_id'],
            $_SESSION['user']['id'] // Lấy ID người tạo từ Session đăng nhập
        ]);
        
        $testcase_id = $pdo->lastInsertId();

        // 2. Lưu các bước thực hiện (Steps)
        if (isset($_POST['steps']) && is_array($_POST['steps'])) {
            $stmtStep = $pdo->prepare("INSERT INTO test_case_steps (testcase_id, step_no, action_text, expected_result) VALUES (?, ?, ?, ?)");
            $step_no = 1;
            
            // Expected result tổng tổng quát lưu ở bước cuối, hoặc phân bổ tùy thiết kế
            // Ở form này, ta gom expected result vào bước cuối cùng để đơn giản hóa UI
            $expected = $_POST['expected_result'] ?? '';

            foreach ($_POST['steps'] as $index => $action) {
                $step_expected = ($index === count($_POST['steps']) - 1) ? $expected : ''; 
                $stmtStep->execute([$testcase_id, $step_no, $action, $step_expected]);
                $step_no++;
            }
        }

        $pdo->commit();
        echo "<script>alert('Thêm Test Case thành công!'); window.location.href='index.php?page=testcase';</script>";
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $error_msg = "Lỗi khi lưu Test Case: " . $e->getMessage();
    }
}
?>

<!-- Header nội dung -->
<header class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="index.php?page=testcase" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl font-bold text-slate-800">Tạo Test Case mới</h1>
            <p class="text-sm text-slate-500 mt-0.5">Xây dựng kịch bản kiểm thử tiêu chuẩn</p>
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

    <form method="POST" action="index.php?page=testcase_add" id="testcaseForm">
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Cột Trái: Thông tin chung (Chiếm 2 phần) -->
            <div class="md:col-span-2 space-y-5">
                <div>
                    <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Tiêu đề kịch bản (Title) <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required placeholder="VD: Đăng nhập với tài khoản không tồn tại..." class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-800">
                </div>

                <div>
                    <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Tiền điều kiện (Preconditions)</label>
                    <textarea name="preconditions" rows="2" placeholder="Các điều kiện cần thiết trước khi thực hiện test..." class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-800"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Module (Phân hệ) <span class="text-red-500">*</span></label>
                        <select name="module_id" required class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-800 bg-white">
                            <option value="">-- Chọn Module --</option>
                            <?php foreach ($modules as $mod): ?>
                                <option value="<?= $mod['id'] ?>"><?= htmlspecialchars($mod['module_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Mức độ ưu tiên (Priority)</label>
                        <select name="priority_id" class="w-full border border-slate-300 rounded-lg px-4 py-2.5 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-800 bg-white">
                            <?php foreach ($priorities as $pri): ?>
                                <option value="<?= $pri['id'] ?>" <?= $pri['name'] == 'Medium' ? 'selected' : '' ?>><?= htmlspecialchars($pri['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Cột Phải: Note/Hướng dẫn (Chiếm 1 phần) -->
            <div class="bg-blue-50/50 rounded-xl p-5 border border-blue-100">
                <h3 class="text-sm font-bold text-blue-800 mb-2"><i class="fa-solid fa-circle-info mr-1"></i> Hướng dẫn nhập liệu</h3>
                <ul class="text-[12px] text-slate-600 space-y-2 list-disc pl-4">
                    <li>Tiêu đề cần ngắn gọn, mô tả đúng mục đích kiểm thử.</li>
                    <li><strong>Bắt buộc:</strong> Mọi bước thao tác đều đã được tự động thêm tiền tố chuẩn hóa để đảm bảo đồng bộ hệ thống.</li>
                    <li>Viết kết quả mong đợi (Expected Result) rõ ràng, dễ đối chiếu.</li>
                </ul>
            </div>
        </div>

        <hr class="border-slate-100 mb-8">

        <!-- Phần Các bước thực hiện -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <label class="block text-[14px] font-bold text-slate-800">Các bước thực hiện (Procedure Steps)</label>
                <button type="button" onclick="addStepRow()" class="flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 rounded text-[12px] font-semibold transition border border-emerald-200">
                    <i class="fa-solid fa-plus"></i> Thêm bước
                </button>
            </div>

            <div id="steps-container" class="space-y-3">
                <!-- Bước 1 (Mặc định) -->
                <div class="flex gap-3 items-start step-row">
                    <div class="w-8 h-8 shrink-0 rounded-full bg-blue-100 flex items-center justify-center text-sm font-bold text-blue-600 mt-0.5 step-number">1</div>
                    <div class="flex-1">
                        <textarea name="steps[]" rows="2" required class="w-full border border-slate-300 rounded-lg px-4 py-2 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-800">Access the website </textarea>
                    </div>
                    <div class="w-8 shrink-0"></div> <!-- Spacer để căn lề nút xóa -->
                </div>
            </div>
        </div>

        <!-- Phần Kết quả mong đợi -->
        <div class="mb-8">
            <label class="block text-[14px] font-bold text-slate-800 mb-2">Kết quả mong đợi (Expected Result) <span class="text-red-500">*</span></label>
            <textarea name="expected_result" rows="3" required placeholder="Mô tả kết quả hệ thống sẽ trả về sau khi thực hiện xong các bước trên..." class="w-full border border-slate-300 rounded-lg px-4 py-3 text-[13px] focus:outline-none focus:ring-2 focus:ring-emerald-500 text-slate-800 bg-emerald-50/20"></textarea>
        </div>

        <!-- Nút Submit -->
        <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
            <a href="index.php?page=testcase" class="px-5 py-2.5 text-[13px] font-medium text-slate-600 hover:bg-slate-100 rounded-lg transition">Hủy bỏ</a>
            <button type="submit" class="px-6 py-2.5 bg-[#2563EB] hover:bg-blue-700 text-white text-[13px] font-medium rounded-lg shadow-sm shadow-blue-500/30 transition flex items-center gap-2">
                <i class="fa-solid fa-floppy-disk"></i> Lưu Test Case
            </button>
        </div>
    </form>
</div>

<!-- JAVASCRIPT XỬ LÝ THÊM BƯỚC -->
<script>
    function updateStepNumbers() {
        const rows = document.querySelectorAll('.step-row');
        rows.forEach((row, index) => {
            row.querySelector('.step-number').textContent = index + 1;
        });
    }

    function addStepRow() {
        const container = document.getElementById('steps-container');
        const stepCount = container.querySelectorAll('.step-row').length + 1;
        
        const stepDiv = document.createElement('div');
        stepDiv.className = 'flex gap-3 items-start step-row';
        stepDiv.innerHTML = `
            <div class="w-8 h-8 shrink-0 rounded-full bg-blue-100 flex items-center justify-center text-sm font-bold text-blue-600 mt-0.5 step-number">${stepCount}</div>
            <div class="flex-1">
                <!-- Tiền tố chuẩn hóa được chèn tự động -->
                <textarea name="steps[]" rows="2" required class="w-full border border-slate-300 rounded-lg px-4 py-2 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-800">Access the website </textarea>
            </div>
            <button type="button" onclick="this.closest('.step-row').remove(); updateStepNumbers();" class="w-8 h-8 shrink-0 rounded flex items-center justify-center text-red-400 hover:text-red-600 hover:bg-red-50 mt-0.5 transition">
                <i class="fa-solid fa-trash-can"></i>
            </button>
        `;
        
        container.appendChild(stepDiv);
    }
</script>