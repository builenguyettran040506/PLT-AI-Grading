<?php
// =====================================================================
// 1. XỬ LÝ AJAX LƯU & XÓA (Gửi ngầm không cần tải lại trang)
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    $db_path = file_exists('includes/database.php') ? 'includes/database.php' : '../includes/database.php';
    if(file_exists($db_path)) {
        require_once $db_path;
        header('Content-Type: application/json'); 
        
        try {
            // XỬ LÝ LƯU 1 DÒNG
            if ($_POST['ajax_action'] === 'save') {
                $tc_code = $_POST['tc_code'];
                $title = $_POST['title'];
                $estimation = $_POST['estimation'];
                $area = $_POST['area'];
                $procedure = $_POST['procedure_steps'];
                $expected = $_POST['expected_results'];
                $priority = $_POST['priority'];
                $result_status = $_POST['result_status'];
                
                $priority_id = 2; // Medium
                if ($priority === 'High') $priority_id = 3;
                if ($priority === 'Critical') $priority_id = 4;
                if ($priority === 'Low') $priority_id = 1;

                $stmtCheck = $pdo->prepare("SELECT id FROM test_cases WHERE tc_code = ?");
                $stmtCheck->execute([$tc_code]);
                
                if ($stmtCheck->rowCount() > 0) {
                    $sql = "UPDATE test_cases SET title=?, estimation=?, area=?, procedure_steps=?, expected_results=?, priority_id=?, result_status=? WHERE tc_code=?";
                    $pdo->prepare($sql)->execute([$title, $estimation, $area, $procedure, $expected, $priority_id, $result_status, $tc_code]);
                } else {
                    $sql = "INSERT INTO test_cases (tc_code, title, estimation, area, procedure_steps, expected_results, priority_id, result_status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 2)";
                    $pdo->prepare($sql)->execute([$tc_code, $title, $estimation, $area, $procedure, $expected, $priority_id, $result_status]);
                }
                
                echo json_encode(['status' => 'success', 'message' => 'Lưu thành công ' . $tc_code]);
                exit;
            }

            // XỬ LÝ LƯU TẤT CẢ CÁC DÒNG (BULK SAVE)
            if ($_POST['ajax_action'] === 'save_all') {
                $testcases = json_decode($_POST['data'], true);
                
                // Sử dụng Transaction để tối ưu tốc độ khi lưu nhiều dòng cùng lúc
                $pdo->beginTransaction();
                
                $stmtCheck = $pdo->prepare("SELECT id FROM test_cases WHERE tc_code = ?");
                $stmtUpdate = $pdo->prepare("UPDATE test_cases SET title=?, estimation=?, area=?, procedure_steps=?, expected_results=?, priority_id=?, result_status=? WHERE tc_code=?");
                $stmtInsert = $pdo->prepare("INSERT INTO test_cases (tc_code, title, estimation, area, procedure_steps, expected_results, priority_id, result_status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 2)");

                foreach ($testcases as $tc) {
                    $tc_code = $tc['tc_code'];
                    $title = $tc['title'];
                    $estimation = $tc['estimation'];
                    $area = $tc['area'];
                    $procedure = $tc['procedure_steps'];
                    $expected = $tc['expected_results'];
                    $priority = $tc['priority'];
                    $result_status = $tc['result_status'];
                    
                    $priority_id = 2; // Medium
                    if ($priority === 'High') $priority_id = 3;
                    if ($priority === 'Critical') $priority_id = 4;
                    if ($priority === 'Low') $priority_id = 1;

                    $stmtCheck->execute([$tc_code]);
                    if ($stmtCheck->rowCount() > 0) {
                        $stmtUpdate->execute([$title, $estimation, $area, $procedure, $expected, $priority_id, $result_status, $tc_code]);
                    } else {
                        $stmtInsert->execute([$tc_code, $title, $estimation, $area, $procedure, $expected, $priority_id, $result_status]);
                    }
                }
                
                $pdo->commit(); // Xác nhận lưu toàn bộ
                echo json_encode(['status' => 'success', 'message' => 'Đã lưu toàn bộ ' . count($testcases) . ' kịch bản!']);
                exit;
            }
            
            // XỬ LÝ XÓA
            if ($_POST['ajax_action'] === 'delete') {
                $tc_code = $_POST['tc_code'];
                $pdo->prepare("DELETE FROM defects WHERE testcase_id = (SELECT id FROM test_cases WHERE tc_code = ?)")->execute([$tc_code]);
                $pdo->prepare("DELETE FROM test_cases WHERE tc_code = ?")->execute([$tc_code]);
                
                echo json_encode(['status' => 'success', 'message' => 'Đã xóa ' . $tc_code]);
                exit;
            }
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack(); // Hoàn tác nếu có lỗi trong quá trình lưu nhiều dòng
            }
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
            exit;
        }
    }
}
?>

<!-- Header nội dung -->
<header class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold text-slate-800">Quản lý Test Case</h1>
    <div class="flex items-center gap-6">
        <div class="flex items-center gap-2 bg-slate-200/50 px-3 py-1.5 rounded-full border border-slate-200">
            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
            <span class="text-sm font-medium text-slate-600">AI Engine: Online</span>
        </div>
        <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-md cursor-pointer">QA</div>
    </div>
</header>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col overflow-hidden">
        
        <!-- Toolbar -->
        <div class="p-4 border-b border-slate-100 flex items-center justify-between gap-4 bg-white overflow-hidden">
            
            <div class="flex items-center gap-2 overflow-x-auto custom-scrollbar pb-1 -mb-1 w-full sm:w-auto justify-start sm:justify-end">
                <div class="relative w-[180px] md:w-[220px] shrink-0">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[12px]"></i>
                    <input type="text" id="searchTC" onkeyup="filterTestCase()" placeholder="Tìm TC ID, Tiêu đề..." class="w-full border border-slate-300 rounded-lg pl-8 pr-3 py-1.5 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 bg-slate-50 focus:bg-white transition-colors">
                </div>
                
                <input type="file" id="excelFileInput" class="hidden" accept=".xlsx, .xls, .csv" onchange="handleExcelUpload(event)">
                <button onclick="triggerExcelImport()" class="flex items-center gap-1.5 px-3 py-1.5 border border-slate-200 rounded-lg text-[13px] text-slate-700 hover:bg-slate-50 transition shrink-0 whitespace-nowrap">
                    <i class="fa-regular fa-file-excel text-emerald-600"></i> Nhập Excel
                </button>
                
                <!-- Đổi nút Lưu nháp thành Lưu tất cả -->
                <button onclick="saveAllRows()" class="flex items-center gap-1.5 px-3 py-1.5 border border-slate-200 rounded-lg text-[13px] text-blue-700 bg-blue-50 hover:bg-blue-100 transition shrink-0 whitespace-nowrap font-semibold">
                    <i class="fa-solid fa-layer-group"></i> Lưu tất cả
                </button>
                
                <button class="flex items-center gap-1.5 px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[13px] font-medium shadow-sm transition shrink-0 whitespace-nowrap">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> AI Chấm điểm
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto flex-1 custom-scrollbar pb-2 relative">
            <div id="toast-msg" class="absolute top-4 right-4 bg-slate-800 text-white px-4 py-2 rounded-lg text-sm shadow-lg opacity-0 transition-opacity pointer-events-none z-50 flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-400"></i> <span id="toast-text">Thành công!</span>
            </div>

            <table class="w-full text-left border-collapse min-w-[1250px]">
                <thead>
                    <tr class="bg-slate-50/30 border-b border-slate-200 text-[13px] font-bold text-slate-700">
                        <th class="px-4 py-3.5 w-20 text-center">TC ID</th>
                        <th class="px-4 py-3.5 min-w-[220px]">Title</th>
                        <th class="px-4 py-3.5 w-24 text-center">Estimation</th>
                        <th class="px-4 py-3.5 w-20 text-center">Area</th>
                        <th class="px-4 py-3.5 min-w-[250px]">Procedure Steps</th>
                        <th class="px-4 py-3.5 min-w-[200px]">Expected Results</th>
                        <th class="px-4 py-3.5 w-24">Priority</th>
                        <th class="px-4 py-3.5 w-28">RESULT</th>
                        <th class="px-4 py-3.5 w-24 text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="testcase-tbody" class="divide-y divide-slate-100">
                    
                    <!-- 1 TEST CASE MẪU DUY NHẤT -->
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-4 py-4 text-[13px] font-semibold text-slate-700 text-center align-top pt-5 tc-code">TC0001</td>
                        <td class="px-4 py-4 align-top">
                            <textarea rows="3" class="w-full text-[13px] text-slate-800 resize-none focus:outline-none bg-transparent leading-relaxed tc-title">Xác thực luồng thanh toán giỏ hàng qua ví điện tử</textarea>
                        </td>
                        <td class="px-4 py-4 align-top">
                            <input type="text" class="w-full text-[13px] text-slate-800 focus:outline-none bg-transparent text-center mt-1 tc-est" value="2h">
                        </td>
                        <td class="px-4 py-4 align-top">
                            <input type="text" class="w-full text-[13px] text-slate-800 focus:outline-none bg-transparent text-center mt-1 tc-area" value="Checkout">
                        </td>
                        <td class="px-4 py-4 align-top">
                            <textarea rows="4" class="w-full text-[13px] text-slate-800 resize-none focus:outline-none bg-transparent leading-relaxed tc-steps">1. Access the website.&#10;2. Chuyển đến trang giỏ hàng.&#10;3. Nhấn nút Tiến hành thanh toán.&#10;4. Chọn Momo.</textarea>
                        </td>
                        <td class="px-4 py-4 align-top">
                            <textarea rows="4" class="w-full text-[13px] text-slate-800 resize-none focus:outline-none bg-transparent leading-relaxed tc-expected">Chuyển hướng sang cổng thanh toán Momo thành công.</textarea>
                        </td>
                        <td class="px-4 py-4 align-top pt-5">
                            <select class="w-full text-[13px] text-slate-800 focus:outline-none bg-transparent appearance-none cursor-pointer font-semibold tc-priority">
                                <option value="High" selected>High</option>
                                <option value="Medium">Medium</option>
                                <option value="Low">Low</option>
                            </select>
                        </td>
                        <td class="px-4 py-4 align-top pt-5">
                            <select class="w-full text-[13px] text-slate-800 focus:outline-none bg-transparent appearance-none cursor-pointer font-medium tc-result">
                                <option value="Untested" selected>Untested</option>
                                <option value="Passed" class="text-emerald-600">Passed</option>
                                <option value="Failed" class="text-red-600">Failed</option>
                            </select>
                        </td>
                        <td class="px-4 py-4 align-top pt-4 text-center">
                            <div class="flex items-center justify-center gap-3 opacity-20 group-hover:opacity-100 transition-opacity">
                                <button onclick="saveRow(this)" title="Lưu dòng này" class="p-1.5 bg-blue-50 text-blue-600 rounded hover:bg-blue-600 hover:text-white transition"><i class="fa-regular fa-floppy-disk"></i></button>
                                <button onclick="deleteRow(this)" title="Xóa dòng này" class="p-1.5 bg-red-50 text-red-500 rounded hover:bg-red-500 hover:text-white transition"><i class="fa-regular fa-trash-can"></i></button>
                            </div>
                        </td>
                    </tr>
                    
                </tbody>
            </table>
            
            <div class="px-6 py-4">
                <button onclick="addTestCaseRow()" class="flex items-center gap-2 text-[13px] font-medium text-blue-600 hover:text-blue-700 transition">
                    <i class="fa-solid fa-plus"></i> Thêm Test Case mới
                </button>
            </div>
        </div>
    </div>

    <!-- CỘT PHẢI: KHU VỰC AI READY -->
    <div class="bg-gradient-to-br from-indigo-50/80 to-blue-50/80 rounded-xl border border-indigo-100 p-8 flex flex-col items-center justify-center text-center h-full min-h-[400px]">
        <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl flex items-center justify-center text-white text-3xl shadow-lg shadow-indigo-200 mb-6"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">AI Sẵn sàng chấm điểm</h3>
        <p class="text-[13px] text-slate-500 leading-relaxed max-w-[250px]">Nhấn nút <span class="font-semibold text-slate-700">AI Chấm điểm</span> để tự động rà soát kịch bản này</p>
    </div>
</div>

<!-- THƯ VIỆN ĐỌC FILE EXCEL (SheetJS) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<script>
    function showToast(message, isError = false) {
        const toast = document.getElementById('toast-msg');
        const textSpan = document.getElementById('toast-text');
        
        textSpan.innerHTML = message;
        toast.className = `absolute top-4 right-4 text-white px-4 py-2 rounded-lg text-sm shadow-lg transition-opacity pointer-events-none z-50 flex items-center gap-2 ${isError ? 'bg-red-600' : 'bg-slate-800'}`;
        toast.innerHTML = (isError ? '<i class="fa-solid fa-circle-exclamation text-white"></i> ' : '<i class="fa-solid fa-circle-check text-emerald-400"></i> ') + `<span>${message}</span>`;
        
        toast.classList.remove('opacity-0');
        setTimeout(() => toast.classList.add('opacity-0'), 3000);
    }

    // --- HÀM LƯU TẤT CẢ ---
    function saveAllRows() {
        const tbody = document.getElementById('testcase-tbody');
        const rows = tbody.getElementsByTagName('tr');
        const dataToSave = [];

        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            
            // Bỏ qua nếu dòng đang bị ẩn bởi bộ lọc tìm kiếm
            if (row.style.display === 'none') continue;

            dataToSave.push({
                tc_code: row.querySelector('.tc-code').textContent.trim(),
                title: row.querySelector('.tc-title').value.trim(),
                estimation: row.querySelector('.tc-est').value.trim(),
                area: row.querySelector('.tc-area').value.trim(),
                procedure_steps: row.querySelector('.tc-steps').value.trim(),
                expected_results: row.querySelector('.tc-expected').value.trim(),
                priority: row.querySelector('.tc-priority').value,
                result_status: row.querySelector('.tc-result').value
            });
        }

        if (dataToSave.length === 0) {
            showToast('Không có dữ liệu nào để lưu!', true);
            return;
        }

        const formData = new FormData();
        formData.append('ajax_action', 'save_all');
        formData.append('data', JSON.stringify(dataToSave));

        fetch('pages/testcase.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                showToast(data.message);
                // Hiệu ứng nháy xanh cho tất cả các dòng vừa lưu
                for (let i = 0; i < rows.length; i++) {
                    if (rows[i].style.display !== 'none') {
                        rows[i].classList.remove('bg-indigo-50/30');
                        rows[i].classList.add('bg-emerald-50');
                        setTimeout(() => rows[i].classList.remove('bg-emerald-50'), 1000);
                    }
                }
            } else {
                showToast(data.message, true);
            }
        })
        .catch(error => {
            showToast('Đã xảy ra lỗi kết nối Database!', true);
        });
    }

    function triggerExcelImport() { document.getElementById('excelFileInput').click(); }

    function handleExcelUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            try {
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, {type: 'array'});
                const worksheet = workbook.Sheets[workbook.SheetNames[0]];
                const jsonData = XLSX.utils.sheet_to_json(worksheet, {header: 1});

                let addedCount = 0;
                for (let i = 1; i < jsonData.length; i++) {
                    const row = jsonData[i];
                    if (!row || row.length === 0 || row.join('').trim() === '') continue;

                    const title = row[0] || '';
                    const est = row[1] || '';
                    const area = row[2] || '';
                    let steps = row[3] || '';
                    if (!steps.includes('Access the website')) {
                        steps = "1. Access the website.\n" + steps;
                    }
                    const expected = row[4] || '';
                    const priority = row[5] || 'Medium';

                    pushRowToTable(title, est, area, steps, expected, priority);
                    addedCount++;
                }

                if (addedCount > 0) {
                    showToast(`Đã tải lên ${addedCount} kịch bản từ Excel! Nhấn "Lưu tất cả" để lưu vào hệ thống.`);
                } else {
                    showToast('Tệp Excel trống!', true);
                }
            } catch (error) {
                showToast('Không thể đọc file.', true);
            }
        };
        reader.readAsArrayBuffer(file);
        event.target.value = ''; 
    }

    function saveRow(btn) {
        const row = btn.closest('tr');
        const tc_code = row.querySelector('.tc-code').textContent.trim();
        const title = row.querySelector('.tc-title').value.trim();
        const estimation = row.querySelector('.tc-est').value.trim();
        const area = row.querySelector('.tc-area').value.trim();
        const procedure_steps = row.querySelector('.tc-steps').value.trim();
        const expected_results = row.querySelector('.tc-expected').value.trim();
        const priority = row.querySelector('.tc-priority').value;
        const result_status = row.querySelector('.tc-result').value;

        const formData = new FormData();
        formData.append('ajax_action', 'save');
        formData.append('tc_code', tc_code);
        formData.append('title', title);
        formData.append('estimation', estimation);
        formData.append('area', area);
        formData.append('procedure_steps', procedure_steps);
        formData.append('expected_results', expected_results);
        formData.append('priority', priority);
        formData.append('result_status', result_status);

        fetch('pages/testcase.php', { method: 'POST', body: formData })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                showToast(data.message);
                row.classList.remove('bg-indigo-50/30');
                row.classList.add('bg-emerald-50');
                setTimeout(() => row.classList.remove('bg-emerald-50'), 1000);
            } else {
                showToast(data.message, true);
            }
        });
    }

    function deleteRow(btn) {
        const row = btn.closest('tr');
        const tc_code = row.querySelector('.tc-code').textContent.trim();

        if (confirm('Bạn có chắc chắn muốn xóa kịch bản ' + tc_code + ' này không?')) {
            const formData = new FormData();
            formData.append('ajax_action', 'delete');
            formData.append('tc_code', tc_code);

            fetch('pages/testcase.php', { method: 'POST', body: formData })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    showToast(data.message);
                    row.remove();
                } else {
                    showToast(data.message, true);
                }
            });
        }
    }

    function getNextTcId() {
        const tbody = document.getElementById('testcase-tbody');
        const rows = tbody.getElementsByTagName('tr');
        let maxId = 0;
        for (let i = 0; i < rows.length; i++) {
            const td = rows[i].querySelector('.tc-code');
            if (td) {
                const text = td.textContent.trim();
                const match = text.match(/\d+/);
                if (match) {
                    const num = parseInt(match[0], 10);
                    if (num > maxId) maxId = num;
                }
            }
        }
        return maxId + 1;
    }

    function pushRowToTable(title, est, area, steps, expected, priority) {
        const tbody = document.getElementById('testcase-tbody');
        const newRow = document.createElement('tr');
        const nextIdNum = getNextTcId();
        const formattedId = "TC" + String(nextIdNum).padStart(4, '0');

        newRow.className = 'hover:bg-slate-50/50 transition-colors group bg-indigo-50/30';
        newRow.innerHTML = `
            <td class="px-4 py-4 text-[13px] font-semibold text-slate-700 text-center align-top pt-5 tc-code">${formattedId}</td>
            <td class="px-4 py-4 align-top"><textarea rows="3" class="w-full text-[13px] text-slate-800 resize-none focus:outline-none bg-transparent leading-relaxed tc-title">${title}</textarea></td>
            <td class="px-4 py-4 align-top"><input type="text" class="w-full text-[13px] text-slate-800 focus:outline-none bg-transparent text-center mt-1 tc-est" value="${est}"></td>
            <td class="px-4 py-4 align-top"><input type="text" class="w-full text-[13px] text-slate-800 focus:outline-none bg-transparent text-center mt-1 tc-area" value="${area}"></td>
            <td class="px-4 py-4 align-top"><textarea rows="4" class="w-full text-[13px] text-slate-800 resize-none focus:outline-none bg-transparent leading-relaxed tc-steps">${steps}</textarea></td>
            <td class="px-4 py-4 align-top"><textarea rows="4" class="w-full text-[13px] text-slate-800 resize-none focus:outline-none bg-transparent leading-relaxed tc-expected">${expected}</textarea></td>
            <td class="px-4 py-4 align-top pt-5">
                <select class="w-full text-[13px] text-slate-800 focus:outline-none bg-transparent appearance-none cursor-pointer font-semibold tc-priority">
                    <option value="High" ${priority.trim().toLowerCase() === 'high' ? 'selected' : ''}>High</option>
                    <option value="Medium" ${priority.trim().toLowerCase() === 'medium' || (priority.trim().toLowerCase() !== 'high' && priority.trim().toLowerCase() !== 'low') ? 'selected' : ''}>Medium</option>
                    <option value="Low" ${priority.trim().toLowerCase() === 'low' ? 'selected' : ''}>Low</option>
                </select>
            </td>
            <td class="px-4 py-4 align-top pt-5">
                <select class="w-full text-[13px] text-slate-800 focus:outline-none bg-transparent appearance-none cursor-pointer font-medium tc-result">
                    <option value="Untested" selected>Untested</option>
                    <option value="Passed" class="text-emerald-600">Passed</option>
                    <option value="Failed" class="text-red-600">Failed</option>
                </select>
            </td>
            <td class="px-4 py-4 align-top pt-4 text-center">
                <div class="flex items-center justify-center gap-3 opacity-20 group-hover:opacity-100 transition-opacity">
                    <button onclick="saveRow(this)" title="Lưu dòng này" class="p-1.5 bg-blue-50 text-blue-600 rounded hover:bg-blue-600 hover:text-white transition"><i class="fa-regular fa-floppy-disk"></i></button>
                    <button onclick="deleteRow(this)" title="Xóa dòng này" class="p-1.5 bg-red-50 text-red-500 rounded hover:bg-red-500 hover:text-white transition"><i class="fa-regular fa-trash-can"></i></button>
                </div>
            </td>
        `;
        tbody.appendChild(newRow);
        newRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function addTestCaseRow() {
        pushRowToTable('', '', '', '1. Access the website.\n2. ', '', 'Medium');
    }

    function filterTestCase() {
        const input = document.getElementById('searchTC');
        const filter = input.value.toLowerCase();
        const tbody = document.getElementById('testcase-tbody');
        const rows = tbody.getElementsByTagName('tr');

        for (let i = 0; i < rows.length; i++) {
            const tcIdCell = rows[i].querySelector('.tc-code');
            const titleCell = rows[i].querySelector('.tc-title');
            
            if (tcIdCell && titleCell) {
                const tcIdText = tcIdCell.textContent || tcIdCell.innerText;
                const titleText = titleCell.value;
                if (tcIdText.toLowerCase().indexOf(filter) > -1 || titleText.toLowerCase().indexOf(filter) > -1) {
                    rows[i].style.display = ""; 
                } else {
                    rows[i].style.display = "none"; 
                }
            }
        }
    }
</script>