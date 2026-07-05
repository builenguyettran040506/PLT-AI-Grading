<?php
// Gọi tệp kết nối CSDL
$db_path = file_exists('includes/database.php') ? 'includes/database.php' : '../includes/database.php';
require_once $db_path;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// =====================================================================
// 1. XỬ LÝ LƯU, XÓA, & NHẬP EXCEL BẰNG PHP THUẦN (POST SUBMIT)
// =====================================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Hàm hỗ trợ: Tìm Test Case ID từ Mã Test Case
        $getTcId = function($tc_code) use ($pdo) {
            if (empty(trim($tc_code))) return null;
            $stmt = $pdo->prepare("SELECT id FROM test_cases WHERE tc_code = ? LIMIT 1");
            $stmt->execute([trim($tc_code)]);
            return $stmt->fetchColumn() ?: null;
        };

        // A. XỬ LÝ LƯU TẤT CẢ HOẶC LƯU 1 DÒNG
        if (isset($_POST['action_save_all']) || isset($_POST['action_save_single'])) {
            $pdo->beginTransaction();
            // Lưu ý: SQL này chỉ lưu các trường cơ bản. Bạn cần cập nhật bảng `defects` trong CSDL 
            // để chứa thêm các cột mới như (test_type, area, steps, actual_result...) nếu muốn lưu đầy đủ.
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
        }

        // B. XỬ LÝ XÓA
        elseif (isset($_POST['action_delete'])) {
            $code = $_POST['action_delete'];
            $pdo->prepare("DELETE FROM defects WHERE defect_code = ?")->execute([$code]);
            $_SESSION['toast_msg'] = "Đã xóa thành công $code!";
            $_SESSION['toast_type'] = "success";
        }

        // C. XỬ LÝ NHẬP EXCEL (CSV)
        elseif (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['excel_file']['tmp_name'];
            $ext = strtolower(pathinfo($_FILES['excel_file']['name'], PATHINFO_EXTENSION));

            if ($ext === 'csv') {
                $handle = fopen($file_tmp, "r");
                fgetcsv($handle, 1000, ","); // Bỏ qua dòng tiêu đề

                $stmtMax = $pdo->query("SELECT defect_code FROM defects ORDER BY id DESC LIMIT 1");
                $maxCode = $stmtMax->fetchColumn();
                $nextNum = 1;
                if ($maxCode && preg_match('/BUG-(\d+)/', $maxCode, $matches)) {
                    $nextNum = intval($matches[1]) + 1;
                }

                $pdo->beginTransaction();
                $stmtInsert = $pdo->prepare("INSERT INTO defects (defect_code, testcase_id, title) VALUES (?, ?, ?)");
                $count = 0;

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if (count($data) < 2) continue; 
                    $tc_id = $getTcId($data[0] ?? '');
                    $title = $data[1] ?? '';
                    $new_code = "BUG-" . str_pad($nextNum++, 4, '0', STR_PAD_LEFT);
                    $stmtInsert->execute([$new_code, $tc_id, $title]);
                    $count++;
                }
                $pdo->commit();
                fclose($handle);

                $_SESSION['toast_msg'] = "Đã tải lên và lưu $count lỗi từ file CSV!";
                $_SESSION['toast_type'] = "success";
            }
        }
    } catch (Exception $e) {
        if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
        $_SESSION['toast_msg'] = "Lỗi: " . $e->getMessage();
        $_SESSION['toast_type'] = "error";
    }

    header("Location: index.php?page=defect");
    exit;
}

// =====================================================================
// 2. GIẢ LẬP DỮ LIỆU ĐỂ HIỂN THỊ ĐÚNG GIAO DIỆN CỦA BẠN
// =====================================================================
// Trong thực tế bạn có thể truy vấn `$pdo->query(...)` để lấy dữ liệu. 
// Ở đây tôi dùng mảng mẫu dựa trên code gốc của bạn.
$defects = [
    [
        'defect_code' => 'BUG-1042',
        'title' => 'Lỗi timeout khi gọi API cổng thanh toán Momo',
        'test_type' => 'Integration',
        'area' => 'Checkout',
        'steps' => "1. Thêm hàng vào giỏ\n2. Chọn Momo\n3. Bấm Thanh toán",
        'expected_result' => 'Chuyển sang trang Momo trong < 3s',
        'actual_result' => 'Load vô hạn và báo lỗi 504 Gateway Timeout',
        'severity' => 'High',
        'priority' => 'High',
        'build_version' => 'v1.4.2',
        'created_date' => '2024-05-12',
        'author' => 'Nguyễn Văn A',
        'tc_code' => 'TC-001',
        'picture' => 'error_momo.png'
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
        'priority' => 'Medium',
        'build_version' => 'v1.4.2',
        'created_date' => '2024-05-13',
        'author' => 'Trần Thị B',
        'tc_code' => 'TC-045',
        'picture' => 'ui_glitch.jpg'
    ]
];
?>

<!-- Header nội dung -->
<header class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold text-slate-800">Danh sách Defect</h1>
    <div class="flex items-center gap-6">
        <div class="flex items-center gap-2 bg-slate-200/50 px-3 py-1.5 rounded-full border border-slate-200">
            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
            <span class="text-sm font-medium text-slate-600">AI Engine: Online</span>
        </div>
        <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-md cursor-pointer">
            QA
        </div>
    </div>
</header>

<!-- Khung Bảng Danh Sách Defect BỌC TRONG FORM -->
<form id="defectForm" method="POST" action="index.php?page=defect" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col overflow-hidden relative">
    
    <!-- Hiển thị Toast Message -->
    <?php if (isset($_SESSION['toast_msg'])): ?>
        <div id="php-toast" class="absolute top-4 right-4 <?= $_SESSION['toast_type'] === 'success' ? 'bg-slate-800' : 'bg-red-600' ?> text-white px-4 py-2 rounded-lg text-sm shadow-lg transition-opacity pointer-events-none z-50 flex items-center gap-2">
            <i class="fa-solid <?= $_SESSION['toast_type'] === 'success' ? 'fa-circle-check text-emerald-400' : 'fa-circle-exclamation text-white' ?>"></i> 
            <span><?= $_SESSION['toast_msg'] ?></span>
        </div>
        <?php unset($_SESSION['toast_msg']); unset($_SESSION['toast_type']); ?>
    <?php endif; ?>

    <!-- Toolbar -->
    <div class="p-4 border-b border-slate-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white">
        <div class="relative w-full lg:w-[350px]">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" id="searchDefect" onkeyup="filterDefect()" placeholder="Tìm kiếm Defect ID, Tiêu đề..." class="w-full border border-slate-300 rounded-lg pl-9 pr-4 py-2 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700">
        </div>
        
        <div class="flex items-center gap-2 flex-wrap">
            <button type="button" class="flex items-center gap-2 px-3 py-2 border border-slate-200 rounded-lg text-[13px] font-medium text-slate-700 hover:bg-slate-50 transition">
                <i class="fa-solid fa-filter text-slate-400"></i> Lọc
            </button>
            
            <input type="file" name="excel_file" id="excelFileInput" class="hidden" accept=".csv" onchange="document.getElementById('defectForm').submit();">
            <button type="button" onclick="document.getElementById('excelFileInput').click();" class="flex items-center gap-2 px-3 py-2 border border-slate-200 rounded-lg text-[13px] font-medium text-slate-700 hover:bg-slate-50 transition">
                <i class="fa-regular fa-file-excel text-emerald-600"></i> Nhập File
            </button>
            
            <button type="submit" name="action_save_all" class="flex items-center gap-2 px-4 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-lg text-[13px] font-medium transition shadow-sm">
                <i class="fa-solid fa-layer-group"></i> Lưu hàng loạt
            </button>

            <button type="button" class="flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[13px] font-medium transition shadow-sm">
                <i class="fa-solid fa-wand-magic-sparkles"></i> AI Chấm
            </button>

            <button type="button" onclick="addDefectRow()" class="flex items-center gap-2 px-4 py-2 bg-[#2563EB] hover:bg-blue-700 text-white rounded-lg text-[13px] font-medium shadow-sm transition">
                <i class="fa-solid fa-plus"></i> Thêm DefectList mới
            </button>
        </div>
    </div>

    <!-- Bảng Dữ Liệu -->
    <div class="overflow-x-auto flex-1 custom-scrollbar pb-2">
        <table class="w-full text-left border-collapse min-w-[2000px]">
            <thead>
                <tr class="bg-slate-50/50 border-b border-slate-200 text-[13px] font-bold text-slate-700">
                    <th class="px-4 py-4 w-12 text-center">STT</th>
                    <th class="px-4 py-4 w-28">DEFECT_ID</th>
                    <th class="px-4 py-4 min-w-[250px]">Title</th>
                    <th class="px-4 py-4 w-28">Test Type</th>
                    <th class="px-4 py-4 w-28">Area</th>
                    <th class="px-4 py-4 min-w-[250px]">Steps</th>
                    <th class="px-4 py-4 min-w-[200px]">Expected Result</th>
                    <th class="px-4 py-4 min-w-[220px]">Actual Result</th>
                    <th class="px-4 py-4 w-24 text-center">Severity</th>
                    <th class="px-4 py-4 w-24">Priority</th>
                    <th class="px-4 py-4 w-28">Build version</th>
                    <th class="px-4 py-4 w-28">Create date</th>
                    <th class="px-4 py-4 w-32">Author</th>
                    <th class="px-4 py-4 w-32">Testcase's ID</th>
                    <th class="px-4 py-4 w-32">Picture</th>
                    <th class="px-4 py-4 w-24 text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody id="defect-tbody" class="divide-y divide-slate-100">
                
                <?php 
                $stt = 1;
                foreach ($defects as $df): 
                    $id = htmlspecialchars($df['defect_code']); 
                ?>
                <tr class="hover:bg-slate-50/50 transition-colors defect-row">
                    <input type="hidden" name="defect_code[]" value="<?= $id ?>">
                    
                    <td class="px-4 py-5 text-[13px] text-slate-500 text-center align-top"><?= $stt++ ?></td>
                    <td class="px-4 py-5 font-semibold text-slate-800 text-[13px] align-top df-code"><?= $id ?></td>
                    <td class="px-4 py-5 align-top">
                        <textarea name="title[<?= $id ?>]" rows="2" class="w-full text-[13px] text-slate-800 resize-none focus:outline-none bg-transparent leading-relaxed df-title"><?= htmlspecialchars($df['title']) ?></textarea>
                    </td>
                    <td class="px-4 py-5 align-top">
                        <input type="text" name="test_type[<?= $id ?>]" class="w-full text-[13px] text-slate-600 focus:outline-none bg-transparent" value="<?= htmlspecialchars($df['test_type']) ?>">
                    </td>
                    <td class="px-4 py-5 align-top">
                        <input type="text" name="area[<?= $id ?>]" class="w-full text-[13px] text-slate-600 focus:outline-none bg-transparent" value="<?= htmlspecialchars($df['area']) ?>">
                    </td>
                    <td class="px-4 py-5 align-top">
                        <textarea name="steps[<?= $id ?>]" rows="4" class="w-full text-[13px] text-slate-600 resize-none focus:outline-none bg-transparent leading-relaxed"><?= htmlspecialchars($df['steps']) ?></textarea>
                    </td>
                    <td class="px-4 py-5 align-top">
                        <textarea name="expected_result[<?= $id ?>]" rows="3" class="w-full text-[13px] text-slate-600 resize-none focus:outline-none bg-transparent leading-relaxed"><?= htmlspecialchars($df['expected_result']) ?></textarea>
                    </td>
                    <td class="px-4 py-5 align-top">
                        <textarea name="actual_result[<?= $id ?>]" rows="3" class="w-full text-[13px] text-slate-600 resize-none focus:outline-none bg-transparent leading-relaxed"><?= htmlspecialchars($df['actual_result']) ?></textarea>
                    </td>
                    <td class="px-4 py-5 align-top text-center">
                        <?php 
                            $sevColor = 'bg-slate-100 text-slate-500 border-slate-200';
                            if ($df['severity'] === 'High') $sevColor = 'bg-orange-50 text-orange-600 border-orange-200';
                        ?>
                        <span class="inline-block px-2.5 py-1 <?= $sevColor ?> border rounded text-[12px] font-semibold"><?= htmlspecialchars($df['severity']) ?></span>
                        <input type="hidden" name="severity[<?= $id ?>]" value="<?= htmlspecialchars($df['severity']) ?>">
                    </td>
                    <td class="px-4 py-5 align-top font-semibold text-slate-800 text-[13px]">
                        <input type="text" name="priority[<?= $id ?>]" class="w-full font-semibold focus:outline-none bg-transparent" value="<?= htmlspecialchars($df['priority']) ?>">
                    </td>
                    <td class="px-4 py-5 align-top text-[13px] text-slate-600">
                        <input type="text" name="build_version[<?= $id ?>]" class="w-full focus:outline-none bg-transparent" value="<?= htmlspecialchars($df['build_version']) ?>">
                    </td>
                    <td class="px-4 py-5 align-top text-[13px] text-slate-600">
                        <input type="text" name="created_date[<?= $id ?>]" class="w-full focus:outline-none bg-transparent" value="<?= htmlspecialchars($df['created_date']) ?>">
                    </td>
                    <td class="px-4 py-5 align-top text-[13px] text-slate-600">
                        <input type="text" name="author[<?= $id ?>]" class="w-full focus:outline-none bg-transparent" value="<?= htmlspecialchars($df['author']) ?>">
                    </td>
                    <td class="px-4 py-5 align-top">
                        <input type="text" name="tc_code[<?= $id ?>]" class="w-full font-medium text-indigo-600 text-[13px] focus:outline-none bg-transparent" value="<?= htmlspecialchars($df['tc_code']) ?>">
                    </td>
                    <td class="px-4 py-5 align-top">
                        <a href="#" class="flex items-center gap-1.5 text-[13px] text-blue-500 hover:text-blue-700">
                            <i class="fa-regular fa-image"></i> <?= htmlspecialchars($df['picture']) ?>
                        </a>
                    </td>
                    <!-- CỘT THAO TÁC -->
                    <td class="px-4 py-5 align-top text-center">
                        <div class="flex items-center justify-center gap-3">
                            <button type="submit" name="action_save_single" value="<?= $id ?>" title="Lưu dòng này" class="p-1.5 text-blue-600 hover:text-blue-700 transition">
                                <i class="fa-regular fa-floppy-disk text-lg"></i>
                            </button>
                            <button type="submit" name="action_delete" value="<?= $id ?>" onclick="return confirm('Xác nhận xóa <?= $id ?>?');" title="Xóa dòng này" class="p-1.5 text-red-500 hover:text-red-600 transition">
                                <i class="fa-regular fa-trash-can text-lg"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                
            </tbody>
        </table>
    </div>
</form>

<!-- JAVASCRIPT -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const toast = document.getElementById('php-toast');
        if(toast) {
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
    });

    let rowCount = <?= count($defects) ?>; 
    let bugIdCounter = 1044; 

    function addDefectRow() {
        rowCount++;
        const tbody = document.getElementById('defect-tbody');
        const today = new Date().toISOString().split('T')[0];
        const id = "BUG-" + bugIdCounter++;
        
        const newRow = document.createElement('tr');
        newRow.className = 'hover:bg-slate-50/50 transition-colors bg-blue-50/20 defect-row'; 
        
        newRow.innerHTML = `
            <input type="hidden" name="defect_code[]" value="${id}">
            <td class="px-4 py-5 text-[13px] text-slate-500 text-center align-top">${rowCount}</td>
            <td class="px-4 py-5 font-semibold text-slate-800 text-[13px] align-top df-code">${id}</td>
            <td class="px-4 py-5 align-top"><textarea name="title[${id}]" rows="2" class="w-full text-[13px] text-slate-800 resize-none focus:outline-none bg-transparent leading-relaxed" placeholder="Nhập tiêu đề lỗi..."></textarea></td>
            <td class="px-4 py-5 align-top"><input type="text" name="test_type[${id}]" class="w-full text-[13px] text-slate-600 focus:outline-none bg-transparent" placeholder="Test Type"></td>
            <td class="px-4 py-5 align-top"><input type="text" name="area[${id}]" class="w-full text-[13px] text-slate-600 focus:outline-none bg-transparent" placeholder="Area"></td>
            <td class="px-4 py-5 align-top"><textarea name="steps[${id}]" rows="4" class="w-full text-[13px] text-slate-600 resize-none focus:outline-none bg-transparent leading-relaxed">1. Access the website...\n2. </textarea></td>
            <td class="px-4 py-5 align-top"><textarea name="expected_result[${id}]" rows="3" class="w-full text-[13px] text-slate-600 resize-none focus:outline-none bg-transparent leading-relaxed" placeholder="Kết quả mong muốn..."></textarea></td>
            <td class="px-4 py-5 align-top"><textarea name="actual_result[${id}]" rows="3" class="w-full text-[13px] text-slate-600 resize-none focus:outline-none bg-transparent leading-relaxed" placeholder="Kết quả thực tế..."></textarea></td>
            <td class="px-4 py-5 align-top text-center">
                <select name="severity[${id}]" class="px-2 py-1 border border-slate-300 rounded text-[12px] font-semibold text-slate-600 focus:outline-none bg-white cursor-pointer">
                    <option value="High" class="text-orange-600">High</option>
                    <option value="Medium" selected>Medium</option>
                    <option value="Low" class="text-slate-500">Low</option>
                </select>
            </td>
            <td class="px-4 py-5 align-top">
                <select name="priority[${id}]" class="w-full text-[13px] text-slate-800 focus:outline-none bg-transparent appearance-none cursor-pointer font-semibold">
                    <option value="High">High</option>
                    <option value="Medium" selected>Medium</option>
                    <option value="Low">Low</option>
                </select>
            </td>
            <td class="px-4 py-5 align-top"><input type="text" name="build_version[${id}]" class="w-full text-[13px] text-slate-600 focus:outline-none bg-transparent" value="v1.4.2"></td>
            <td class="px-4 py-5 align-top text-[13px] text-slate-600"><input type="text" name="created_date[${id}]" class="w-full focus:outline-none bg-transparent" value="${today}"></td>
            <td class="px-4 py-5 align-top"><input type="text" name="author[${id}]" class="w-full text-[13px] text-slate-600 focus:outline-none bg-transparent" placeholder="Tên tác giả"></td>
            <td class="px-4 py-5 align-top"><input type="text" name="tc_code[${id}]" class="w-full text-[13px] text-indigo-600 font-medium focus:outline-none bg-transparent" placeholder="TC-XXX"></td>
            <td class="px-4 py-5 align-top"><input type="file" class="text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[11px] file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer w-[120px]"></td>
            <!-- CỘT THAO TÁC -->
            <td class="px-4 py-5 align-top text-center">
                <div class="flex items-center justify-center gap-3">
                    <button type="submit" name="action_save_single" value="${id}" title="Lưu dòng này" class="p-1.5 text-blue-600 hover:text-blue-700 transition">
                        <i class="fa-regular fa-floppy-disk text-lg"></i>
                    </button>
                    <button type="button" onclick="this.closest('tr').remove()" title="Hủy dòng" class="p-1.5 text-red-500 hover:text-red-600 transition">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>
            </td>
        `;
        
        tbody.appendChild(newRow);
        newRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    function filterDefect() {
        const input = document.getElementById('searchDefect');
        const filter = input.value.toLowerCase();
        const rows = document.getElementsByClassName('defect-row');

        for (let i = 0; i < rows.length; i++) {
            const codeCell = rows[i].querySelector('.df-code');
            const titleCell = rows[i].querySelector('.df-title');
            
            if (codeCell && titleCell) {
                const codeText = codeCell.textContent || codeCell.innerText;
                const titleText = titleCell.value;
                if (codeText.toLowerCase().indexOf(filter) > -1 || titleText.toLowerCase().indexOf(filter) > -1) {
                    rows[i].style.display = ""; 
                } else {
                    rows[i].style.display = "none"; 
                }
            }
        }
    }
</script>