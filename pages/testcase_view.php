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

try {
    // 1. Truy vấn thông tin chung của Test Case
    $stmt = $pdo->prepare("
        SELECT tc.*, m.module_name, p.name as priority_name, u.full_name as author_name 
        FROM test_cases tc
        LEFT JOIN modules m ON tc.module_id = m.id
        LEFT JOIN priorities p ON tc.priority_id = p.id
        LEFT JOIN users u ON tc.created_by = u.id
        WHERE tc.id = ?
    ");
    $stmt->execute([$tc_id]);
    $tc = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$tc) {
        echo "<script>alert('Test Case không tồn tại hoặc đã bị xóa!'); window.location.href='index.php?page=testcase';</script>";
        exit;
    }

    // 2. Truy vấn danh sách các bước thực hiện (Steps)
    $stmtSteps = $pdo->prepare("SELECT * FROM test_case_steps WHERE testcase_id = ? ORDER BY step_no ASC");
    $stmtSteps->execute([$tc_id]);
    $steps = $stmtSteps->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("<div class='p-4 bg-red-50 text-red-600'>Lỗi truy vấn: " . $e->getMessage() . "</div>");
}
?>

<!-- Header nội dung -->
<header class="flex flex-col md:flex-row md:items-center justify-between mb-6 gap-4">
    <div class="flex items-center gap-3">
        <a href="index.php?page=testcase" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-600 transition">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-xl font-bold text-slate-800"><?= htmlspecialchars($tc['tc_code']) ?></h1>
                <span class="px-2.5 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-600 border border-blue-200">
                    <?= htmlspecialchars($tc['module_name'] ?? 'N/A') ?>
                </span>
            </div>
            <p class="text-sm text-slate-500 mt-1"><?= htmlspecialchars($tc['title']) ?></p>
        </div>
    </div>
    
    <div class="flex items-center gap-2">
        <a href="index.php?page=testcase_edit&id=<?= $tc['id'] ?>" class="flex items-center gap-2 px-4 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 rounded-lg text-[13px] font-medium transition shadow-sm">
            <i class="fa-solid fa-pen-to-square"></i> Chỉnh sửa
        </a>
        <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-[13px] font-medium shadow-sm transition">
            <i class="fa-solid fa-print"></i> In/Xuất PDF
        </button>
    </div>
</header>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Cột Trái (Chiếm 2 phần): Nội dung chi tiết các bước -->
    <div class="lg:col-span-2 space-y-6">
        
        <!-- Preconditions -->
        <?php if (!empty($tc['preconditions'])): ?>
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 border-l-4 border-l-purple-500">
            <h3 class="text-[13px] font-bold text-slate-800 uppercase tracking-wide mb-2 flex items-center gap-2">
                <i class="fa-solid fa-list-check text-purple-500"></i> Tiền điều kiện (Preconditions)
            </h3>
            <p class="text-[13px] text-slate-600 leading-relaxed whitespace-pre-line"><?= htmlspecialchars($tc['preconditions']) ?></p>
        </div>
        <?php endif; ?>

        <!-- Các bước thực hiện (Procedure Steps) -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-[14px] font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-shoe-prints text-blue-500"></i> Các bước thực hiện (Procedure)
                </h3>
            </div>
            
            <div class="p-0">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[12px] font-bold text-slate-500">
                            <th class="px-5 py-3 w-16 text-center">Bước</th>
                            <th class="px-5 py-3">Thao tác (Action)</th>
                            <th class="px-5 py-3">Kết quả mong đợi (Expected Result)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php if (count($steps) > 0): ?>
                            <?php foreach ($steps as $step): ?>
                            <tr class="hover:bg-slate-50/30 transition-colors">
                                <td class="px-5 py-4 text-center align-top">
                                    <span class="w-6 h-6 inline-flex items-center justify-center rounded-full bg-slate-100 text-slate-600 text-[11px] font-bold">
                                        <?= htmlspecialchars($step['step_no']) ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4 align-top text-[13px] text-slate-700 font-medium whitespace-pre-line">
                                    <?= htmlspecialchars($step['action_text']) ?>
                                </td>
                                <td class="px-5 py-4 align-top text-[13px] text-slate-600 whitespace-pre-line bg-emerald-50/10">
                                    <?= htmlspecialchars($step['expected_result']) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="px-5 py-8 text-center text-[13px] text-slate-500">Chưa có bước thực hiện nào được định nghĩa.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Cột Phải (Chiếm 1 phần): Thông tin Meta -->
    <div class="space-y-6">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <h3 class="text-[14px] font-bold text-slate-800 mb-4 border-b border-slate-100 pb-3">Thông tin chi tiết</h3>
            
            <ul class="space-y-4">
                <li class="flex flex-col gap-1">
                    <span class="text-[12px] font-semibold text-slate-400 uppercase tracking-wider">Mức độ ưu tiên</span>
                    <?php
                        $prioColor = 'text-slate-700';
                        $priority = $tc['priority_name'];
                        if ($priority === 'Critical') $prioColor = 'text-red-600 font-bold';
                        elseif ($priority === 'High') $prioColor = 'text-orange-600 font-bold';
                        elseif ($priority === 'Medium') $prioColor = 'text-blue-600 font-semibold';
                    ?>
                    <span class="text-[14px] <?= $prioColor ?>"><?= htmlspecialchars($priority ?? 'N/A') ?></span>
                </li>
                
                <li class="flex flex-col gap-1">
                    <span class="text-[12px] font-semibold text-slate-400 uppercase tracking-wider">Người tạo</span>
                    <div class="flex items-center gap-2 mt-0.5">
                        <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-[11px] font-bold text-indigo-600">
                            <?= substr(htmlspecialchars($tc['author_name']), 0, 1) ?>
                        </div>
                        <span class="text-[13px] font-medium text-slate-700"><?= htmlspecialchars($tc['author_name']) ?></span>
                    </div>
                </li>
                
                <li class="flex flex-col gap-1">
                    <span class="text-[12px] font-semibold text-slate-400 uppercase tracking-wider">Ngày tạo</span>
                    <span class="text-[13px] font-medium text-slate-700">
                        <i class="fa-regular fa-calendar-days mr-1.5 text-slate-400"></i>
                        <?= date('H:i - d/m/Y', strtotime($tc['created_at'])) ?>
                    </span>
                </li>
                
                <li class="flex flex-col gap-1">
                    <span class="text-[12px] font-semibold text-slate-400 uppercase tracking-wider">Cập nhật lần cuối</span>
                    <span class="text-[13px] font-medium text-slate-700">
                        <i class="fa-solid fa-clock-rotate-left mr-1.5 text-slate-400"></i>
                        <?= date('H:i - d/m/Y', strtotime($tc['updated_at'])) ?>
                    </span>
                </li>
            </ul>
        </div>
    </div>
</div>