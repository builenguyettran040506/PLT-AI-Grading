<?php
require_once 'includes/database.php';

// XỬ LÝ THÊM DEFECT MỚI
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_defect'])) {
    $title = trim($_POST['title']);
    $severity = $_POST['severity'];
    
    // Tạo Defect Code tiếp theo
    $stmtId = $pdo->query("SELECT MAX(CAST(SUBSTRING(defect_code, 5) AS UNSIGNED)) as max_id FROM defects");
    $row = $stmtId->fetch();
    $nextNum = ($row['max_id']) ? $row['max_id'] + 1 : 1045;
    $defect_code = "BUG-" . $nextNum;

    // AI Phân tích giả lập
    $lowerTitle = mb_strtolower($title, 'UTF-8');
    if (strpos($lowerTitle, 'api') !== false || strpos($lowerTitle, 'timeout') !== false) {
        $aiScore = rand(90, 98);
        $aiFeedback = "Lỗi logic hệ thống liên kết. Đề xuất rà soát mã trạng thái HTTP trả về từ server, tăng timeout.";
    } elseif (strpos($lowerTitle, 'ui') !== false || strpos($lowerTitle, 'lệch') !== false) {
        $aiScore = rand(60, 75);
        $aiFeedback = "Lỗi giao diện front-end. Cần kiểm tra lại CSS định dạng responsive layout.";
    } else {
        $aiScore = rand(50, 80); 
        $aiFeedback = "Tiêu đề defect cần bổ sung thêm chi tiết bước tái hiện (Steps to reproduce).";
    }

    // Insert vào CSDL (bảng defects)
    $stmt = $pdo->prepare("INSERT INTO defects (defect_code, title, severity, status, ai_confidence, ai_suggestion) VALUES (:code, :title, :severity, 'Open', :score, :suggestion)");
    $stmt->execute([
        ':code' => $defect_code,
        ':title' => $title,
        ':severity' => $severity,
        ':score' => $aiScore,
        ':suggestion' => $aiFeedback
    ]);
    
    // Redirect để tránh resubmit form
    header("Location: index.php?page=defect");
    exit;
}

// LẤY DANH SÁCH LỖI
$stmt = $pdo->query("SELECT * FROM defects ORDER BY id DESC");
$defects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<header class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold text-slate-800">Danh sách Defect</h1>
    <div class="flex items-center gap-6">
        <div class="flex items-center gap-2 bg-slate-200/50 px-3 py-1.5 rounded-full border border-slate-200">
            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
            <span class="text-sm font-medium text-slate-600">AI Engine: Online</span>
        </div>
        <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-md">QA</div>
    </div>
</header>

<!-- Form Báo cáo Lỗi Mới -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 mb-6">
    <h3 class="text-sm font-semibold text-slate-700 uppercase mb-4 flex items-center gap-2">
        <i class="fa-solid fa-plus-circle text-blue-500"></i> Báo cáo Defect mới
    </h3>
    <form method="POST" action="index.php?page=defect" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div class="md:col-span-2">
            <label class="block text-xs font-medium text-slate-500 mb-1.5">Tiêu đề lỗi</label>
            <input type="text" name="title" required placeholder="Ví dụ: Lỗi không nhấn được nút thanh toán..." class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 mb-1.5">Mức độ nghiêm trọng (Severity)</label>
            <select name="severity" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="Low">Low</option>
                <option value="Medium">Medium</option>
                <option value="High" selected>High</option>
                <option value="Critical">Critical</option>
            </select>
        </div>
        <div>
            <button type="submit" name="submit_defect" class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold shadow-sm transition h-[38px]">
                <i class="fa-solid fa-wand-magic-sparkles"></i> Báo lỗi mới
            </button>
        </div>
    </form>
</div>

<!-- Bảng Lỗi -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200 text-xs font-semibold text-slate-500 uppercase">
                <th class="px-6 py-4 w-28">Defect ID</th>
                <th class="px-6 py-4 min-w-[200px]">Tiêu đề</th>
                <th class="px-6 py-4 w-24">Mức độ</th>
                <th class="px-6 py-4 w-28">Trạng thái</th>
                <th class="px-6 py-4 min-w-[320px] text-indigo-600 flex items-center gap-1.5"><i class="fa-solid fa-wand-magic-sparkles"></i> AI Phân tích & Gợi ý</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            <?php foreach ($defects as $bug): 
                // Xử lý màu sắc Badge
                $sevColor = ($bug['severity'] == 'High' || $bug['severity'] == 'Critical') ? 'bg-orange-100 text-orange-700 border-orange-200' : 'bg-slate-100 text-slate-600 border-slate-200';
                $statusColor = ($bug['status'] == 'Open') ? 'text-red-500' : 'text-blue-600';
                $progressColor = ($bug['ai_confidence'] > 80) ? 'bg-emerald-500' : 'bg-slate-400';
            ?>
            <tr class="hover:bg-slate-50 transition">
                <td class="px-6 py-4 font-semibold text-slate-700 text-sm align-top"><?= htmlspecialchars($bug['defect_code']) ?></td>
                <td class="px-6 py-4 align-top">
                    <h4 class="font-medium text-slate-800 text-sm"><?= htmlspecialchars($bug['title']) ?></h4>
                    <span class="text-xs text-slate-500 mt-1 block">Project/Module (ID: <?= $bug['testcase_id'] ?: 'N/A' ?>)</span>
                </td>
                <td class="px-6 py-4 align-top">
                    <span class="border px-2.5 py-1 rounded-full text-xs font-medium <?= $sevColor ?>"><?= $bug['severity'] ?></span>
                </td>
                <td class="px-6 py-4 align-top">
                    <span class="text-sm font-medium <?= $statusColor ?>"><?= $bug['status'] ?></span>
                </td>
                <td class="px-6 py-4 align-top">
                    <div class="flex justify-end mb-1"><span class="text-xs text-slate-500"><?= (float)$bug['ai_confidence'] ?>% tin cậy</span></div>
                    <div class="w-full bg-slate-200 rounded-full h-1.5 mb-2.5">
                        <div class="<?= $progressColor ?> h-1.5 rounded-full" style="width: <?= $bug['ai_confidence'] ?>%"></div>
                    </div>
                    <div class="bg-slate-50 border border-slate-200 rounded-lg p-3 text-[13px] text-slate-600 leading-relaxed">
                        <?= htmlspecialchars($bug['ai_suggestion']) ?>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>