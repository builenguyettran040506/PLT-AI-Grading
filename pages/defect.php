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
// 1. XỬ LÝ AJAX CHO AI PHÂN TÍCH DEFECT
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {

    $isGuest = (isset($_SESSION['role']) && $_SESSION['role'] === 'guest');
    if ($isGuest) {
        if (in_array($_POST['ajax_action'], ['save', 'save_all', 'delete'])) {
            // Giả lập thành công trả về cho giao diện (Nhưng thực chất không Insert vào Database)
            echo json_encode(['status' => 'success', 'message' => 'Lưu ảo thành công (Chế độ dùng thử không thay đổi DB)!']);
            exit;
        }
    }
    if ($_POST['ajax_action'] === 'ai_analyze_defect') {
        header('Content-Type: application/json');

        $df_data = json_decode($_POST['df_data'] ?? '[]', true);
        $score = 100;
        $good_points = [];
        $missing_points = [];
        $suggestions = [];
        $empty_actual = 0;
        $short_steps = 0;

        foreach ($df_data as $df) {
            if (empty(trim($df['actual']))) {
                $score -= 5;
                $empty_actual++;
            }
            if (strlen(trim($df['steps'])) < 20) {
                $score -= 3;
                $short_steps++;
            }
        }

        $score = max(40, min(100, $score));

        if ($score === 100) {
            $good_points[] = "Mô tả lỗi (Actual Result) được ghi nhận rất chi tiết.";
            $good_points[] = "Các bước tái hiện (Steps) đầy đủ, dễ dàng cho Developer debug.";
            $suggestions[] = "Rất tốt! Hãy tiếp tục duy trì form mẫu log bug chuẩn xác này.";
        } else {
            if ($short_steps == 0 && $empty_actual == 0) $good_points[] = "Cấu trúc log lỗi đầy đủ các thành phần cơ bản.";
            if ($empty_actual > 0) {
                $missing_points[] = "Có $empty_actual Defect đang bị trống 'Kết quả thực tế' (Actual Result).";
                $suggestions[] = "Bổ sung Actual Result kèm hình ảnh (nếu có) để Developer hiểu rõ lỗi xảy ra như thế nào.";
            }
            if ($short_steps > 0) {
                $missing_points[] = "Phát hiện $short_steps Defect có các bước tái hiện (Steps) quá ngắn.";
                $suggestions[] = "Cần ghi rõ Step-by-step để Developer có thể tái hiện lỗi nhanh chóng.";
            }
        }

        if (empty($good_points)) $good_points[] = "Đã có ý thức phân loại mức độ nghiêm trọng (Severity).";

        if ($score >= 90) {
            $status = "Rất tốt";
            $color = "emerald";
            $icon = "fa-check";
        } elseif ($score >= 70) {
            $status = "Cần cải thiện";
            $color = "amber";
            $icon = "fa-exclamation";
        } else {
            $status = "Yếu";
            $color = "red";
            $icon = "fa-xmark";
        }

        echo json_encode(['status' => 'success', 'score' => $score, 'rating' => $status, 'color' => $color, 'icon' => $icon, 'good_points' => $good_points, 'missing_points' => $missing_points, 'suggestions' => $suggestions]);
        exit;
    }
}

// =====================================================================
// 2. XỬ LÝ LƯU, XÓA POST SUBMIT
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['ajax_action'])) {
    try {
        $getTcId = function ($tc_code) use ($pdo) {
            if (empty(trim($tc_code))) return null;
            $stmt = $pdo->prepare("SELECT id FROM test_cases WHERE tc_code = ? LIMIT 1");
            $stmt->execute([trim($tc_code)]);
            return $stmt->fetchColumn() ?: null;
        };

        if (isset($_POST['action_save_all']) || isset($_POST['action_save_single'])) {
            $pdo->beginTransaction();
            $stmtCheck = $pdo->prepare("SELECT id FROM defects WHERE defect_code = ?");
            $stmtUpdate = $pdo->prepare("UPDATE defects SET testcase_id=?, title=? WHERE defect_code=?");
            $stmtInsert = $pdo->prepare("INSERT INTO defects (defect_code, testcase_id, title) VALUES (?, ?, ?)");

            $idsToSave = isset($_POST['action_save_single']) ? [$_POST['action_save_single']] : ($_POST['defect_code'] ?? []);

            foreach ($idsToSave as $id) {
                $tc_code = $_POST['tc_code'][$id] ?? '';
                $title = $_POST['title'][$id] ?? '';
                $tc_id = $getTcId($tc_code);

                $stmtCheck->execute([$id]);
                if ($stmtCheck->rowCount() > 0) {
                    $stmtUpdate->execute([$tc_id, $title, $id]);
                } else {
                    $stmtInsert->execute([$id, $tc_id, $title]);
                }
            }
            $pdo->commit();
            $_SESSION['toast_msg'] = isset($_POST['action_save_single']) ? "Lưu thành công $idsToSave[0]!" : "Đã lưu toàn bộ danh sách Defect!";
            $_SESSION['toast_type'] = "success";
        } elseif (isset($_POST['action_delete'])) {
            $code = $_POST['action_delete'];
            $pdo->prepare("DELETE FROM defects WHERE defect_code = ?")->execute([$code]);
            $_SESSION['toast_msg'] = "Đã xóa thành công $code!";
            $_SESSION['toast_type'] = "success";
        }
    } catch (Exception $e) {
        if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
        $_SESSION['toast_msg'] = "Lỗi: " . $e->getMessage();
        $_SESSION['toast_type'] = "error";
    }
    header("Location: index.php?page=defect");
    exit;
}

// Giả lập dữ liệu mẫu
$defects = [
    [
        'defect_code' => 'BUG-1042',
        'title' => 'Lỗi timeout khi gọi API cổng thanh toán',
        'test_type' => 'Integration',
        'area' => 'Checkout',
        'steps' => "1. Thêm hàng vào giỏ\n2. Chọn Momo\n3. Bấm Thanh toán",
        'expected_result' => 'Chuyển sang trang Momo trong < 3s',
        'actual_result' => 'Load vô hạn và báo lỗi 504 Gateway Timeout',
        'severity' => 'High',
        'priority' => 'High'
    ],
    [
        'defect_code' => 'BUG-1043',
        'title' => "Nút 'Thêm vào giỏ hàng' bị lệch trên mobile UI",
        'test_type' => 'UI/UX',
        'area' => 'Product',
        'steps' => "1. Mở web trên iPhone 12\n2. Vào chi tiết sản phẩm",
        'expected_result' => 'Nút nằm giữa màn hình',
        'actual_result' => 'Nút bị tràn lề phải 20px',
        'severity' => 'Low',
        'priority' => 'Medium'
    ]
];
?>

<!-- Header Đã Sửa Tên -->
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

<form id="defectForm" method="POST" action="index.php?page=defect" enctype="multipart/form-data" class="w-full bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col overflow-hidden relative">

    <?php if (isset($_SESSION['toast_msg'])): ?>
        <div id="php-toast" class="absolute top-4 right-4 <?= $_SESSION['toast_type'] === 'success' ? 'bg-slate-800' : 'bg-red-600' ?> text-white px-5 py-3 rounded-lg text-[13px] font-medium shadow-xl transition-opacity pointer-events-none z-50 flex items-center gap-2">
            <i class="fa-solid <?= $_SESSION['toast_type'] === 'success' ? 'fa-circle-check text-emerald-400' : 'fa-circle-exclamation text-white' ?>"></i>
            <span><?= $_SESSION['toast_msg'] ?></span>
        </div>
        <?php unset($_SESSION['toast_msg']);
        unset($_SESSION['toast_type']); ?>
    <?php endif; ?>

    <!-- Toolbar Đã Sắp Xếp Lại -->
    <div class="p-4 border-b border-slate-100 flex flex-col xl:flex-row xl:items-center justify-between gap-4 bg-white">
        <div class="relative w-full xl:w-[320px] shrink-0">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[13px]"></i>
            <input type="text" id="searchDefect" onkeyup="filterDefect()" placeholder="Tìm kiếm Defect ID, Tiêu đề..." class="w-full border border-slate-300 rounded-lg pl-9 pr-3 py-2.5 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 bg-slate-50 hover:bg-slate-100 focus:bg-white transition-colors">
        </div>

        <div class="flex items-center gap-2 overflow-x-auto custom-scrollbar pb-1 -mb-1 w-full justify-start xl:justify-end">
            <!-- Nút Lọc (Chỉ hiển thị cho đẹp) -->
            <button type="button" class="flex items-center gap-1.5 px-4 py-2 border border-slate-200 rounded-lg text-[13px] text-slate-600 hover:bg-slate-50 transition shrink-0 font-medium">
                <i class="fa-solid fa-filter text-slate-400"></i> Lọc
            </button>

            <!-- Import Excel -->
            <input type="file" id="excelFileInput" class="hidden" accept=".xlsx, .xls, .csv" onchange="handleExcelUpload(event)">
            <button type="button" onclick="document.getElementById('excelFileInput').click();" class="flex items-center gap-1.5 px-4 py-2 border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 rounded-lg text-[13px] text-emerald-700 transition shrink-0 font-semibold shadow-sm">
                <i class="fa-regular fa-file-excel text-emerald-600 text-lg"></i> Nhập Excel
            </button>

            <!-- Lưu hàng loạt -->
            <button type="submit" name="action_save_all" class="flex items-center gap-1.5 px-4 py-2 border border-blue-200 bg-blue-50 hover:bg-blue-100 rounded-lg text-[13px] text-blue-700 transition shrink-0 font-semibold shadow-sm">
                <i class="fa-solid fa-layer-group"></i> Lưu hàng loạt
            </button>

            <!-- Nút AI Phân tích -->
            <button type="button" onclick="openAIModal()" class="flex items-center gap-2 px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[13px] font-semibold shadow-md shadow-indigo-500/30 transition shrink-0">
                <i class="fa-solid fa-wand-magic-sparkles"></i> AI Chấm điểm
            </button>
        </div>
    </div>

    <!-- Bảng Dữ Liệu Được Bo Viền Cho Textarea -->
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
                    <th class="px-5 py-4 w-32 text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody id="defect-tbody" class="divide-y divide-slate-100">

                <?php
                $stt = 1;
                foreach ($defects as $df):
                    $id = htmlspecialchars($df['defect_code']);
                ?>
                    <tr class="hover:bg-slate-50/50 transition-colors defect-row group bg-white">
                        <input type="hidden" name="defect_code[]" value="<?= $id ?>">

                        <td class="px-5 py-5 text-[13px] text-slate-500 text-center align-top pt-7"><?= $stt++ ?></td>
                        <td class="px-5 py-5 font-semibold text-slate-800 text-[13px] text-center align-top pt-7 df-code"><?= $id ?></td>

                        <td class="px-5 py-5 align-top">
                            <textarea name="title[<?= $id ?>]" rows="3" class="w-full text-[13px] text-slate-800 resize-none bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors leading-relaxed df-title"><?= htmlspecialchars($df['title']) ?></textarea>
                        </td>

                        <td class="px-5 py-5 align-top pt-6">
                            <input type="text" name="test_type[<?= $id ?>]" class="w-full text-[13px] text-slate-800 text-center bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors" value="<?= htmlspecialchars($df['test_type']) ?>">
                        </td>

                        <td class="px-5 py-5 align-top pt-6">
                            <input type="text" name="area[<?= $id ?>]" class="w-full text-[13px] text-slate-800 text-center bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors" value="<?= htmlspecialchars($df['area']) ?>">
                        </td>

                        <td class="px-5 py-5 align-top">
                            <textarea name="steps[<?= $id ?>]" rows="4" class="w-full text-[13px] text-slate-800 resize-none bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors leading-relaxed"><?= htmlspecialchars($df['steps']) ?></textarea>
                        </td>

                        <td class="px-5 py-5 align-top">
                            <textarea name="expected_result[<?= $id ?>]" rows="4" class="w-full text-[13px] text-slate-800 resize-none bg-emerald-50/30 hover:bg-emerald-50/80 focus:bg-white border border-transparent focus:border-emerald-500 rounded-md p-2 transition-colors leading-relaxed"><?= htmlspecialchars($df['expected_result']) ?></textarea>
                        </td>

                        <td class="px-5 py-5 align-top">
                            <textarea name="actual_result[<?= $id ?>]" rows="4" class="w-full text-[13px] text-slate-800 resize-none bg-red-50/30 hover:bg-red-50/80 focus:bg-white border border-transparent focus:border-red-500 rounded-md p-2 transition-colors leading-relaxed"><?= htmlspecialchars($df['actual_result']) ?></textarea>
                        </td>

                        <td class="px-5 py-5 align-top text-center pt-7">
                            <?php
                            $sevColor = 'bg-slate-100 text-slate-500 border-slate-200';
                            if ($df['severity'] === 'High') $sevColor = 'bg-orange-50 text-orange-600 border-orange-200';
                            if ($df['severity'] === 'Critical') $sevColor = 'bg-red-50 text-red-600 border-red-200';
                            ?>
                            <span class="inline-block px-3 py-1 <?= $sevColor ?> border rounded-full text-[12px] font-bold shadow-sm"><?= htmlspecialchars($df['severity']) ?></span>
                            <input type="hidden" name="severity[<?= $id ?>]" value="<?= htmlspecialchars($df['severity']) ?>">
                        </td>

                        <td class="px-5 py-5 align-top pt-6 text-center">
                            <input type="text" name="priority[<?= $id ?>]" class="w-full font-bold text-slate-700 text-center text-[13px] bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors" value="<?= htmlspecialchars($df['priority']) ?>">
                        </td>

                        <td class="px-5 py-5 align-top pt-6 text-center">
                            <div class="flex items-center justify-center gap-2 opacity-30 group-hover:opacity-100 transition-opacity">
                                <button type="submit" name="action_save_single" value="<?= $id ?>" title="Lưu dòng này" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition shadow-sm">
                                    <i class="fa-regular fa-floppy-disk"></i>
                                </button>
                                <button type="submit" name="action_delete" value="<?= $id ?>" onclick="return confirm('Xác nhận xóa <?= $id ?>?');" title="Xóa dòng này" class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition shadow-sm">
                                    <i class="fa-regular fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Nút Thêm Mới được dời xuống đáy bảng cho gọn gàng -->
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50">
            <button type="button" onclick="addDefectRow()" class="flex items-center gap-2 text-[14px] font-semibold text-blue-600 hover:text-blue-800 transition px-4 py-2 bg-white hover:bg-blue-50 border border-slate-200 hover:border-blue-200 rounded-lg shadow-sm">
                <i class="fa-solid fa-plus"></i> Thêm Defect mới
            </button>
        </div>
    </div>
</form>

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
                        <ul id="aiMissingPoints" class="space-y-2 text-[13px] text-slate-700 mb-3"></ul><button class="w-full py-2 bg-amber-200/50 hover:bg-amber-200 text-amber-700 text-[12px] font-bold rounded transition-colors flex items-center justify-center gap-1.5 border border-amber-300/50"><i class="fa-solid fa-plus"></i> Tự động cập nhật Defect</button>
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
    let bugIdCounter = 1044;

    function addDefectRow() {
        rowCount++;
        const tbody = document.getElementById('defect-tbody');
        const id = "BUG-" + bugIdCounter++;

        const newRow = document.createElement('tr');
        newRow.className = 'hover:bg-slate-50/80 transition-colors defect-row group bg-indigo-50/30';

        newRow.innerHTML = `
            <input type="hidden" name="defect_code[]" value="${id}">
            <td class="px-5 py-5 text-[13px] text-slate-500 text-center align-top pt-7">${rowCount}</td>
            <td class="px-5 py-5 font-semibold text-slate-800 text-[13px] text-center align-top pt-7 df-code">${id}</td>
            <td class="px-5 py-5 align-top"><textarea name="title[${id}]" rows="3" class="w-full text-[13px] text-slate-800 resize-none bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors leading-relaxed df-title" placeholder="Nhập tiêu đề lỗi..."></textarea></td>
            <td class="px-5 py-5 align-top pt-6"><input type="text" name="test_type[${id}]" class="w-full text-[13px] text-slate-800 text-center bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors" placeholder="Test Type"></td>
            <td class="px-5 py-5 align-top pt-6"><input type="text" name="area[${id}]" class="w-full text-[13px] text-slate-800 text-center bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors" placeholder="Area"></td>
            <td class="px-5 py-5 align-top"><textarea name="steps[${id}]" rows="4" class="w-full text-[13px] text-slate-800 resize-none bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors leading-relaxed">1. Access the website...\n2. </textarea></td>
            <td class="px-5 py-5 align-top"><textarea name="expected_result[${id}]" rows="4" class="w-full text-[13px] text-slate-800 resize-none bg-emerald-50/30 hover:bg-emerald-50/80 focus:bg-white border border-transparent focus:border-emerald-500 rounded-md p-2 transition-colors leading-relaxed" placeholder="Kết quả mong muốn..."></textarea></td>
            <td class="px-5 py-5 align-top"><textarea name="actual_result[${id}]" rows="4" class="w-full text-[13px] text-slate-800 resize-none bg-red-50/30 hover:bg-red-50/80 focus:bg-white border border-transparent focus:border-red-500 rounded-md p-2 transition-colors leading-relaxed" placeholder="Kết quả thực tế..."></textarea></td>
            <td class="px-5 py-5 align-top text-center pt-7"><select name="severity[${id}]" class="px-3 py-1 border border-slate-300 rounded-full text-[12px] font-bold text-slate-600 focus:outline-none bg-white cursor-pointer"><option value="High" class="text-orange-600">High</option><option value="Medium" selected>Medium</option><option value="Low" class="text-slate-500">Low</option></select></td>
            <td class="px-5 py-5 align-top pt-6 text-center"><select name="priority[${id}]" class="w-full text-[13px] text-slate-800 font-bold text-center bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors appearance-none cursor-pointer"><option value="High">High</option><option value="Medium" selected>Medium</option><option value="Low">Low</option></select></td>
            <td class="px-5 py-5 align-top pt-6 text-center"><div class="flex items-center justify-center gap-2 opacity-30 group-hover:opacity-100 transition-opacity"><button type="submit" name="action_save_single" value="${id}" title="Lưu dòng này" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition shadow-sm"><i class="fa-regular fa-floppy-disk"></i></button><button type="button" onclick="this.closest('tr').remove()" title="Hủy dòng" class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition shadow-sm"><i class="fa-solid fa-xmark"></i></button></div></td>
        `;
        tbody.appendChild(newRow);
        newRow.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest'
        });
    }

    function handleExcelUpload(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, {
                    type: 'array'
                });
                const worksheet = workbook.Sheets[workbook.SheetNames[0]];
                const jsonData = XLSX.utils.sheet_to_json(worksheet, {
                    header: 1
                });
                document.querySelectorAll('.defect-row').forEach(r => r.remove());
                for (let i = 1; i < jsonData.length; i++) {
                    const row = jsonData[i];
                    if (!row || row.length === 0 || row.join('').trim() === '') continue;
                    let steps = row[5] || '';
                    if (!steps.toLowerCase().includes('access the website') && steps !== '') {
                        steps = "1. Access the website.\n" + (steps ? "2. " + steps : "");
                    }
                    const id = row[1] || "BUG-" + Math.floor(Math.random() * 9000 + 1000);

                    const newRow = document.createElement('tr');
                    newRow.className = 'hover:bg-slate-50/80 transition-colors defect-row group bg-indigo-50/30';
                    newRow.innerHTML = `
                        <input type="hidden" name="defect_code[]" value="${id}">
                        <td class="px-5 py-5 text-[13px] text-slate-500 text-center align-top pt-7">#</td>
                        <td class="px-5 py-5 font-semibold text-slate-800 text-[13px] text-center align-top pt-7 df-code">${id}</td>
                        <td class="px-5 py-5 align-top"><textarea name="title[${id}]" rows="3" class="w-full text-[13px] text-slate-800 resize-none bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors leading-relaxed df-title">${row[2] || ''}</textarea></td>
                        <td class="px-5 py-5 align-top pt-6"><input type="text" name="test_type[${id}]" class="w-full text-[13px] text-slate-800 text-center bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors" value="${row[3] || ''}"></td>
                        <td class="px-5 py-5 align-top pt-6"><input type="text" name="area[${id}]" class="w-full text-[13px] text-slate-800 text-center bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors" value="${row[4] || ''}"></td>
                        <td class="px-5 py-5 align-top"><textarea name="steps[${id}]" rows="4" class="w-full text-[13px] text-slate-800 resize-none bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors leading-relaxed">${steps}</textarea></td>
                        <td class="px-5 py-5 align-top"><textarea name="expected_result[${id}]" rows="4" class="w-full text-[13px] text-slate-800 resize-none bg-emerald-50/30 hover:bg-emerald-50/80 focus:bg-white border border-transparent focus:border-emerald-500 rounded-md p-2 transition-colors leading-relaxed">${row[6] || ''}</textarea></td>
                        <td class="px-5 py-5 align-top"><textarea name="actual_result[${id}]" rows="4" class="w-full text-[13px] text-slate-800 resize-none bg-red-50/30 hover:bg-red-50/80 focus:bg-white border border-transparent focus:border-red-500 rounded-md p-2 transition-colors leading-relaxed">${row[7] || ''}</textarea></td>
                        <td class="px-5 py-5 align-top text-center pt-7"><select name="severity[${id}]" class="px-3 py-1 border border-slate-300 rounded-full text-[12px] font-bold text-slate-600 focus:outline-none bg-white cursor-pointer"><option value="High" class="text-orange-600" ${(row[8]||'').trim().toLowerCase() === 'high' ? 'selected' : ''}>High</option><option value="Medium" ${(row[8]||'').trim().toLowerCase() === 'medium' || ((row[8]||'').trim().toLowerCase() !== 'high' && (row[8]||'').trim().toLowerCase() !== 'low') ? 'selected' : ''}>Medium</option><option value="Low" class="text-slate-500" ${(row[8]||'').trim().toLowerCase() === 'low' ? 'selected' : ''}>Low</option></select></td>
                        <td class="px-5 py-5 align-top pt-6 text-center"><select name="priority[${id}]" class="w-full text-[13px] text-slate-800 font-bold text-center bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors appearance-none cursor-pointer"><option value="High" ${(row[9]||'').trim().toLowerCase() === 'high' ? 'selected' : ''}>High</option><option value="Medium" ${(row[9]||'').trim().toLowerCase() === 'medium' || ((row[9]||'').trim().toLowerCase() !== 'high' && (row[9]||'').trim().toLowerCase() !== 'low') ? 'selected' : ''}>Medium</option><option value="Low" ${(row[9]||'').trim().toLowerCase() === 'low' ? 'selected' : ''}>Low</option></select></td>
                        <td class="px-5 py-5 align-top pt-6 text-center"><div class="flex items-center justify-center gap-2 opacity-30 group-hover:opacity-100 transition-opacity"><button type="submit" name="action_save_single" value="${id}" title="Lưu dòng này" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition shadow-sm"><i class="fa-regular fa-floppy-disk"></i></button><button type="button" onclick="this.closest('tr').remove()" title="Hủy dòng" class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition shadow-sm"><i class="fa-solid fa-xmark"></i></button></div></td>
                    `;
                    document.getElementById('defect-tbody').appendChild(newRow);
                }
            } catch (error) {
                alert('Không thể đọc file Excel này.');
            } finally {
                event.target.value = '';
            }
        };
        reader.readAsArrayBuffer(file);
    }

    function filterDefect() {
        const filter = document.getElementById('searchDefect').value.toLowerCase();
        document.querySelectorAll('.defect-row').forEach(row => {
            const codeCell = row.querySelector('.df-code');
            const titleCell = row.querySelector('.df-title');
            if (codeCell && titleCell) {
                const textCode = codeCell.innerText || codeCell.textContent;
                const textTitle = titleCell.value;
                row.style.display = (textCode.toLowerCase().indexOf(filter) > -1 || textTitle.toLowerCase().indexOf(filter) > -1) ? "" : "none";
            }
        });
    }

    function openAIModal() {
        const dataRows = document.querySelectorAll('.defect-row');
        if (dataRows.length === 0) {
            alert('Không có Defect nào để AI phân tích!');
            return;
        }

        const dfData = [];
        dataRows.forEach(row => {
            if (row.style.display !== 'none') {
                const title = row.querySelector('.df-title') ? row.querySelector('.df-title').value : '';
                const steps = row.querySelector('textarea[name^="steps"]') ? row.querySelector('textarea[name^="steps"]').value : '';
                const actual = row.querySelector('textarea[name^="actual"]') ? row.querySelector('textarea[name^="actual"]').value : '';
                dfData.push({
                    title: title,
                    steps: steps,
                    actual: actual
                });
            }
        });

        const modal = document.getElementById('aiModal');
        const modalContent = document.getElementById('aiModalContent');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
            modalContent.classList.add('scale-100');
        }, 10);

        document.getElementById('aiLoading').classList.remove('hidden');
        document.getElementById('aiLoadingHeader').classList.remove('hidden');
        document.getElementById('aiLoadingFooter').classList.remove('hidden');
        document.getElementById('aiResult').classList.add('hidden');
        document.getElementById('aiResult').classList.remove('flex');

        const formData = new FormData();
        formData.append('ajax_action', 'ai_analyze_defect');
        formData.append('df_data', JSON.stringify(dfData));

        fetch('index.php?page=defect', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.getElementById('aiScore').textContent = data.score;
                    const iconWrapper = document.getElementById('aiScoreIconWrapper');
                    const icon = document.getElementById('aiScoreIcon');
                    const badge = document.getElementById('aiStatusBadge');

                    iconWrapper.className = `w-12 h-12 rounded-full flex items-center justify-center text-xl mb-3 shadow-sm bg-${data.color}-100 text-${data.color}-500`;
                    icon.className = `fa-solid ${data.icon}`;
                    badge.className = `px-3 py-1 rounded-full text-[12px] font-bold border border-${data.color}-200 bg-${data.color}-50 text-${data.color}-600`;
                    badge.textContent = data.rating;

                    document.getElementById('aiGoodPoints').innerHTML = data.good_points.map(p => `<li class="flex gap-2 items-start"><i class="fa-regular fa-circle-check text-emerald-500 mt-0.5"></i> <span>${p}</span></li>`).join('');

                    const missingContainer = document.getElementById('aiMissingPoints').parentElement.parentElement;
                    if (data.missing_points.length > 0) {
                        missingContainer.style.display = 'block';
                        document.getElementById('aiMissingPoints').innerHTML = data.missing_points.map(p => `<li class="flex gap-2 items-start text-amber-900"><span class="w-1.5 h-1.5 rounded-full bg-amber-400 mt-1.5 shrink-0"></span> <span>${p}</span></li>`).join('');
                    } else {
                        missingContainer.style.display = 'none';
                    }

                    document.getElementById('aiSuggestions').innerHTML = data.suggestions.join('<br>');

                    document.getElementById('aiLoading').classList.add('hidden');
                    document.getElementById('aiLoadingHeader').classList.add('hidden');
                    document.getElementById('aiLoadingFooter').classList.add('hidden');
                    document.getElementById('aiResult').classList.remove('hidden');
                    document.getElementById('aiResult').classList.add('flex');
                }
            });
    }

    function closeAIModal() {
        const modal = document.getElementById('aiModal');
        const modalContent = document.getElementById('aiModalContent');
        modal.classList.add('opacity-0');
        modalContent.classList.remove('scale-100');
        modalContent.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 300);
    }
</script>