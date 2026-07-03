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

<!-- Khung Bảng Danh Sách Defect -->
<div class="bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col overflow-hidden">
    
    <!-- Toolbar -->
    <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-white">
        <div class="relative w-[350px]">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" placeholder="Tìm kiếm Defect ID, Tiêu đề..." class="w-full border border-slate-300 rounded-lg pl-9 pr-4 py-2 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700">
        </div>
        <div class="flex items-center gap-3">
            <button class="flex items-center gap-2 px-4 py-2 border border-slate-200 rounded-lg text-[13px] font-medium text-slate-700 hover:bg-slate-50 transition">
                <i class="fa-solid fa-filter text-slate-400"></i> Lọc
            </button>
            <button onclick="addDefectRow()" class="flex items-center gap-2 px-4 py-2 bg-[#2563EB] hover:bg-blue-700 text-white rounded-lg text-[13px] font-medium shadow-sm transition">
                <i class="fa-solid fa-plus"></i> Thêm DefectList mới
            </button>
        </div>
    </div>

    <!-- Bảng Dữ Liệu (Có thanh cuộn ngang) -->
    <div class="overflow-x-auto flex-1 custom-scrollbar pb-2">
        <table class="w-full text-left border-collapse min-w-[1800px]">
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
                </tr>
            </thead>
            <tbody id="defect-tbody" class="divide-y divide-slate-100">
                
                <!-- Dòng dữ liệu mẫu 1 -->
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-4 py-5 text-[13px] text-slate-500 text-center align-top">1</td>
                    <td class="px-4 py-5 font-semibold text-slate-800 text-[13px] align-top">BUG-1042</td>
                    <td class="px-4 py-5 align-top">
                        <textarea rows="2" class="w-full text-[13px] text-slate-800 resize-none focus:outline-none bg-transparent leading-relaxed">Lỗi timeout khi gọi API cổng thanh toán Momo</textarea>
                    </td>
                    <td class="px-4 py-5 align-top">
                        <input type="text" class="w-full text-[13px] text-slate-600 focus:outline-none bg-transparent" value="Integration">
                    </td>
                    <td class="px-4 py-5 align-top">
                        <input type="text" class="w-full text-[13px] text-slate-600 focus:outline-none bg-transparent" value="Checkout">
                    </td>
                    <td class="px-4 py-5 align-top">
                        <textarea rows="4" class="w-full text-[13px] text-slate-600 resize-none focus:outline-none bg-transparent leading-relaxed">1. Thêm hàng vào giỏ&#10;2. Chọn Momo&#10;3. Bấm Thanh toán</textarea>
                    </td>
                    <td class="px-4 py-5 align-top">
                        <textarea rows="3" class="w-full text-[13px] text-slate-600 resize-none focus:outline-none bg-transparent leading-relaxed">Chuyển sang trang Momo trong < 3s</textarea>
                    </td>
                    <td class="px-4 py-5 align-top">
                        <textarea rows="3" class="w-full text-[13px] text-slate-600 resize-none focus:outline-none bg-transparent leading-relaxed">Load vô hạn và báo lỗi 504 Gateway Timeout</textarea>
                    </td>
                    <td class="px-4 py-5 align-top text-center">
                        <span class="inline-block px-2.5 py-1 bg-orange-50 text-orange-600 border border-orange-200 rounded text-[12px] font-semibold">High</span>
                    </td>
                    <td class="px-4 py-5 align-top font-semibold text-slate-800 text-[13px]">High</td>
                    <td class="px-4 py-5 align-top text-[13px] text-slate-600">v1.4.2</td>
                    <td class="px-4 py-5 align-top text-[13px] text-slate-600">2024-05-12</td>
                    <td class="px-4 py-5 align-top text-[13px] text-slate-600">Nguyễn Văn A</td>
                    <td class="px-4 py-5 align-top font-medium text-indigo-600 text-[13px] cursor-pointer hover:underline">TC-001</td>
                    <td class="px-4 py-5 align-top">
                        <a href="#" class="flex items-center gap-1.5 text-[13px] text-blue-500 hover:text-blue-700">
                            <i class="fa-regular fa-image"></i> error_momo.png
                        </a>
                    </td>
                </tr>

                <!-- Dòng dữ liệu mẫu 2 -->
                <tr class="hover:bg-slate-50/50 transition-colors">
                    <td class="px-4 py-5 text-[13px] text-slate-500 text-center align-top">2</td>
                    <td class="px-4 py-5 font-semibold text-slate-800 text-[13px] align-top">BUG-1043</td>
                    <td class="px-4 py-5 align-top">
                        <textarea rows="2" class="w-full text-[13px] text-slate-800 resize-none focus:outline-none bg-transparent leading-relaxed">Nút 'Thêm vào giỏ hàng' bị lệch trên mobile UI</textarea>
                    </td>
                    <td class="px-4 py-5 align-top">
                        <input type="text" class="w-full text-[13px] text-slate-600 focus:outline-none bg-transparent" value="UI/UX">
                    </td>
                    <td class="px-4 py-5 align-top">
                        <input type="text" class="w-full text-[13px] text-slate-600 focus:outline-none bg-transparent" value="Product">
                    </td>
                    <td class="px-4 py-5 align-top">
                        <textarea rows="3" class="w-full text-[13px] text-slate-600 resize-none focus:outline-none bg-transparent leading-relaxed">1. Mở web trên iPhone 12&#10;2. Vào chi tiết sản phẩm</textarea>
                    </td>
                    <td class="px-4 py-5 align-top">
                        <textarea rows="2" class="w-full text-[13px] text-slate-600 resize-none focus:outline-none bg-transparent leading-relaxed">Nút nằm giữa màn hình</textarea>
                    </td>
                    <td class="px-4 py-5 align-top">
                        <textarea rows="2" class="w-full text-[13px] text-slate-600 resize-none focus:outline-none bg-transparent leading-relaxed">Nút bị tràn lề phải 20px</textarea>
                    </td>
                    <td class="px-4 py-5 align-top text-center">
                        <span class="inline-block px-2.5 py-1 bg-slate-100 text-slate-500 border border-slate-200 rounded text-[12px] font-semibold">Low</span>
                    </td>
                    <td class="px-4 py-5 align-top font-semibold text-slate-800 text-[13px]">Medium</td>
                    <td class="px-4 py-5 align-top text-[13px] text-slate-600">v1.4.2</td>
                    <td class="px-4 py-5 align-top text-[13px] text-slate-600">2024-05-13</td>
                    <td class="px-4 py-5 align-top text-[13px] text-slate-600">Trần Thị B</td>
                    <td class="px-4 py-5 align-top font-medium text-indigo-600 text-[13px] cursor-pointer hover:underline">TC-045</td>
                    <td class="px-4 py-5 align-top">
                        <a href="#" class="flex items-center gap-1.5 text-[13px] text-blue-500 hover:text-blue-700">
                            <i class="fa-regular fa-image"></i> ui_glitch.jpg
                        </a>
                    </td>
                </tr>
                
            </tbody>
        </table>
    </div>
</div>

<!-- JAVASCRIPT: XỬ LÝ THÊM DÒNG MỚI -->
<script>
    let rowCount = 2; // Bắt đầu từ 2 do đã có 2 dòng mẫu
    let bugIdCounter = 1044; // Mã ID kế tiếp

    function addDefectRow() {
        rowCount++;
        const tbody = document.getElementById('defect-tbody');
        
        // Lấy ngày hiện tại format YYYY-MM-DD
        const today = new Date().toISOString().split('T')[0];
        
        const newRow = document.createElement('tr');
        newRow.className = 'hover:bg-slate-50/50 transition-colors bg-blue-50/20'; // Highlight nhẹ dòng mới
        
        newRow.innerHTML = `
            <td class="px-4 py-5 text-[13px] text-slate-500 text-center align-top">${rowCount}</td>
            <td class="px-4 py-5 font-semibold text-slate-800 text-[13px] align-top">BUG-${bugIdCounter++}</td>
            <td class="px-4 py-5 align-top">
                <textarea rows="2" class="w-full text-[13px] text-slate-800 resize-none focus:outline-none bg-transparent leading-relaxed" placeholder="Nhập tiêu đề lỗi..."></textarea>
            </td>
            <td class="px-4 py-5 align-top">
                <input type="text" class="w-full text-[13px] text-slate-600 focus:outline-none bg-transparent" placeholder="Test Type">
            </td>
            <td class="px-4 py-5 align-top">
                <input type="text" class="w-full text-[13px] text-slate-600 focus:outline-none bg-transparent" placeholder="Area">
            </td>
            <td class="px-4 py-5 align-top">
                <textarea rows="4" class="w-full text-[13px] text-slate-600 resize-none focus:outline-none bg-transparent leading-relaxed">1. Access the website...&#10;2. </textarea>
            </td>
            <td class="px-4 py-5 align-top">
                <textarea rows="3" class="w-full text-[13px] text-slate-600 resize-none focus:outline-none bg-transparent leading-relaxed" placeholder="Kết quả mong muốn..."></textarea>
            </td>
            <td class="px-4 py-5 align-top">
                <textarea rows="3" class="w-full text-[13px] text-slate-600 resize-none focus:outline-none bg-transparent leading-relaxed" placeholder="Kết quả thực tế..."></textarea>
            </td>
            <td class="px-4 py-5 align-top text-center">
                <select class="px-2 py-1 border border-slate-300 rounded text-[12px] font-semibold text-slate-600 focus:outline-none bg-white cursor-pointer">
                    <option value="High" class="text-orange-600">High</option>
                    <option value="Medium">Medium</option>
                    <option value="Low" class="text-slate-500">Low</option>
                </select>
            </td>
            <td class="px-4 py-5 align-top">
                <select class="w-full text-[13px] text-slate-800 focus:outline-none bg-transparent appearance-none cursor-pointer font-semibold">
                    <option value="High">High</option>
                    <option value="Medium" selected>Medium</option>
                    <option value="Low">Low</option>
                </select>
            </td>
            <td class="px-4 py-5 align-top">
                <input type="text" class="w-full text-[13px] text-slate-600 focus:outline-none bg-transparent" value="v1.4.2">
            </td>
            <td class="px-4 py-5 align-top text-[13px] text-slate-600">${today}</td>
            <td class="px-4 py-5 align-top">
                <input type="text" class="w-full text-[13px] text-slate-600 focus:outline-none bg-transparent" placeholder="Tên tác giả">
            </td>
            <td class="px-4 py-5 align-top">
                <input type="text" class="w-full text-[13px] text-indigo-600 font-medium focus:outline-none bg-transparent" placeholder="TC-XXX">
            </td>
            <td class="px-4 py-5 align-top">
                <input type="file" class="text-[11px] text-slate-500 file:mr-2 file:py-1 file:px-2 file:rounded file:border-0 file:text-[11px] file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer w-[120px]">
            </td>
        `;
        
        // Chèn vào cuối bảng
        tbody.appendChild(newRow);
        
        // Cuộn để người dùng dễ nhìn thấy dòng mới
        newRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
</script>