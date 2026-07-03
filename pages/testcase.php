<!-- Header nội dung -->
<header class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-semibold text-slate-800">Quản lý Test Case</h1>
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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- CỘT TRÁI: KHU VỰC BẢNG NHẬP TEST CASE -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col overflow-hidden">
        
        <!-- Toolbar (Đã tinh chỉnh kích thước nhỏ gọn trên 1 hàng) -->
        <div class="p-4 border-b border-slate-100 flex items-center justify-between gap-4 bg-white overflow-hidden">
            
            
            <!-- Container tìm kiếm và nút (Chống rớt dòng) -->
            <div class="flex items-center gap-2 overflow-x-auto custom-scrollbar pb-1 -mb-1 w-full sm:w-auto justify-start sm:justify-end">
                
                <!-- Thanh tìm kiếm -->
                <div class="relative w-[180px] md:w-[220px] shrink-0">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[12px]"></i>
                    <input type="text" id="searchTC" onkeyup="filterTestCase()" placeholder="Tìm TC ID, Tiêu đề..." 
                           class="w-full border border-slate-300 rounded-lg pl-8 pr-3 py-1.5 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 bg-slate-50 focus:bg-white transition-colors">
                </div>

                <!-- Các nút chức năng (Thu nhỏ & chống xuống dòng) -->
                <button class="flex items-center gap-1.5 px-3 py-1.5 border border-slate-200 rounded-lg text-[13px] text-slate-700 hover:bg-slate-50 transition shrink-0 whitespace-nowrap">
                    <i class="fa-regular fa-file-excel text-emerald-600"></i> Nhập Excel
                </button>
                <button class="flex items-center gap-1.5 px-3 py-1.5 border border-slate-200 rounded-lg text-[13px] text-slate-700 hover:bg-slate-50 transition shrink-0 whitespace-nowrap">
                    <i class="fa-regular fa-floppy-disk text-slate-500"></i> Lưu nháp
                </button>
                <button class="flex items-center gap-1.5 px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-[13px] font-medium shadow-sm transition shrink-0 whitespace-nowrap">
                    <i class="fa-solid fa-wand-magic-sparkles"></i> AI Chấm điểm
                </button>
            </div>
        </div>
        
        <!-- Bảng nhập liệu -->
        <div class="overflow-x-auto flex-1 custom-scrollbar pb-2">
            <table class="w-full text-left border-collapse min-w-[1100px]">
                <thead>
                    <tr class="bg-slate-50/30 border-b border-slate-200 text-[13px] font-bold text-slate-700">
                        <th class="px-4 py-3.5 w-20 text-center">TC ID</th>
                        <th class="px-4 py-3.5 min-w-[220px]">Title</th>
                        <th class="px-4 py-3.5 w-24 text-center">Estimation</th>
                        <th class="px-4 py-3.5 w-20 text-center">Area</th>
                        <th class="px-4 py-3.5 min-w-[250px]">Procedure Steps</th>
                        <th class="px-4 py-3.5 min-w-[200px]">Expected Results</th>
                        <th class="px-4 py-3.5 w-24">Priority</th>
                        <th class="px-4 py-3.5 w-24">RESULT</th>
                    </tr>
                </thead>
                <tbody id="testcase-tbody" class="divide-y divide-slate-100">
                    
                    <!-- Dòng dữ liệu ban đầu -->
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-4 py-4 text-[13px] font-semibold text-slate-700 text-center align-top pt-5">TC-001</td>
                        <td class="px-4 py-4 align-top">
                            <textarea rows="3" class="w-full text-[13px] text-slate-800 resize-none focus:outline-none bg-transparent leading-relaxed" placeholder="Nhập tiêu đề...">Xác thực luồng thanh toán giỏ hàng qua ví điện tử</textarea>
                        </td>
                        <td class="px-4 py-4 align-top">
                            <input type="text" class="w-full text-[13px] text-slate-800 focus:outline-none bg-transparent text-center mt-1" value="2h">
                        </td>
                        <td class="px-4 py-4 align-top">
                            <input type="text" class="w-full text-[13px] text-slate-800 focus:outline-none bg-transparent text-center mt-1" value="CI">
                        </td>
                        <td class="px-4 py-4 align-top">
                            <textarea rows="4" class="w-full text-[13px] text-slate-800 resize-none focus:outline-none bg-transparent leading-relaxed">1. Access the website...&#10;2. Nhấn 'Tiến hành thanh toán'&#10;3. Chọn Momo</textarea>
                        </td>
                        <td class="px-4 py-4 align-top">
                            <textarea rows="4" class="w-full text-[13px] text-slate-800 resize-none focus:outline-none bg-transparent leading-relaxed">Chuyển hướng sang cổng thanh toán Momo thành công.</textarea>
                        </td>
                        <td class="px-4 py-4 align-top pt-5">
                            <select class="w-full text-[13px] text-slate-800 focus:outline-none bg-transparent appearance-none cursor-pointer font-semibold">
                                <option value="High" selected>High</option>
                                <option value="Medium">Medium</option>
                                <option value="Low">Low</option>
                            </select>
                        </td>
                        <td class="px-4 py-4 align-top pt-5 text-[13px] text-slate-600">
                            Untested
                        </td>
                    </tr>
                    
                </tbody>
            </table>
            
            <!-- Nút Thêm dòng -->
            <div class="px-6 py-4">
                <button onclick="addTestCaseRow()" class="flex items-center gap-2 text-[13px] font-medium text-blue-600 hover:text-blue-700 transition">
                    <i class="fa-solid fa-plus"></i> Thêm Test Case mới
                </button>
            </div>
        </div>
    </div>

    <!-- CỘT PHẢI: KHU VỰC AI READY -->
    <div class="bg-gradient-to-br from-indigo-50/80 to-blue-50/80 rounded-xl border border-indigo-100 p-8 flex flex-col items-center justify-center text-center h-full min-h-[400px]">
        <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl flex items-center justify-center text-white text-3xl shadow-lg shadow-indigo-200 mb-6">
            <i class="fa-solid fa-wand-magic-sparkles"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-800 mb-2">AI Sẵn sàng chấm điểm</h3>
        <p class="text-[13px] text-slate-500 leading-relaxed max-w-[250px]">
            Nhấn nút <span class="font-semibold text-slate-700">AI Chấm điểm</span> để tự động rà soát kịch bản này
        </p>
    </div>

</div>

<!-- JAVASCRIPT XỬ LÝ -->
<script>
    let tcIdCounter = 2; // Biến đếm để tạo TC-002, TC-003,...

    // Hàm tạo thêm hàng nhập liệu mới
    function addTestCaseRow() {
        const tbody = document.getElementById('testcase-tbody');
        const newRow = document.createElement('tr');
        
        // Tạo mã ID tự động (VD: TC-002)
        const formattedId = "TC-" + String(tcIdCounter).padStart(3, '0');
        tcIdCounter++;

        newRow.className = 'hover:bg-slate-50/50 transition-colors group bg-blue-50/20';
        
        newRow.innerHTML = `
            <td class="px-4 py-4 text-[13px] font-semibold text-slate-700 text-center align-top pt-5">${formattedId}</td>
            <td class="px-4 py-4 align-top">
                <textarea rows="3" class="w-full text-[13px] text-slate-800 resize-none focus:outline-none bg-transparent leading-relaxed" placeholder="Nhập tiêu đề..."></textarea>
            </td>
            <td class="px-4 py-4 align-top">
                <input type="text" class="w-full text-[13px] text-slate-800 focus:outline-none bg-transparent text-center mt-1" placeholder="Ví dụ: 1h">
            </td>
            <td class="px-4 py-4 align-top">
                <input type="text" class="w-full text-[13px] text-slate-800 focus:outline-none bg-transparent text-center mt-1" placeholder="-">
            </td>
            <td class="px-4 py-4 align-top">
                <textarea rows="4" class="w-full text-[13px] text-slate-800 resize-none focus:outline-none bg-transparent leading-relaxed">1. Access the website...&#10;2. </textarea>
            </td>
            <td class="px-4 py-4 align-top">
                <textarea rows="4" class="w-full text-[13px] text-slate-800 resize-none focus:outline-none bg-transparent leading-relaxed" placeholder="Kết quả mong muốn..."></textarea>
            </td>
            <td class="px-4 py-4 align-top pt-5">
                <select class="w-full text-[13px] text-slate-800 focus:outline-none bg-transparent appearance-none cursor-pointer font-semibold">
                    <option value="High">High</option>
                    <option value="Medium" selected>Medium</option>
                    <option value="Low">Low</option>
                </select>
            </td>
            <td class="px-4 py-4 align-top pt-5 text-[13px] text-slate-600">
                Untested
            </td>
        `;
        
        tbody.appendChild(newRow);
        newRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    // Hàm Lọc (Tìm kiếm) Test Case
    function filterTestCase() {
        // Lấy giá trị đang nhập ở ô tìm kiếm
        const input = document.getElementById('searchTC');
        const filter = input.value.toLowerCase();
        
        // Trỏ vào bảng
        const tbody = document.getElementById('testcase-tbody');
        const rows = tbody.getElementsByTagName('tr');

        // Duyệt qua từng dòng
        for (let i = 0; i < rows.length; i++) {
            // Lấy cột chứa ID (cột 0) và cột chứa Title (cột 1)
            const tcIdCell = rows[i].getElementsByTagName('td')[0];
            const titleCell = rows[i].getElementsByTagName('td')[1];
            
            if (tcIdCell && titleCell) {
                // Lấy nội dung text
                const tcIdText = tcIdCell.textContent || tcIdCell.innerText;
                
                // Vì Title nằm trong thẻ textarea, cần lấy .value thay vì text content thông thường
                const titleTextarea = titleCell.querySelector('textarea');
                const titleText = titleTextarea ? titleTextarea.value : (titleCell.textContent || titleCell.innerText);
                
                // So sánh chuỗi nhập vào với ID và Title
                if (tcIdText.toLowerCase().indexOf(filter) > -1 || titleText.toLowerCase().indexOf(filter) > -1) {
                    rows[i].style.display = ""; // Hiện nếu khớp
                } else {
                    rows[i].style.display = "none"; // Ẩn nếu không khớp
                }
            }
        }
    }
</script>