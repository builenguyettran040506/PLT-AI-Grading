<?php
$db_path = file_exists('includes/database.php') ? 'includes/database.php' : '../includes/database.php';
if (file_exists($db_path)) { require_once $db_path; }

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    try {
        $isGuest = (!isset($_SESSION['user']) && isset($_SESSION['role']) && $_SESSION['role'] === 'guest');
        
        // Ngăn Guest ghi Database
        if ($isGuest && in_array($_POST['ajax_action'], ['save', 'save_all', 'delete'])) {
            echo json_encode(['status' => 'success', 'message' => 'Lưu mô phỏng thành công (Bản dùng thử không thay đổi CSDL)!']);
            exit;
        }

        if ($_POST['ajax_action'] === 'ai_grade') {
            $tc_data = json_decode($_POST['tc_data'] ?? '[]', true);
            $score = 100;
            $good_points = []; $missing_points = []; $suggestions = [];
            $empty_expected = 0; $short_steps = 0;
            
            foreach ($tc_data as $tc) {
                if (empty(trim($tc['expected']))) { $score -= 5; $empty_expected++; }
                if (strlen(trim($tc['steps'])) < 30) { $score -= 3; $short_steps++; }
            }
            $score = max(40, min(100, $score));

            if ($score === 100) {
                $good_points[] = "Mô tả các bước thực hiện chi tiết, rõ ràng.";
                $good_points[] = "Tất cả kịch bản đều có Expected Result đầy đủ.";
                $suggestions[] = "Tuyệt vời! Bạn có thể xem xét bổ sung thêm Performance Test nếu cần.";
            } else {
                if ($short_steps == 0 && $empty_expected == 0) $good_points[] = "Cấu trúc kịch bản đầy đủ các thành phần cơ bản.";
                if ($empty_expected > 0) {
                    $missing_points[] = "Có $empty_expected kịch bản đang bị trống 'Kết quả mong đợi'.";
                    $suggestions[] = "Hãy bổ sung Expected Result để Tester biết cần đối chiếu với điều gì.";
                }
                if ($short_steps > 0) {
                    $missing_points[] = "Phát hiện $short_steps kịch bản có các bước (Procedure Steps) quá sơ sài.";
                    $suggestions[] = "Mô tả chi tiết hơn các thao tác click, nhập liệu để tránh hiểu lầm.";
                }
            }
            if (empty($good_points)) $good_points[] = "Đã xác định được luồng kiểm thử cơ bản.";

            if ($score >= 90) { $status = "Rất tốt"; $color = "emerald"; $icon = "fa-check"; } 
            elseif ($score >= 70) { $status = "Cần cải thiện"; $color = "amber"; $icon = "fa-exclamation"; } 
            else { $status = "Yếu"; $color = "red"; $icon = "fa-xmark"; }

            echo json_encode(['status' => 'success', 'score' => $score, 'rating' => $status, 'color' => $color, 'icon' => $icon, 'good_points' => $good_points, 'missing_points' => $missing_points, 'suggestions' => $suggestions]);
            exit;
        }

        if ($_POST['ajax_action'] === 'save') {
            $tc_code = $_POST['tc_code']; $priority = $_POST['priority'];
            $priority_id = ($priority === 'High') ? 3 : (($priority === 'Critical') ? 4 : (($priority === 'Low') ? 1 : 2));
            $stmtCheck = $pdo->prepare("SELECT id FROM test_cases WHERE tc_code = ?");
            $stmtCheck->execute([$tc_code]);
            if ($stmtCheck->rowCount() > 0) {
                $pdo->prepare("UPDATE test_cases SET title=?, estimation=?, area=?, procedure_steps=?, expected_results=?, priority_id=?, result_status=? WHERE tc_code=?")->execute([$_POST['title'], $_POST['estimation'], $_POST['area'], $_POST['procedure_steps'], $_POST['expected_results'], $priority_id, $_POST['result_status'], $tc_code]);
            } else {
                $pdo->prepare("INSERT INTO test_cases (tc_code, title, estimation, area, procedure_steps, expected_results, priority_id, result_status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 2)")->execute([$tc_code, $_POST['title'], $_POST['estimation'], $_POST['area'], $_POST['procedure_steps'], $_POST['expected_results'], $priority_id, $_POST['result_status']]);
            }
            echo json_encode(['status' => 'success', 'message' => 'Lưu thành công ' . $tc_code]);
            exit;
        }

        if ($_POST['ajax_action'] === 'save_all') {
            $testcases = json_decode($_POST['data'], true);
            $pdo->beginTransaction();
            $stmtCheck = $pdo->prepare("SELECT id FROM test_cases WHERE tc_code = ?");
            $stmtUpdate = $pdo->prepare("UPDATE test_cases SET title=?, estimation=?, area=?, procedure_steps=?, expected_results=?, priority_id=?, result_status=? WHERE tc_code=?");
            $stmtInsert = $pdo->prepare("INSERT INTO test_cases (tc_code, title, estimation, area, procedure_steps, expected_results, priority_id, result_status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 2)");

            foreach ($testcases as $tc) {
                $tc_code = $tc['tc_code'];
                $priority_id = ($tc['priority'] === 'High') ? 3 : (($tc['priority'] === 'Critical') ? 4 : (($tc['priority'] === 'Low') ? 1 : 2));
                $stmtCheck->execute([$tc_code]);
                if ($stmtCheck->rowCount() > 0) { $stmtUpdate->execute([$tc['title'], $tc['estimation'], $tc['area'], $tc['procedure_steps'], $tc['expected_results'], $priority_id, $tc['result_status'], $tc_code]); } 
                else { $stmtInsert->execute([$tc_code, $tc['title'], $tc['estimation'], $tc['area'], $tc['procedure_steps'], $tc['expected_results'], $priority_id, $tc['result_status']]); }
            }
            $pdo->commit();
            echo json_encode(['status' => 'success', 'message' => 'Đã lưu toàn bộ ' . count($testcases) . ' kịch bản!']);
            exit;
        }

        if ($_POST['ajax_action'] === 'delete') {
            $tc_code = $_POST['tc_code'];
            $pdo->prepare("DELETE FROM defects WHERE testcase_id = (SELECT id FROM test_cases WHERE tc_code = ?)")->execute([$tc_code]);
            $pdo->prepare("DELETE FROM test_cases WHERE tc_code = ?")->execute([$tc_code]);
            echo json_encode(['status' => 'success', 'message' => 'Đã xóa ' . $tc_code]);
            exit;
        }
    } catch (Exception $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        exit;
    }
}
?>

<!-- Khởi tạo Header -->
<header class="flex flex-wrap items-center justify-between mb-8 gap-4">
    <h1 class="text-2xl font-bold text-slate-800">Khung làm việc Test Case</h1>
    
    <div class="flex items-center gap-3">
        <a href="landing.php" class="flex items-center gap-2 px-4 py-2 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-[13px] font-semibold text-slate-600 transition shadow-sm">
            <i class="fa-solid fa-arrow-left-long text-slate-400"></i> Trang chủ
        </a>
        <div class="flex items-center gap-2 bg-slate-200/50 px-4 py-2 rounded-lg border border-slate-200">
            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
            <span class="text-[13px] font-bold text-slate-600">AI Engine: Online</span>
        </div>
        
        <?php $isGuest = (!isset($_SESSION['user']) && isset($_SESSION['role']) && $_SESSION['role'] === 'guest'); ?>
        <?php if ($isGuest): ?>
            <a href="landing.php" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold text-[13px] shadow-md shadow-indigo-500/30 transition">
                Đăng ký tài khoản
            </a>
        <?php else: ?>
            <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-md">QA</div>
        <?php endif; ?>
    </div>
</header>

<!-- Nội dung Container -->
<div class="w-full bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col overflow-hidden relative">
    
    <!-- Toolbar -->
    <div class="p-4 border-b border-slate-100 flex items-center justify-between gap-4 bg-white flex-wrap">
        <div class="relative w-full xl:w-[320px] shrink-0">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[13px]"></i>
            <input type="text" id="searchTC" onkeyup="filterTestCase()" placeholder="Tìm TC ID, Tiêu đề..." class="w-full border border-slate-300 rounded-lg pl-9 pr-3 py-2.5 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 bg-slate-50 hover:bg-slate-100 transition-colors">
        </div>

        <div class="flex items-center gap-2 overflow-x-auto custom-scrollbar w-full xl:w-auto">
            <input type="file" id="excelFileInput" class="hidden" accept=".xlsx, .xls, .csv" onchange="handleExcelUpload(event)">
            <button onclick="triggerExcelImport()" class="flex items-center gap-1.5 px-4 py-2.5 border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 rounded-lg text-[13px] text-emerald-700 transition shrink-0 font-semibold shadow-sm"><i class="fa-regular fa-file-excel text-emerald-600 text-lg"></i> Nhập Excel</button>
            <button onclick="saveAllRows()" class="flex items-center gap-1.5 px-4 py-2.5 border border-blue-200 bg-blue-50 hover:bg-blue-100 rounded-lg text-[13px] text-blue-700 transition shrink-0 font-semibold shadow-sm"><i class="fa-solid fa-layer-group"></i> Lưu tất cả</button>
            <button onclick="openAIModal()" class="flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[13px] font-semibold shadow-md shadow-indigo-500/30 transition shrink-0"><i class="fa-solid fa-wand-magic-sparkles"></i> AI Chấm điểm</button>
        </div>
    </div>

    <!-- Khung Bảng -->
    <div class="overflow-x-auto w-full custom-scrollbar min-h-[400px]">
        <div id="toast-msg" class="fixed top-6 right-6 bg-slate-800 text-white px-5 py-3 rounded-lg text-[13px] font-medium shadow-2xl opacity-0 transition-opacity pointer-events-none z-[100] flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-400"></i> <span id="toast-text">Thành công!</span>
        </div>

        <table class="w-full text-left border-collapse min-w-[1300px]">
            <thead>
                <tr class="bg-slate-50/70 border-b border-slate-200 text-[12px] font-bold text-slate-600 uppercase tracking-wide">
                    <th class="px-5 py-4 w-24 text-center">TC ID</th>
                    <th class="px-5 py-4 min-w-[250px]">Title</th>
                    <th class="px-5 py-4 w-24 text-center">Est.</th>
                    <th class="px-5 py-4 w-24 text-center">Area</th>
                    <th class="px-5 py-4 min-w-[280px]">Procedure Steps</th>
                    <th class="px-5 py-4 min-w-[250px]">Expected Results</th>
                    <th class="px-5 py-4 w-28 text-center">Priority</th>
                    <th class="px-5 py-4 w-32 text-center">RESULT</th>
                    <th class="px-5 py-4 w-32 text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody id="testcase-tbody" class="divide-y divide-slate-100">
                <tr id="empty-state-row">
                    <td colspan="9" class="px-4 py-24 text-center text-slate-400">
                        <div class="flex flex-col items-center justify-center opacity-60">
                            <i class="fa-regular fa-folder-open text-6xl mb-4 text-slate-300"></i>
                            <p class="text-[15px] font-semibold text-slate-500 mb-1">Khung làm việc đang trống</p>
                            <p class="text-[13px] text-slate-400 max-w-sm">Vui lòng bấm <b class="text-blue-500">"Thêm Test Case mới"</b> hoặc <b class="text-emerald-500">"Nhập Excel"</b> để bắt đầu soạn thảo.</p>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Footer Toolbar -->
    <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 w-full shrink-0">
        <button onclick="addTestCaseRow()" class="flex items-center gap-2 text-[14px] font-semibold text-blue-600 hover:text-blue-800 transition px-5 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 hover:border-blue-300 rounded-lg shadow-sm">
            <i class="fa-solid fa-plus"></i> Thêm Test Case mới
        </button>
    </div>
</div>

<!-- MODAL AI CHẤM ĐIỂM -->
<div id="aiModal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity opacity-0">
    <div class="bg-[#F8FAFC] rounded-2xl shadow-2xl w-full max-w-[420px] overflow-hidden transform scale-95 transition-transform duration-300 flex flex-col max-h-[90vh]" id="aiModalContent">
        <div id="aiLoadingHeader" class="bg-[#3B82F6] p-4 text-white flex justify-between items-center relative overflow-hidden">
            <h3 class="font-bold text-[15px] flex items-center gap-2 relative z-10"><i class="fa-solid fa-robot"></i> AI Tự Động Phân Tích</h3>
            <button onclick="closeAIModal()" class="text-white/80 hover:text-white transition-colors relative z-10"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <div id="aiLoading" class="flex flex-col items-center justify-center py-16 bg-white">
            <div class="relative w-12 h-12 mb-4"><div class="absolute inset-0 border-4 border-blue-200 rounded-full"></div><div class="absolute inset-0 border-4 border-blue-600 rounded-full border-t-transparent animate-spin"></div></div>
            <p class="text-[14px] font-medium text-slate-700">AI đang quét và phân tích kịch bản...</p>
        </div>
        <div id="aiResult" class="hidden flex-col h-full overflow-hidden">
            <button onclick="closeAIModal()" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 z-10"><i class="fa-solid fa-xmark text-lg"></i></button>
            <div class="bg-gradient-to-b from-blue-50 to-white px-6 pt-10 pb-6 flex flex-col items-center justify-center border-b border-slate-100 shrink-0">
                <div id="aiScoreIconWrapper" class="w-12 h-12 rounded-full flex items-center justify-center text-xl mb-3 shadow-sm"><i id="aiScoreIcon" class="fa-solid"></i></div>
                <div class="flex items-baseline gap-1 mb-2"><span id="aiScore" class="text-4xl font-extrabold text-slate-800">0</span><span class="text-xl font-bold text-slate-400">/100</span></div>
                <span id="aiStatusBadge" class="px-3 py-1 rounded-full text-[12px] font-bold border">Trạng thái</span>
            </div>
            <div class="p-5 overflow-y-auto custom-scrollbar bg-white flex-1">
                <div class="flex items-center gap-2 mb-4"><i class="fa-solid fa-wand-magic-sparkles text-indigo-500"></i><h4 class="font-bold text-slate-800 text-[14px]">Phân tích từ AI</h4></div>
                <div class="mb-5"><h5 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">ĐIỂM TỐT</h5><ul id="aiGoodPoints" class="space-y-2 text-[13px] text-slate-700"></ul></div>
                <div class="mb-5"><h5 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">THIẾU SÓT LOGIC / EDGE CASES</h5><div class="bg-amber-50 border border-amber-100 rounded-lg p-3"><ul id="aiMissingPoints" class="space-y-2 text-[13px] text-slate-700 mb-3"></ul></div></div>
                <div><h5 class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">GỢI Ý CẢI THIỆN</h5><div class="bg-blue-50 border border-blue-100 rounded-lg p-3 flex gap-2"><i class="fa-solid fa-angle-right text-blue-400 mt-0.5"></i><p id="aiSuggestions" class="text-[13px] text-slate-700 m-0"></p></div></div>
            </div>
        </div>
        <div id="aiLoadingFooter" class="p-4 bg-white flex justify-end border-t border-slate-100 shrink-0"><button onclick="closeAIModal()" class="px-6 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-[13px] font-bold rounded-lg transition-colors">Đóng</button></div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    function checkEmptyState() {
        const tbody = document.getElementById('testcase-tbody');
        const dataRows = tbody.querySelectorAll('.tc-data-row');
        document.getElementById('empty-state-row').style.display = dataRows.length === 0 ? 'table-row' : 'none';
    }

    function openAIModal() {
        const dataRows = document.querySelectorAll('.tc-data-row');
        if (dataRows.length === 0) { alert('Không có Test Case nào để AI phân tích!'); return; }

        const tcData = [];
        dataRows.forEach(row => {
            if (row.style.display !== 'none') {
                tcData.push({ title: row.querySelector('.tc-title').value, steps: row.querySelector('.tc-steps').value, expected: row.querySelector('.tc-expected').value });
            }
        });

        const modal = document.getElementById('aiModal'); const modalContent = document.getElementById('aiModalContent');
        modal.classList.remove('hidden'); modal.classList.add('flex');
        setTimeout(() => { modal.classList.remove('opacity-0'); modalContent.classList.remove('scale-95'); modalContent.classList.add('scale-100'); }, 10);
        
        document.getElementById('aiLoading').classList.remove('hidden'); document.getElementById('aiLoadingHeader').classList.remove('hidden'); document.getElementById('aiLoadingFooter').classList.remove('hidden'); document.getElementById('aiResult').classList.add('hidden'); document.getElementById('aiResult').classList.remove('flex');

        const formData = new FormData(); formData.append('ajax_action', 'ai_grade'); formData.append('tc_data', JSON.stringify(tcData)); 

        fetch('index.php?page=testcase', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                document.getElementById('aiScore').textContent = data.score;
                const iconWrapper = document.getElementById('aiScoreIconWrapper'); const icon = document.getElementById('aiScoreIcon'); const badge = document.getElementById('aiStatusBadge');
                
                iconWrapper.className = `w-12 h-12 rounded-full flex items-center justify-center text-xl mb-3 shadow-sm bg-${data.color}-100 text-${data.color}-500`;
                icon.className = `fa-solid ${data.icon}`;
                badge.className = `px-3 py-1 rounded-full text-[12px] font-bold border border-${data.color}-200 bg-${data.color}-50 text-${data.color}-600`;
                badge.textContent = data.rating;

                document.getElementById('aiGoodPoints').innerHTML = data.good_points.map(p => `<li class="flex gap-2 items-start"><i class="fa-regular fa-circle-check text-emerald-500 mt-0.5"></i> <span>${p}</span></li>`).join('');
                
                const missingContainer = document.getElementById('aiMissingPoints').parentElement.parentElement;
                if (data.missing_points.length > 0) {
                    missingContainer.style.display = 'block';
                    document.getElementById('aiMissingPoints').innerHTML = data.missing_points.map(p => `<li class="flex gap-2 items-start text-amber-900"><span class="w-1.5 h-1.5 rounded-full bg-amber-400 mt-1.5 shrink-0"></span> <span>${p}</span></li>`).join('');
                } else { missingContainer.style.display = 'none'; }

                document.getElementById('aiSuggestions').innerHTML = data.suggestions.join('<br>');
                
                document.getElementById('aiLoading').classList.add('hidden'); document.getElementById('aiLoadingHeader').classList.add('hidden'); document.getElementById('aiLoadingFooter').classList.add('hidden'); document.getElementById('aiResult').classList.remove('hidden'); document.getElementById('aiResult').classList.add('flex');
            }
        });
    }

    function closeAIModal() {
        const modal = document.getElementById('aiModal'); const modalContent = document.getElementById('aiModalContent');
        modal.classList.add('opacity-0'); modalContent.classList.remove('scale-100'); modalContent.classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); }, 300);
    }

    function showToast(message, isError = false) {
        const toast = document.getElementById('toast-msg'); document.getElementById('toast-text').innerHTML = message;
        toast.className = `fixed top-6 right-6 text-white px-5 py-3 rounded-lg text-[13px] font-medium shadow-2xl transition-opacity z-[100] flex items-center gap-2 ${isError ? 'bg-red-600' : 'bg-slate-800'}`;
        toast.innerHTML = (isError ? '<i class="fa-solid fa-circle-exclamation text-white"></i> ' : '<i class="fa-solid fa-circle-check text-emerald-400"></i> ') + `<span>${message}</span>`;
        toast.classList.remove('opacity-0'); setTimeout(() => toast.classList.add('opacity-0'), 3000);
    }

    function saveAllRows() {
        const rows = document.querySelectorAll('.tc-data-row'); const dataToSave = [];
        rows.forEach(row => {
            if (row.style.display !== 'none') {
                dataToSave.push({ tc_code: row.querySelector('.tc-code').value.trim(), title: row.querySelector('.tc-title').value.trim(), estimation: row.querySelector('.tc-est').value.trim(), area: row.querySelector('.tc-area').value.trim(), procedure_steps: row.querySelector('.tc-steps').value.trim(), expected_results: row.querySelector('.tc-expected').value.trim(), priority: row.querySelector('.tc-priority').value, result_status: row.querySelector('.tc-result').value });
            }
        });
        if (dataToSave.length === 0) { showToast('Không có dữ liệu để lưu!', true); return; }
        const formData = new FormData(); formData.append('ajax_action', 'save_all'); formData.append('data', JSON.stringify(dataToSave));
        fetch('index.php?page=testcase', { method: 'POST', body: formData }).then(r => r.json()).then(data => {
            if(data.status === 'success') {
                showToast(data.message);
                rows.forEach(row => { if (row.style.display !== 'none') { row.classList.remove('bg-indigo-50/40'); row.classList.add('bg-emerald-50'); setTimeout(() => row.classList.remove('bg-emerald-50'), 1000); } });
            } else { showToast(data.message, true); }
        });
    }

    function triggerExcelImport() { document.getElementById('excelFileInput').click(); }

    function handleExcelUpload(event) {
        const file = event.target.files[0]; if (!file) return;
        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const data = new Uint8Array(e.target.result); const workbook = XLSX.read(data, {type: 'array'});
                const worksheet = workbook.Sheets[workbook.SheetNames[0]]; const jsonData = XLSX.utils.sheet_to_json(worksheet, {header: 1});
                document.querySelectorAll('.tc-data-row').forEach(r => r.remove());
                for (let i = 1; i < jsonData.length; i++) {
                    const row = jsonData[i]; if (!row || row.length === 0 || row.join('').trim() === '') continue;
                    let steps = row[4] || ''; if (!steps.toLowerCase().includes('access the website') && steps !== '') { steps = "1. Access the website.\n" + (steps ? "2. " + steps : ""); }
                    pushRowToTable(row[1] || '', row[2] || '', '', row[3] || '', steps, row[5] || '', row[6] || 'Medium');
                }
                showToast('Đã nạp file Excel!');
            } catch (error) { showToast('Không thể đọc file.', true); } 
            finally { event.target.value = ''; checkEmptyState(); }
        };
        reader.readAsArrayBuffer(file);
    }

    function saveRow(btn) {
        const row = btn.closest('tr'); const formData = new FormData();
        formData.append('ajax_action', 'save'); formData.append('tc_code', row.querySelector('.tc-code').value.trim()); formData.append('title', row.querySelector('.tc-title').value.trim()); formData.append('estimation', row.querySelector('.tc-est').value.trim()); formData.append('area', row.querySelector('.tc-area').value.trim()); formData.append('procedure_steps', row.querySelector('.tc-steps').value.trim()); formData.append('expected_results', row.querySelector('.tc-expected').value.trim()); formData.append('priority', row.querySelector('.tc-priority').value); formData.append('result_status', row.querySelector('.tc-result').value);
        fetch('index.php?page=testcase', { method: 'POST', body: formData }).then(r => r.json()).then(data => {
            if(data.status === 'success') { showToast(data.message); row.classList.remove('bg-indigo-50/40'); row.classList.add('bg-emerald-50'); setTimeout(() => row.classList.remove('bg-emerald-50'), 1000); } 
            else { showToast(data.message, true); }
        });
    }

    function deleteRow(btn) {
        if (confirm('Bạn có chắc chắn muốn xóa kịch bản này?')) {
            const row = btn.closest('tr'); const formData = new FormData(); formData.append('ajax_action', 'delete'); formData.append('tc_code', row.querySelector('.tc-code').value.trim());
            fetch('index.php?page=testcase', { method: 'POST', body: formData }).then(r => r.json()).then(data => {
                if(data.status === 'success') { showToast(data.message); row.remove(); checkEmptyState(); } 
                else { showToast(data.message, true); }
            });
        }
    }

    function getNextTcId() {
        let maxId = 0;
        document.querySelectorAll('.tc-code').forEach(input => {
            const match = input.value.trim().match(/\d+/); 
            if (match) { const num = parseInt(match[0], 10); if (num > maxId) maxId = num; }
        });
        return maxId + 1;
    }

    function pushRowToTable(tcId, title, est, area, steps, expected, priority) {
        const tbody = document.getElementById('testcase-tbody'); const newRow = document.createElement('tr');
        if (!tcId) { tcId = "TC" + String(getNextTcId()).padStart(4, '0'); }
        newRow.className = 'tc-data-row hover:bg-slate-50/80 transition-colors group bg-white';
        newRow.innerHTML = `
            <td class="px-5 py-4 align-top pt-6"><input type="text" class="w-[90px] text-[13px] font-bold text-slate-700 text-center bg-transparent focus:outline-none border-b border-dashed border-slate-300 focus:border-blue-500 tc-code transition-colors" value="${tcId}"></td>
            <td class="px-5 py-4 align-top"><textarea rows="4" class="w-full text-[13px] text-slate-800 resize-none bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors leading-relaxed tc-title">${title}</textarea></td>
            <td class="px-5 py-4 align-top pt-5"><input type="text" class="w-full text-[13px] text-slate-800 text-center bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors tc-est" value="${est}"></td>
            <td class="px-5 py-4 align-top pt-5"><input type="text" class="w-full text-[13px] text-slate-800 text-center bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors tc-area" value="${area}"></td>
            <td class="px-5 py-4 align-top"><textarea rows="4" class="w-full text-[13px] text-slate-800 resize-none bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors leading-relaxed tc-steps">${steps}</textarea></td>
            <td class="px-5 py-4 align-top"><textarea rows="4" class="w-full text-[13px] text-slate-800 resize-none bg-emerald-50/20 hover:bg-emerald-50/70 focus:bg-white border border-transparent focus:border-emerald-500 rounded-md p-2 transition-colors leading-relaxed tc-expected">${expected}</textarea></td>
            <td class="px-5 py-4 align-top pt-5 text-center"><select class="w-full text-[13px] text-slate-800 font-bold bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors appearance-none cursor-pointer tc-priority text-center"><option value="High" ${priority.trim().toLowerCase() === 'high' ? 'selected' : ''}>High</option><option value="Medium" ${priority.trim().toLowerCase() === 'medium' || (priority.trim().toLowerCase() !== 'high' && priority.trim().toLowerCase() !== 'low') ? 'selected' : ''}>Medium</option><option value="Low" ${priority.trim().toLowerCase() === 'low' ? 'selected' : ''}>Low</option></select></td>
            <td class="px-5 py-4 align-top pt-5 text-center"><select class="w-full text-[13px] text-slate-800 font-bold bg-slate-50/50 hover:bg-slate-100 focus:bg-white border border-transparent focus:border-blue-500 rounded-md p-2 transition-colors appearance-none cursor-pointer tc-result text-center"><option value="Untested" selected>Untested</option><option value="Passed" class="text-emerald-600">Passed</option><option value="Failed" class="text-red-600">Failed</option></select></td>
            <td class="px-5 py-4 align-top pt-5 text-center"><div class="flex items-center justify-center gap-2 opacity-30 group-hover:opacity-100 transition-opacity"><a href="#" onclick="alert('Tính năng Xem chi tiết sẽ mở khi đã lưu.'); return false;" class="p-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition inline-flex items-center justify-center shadow-sm"><i class="fa-regular fa-eye"></i></a><button onclick="saveRow(this)" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition shadow-sm"><i class="fa-regular fa-floppy-disk"></i></button><button onclick="deleteRow(this)" class="p-2 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition shadow-sm"><i class="fa-solid fa-xmark"></i></button></div></td>
        `;
        document.getElementById('testcase-tbody').appendChild(newRow);
        checkEmptyState(); 
        newRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function addTestCaseRow() { pushRowToTable('', '', '', '', '1. Access the website.\n2. ', '', 'Medium'); }
    function filterTestCase() { const filter = document.getElementById('searchTC').value.toLowerCase(); document.querySelectorAll('.tc-data-row').forEach(row => { row.style.display = (row.querySelector('.tc-code').value.toLowerCase().indexOf(filter) > -1 || row.querySelector('.tc-title').value.toLowerCase().indexOf(filter) > -1) ? "" : "none"; }); }
    document.addEventListener("DOMContentLoaded", function() { checkEmptyState(); });
</script>