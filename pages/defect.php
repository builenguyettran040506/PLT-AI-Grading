<?php
// Mở kết nối Database
$db_path = file_exists('includes/database.php') ? 'includes/database.php' : '../includes/database.php';
if (file_exists($db_path)) {
    require_once $db_path;
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$isGuest = (!isset($_SESSION['user']) && isset($_SESSION['role']) && $_SESSION['role'] === 'guest');

// =====================================================================
// 1. XỬ LÝ AJAX (LƯU, XÓA, AI CHẤM ĐIỂM)
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');

    $isGuestAjax = (isset($_SESSION['role']) && $_SESSION['role'] === 'guest');
    if ($isGuestAjax && in_array($_POST['ajax_action'], ['save_all_defects', 'delete_defect'])) {
        echo json_encode(['status' => 'success', 'message' => 'Lưu/Xóa mô phỏng thành công (Bản dùng thử)!']);
        exit;
    }

    // Xử lý LƯU VÀO DB BẰNG AJAX
    if ($_POST['ajax_action'] === 'save_all_defects') {
        try {
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->beginTransaction();
            $stmtCheck = $pdo->prepare("SELECT id FROM defects WHERE defect_code = ?");
            // Thêm các trường cần lưu
            $stmtUpdate = $pdo->prepare("UPDATE defects SET title=?, test_type=?, area=?, steps=?, expected_result=?, actual_result=?, severity=?, priority=? WHERE defect_code=?");
            $stmtInsert = $pdo->prepare("INSERT INTO defects (defect_code, title, test_type, area, steps, expected_result, actual_result, severity, priority) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $defectsData = json_decode($_POST['data'], true);

            foreach ($defectsData as $df) {
                $id = $df['defect_code'];
                $stmtCheck->execute([$id]);
                if ($stmtCheck->rowCount() > 0) {
                    $stmtUpdate->execute([$df['title'], $df['test_type'], $df['area'], $df['steps'], $df['expected_result'], $df['actual_result'], $df['severity'], $df['priority'], $id]);
                } else {
                    $stmtInsert->execute([$id, $df['title'], $df['test_type'], $df['area'], $df['steps'], $df['expected_result'], $df['actual_result'], $df['severity'], $df['priority']]);
                }
            }
            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Đã lưu đồng bộ toàn bộ dữ liệu thành công!']);
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Lỗi DB: ' . $e->getMessage()]);
        }
        exit;
    }

    // Xử lý XÓA BẰNG AJAX
    if ($_POST['ajax_action'] === 'delete_defect') {
        try {
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $stmt = $pdo->prepare("DELETE FROM defects WHERE defect_code = ?");
            $stmt->execute([$_POST['defect_code']]);
            echo json_encode(['status' => 'success', 'message' => 'Đã xóa Defect ' . $_POST['defect_code'] . ' thành công!']);
        } catch (Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Lỗi DB: ' . $e->getMessage()]);
        }
        exit;
    }

    // Xử lý AI
    if ($_POST['ajax_action'] === 'ai_analyze_defect') {
        $df_data = json_decode($_POST['df_data'] ?? '[]', true);
        $total_cases = count($df_data);
        $sample_data = array_slice($df_data, 0, 10);
        
        $score = 100; $empty_actual = 0; $short_steps = 0;
        $good_points = []; $missing_points = []; $suggestions = [];

        foreach ($sample_data as $df) {
            if (empty(trim($df['actual'] ?? ''))) { $score -= 5; $empty_actual++; }
            if (strlen(trim($df['steps'] ?? '')) < 15) { $score -= 3; $short_steps++; }
        }
        $score = max(40, min(100, $score));

        if ($score >= 90) {
            $status = "Rất tốt"; $color = "emerald"; $icon = "fa-check";
            $good_points[] = "Tổng số kịch bản lỗi (Defect) được log rất chi tiết, rõ ràng.";
            $suggestions[] = "Hệ thống quản lý lỗi đạt chuẩn, sẵn sàng bàn giao.";
        } else {
            $status = "Cần cải thiện"; $color = "amber"; $icon = "fa-exclamation";
            if ($empty_actual > 0) $missing_points[] = "Phát hiện $empty_actual lỗi đang bị trống 'Kết quả thực tế'.";
            if ($short_steps > 0) $missing_points[] = "Có $short_steps lỗi có các bước tái hiện (Steps) quá sơ sài.";
            $suggestions[] = "Hãy bổ sung mô tả thực tế đầy đủ hơn để lập trình viên dễ fix bug.";
        }
        echo json_encode(['status' => 'success', 'score' => $score, 'rating' => $status, 'color' => $color, 'icon' => $icon, 'good_points' => $good_points, 'missing_points' => $missing_points, 'suggestions' => $suggestions]);
        exit;
    }
}

// Lấy dữ liệu từ Database
$defects = [];
try {
    if (isset($pdo)) {
        $stmt = $pdo->query("SELECT * FROM defects ORDER BY id DESC");
        $defects = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (Exception $e) { }

if (empty($defects)) {
    $defects = [
        [
            'defect_code' => 'DL-01',
            'title' => 'Lỗi timeout khi gọi API cổng thanh toán',
            'test_type' => 'Integration',
            'area' => 'Checkout',
            'steps' => "1. Access the website.\n2. Chọn Momo\n3. Bấm Thanh toán",
            'expected_result' => 'Chuyển sang trang Momo trong < 3s',
            'actual_result' => 'Load vô hạn và báo lỗi 504 Gateway Timeout',
            'severity' => 'High',
            'priority' => 'High'
        ]
    ];
}
?>

<!-- Header -->
<header class="flex flex-wrap items-center justify-between mb-6 gap-4">
    <h1 class="text-2xl font-bold text-slate-800">Danh sách Defect</h1>
    <div class="flex items-center gap-4">
        <a href="landing.php" class="flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-slate-200 bg-white hover:bg-slate-50 text-[13px] font-semibold text-slate-600 transition shadow-sm">
            <i class="fa-solid fa-arrow-left-long text-slate-400"></i> Trang chủ
        </a>
        <div class="flex items-center gap-2 bg-slate-200/50 px-3 py-1.5 rounded-full border border-slate-200">
            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
            <span class="text-sm font-medium text-slate-600">AI Engine: Online</span>
        </div>
        <?php if ($isGuest): ?>
            <a href="landing.php" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full font-bold text-[13px] shadow-md shadow-indigo-500/30 transition">Đăng ký tài khoản</a>
        <?php else: ?>
            <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-md cursor-pointer">QA</div>
        <?php endif; ?>
    </div>
</header>

<!-- CHUYỂN TỪ <FORM> SANG <DIV> ĐỂ KHÔNG BỊ RELOAD TRANG NỮA -->
<div id="defectForm" class="w-full bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col overflow-hidden relative">

    <!-- Toast Thông báo JS -->
    <div id="toast-msg" class="fixed top-6 right-6 bg-slate-800 text-white px-5 py-3 rounded-lg text-[13px] font-medium shadow-2xl opacity-0 transition-opacity pointer-events-none z-[150] flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-emerald-400"></i> <span id="toast-text">Thành công!</span>
    </div>

    <!-- Toolbar -->
    <div class="p-4 border-b border-slate-100 flex flex-col xl:flex-row xl:items-center justify-between gap-4 bg-white">
        <div class="relative w-full xl:w-[320px] shrink-0">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[13px]"></i>
            <input type="text" id="searchDefect" onkeyup="filterDefect()" placeholder="Tìm kiếm Defect ID, Tiêu đề..." class="w-full border border-slate-300 rounded-lg pl-9 pr-3 py-2.5 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 bg-slate-50 hover:bg-slate-100 focus:bg-white transition-colors">
        </div>

        <div class="flex items-center gap-2 overflow-x-auto custom-scrollbar pb-1 -mb-1 w-full justify-start xl:justify-end">
            <input type="file" id="excelFileInput" class="hidden" accept=".xlsx, .xls, .csv" onchange="handleExcelUpload(event)">
            <button type="button" onclick="document.getElementById('excelFileInput').click();" class="flex items-center gap-1.5 px-4 py-2 border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 rounded-lg text-[13px] text-emerald-700 transition shrink-0 font-semibold shadow-sm">
                <i class="fa-regular fa-file-excel text-emerald-600 text-lg"></i> Nhập Excel
            </button>
            <button type="button" onclick="exportVisibleDefects()" class="flex items-center gap-1.5 px-4 py-2 border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 rounded-lg text-[13px] text-emerald-700 transition shrink-0 font-semibold shadow-sm">
                <i class="fa-regular fa-file-excel text-emerald-600 text-lg"></i> Xuất Excel (Dòng hiển thị)
            </button>
            <!-- NÚT LƯU ĐƯỢC CHUYỂN SANG AJAX SỬ DỤNG ONCLICK -->
            <button type="button" onclick="saveAllDefects()" class="flex items-center gap-1.5 px-4 py-2 border border-blue-200 bg-blue-50 hover:bg-blue-100 rounded-lg text-[13px] text-blue-700 transition shrink-0 font-semibold shadow-sm">
                <i class="fa-solid fa-layer-group"></i> Lưu hàng loạt
            </button>
            <button type="button" onclick="openAIModal()" class="flex items-center gap-2 px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[13px] font-semibold shadow-md shadow-indigo-500/30 transition shrink-0">
                <i class="fa-solid fa-wand-magic-sparkles"></i> AI Chấm điểm
            </button>
        </div>
    </div>

    <!-- Bảng Dữ Liệu -->
    <div class="overflow-x-auto flex-1 custom-scrollbar pb-2 min-h-[400px]">
        <table class="w-full text-left border-collapse min-w-[2000px]">
            <thead>
                <tr class="bg-slate-50/70 border-b border-slate-200 text-[12px] font-bold text-slate-600 uppercase tracking-wide">
                    <th class="px-5 py-4 w-12 text-center">STT</th>
                    <th class="px-5 py-4 w-28 text-center">DEFECT_ID</th>
                    <th class="px-5 py-4 min-w-[250px]">Title</th>
                    <th class="px-5 py-4 w-28 text-center">Test Type</th>
                    <th class="px-5 py-4 w-28 text-center">Area</th>
                    <th class="px-5 py-4 min-w-[280px]">Steps</th>
                    <th class="px-5 py-4 min-w-[250px]">Expected Result</th>
                    <th class="px-5 py-4 min-w-[250px]">Actual Result</th>
                    <th class="px-5 py-4 w-32 text-center">Severity</th>
                    <th class="px-5 py-4 w-24 text-center">Priority</th>
                    <th class="px-5 py-4 w-36 text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody id="defect-tbody" class="divide-y divide-slate-100">
                <?php
                $stt = 1;
                foreach ($defects as $df):
                    $id = htmlspecialchars($df['defect_code'] ?? 'DL-01');
                ?>
                    <tr class="hover:bg-slate-50/50 transition-colors defect-row group bg-white">
                        <td class="px-5 py-5 text-[13px] text-slate-500 text-center align-top pt-7"><?= $stt++ ?></td>
                        <td class="px-5 py-5 font-semibold text-slate-800 text-[13px] text-center align-top pt-7 df-code"><?= $id ?></td>
                        
                        <td class="px-5 py-5 align-top">
                            <textarea rows="3" class="w-full text-[13px] text-slate-800 resize-none bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors leading-relaxed df-title"><?= htmlspecialchars($df['title'] ?? '') ?></textarea>
                        </td>

                        <td class="px-5 py-5 align-top pt-6">
                            <input type="text" class="w-full text-[13px] text-slate-800 text-center bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors df-testtype" value="<?= htmlspecialchars($df['test_type'] ?? 'Functional') ?>">
                        </td>

                        <td class="px-5 py-5 align-top pt-6">
                            <input type="text" class="w-full text-[13px] text-slate-800 text-center bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors df-area" value="<?= htmlspecialchars($df['area'] ?? '') ?>">
                        </td>

                        <td class="px-5 py-5 align-top">
                            <textarea rows="4" class="w-full text-[13px] text-slate-800 resize-none bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors leading-relaxed df-steps"><?= htmlspecialchars($df['steps'] ?? '') ?></textarea>
                        </td>

                        <td class="px-5 py-5 align-top">
                            <textarea rows="4" class="w-full text-[13px] text-slate-800 resize-none bg-emerald-50/30 hover:bg-emerald-50/80 focus:bg-white border border-transparent focus:border-emerald-500 rounded-md p-2 transition-colors leading-relaxed df-expected"><?= htmlspecialchars($df['expected_result'] ?? '') ?></textarea>
                        </td>

                        <td class="px-5 py-5 align-top">
                            <textarea rows="4" class="w-full text-[13px] text-slate-800 resize-none bg-red-50/30 hover:bg-red-50/80 focus:bg-white border border-transparent focus:border-red-500 rounded-md p-2 transition-colors leading-relaxed df-actual"><?= htmlspecialchars($df['actual_result'] ?? '') ?></textarea>
                        </td>

                        <td class="px-5 py-5 align-top text-center pt-7">
                            <select class="px-3 py-1 border border-slate-300 rounded-full text-[12px] font-bold text-slate-600 focus:outline-none bg-white cursor-pointer df-severity">
                                <option value="Critical" <?= ($df['severity'] ?? '') === 'Critical' ? 'selected' : '' ?> class="text-red-600">Critical</option>
                                <option value="High" <?= ($df['severity'] ?? '') === 'High' ? 'selected' : '' ?> class="text-orange-600">High</option>
                                <option value="Medium" <?= ($df['severity'] ?? 'Medium') === 'Medium' ? 'selected' : '' ?>>Medium</option>
                                <option value="Low" <?= ($df['severity'] ?? '') === 'Low' ? 'selected' : '' ?> class="text-slate-500">Low</option>
                            </select>
                        </td>

                        <td class="px-5 py-5 align-top pt-6 text-center">
                            <select class="w-full text-[13px] text-slate-800 font-bold text-center bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors appearance-none cursor-pointer df-priority">
                                <option value="High" <?= ($df['priority'] ?? '') === 'High' ? 'selected' : '' ?>>High</option>
                                <option value="Medium" <?= ($df['priority'] ?? 'Medium') === 'Medium' ? 'selected' : '' ?>>Medium</option>
                                <option value="Low" <?= ($df['priority'] ?? '') === 'Low' ? 'selected' : '' ?>>Low</option>
                            </select>
                        </td>

                        <td class="px-5 py-5 align-top pt-6 text-center">
                            <div class="flex items-center justify-center gap-1.5 opacity-30 group-hover:opacity-100 transition-opacity">
                                <button type="button" onclick="openEditDefectModal(this)" class="p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-600 hover:text-white transition inline-flex shadow-sm"><i class="fa-regular fa-pen-to-square"></i></button>
                                <button type="button" onclick="viewDefectDetail(this)" class="p-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition inline-flex shadow-sm"><i class="fa-regular fa-eye"></i></button>
                                <!-- NÚT LƯU DÒNG CŨNG CHUYỂN SANG AJAX -->
                                <button type="button" onclick="saveSingleDefect(this)" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition shadow-sm"><i class="fa-regular fa-floppy-disk"></i></button>
                                <!-- NÚT XÓA CHUYỂN SANG AJAX -->
                                <button type="button" onclick="deleteDefectRow(this, '<?= $id ?>')" class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition shadow-sm"><i class="fa-regular fa-trash-can"></i></button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Nút Thêm Mới -->
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            <button type="button" onclick="addDefectRow()" class="flex items-center gap-2 text-[14px] font-semibold text-blue-600 hover:text-blue-800 transition px-4 py-2 bg-white hover:bg-blue-50 border border-slate-200 hover:border-blue-200 rounded-lg shadow-sm">
                <i class="fa-solid fa-plus"></i> Thêm Defect mới
            </button>
        </div>
    </div>
</div>

<!-- MODAL XEM CHI TIẾT DEFECT -->
<div id="viewDefectModal" class="fixed inset-0 z-[110] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[600px] overflow-hidden transform scale-95 transition-transform duration-300 flex flex-col" id="viewDefectContent">
        <div class="bg-slate-800 p-4 text-white flex justify-between items-center">
            <h3 class="font-bold text-[15px] flex items-center gap-2"><i class="fa-regular fa-eye text-indigo-400"></i> Chi tiết Lỗi (Defect)</h3>
            <button onclick="closeDefectDetailModal()" class="text-white/80 hover:text-white transition-colors"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto custom-scrollbar text-[13px]">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="font-bold text-slate-400 uppercase text-[11px]">Mã Lỗi / ID</label>
                    <p id="detailDefectId" class="font-bold text-slate-800 text-sm mt-1"></p>
                </div>
                <div>
                    <label class="font-bold text-slate-400 uppercase text-[11px]">Loại kiểm thử</label>
                    <p id="detailDefectType" class="font-semibold text-indigo-600 mt-1"></p>
                </div>
            </div>
            <div>
                <label class="font-bold text-slate-400 uppercase text-[11px]">Tiêu đề lỗi (Title)</label>
                <p id="detailDefectTitle" class="text-slate-800 font-medium mt-1 bg-slate-50 p-2.5 rounded-lg border border-slate-100"></p>
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="font-bold text-slate-400 uppercase text-[11px]">Mức độ nghiêm trọng</label>
                    <p id="detailDefectSeverity" class="font-bold mt-1 text-red-600"></p>
                </div>
                <div>
                    <label class="font-bold text-slate-400 uppercase text-[11px]">Độ ưu tiên</label>
                    <p id="detailDefectPriority" class="font-bold mt-1 text-blue-600"></p>
                </div>
            </div>
            <div>
                <label class="font-bold text-slate-400 uppercase text-[11px]">Các bước thực hiện</label>
                <div id="detailDefectSteps" class="text-slate-700 mt-1 bg-slate-50 p-3 rounded-lg border border-slate-100 whitespace-pre-line leading-relaxed"></div>
            </div>
            <div>
                <label class="font-bold text-slate-400 uppercase text-[11px]">Kết quả mong đợi & Kết quả thực tế</label>
                <div id="detailDefectExpected" class="text-slate-700 mt-1 bg-emerald-50/40 p-3 rounded-lg border border-emerald-100 whitespace-pre-line leading-relaxed mb-2"></div>
                <div id="detailDefectActual" class="text-slate-700 mt-1 bg-red-50/40 p-3 rounded-lg border border-red-100 whitespace-pre-line leading-relaxed"></div>
            </div>
        </div>
        <div class="p-4 bg-slate-50 flex justify-end border-t border-slate-100">
            <button onclick="closeDefectDetailModal()" class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-[13px] font-bold rounded-lg transition-colors">Đóng</button>
        </div>
    </div>
</div>

<!-- MODAL SỬA DEFECT -->
<div id="editDefectModal" class="fixed inset-0 z-[120] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[600px] overflow-hidden transform scale-95 transition-transform duration-300 flex flex-col" id="editDefectContent">
        <div class="bg-amber-600 p-4 text-white flex justify-between items-center">
            <h3 class="font-bold text-[15px] flex items-center gap-2"><i class="fa-regular fa-pen-to-square"></i> Chỉnh sửa thông tin Lỗi</h3>
            <button onclick="closeEditDefectModal()" class="text-white/80 hover:text-white transition-colors"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <div class="p-6 space-y-4 max-h-[75vh] overflow-y-auto custom-scrollbar text-[13px]">
            <div>
                <label class="font-bold text-slate-500 uppercase text-[11px] mb-1 block">Tiêu đề lỗi (Title)</label>
                <input type="text" id="editDefectTitleInput" class="w-full border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-amber-500 text-slate-800">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="font-bold text-slate-500 uppercase text-[11px] mb-1 block">Mức độ nghiêm trọng</label>
                    <select id="editDefectSeverityInput" class="w-full border border-slate-300 rounded-lg px-3 py-2 font-bold text-slate-800">
                        <option value="Critical">Critical</option>
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </div>
                <div>
                    <label class="font-bold text-slate-500 uppercase text-[11px] mb-1 block">Độ ưu tiên</label>
                    <select id="editDefectPriorityInput" class="w-full border border-slate-300 rounded-lg px-3 py-2 font-bold text-slate-800">
                        <option value="High">High</option>
                        <option value="Medium">Medium</option>
                        <option value="Low">Low</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="font-bold text-slate-500 uppercase text-[11px] mb-1 block">Các bước (Steps)</label>
                <textarea id="editDefectStepsInput" rows="3" class="w-full border border-slate-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-amber-500 text-slate-800"></textarea>
            </div>
            <div>
                <label class="font-bold text-slate-500 uppercase text-[11px] mb-1 block">Kết quả thực tế (Actual)</label>
                <textarea id="editDefectActualInput" rows="3" class="w-full border border-slate-300 rounded-lg p-2.5 focus:outline-none focus:ring-2 focus:ring-amber-500 text-slate-800"></textarea>
            </div>
        </div>
        <div class="p-4 bg-slate-50 flex justify-end gap-2 border-t border-slate-100">
            <button onclick="closeEditDefectModal()" class="px-5 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-[13px] font-bold rounded-lg transition-colors">Hủy</button>
            <button onclick="saveEditDefectData()" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white text-[13px] font-bold rounded-lg transition-colors shadow-sm">Cập nhật vào bảng</button>
        </div>
    </div>
</div>

<!-- MODAL AI CHẤM ĐIỂM -->
<div id="aiModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/40 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-[#F8FAFC] rounded-2xl shadow-2xl w-full max-w-[420px] overflow-hidden transform scale-95 transition-transform duration-300 flex flex-col max-h-[90vh]" id="aiModalContent">
        <div id="aiLoadingHeader" class="bg-[#3B82F6] p-4 text-white flex justify-between items-center relative overflow-hidden">
            <h3 class="font-bold text-[15px] flex items-center gap-2 relative z-10"><i class="fa-solid fa-robot"></i> AI Tự Động Phân Tích</h3>
            <button onclick="closeAIModal()" class="text-white/80 hover:text-white transition-colors relative z-10"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <div id="aiLoading" class="flex flex-col items-center justify-center py-16 bg-white">
            <div class="relative w-12 h-12 mb-4">
                <div class="absolute inset-0 border-4 border-blue-200 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-blue-600 rounded-full border-t-transparent animate-spin"></div>
            </div>
            <p class="text-[14px] font-medium text-slate-700">AI đang quét và phân tích Defect...</p>
        </div>
        <div id="aiResult" class="hidden flex-col h-full overflow-hidden">
            <button onclick="closeAIModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 z-10"><i class="fa-solid fa-xmark text-lg"></i></button>
            <div class="bg-gradient-to-b from-blue-50 to-white px-6 pt-10 pb-6 flex flex-col items-center justify-center border-b border-slate-100 shrink-0">
                <div id="aiScoreIconWrapper" class="w-12 h-12 rounded-full flex items-center justify-center text-xl mb-3 shadow-sm"><i id="aiScoreIcon" class="fa-solid"></i></div>
                <div class="flex items-baseline gap-1 mb-2"><span id="aiScore" class="text-4xl font-extrabold text-slate-800">0</span><span class="text-xl font-bold text-slate-400">/100</span></div>
                <span id="aiStatusBadge" class="px-3 py-1 rounded-full text-[12px] font-bold border">Trạng thái</span>
            </div>
            <div class="p-5 overflow-y-auto custom-scrollbar bg-white flex-1">
                <div class="flex items-center gap-2 mb-4"><i class="fa-solid fa-wand-magic-sparkles text-indigo-500"></i>
                    <h4 class="font-bold text-slate-800 text-[14px]">Phân tích từ AI</h4>
                </div>
                <div class="mb-5">
                    <h5 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">ĐIỂM TỐT</h5>
                    <ul id="aiGoodPoints" class="space-y-2 text-[13px] text-slate-700"></ul>
                </div>
                <div class="mb-5">
                    <h5 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">THIẾU SÓT LOGIC / EDGE CASES</h5>
                    <div class="bg-amber-50 border border-amber-100 rounded-lg p-3">
                        <ul id="aiMissingPoints" class="space-y-2 text-[13px] text-slate-700 mb-3"></ul>
                    </div>
                </div>
                <div>
                    <h5 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">GỢI Ý CẢI THIỆN</h5>
                    <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 flex gap-2"><i class="fa-solid fa-angle-right text-blue-400 mt-0.5"></i>
                        <p id="aiSuggestions" class="text-[13px] text-slate-700 m-0"></p>
                    </div>
                </div>
            </div>
        </div>
        <div id="aiLoadingFooter" class="p-4 bg-white flex justify-end border-t border-slate-100 shrink-0"><button onclick="closeAIModal()" class="px-6 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[13px] font-bold rounded-lg transition-colors">Đóng</button></div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    let rowCount = <?= count($defects) ?>;
    let currentEditingDefectRow = null;

    function showToast(message, isError = false) {
        const toast = document.getElementById('toast-msg');
        if (!toast) return;
        document.getElementById('toast-text').innerHTML = message;
        toast.className = `fixed top-6 right-6 text-white px-5 py-3 rounded-lg text-[13px] font-medium shadow-2xl transition-opacity z-[150] flex items-center gap-2 ${isError ? 'bg-red-600' : 'bg-emerald-600'}`;
        toast.innerHTML = (isError ? '<i class="fa-solid fa-circle-exclamation text-white"></i> ' : '<i class="fa-solid fa-circle-check text-white"></i> ') + `<span>${message}</span>`;
        toast.classList.remove('opacity-0');
        setTimeout(() => toast.classList.add('opacity-0'), 3500);
    }

    function getNextDefectId() {
        const codes = document.querySelectorAll('.df-code');
        let maxNum = 0;
        codes.forEach(el => {
            const val = el.innerText || el.textContent || '';
            const match = val.match(/DL-(\d+)/);
            if (match) {
                const num = parseInt(match[1], 10);
                if (num > maxNum) maxNum = num;
            }
        });
        return maxNum + 1;
    }

    function addDefectRow() {
        const tbody = document.getElementById('defect-tbody');
        const id = "DL-" + String(getNextDefectId()).padStart(2, '0');
        rowCount++;

        const newRow = document.createElement('tr');
        newRow.className = 'hover:bg-slate-50/80 transition-colors defect-row group bg-indigo-50/30';
        newRow.innerHTML = `
            <td class="px-5 py-5 text-[13px] text-slate-500 text-center align-top pt-7">${rowCount}</td>
            <td class="px-5 py-5 font-semibold text-slate-800 text-[13px] text-center align-top pt-7 df-code">${id}</td>
            <td class="px-5 py-5 align-top"><textarea rows="3" class="w-full text-[13px] text-slate-800 resize-none bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors leading-relaxed df-title" placeholder="Nhập tiêu đề lỗi..."></textarea></td>
            <td class="px-5 py-5 align-top pt-6"><input type="text" class="w-full text-[13px] text-slate-800 text-center bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors df-testtype" value="Functional"></td>
            <td class="px-5 py-5 align-top pt-6"><input type="text" class="w-full text-[13px] text-slate-800 text-center bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors df-area" placeholder="Nhập phân hệ..."></td>
            <td class="px-5 py-5 align-top"><textarea rows="4" class="w-full text-[13px] text-slate-800 resize-none bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors leading-relaxed df-steps" placeholder="1. Access the website.&#10;2. "></textarea></td>
            <td class="px-5 py-5 align-top"><textarea rows="4" class="w-full text-[13px] text-slate-800 resize-none bg-emerald-50/30 hover:bg-emerald-50/80 focus:bg-white border border-transparent focus:border-emerald-500 rounded-md p-2 transition-colors leading-relaxed df-expected" placeholder="Kết quả mong muốn..."></textarea></td>
            <td class="px-5 py-5 align-top"><textarea rows="4" class="w-full text-[13px] text-slate-800 resize-none bg-red-50/30 hover:bg-red-50/80 focus:bg-white border border-transparent focus:border-red-500 rounded-md p-2 transition-colors leading-relaxed df-actual" placeholder="Kết quả thực tế..."></textarea></td>
            <td class="px-5 py-5 align-top text-center pt-7"><select class="px-3 py-1 border border-slate-300 rounded-full text-[12px] font-bold text-slate-600 focus:outline-none bg-white cursor-pointer df-severity"><option value="Critical" class="text-red-600">Critical</option><option value="High" class="text-orange-600">High</option><option value="Medium" selected>Medium</option><option value="Low" class="text-slate-500">Low</option></select></td>
            <td class="px-5 py-5 align-top pt-6 text-center"><select class="w-full text-[13px] text-slate-800 font-bold text-center bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors appearance-none cursor-pointer df-priority"><option value="High">High</option><option value="Medium" selected>Medium</option><option value="Low">Low</option></select></td>
            <td class="px-5 py-5 align-top pt-6 text-center">
                <div class="flex items-center justify-center gap-1.5 opacity-30 group-hover:opacity-100 transition-opacity">
                    <button type="button" onclick="openEditDefectModal(this)" class="p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-600 hover:text-white transition shadow-sm"><i class="fa-regular fa-pen-to-square"></i></button>
                    <button type="button" onclick="viewDefectDetail(this)" class="p-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition shadow-sm"><i class="fa-regular fa-eye"></i></button>
                    <button type="button" onclick="saveSingleDefect(this)" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition shadow-sm"><i class="fa-regular fa-floppy-disk"></i></button>
                    <button type="button" onclick="deleteDefectRow(this, '${id}')" class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition shadow-sm"><i class="fa-solid fa-xmark"></i></button>
                </div>
            </td>
        `;
        tbody.appendChild(newRow);
        newRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // === GỌI TRỰC TIẾP VÀO pages/defect.php ĐỂ TRÁNH HTML RÁC TỪ index.php ===
    function saveAllDefects() {
        const rows = document.querySelectorAll('.defect-row');
        const dataToSave = [];
        rows.forEach(row => {
            if (row.style.display !== 'none') {
                dataToSave.push({
                    defect_code: row.querySelector('.df-code') ? row.querySelector('.df-code').innerText.trim() : '',
                    title: row.querySelector('.df-title') ? row.querySelector('.df-title').value.trim() : '',
                    test_type: row.querySelector('.df-testtype') ? row.querySelector('.df-testtype').value.trim() : 'Functional',
                    area: row.querySelector('.df-area') ? row.querySelector('.df-area').value.trim() : '',
                    steps: row.querySelector('.df-steps') ? row.querySelector('.df-steps').value.trim() : '',
                    expected_result: row.querySelector('.df-expected') ? row.querySelector('.df-expected').value.trim() : '',
                    actual_result: row.querySelector('.df-actual') ? row.querySelector('.df-actual').value.trim() : '',
                    severity: row.querySelector('.df-severity') ? row.querySelector('.df-severity').value : 'Medium',
                    priority: row.querySelector('.df-priority') ? row.querySelector('.df-priority').value : 'Medium'
                });
            }
        });

        if (dataToSave.length === 0) return showToast('Không có dữ liệu để lưu!', true);

        const formData = new FormData();
        formData.append('ajax_action', 'save_all_defects');
        formData.append('data', JSON.stringify(dataToSave));

        // Đổi đường dẫn tại đây
        fetch('pages/defect.php', { method: 'POST', body: formData })
            .then(async r => {
                const text = await r.text();
                try { return JSON.parse(text); } 
                catch (e) { alert("Lỗi Database chi tiết:\n" + text); throw new Error("Server trả về HTML rác"); }
            })
            .then(data => {
                if (data.status === 'success') {
                    showToast(data.message);
                    rows.forEach(row => { row.classList.remove('bg-indigo-50/30', 'bg-amber-50'); row.classList.add('bg-emerald-50/40'); setTimeout(() => row.classList.remove('bg-emerald-50/40'), 1500); });
                } else { showToast(data.message, true); }
            }).catch(() => showToast('Gặp lỗi khi lưu!', true));
    }

    function saveSingleDefect(btn) {
        const row = btn.closest('tr');
        const dataToSave = [{
            defect_code: row.querySelector('.df-code') ? row.querySelector('.df-code').innerText.trim() : '',
            title: row.querySelector('.df-title') ? row.querySelector('.df-title').value.trim() : '',
            test_type: row.querySelector('.df-testtype') ? row.querySelector('.df-testtype').value.trim() : 'Functional',
            area: row.querySelector('.df-area') ? row.querySelector('.df-area').value.trim() : '',
            steps: row.querySelector('.df-steps') ? row.querySelector('.df-steps').value.trim() : '',
            expected_result: row.querySelector('.df-expected') ? row.querySelector('.df-expected').value.trim() : '',
            actual_result: row.querySelector('.df-actual') ? row.querySelector('.df-actual').value.trim() : '',
            severity: row.querySelector('.df-severity') ? row.querySelector('.df-severity').value : 'Medium',
            priority: row.querySelector('.df-priority') ? row.querySelector('.df-priority').value : 'Medium'
        }];

        const formData = new FormData();
        formData.append('ajax_action', 'save_all_defects');
        formData.append('data', JSON.stringify(dataToSave));

        // Đổi đường dẫn tại đây
        fetch('pages/defect.php', { method: 'POST', body: formData })
            .then(async r => {
                const text = await r.text();
                try { return JSON.parse(text); } 
                catch (e) { alert("Lỗi Database chi tiết:\n" + text); throw new Error("Server trả về HTML rác"); }
            })
            .then(data => {
                if (data.status === 'success') {
                    showToast('Lưu ' + dataToSave[0].defect_code + ' thành công!');
                    row.classList.remove('bg-indigo-50/30', 'bg-amber-50'); row.classList.add('bg-emerald-50/40'); setTimeout(() => row.classList.remove('bg-emerald-50/40'), 1500);
                } else { showToast(data.message, true); }
            }).catch(() => showToast('Gặp lỗi khi lưu!', true));
    }

    function deleteDefectRow(btn, id) {
        if (!confirm('Xác nhận xóa lỗi ' + id + '?')) return;
        const row = btn.closest('tr');
        if (row.classList.contains('bg-indigo-50/30')) { row.remove(); return showToast('Đã hủy dòng chưa lưu!'); }

        const formData = new FormData();
        formData.append('ajax_action', 'delete_defect');
        formData.append('defect_code', id);

        // Đổi đường dẫn tại đây
        fetch('pages/defect.php', { method: 'POST', body: formData })
            .then(async r => {
                const text = await r.text();
                try { return JSON.parse(text); } 
                catch (e) { alert("Lỗi Database:\n" + text); throw new Error("Lỗi HTML rác"); }
            })
            .then(data => {
                if (data.status === 'success') { showToast(data.message); row.remove(); } 
                else { showToast(data.message, true); }
            }).catch(() => showToast('Gặp lỗi khi xóa!', true));
    }

    // Modal Handlers
    function viewDefectDetail(btn) {
        const row = btn.closest('tr');
        document.getElementById('detailDefectId').textContent = row.querySelector('.df-code') ? row.querySelector('.df-code').innerText : '';
        document.getElementById('detailDefectTitle').textContent = row.querySelector('.df-title') ? row.querySelector('.df-title').value : '';
        document.getElementById('detailDefectType').textContent = row.querySelector('.df-testtype') ? row.querySelector('.df-testtype').value : 'Functional';
        document.getElementById('detailDefectSeverity').textContent = row.querySelector('.df-severity') ? row.querySelector('.df-severity').value : 'Medium';
        document.getElementById('detailDefectPriority').textContent = row.querySelector('.df-priority') ? row.querySelector('.df-priority').value : 'Medium';
        document.getElementById('detailDefectSteps').textContent = row.querySelector('.df-steps') ? row.querySelector('.df-steps').value : '';
        document.getElementById('detailDefectExpected').textContent = "Expected: " + (row.querySelector('.df-expected') ? row.querySelector('.df-expected').value : '');
        document.getElementById('detailDefectActual').textContent = "Actual: " + (row.querySelector('.df-actual') ? row.querySelector('.df-actual').value : '');

        const modal = document.getElementById('viewDefectModal'); const content = document.getElementById('viewDefectContent');
        modal.classList.remove('hidden'); modal.classList.add('flex');
        setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); content.classList.add('scale-100'); }, 10);
    }
    function closeDefectDetailModal() {
        const modal = document.getElementById('viewDefectModal'); const content = document.getElementById('viewDefectContent');
        modal.classList.add('opacity-0'); content.classList.remove('scale-100'); content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); }, 300);
    }

    function openEditDefectModal(btn) {
        currentEditingDefectRow = btn.closest('tr');
        document.getElementById('editDefectTitleInput').value = currentEditingDefectRow.querySelector('.df-title') ? currentEditingDefectRow.querySelector('.df-title').value : '';
        document.getElementById('editDefectSeverityInput').value = currentEditingDefectRow.querySelector('.df-severity') ? currentEditingDefectRow.querySelector('.df-severity').value : 'Medium';
        document.getElementById('editDefectPriorityInput').value = currentEditingDefectRow.querySelector('.df-priority') ? currentEditingDefectRow.querySelector('.df-priority').value : 'Medium';
        document.getElementById('editDefectStepsInput').value = currentEditingDefectRow.querySelector('.df-steps') ? currentEditingDefectRow.querySelector('.df-steps').value : '';
        document.getElementById('editDefectActualInput').value = currentEditingDefectRow.querySelector('.df-actual') ? currentEditingDefectRow.querySelector('.df-actual').value : '';

        const modal = document.getElementById('editDefectModal'); const content = document.getElementById('editDefectContent');
        modal.classList.remove('hidden'); modal.classList.add('flex');
        setTimeout(() => { modal.classList.remove('opacity-0'); content.classList.remove('scale-95'); content.classList.add('scale-100'); }, 10);
    }
    function closeEditDefectModal() {
        const modal = document.getElementById('editDefectModal'); const content = document.getElementById('editDefectContent');
        modal.classList.add('opacity-0'); content.classList.remove('scale-100'); content.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); currentEditingDefectRow = null; }, 300);
    }
    function saveEditDefectData() {
        if (!currentEditingDefectRow) return;
        currentEditingDefectRow.querySelector('.df-title').value = document.getElementById('editDefectTitleInput').value;
        currentEditingDefectRow.querySelector('.df-severity').value = document.getElementById('editDefectSeverityInput').value;
        currentEditingDefectRow.querySelector('.df-priority').value = document.getElementById('editDefectPriorityInput').value;
        currentEditingDefectRow.querySelector('.df-steps').value = document.getElementById('editDefectStepsInput').value;
        currentEditingDefectRow.querySelector('.df-actual').value = document.getElementById('editDefectActualInput').value;

        closeEditDefectModal(); showToast('Đã cập nhật lỗi (chưa lưu vào DB)!');
        currentEditingDefectRow.classList.add('bg-amber-50');
    }

    function handleExcelUpload(event) {
        const file = event.target.files[0]; if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const workbook = XLSX.read(new Uint8Array(e.target.result), { type: 'array' });
                const jsonData = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]], { header: 1 });
                document.querySelectorAll('.defect-row').forEach(r => r.remove());
                let nextNum = getNextDefectId();

                for (let i = 1; i < jsonData.length; i++) {
                    const row = jsonData[i];
                    if (!row || row.length === 0 || row.join('').trim() === '') continue;
                    let steps = row[5] || '';
                    if (!steps.toLowerCase().includes('access the website') && steps !== '') steps = "1. Access the website.\n" + (steps ? "2. " + steps : "");
                    
                    let id = row[1];
                    if (!id || String(id).trim() === '') { id = "DL-" + String(nextNum).padStart(2, '0'); nextNum++; }

                    const tbody = document.getElementById('defect-tbody');
                    const newRow = document.createElement('tr');
                    newRow.className = 'hover:bg-slate-50/80 transition-colors defect-row group bg-indigo-50/30';
                    newRow.innerHTML = `
                        <td class="px-5 py-5 text-[13px] text-slate-500 text-center align-top pt-7">#</td>
                        <td class="px-5 py-5 font-semibold text-slate-800 text-[13px] text-center align-top pt-7 df-code">${id}</td>
                        <td class="px-5 py-5 align-top"><textarea rows="3" class="w-full text-[13px] text-slate-800 resize-none bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors leading-relaxed df-title">${row[2] || ''}</textarea></td>
                        <td class="px-5 py-5 align-top pt-6"><input type="text" class="w-full text-[13px] text-slate-800 text-center bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors df-testtype" value="${row[3] || 'Functional'}"></td>
                        <td class="px-5 py-5 align-top pt-6"><input type="text" class="w-full text-[13px] text-slate-800 text-center bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors df-area" value="${row[4] || ''}"></td>
                        <td class="px-5 py-5 align-top"><textarea rows="4" class="w-full text-[13px] text-slate-800 resize-none bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors leading-relaxed df-steps">${steps}</textarea></td>
                        <td class="px-5 py-5 align-top"><textarea rows="4" class="w-full text-[13px] text-slate-800 resize-none bg-emerald-50/30 hover:bg-emerald-50/80 focus:bg-white border border-transparent focus:border-emerald-500 rounded-md p-2 transition-colors leading-relaxed df-expected">${row[6] || ''}</textarea></td>
                        <td class="px-5 py-5 align-top"><textarea rows="4" class="w-full text-[13px] text-slate-800 resize-none bg-red-50/30 hover:bg-red-50/80 focus:bg-white border border-transparent focus:border-red-500 rounded-md p-2 transition-colors leading-relaxed df-actual">${row[7] || ''}</textarea></td>
                        <td class="px-5 py-5 align-top text-center pt-7"><select class="px-3 py-1 border border-slate-300 rounded-full text-[12px] font-bold text-slate-600 focus:outline-none bg-white cursor-pointer df-severity"><option value="Critical" class="text-red-600">Critical</option><option value="High" class="text-orange-600">High</option><option value="Medium" selected>Medium</option><option value="Low" class="text-slate-500">Low</option></select></td>
                        <td class="px-5 py-5 align-top pt-6 text-center"><select class="w-full text-[13px] text-slate-800 font-bold text-center bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors appearance-none cursor-pointer df-priority"><option value="High">High</option><option value="Medium" selected>Medium</option><option value="Low">Low</option></select></td>
                        <td class="px-5 py-5 align-top pt-6 text-center">
                            <div class="flex items-center justify-center gap-1.5 opacity-30 group-hover:opacity-100 transition-opacity">
                                <button type="button" onclick="openEditDefectModal(this)" class="p-2 bg-amber-50 text-amber-600 rounded-lg hover:bg-amber-600 hover:text-white transition shadow-sm"><i class="fa-regular fa-pen-to-square"></i></button>
                                <button type="button" onclick="viewDefectDetail(this)" class="p-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition shadow-sm"><i class="fa-regular fa-eye"></i></button>
                                <button type="button" onclick="saveSingleDefect(this)" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition shadow-sm"><i class="fa-regular fa-floppy-disk"></i></button>
                                <button type="button" onclick="deleteDefectRow(this, '${id}')" class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition shadow-sm"><i class="fa-solid fa-xmark"></i></button>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(newRow);
                }
                showToast('Đã nạp file Excel thành công!');
            } catch (error) { showToast('Không thể đọc file Excel.', true); } finally { event.target.value = ''; }
        };
        reader.readAsArrayBuffer(file);
    }

    function filterDefect() {
        const filter = document.getElementById('searchDefect').value.toLowerCase();
        document.querySelectorAll('.defect-row').forEach(row => {
            const codeCell = row.querySelector('.df-code'); const titleCell = row.querySelector('.df-title');
            if (codeCell && titleCell) {
                row.style.display = ((codeCell.innerText || codeCell.textContent).toLowerCase().indexOf(filter) > -1 || titleCell.value.toLowerCase().indexOf(filter) > -1) ? "" : "none";
            }
        });
    }

    function exportVisibleDefects() {
        const rows = document.querySelectorAll('.defect-row'); const dataToExport = [];
        rows.forEach(row => {
            if (row.style.display !== 'none') {
                dataToExport.push({
                    defect_code: row.querySelector('.df-code') ? row.querySelector('.df-code').innerText.trim() : '',
                    title: row.querySelector('.df-title') ? row.querySelector('.df-title').value.trim() : '',
                    test_type: row.querySelector('.df-testtype') ? row.querySelector('.df-testtype').value.trim() : '',
                    area: row.querySelector('.df-area') ? row.querySelector('.df-area').value.trim() : '',
                    steps: row.querySelector('.df-steps') ? row.querySelector('.df-steps').value.trim() : '',
                    expected_result: row.querySelector('.df-expected') ? row.querySelector('.df-expected').value.trim() : '',
                    actual_result: row.querySelector('.df-actual') ? row.querySelector('.df-actual').value.trim() : '',
                    severity: row.querySelector('.df-severity') ? row.querySelector('.df-severity').value : 'Medium',
                    priority: row.querySelector('.df-priority') ? row.querySelector('.df-priority').value : 'Medium'
                });
            }
        });
        if (dataToExport.length === 0) return showToast('Không có dữ liệu lỗi để xuất!', true);
        const form = document.createElement('form'); form.method = 'POST'; form.action = 'export_defects.php';
        const input = document.createElement('input'); input.type = 'hidden'; input.name = 'defects_data'; input.value = JSON.stringify(dataToExport);
        form.appendChild(input); document.body.appendChild(form); form.submit(); document.body.removeChild(form);
    }

    function openAIModal() {
        const dataRows = document.querySelectorAll('.defect-row');
        if (dataRows.length === 0) return showToast('Không có Defect nào để AI phân tích!', true);
        const dfData = [];
        dataRows.forEach(row => {
            if (row.style.display !== 'none') dfData.push({ title: row.querySelector('.df-title') ? row.querySelector('.df-title').value : '', steps: row.querySelector('.df-steps') ? row.querySelector('.df-steps').value : '', actual: row.querySelector('.df-actual') ? row.querySelector('.df-actual').value : '' });
        });
        const modal = document.getElementById('aiModal'); const modalContent = document.getElementById('aiModalContent');
        modal.classList.remove('hidden'); modal.classList.add('flex');
        setTimeout(() => { modal.classList.remove('opacity-0'); modalContent.classList.remove('scale-95'); modalContent.classList.add('scale-100'); }, 10);
        document.getElementById('aiLoading').classList.remove('hidden'); document.getElementById('aiLoadingHeader').classList.remove('hidden'); document.getElementById('aiLoadingFooter').classList.remove('hidden'); document.getElementById('aiResult').classList.add('hidden'); document.getElementById('aiResult').classList.remove('flex');

        const formData = new FormData(); formData.append('ajax_action', 'ai_analyze_defect'); formData.append('df_data', JSON.stringify(dfData));
        
        // Đổi đường dẫn tại đây
        fetch('pages/defect.php', { method: 'POST', body: formData })
            .then(async r => {
                const text = await r.text();
                try { return JSON.parse(text); } 
                catch (e) { alert("Lỗi AI chi tiết:\n" + text); throw new Error("Lỗi HTML rác"); }
            })
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('aiScore').textContent = data.score;
                    document.getElementById('aiScoreIconWrapper').className = `w-12 h-12 rounded-full flex items-center justify-center text-xl mb-3 shadow-sm bg-${data.color}-100 text-${data.color}-500`;
                    document.getElementById('aiScoreIcon').className = `fa-solid ${data.icon}`;
                    document.getElementById('aiStatusBadge').className = `px-3 py-1 rounded-full text-[12px] font-bold border border-${data.color}-200 bg-${data.color}-50 text-${data.color}-600`;
                    document.getElementById('aiStatusBadge').textContent = data.rating;
                    document.getElementById('aiGoodPoints').innerHTML = data.good_points.map(p => `<li class="flex gap-2 items-start"><i class="fa-regular fa-circle-check text-emerald-500 mt-0.5"></i> <span>${p}</span></li>`).join('');
                    const missingContainer = document.getElementById('aiMissingPoints').parentElement.parentElement;
                    if (data.missing_points.length > 0) {
                        missingContainer.style.display = 'block';
                        document.getElementById('aiMissingPoints').innerHTML = data.missing_points.map(p => `<li class="flex gap-2 items-start text-amber-900"><span class="w-1.5 h-1.5 rounded-full bg-amber-400 mt-1.5 shrink-0"></span> <span>${p}</span></li>`).join('');
                    } else { missingContainer.style.display = 'none'; }
                    document.getElementById('aiSuggestions').innerHTML = data.suggestions.join('<br>');
                    document.getElementById('aiLoading').classList.add('hidden'); document.getElementById('aiLoadingHeader').classList.add('hidden'); document.getElementById('aiLoadingFooter').classList.add('hidden'); document.getElementById('aiResult').classList.remove('hidden'); document.getElementById('aiResult').classList.add('flex');
                }
            }).catch(() => { showToast('Lỗi kết nối khi phân tích AI!', true); closeAIModal(); });
    }
    function closeAIModal() {
        const modal = document.getElementById('aiModal'); const modalContent = document.getElementById('aiModalContent');
        modal.classList.add('opacity-0'); modalContent.classList.remove('scale-100'); modalContent.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); }, 300);
    }
</script>