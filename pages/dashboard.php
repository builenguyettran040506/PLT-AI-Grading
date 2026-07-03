<!-- Header nội dung -->
<header class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-slate-800">Tổng quan Dự án</h1>
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

<div class="h-full overflow-y-auto custom-scrollbar pb-10">
    
    <!-- TOP CARDS (5 THẺ THỐNG KÊ) -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <!-- Thẻ 1 -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 flex flex-col justify-center">
            <div class="flex justify-between items-start mb-2">
                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500">
                    <i class="fa-regular fa-file-lines"></i>
                </div>
                <span class="text-xs font-semibold text-emerald-500 flex items-center gap-1"><i class="fa-solid fa-arrow-trend-up"></i> +45</span>
            </div>
            <h3 class="text-2xl font-bold text-slate-800">1,250</h3>
            <p class="text-[13px] text-slate-500 mt-1">Tổng bài nộp</p>
        </div>
        <!-- Thẻ 2 -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 flex flex-col justify-center">
            <div class="flex justify-between items-start mb-2">
                <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-500">
                    <i class="fa-regular fa-circle-check"></i>
                </div>
                <span class="text-xs font-semibold text-emerald-500 flex items-center gap-1"><i class="fa-solid fa-arrow-trend-up"></i> +120</span>
            </div>
            <h3 class="text-2xl font-bold text-slate-800">1,100</h3>
            <p class="text-[13px] text-slate-500 mt-1">Đã chấm</p>
        </div>
        <!-- Thẻ 3 -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 flex flex-col justify-center">
            <div class="flex justify-between items-start mb-2">
                <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center text-orange-400">
                    <i class="fa-regular fa-circle-question"></i>
                </div>
            </div>
            <h3 class="text-2xl font-bold text-slate-800">150</h3>
            <p class="text-[13px] text-slate-500 mt-1">Chưa chấm</p>
        </div>
        <!-- Thẻ 4 -->
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 flex flex-col justify-center">
            <div class="flex justify-between items-start mb-2">
                <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center text-purple-500">
                    <i class="fa-regular fa-star"></i>
                </div>
                <span class="text-xs font-semibold text-emerald-500 flex items-center gap-1"><i class="fa-solid fa-arrow-trend-up"></i> +0.5</span>
            </div>
            <h3 class="text-2xl font-bold text-slate-800">8.5<span class="text-sm font-medium text-slate-400">/10</span></h3>
            <p class="text-[13px] text-slate-500 mt-1">Điểm TB</p>
        </div>
        <!-- Thẻ 5 (Highlight AI) -->
        <div class="bg-gradient-to-br from-[#4F46E5] to-[#2563EB] rounded-xl p-5 shadow-md flex flex-col justify-center relative overflow-hidden">
            <div class="absolute top-2 right-3 text-white/20 text-4xl"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
            <div class="flex justify-between items-start mb-2 relative z-10">
                <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-white backdrop-blur-sm">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
            </div>
            <h3 class="text-3xl font-bold text-white relative z-10">98.2%</h3>
            <p class="text-[13px] text-blue-100 mt-1 font-medium relative z-10">AI Accuracy</p>
        </div>
    </div>

    <!-- MIDDLE SECTION: BIỂU ĐỒ -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        
        <!-- Biểu đồ Donut: Trạng thái Test Case -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 flex flex-col">
            <h3 class="text-[15px] font-bold text-slate-800 mb-8">Trạng thái Test Case</h3>
            <div class="flex-1 flex flex-col items-center justify-center">
                
                <!-- SVG Donut Chart -->
                <div class="relative w-48 h-48 mb-8">
                    <svg viewBox="0 0 36 36" class="w-full h-full transform -rotate-90">
                        <!-- Untested (Grey) -->
                        <path class="text-slate-400" stroke-width="6" stroke="currentColor" fill="none" stroke-linecap="butt"
                            stroke-dasharray="20, 100" stroke-dashoffset="0"
                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <!-- Needs Update (Yellow) -->
                        <path class="text-amber-500" stroke-width="6" stroke="currentColor" fill="none" stroke-linecap="butt"
                            stroke-dasharray="5, 100" stroke-dashoffset="-21"
                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <!-- Fail (Red) -->
                        <path class="text-red-500" stroke-width="6" stroke="currentColor" fill="none" stroke-linecap="butt"
                            stroke-dasharray="10, 100" stroke-dashoffset="-27"
                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <!-- Pass (Green) -->
                        <path class="text-emerald-500" stroke-width="6" stroke="currentColor" fill="none" stroke-linecap="butt"
                            stroke-dasharray="60, 100" stroke-dashoffset="-38"
                            d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    </svg>
                    <!-- Vòng tròn trắng ở giữa để tạo khoảng trống (Gaps) giả lập -->
                    <div class="absolute inset-0 rounded-full border-[3px] border-white pointer-events-none scale-105"></div>
                    <div class="absolute inset-0 rounded-full border-[3px] border-white pointer-events-none scale-75"></div>
                    <!-- Lưới gap màu trắng tách các khối (Dùng pseudo elements) -->
                    <div class="absolute inset-0 pointer-events-none">
                        <div class="absolute w-full h-[4px] bg-white top-1/2 left-0 -translate-y-1/2 rotate-[15deg]"></div>
                        <div class="absolute w-full h-[4px] bg-white top-1/2 left-0 -translate-y-1/2 rotate-[70deg]"></div>
                        <div class="absolute w-full h-[4px] bg-white top-1/2 left-0 -translate-y-1/2 rotate-[130deg]"></div>
                        <div class="absolute w-full h-[4px] bg-white top-1/2 left-0 -translate-y-1/2 rotate-[-10deg]"></div>
                    </div>
                </div>

                <!-- Legend -->
                <div class="flex flex-wrap items-center justify-center gap-4 text-[12px] text-slate-600 font-medium w-full">
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Pass (145)</div>
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-500"></span> Fail (24)</div>
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-500"></span> Needs Update (12)</div>
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-slate-400"></span> Untested (45)</div>
                </div>
            </div>
        </div>

        <!-- Biểu đồ Cột: Mức độ nghiêm trọng Defect -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 flex flex-col">
            <h3 class="text-[15px] font-bold text-slate-800 mb-8">Mức độ nghiêm trọng Defect (Severity)</h3>
            <div class="flex-1 relative mt-2">
                
                <!-- Lưới Y-Axis (Đường kẻ ngang) -->
                <div class="absolute inset-0 flex flex-col justify-between text-[11px] text-slate-400 pb-6 z-0">
                    <div class="flex items-center gap-3 w-full border-b border-dashed border-slate-200 pb-1"><span>40</span></div>
                    <div class="flex items-center gap-3 w-full border-b border-dashed border-slate-200 pb-1"><span>30</span></div>
                    <div class="flex items-center gap-3 w-full border-b border-dashed border-slate-200 pb-1"><span>20</span></div>
                    <div class="flex items-center gap-3 w-full border-b border-dashed border-slate-200 pb-1"><span>10</span></div>
                    <div class="flex items-center gap-3 w-full border-b border-dashed border-slate-200 pb-1"><span>0</span></div>
                </div>

                <!-- Các Cột (Bars) -->
                <div class="absolute inset-0 pl-10 pb-6 flex items-end justify-around z-10">
                    <!-- Cột Critical -->
                    <div class="w-16 bg-[#EF4444] rounded-t-sm hover:opacity-90 transition-opacity cursor-pointer" style="height: 10%;"></div>
                    <!-- Cột High -->
                    <div class="w-16 bg-[#F97316] rounded-t-sm hover:opacity-90 transition-opacity cursor-pointer" style="height: 38%;"></div>
                    <!-- Cột Medium -->
                    <div class="w-16 bg-[#3B82F6] rounded-t-sm hover:opacity-90 transition-opacity cursor-pointer" style="height: 95%;"></div>
                    <!-- Cột Low -->
                    <div class="w-16 bg-[#64748B] rounded-t-sm hover:opacity-90 transition-opacity cursor-pointer" style="height: 30%;"></div>
                </div>

                <!-- Nhãn X-Axis -->
                <div class="absolute bottom-0 left-0 w-full pl-10 flex items-center justify-around text-[12px] text-slate-500 font-medium">
                    <div class="w-16 text-center">Critical</div>
                    <div class="w-16 text-center">High</div>
                    <div class="w-16 text-center">Medium</div>
                    <div class="w-16 text-center">Low</div>
                </div>
            </div>
        </div>

    </div>

    <!-- BOTTOM SECTION: AI ĐÁNH GIÁ & DEFECT MỚI -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Cột Trái (2 phần): Đánh giá chất lượng từ AI -->
        <div class="lg:col-span-2 bg-white rounded-xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-[16px] font-bold text-slate-800 flex items-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles text-indigo-500"></i> Đánh giá chất lượng từ AI
                </h3>
                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 border border-emerald-200 rounded-full text-xs font-semibold">
                    Dự án Tốt (85/100)
                </span>
            </div>

            <div class="space-y-4">
                <!-- Hộp Cảnh báo (Warning) -->
                <div class="flex gap-4 p-5 rounded-xl bg-orange-50/50 border border-orange-100">
                    <div class="mt-0.5 text-orange-500 text-lg">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h4 class="text-[14px] font-semibold text-slate-800 mb-1">Cảnh báo phân hệ Checkout</h4>
                        <p class="text-[13px] text-slate-600 leading-relaxed">
                            Tỷ lệ lỗi (Defect Density) ở khu vực Thanh toán đang tăng 15% so với tuần trước. AI đề xuất viết thêm Test Case cho các luồng thanh toán qua ví điện tử.
                        </p>
                    </div>
                </div>

                <!-- Hộp Tốt (Success) -->
                <div class="flex gap-4 p-5 rounded-xl bg-emerald-50/50 border border-emerald-100">
                    <div class="mt-0.5 text-emerald-500 text-lg">
                        <i class="fa-regular fa-circle-check"></i>
                    </div>
                    <div>
                        <h4 class="text-[14px] font-semibold text-slate-800 mb-1">Độ phủ kịch bản (Test Coverage)</h4>
                        <p class="text-[13px] text-slate-600 leading-relaxed">
                            Hệ thống ghi nhận 145/226 Test Case đã pass. Module "Đăng nhập" và "Giỏ hàng" đã đạt độ phủ 98% ổn định.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cột Phải (1 phần): Defect mới cập nhật -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 flex flex-col h-full">
            <h3 class="text-[15px] font-bold text-slate-800 mb-4">Defect mới cập nhật</h3>
            
            <div class="flex-1 flex flex-col">
                <!-- Defect 1 -->
                <div class="py-3 border-b border-slate-100 last:border-0 group cursor-pointer">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="text-[13px] font-bold text-slate-700">BUG-1089</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-red-50 text-red-500 border border-red-100">Open</span>
                    </div>
                    <p class="text-[13px] font-medium text-slate-800 mb-1 group-hover:text-blue-600 transition-colors">Crash khi thanh toán bằng thẻ Visa</p>
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-400">
                        <i class="fa-regular fa-clock"></i> 10 phút trước
                    </div>
                </div>

                <!-- Defect 2 -->
                <div class="py-3 border-b border-slate-100 last:border-0 group cursor-pointer">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="text-[13px] font-bold text-slate-700">BUG-1088</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">Resolved</span>
                    </div>
                    <p class="text-[13px] font-medium text-slate-800 mb-1 group-hover:text-blue-600 transition-colors">Sai màu nút Đăng nhập</p>
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-400">
                        <i class="fa-regular fa-clock"></i> 1 giờ trước
                    </div>
                </div>

                <!-- Defect 3 -->
                <div class="py-3 border-b border-slate-100 last:border-0 group cursor-pointer">
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="text-[13px] font-bold text-slate-700">BUG-1087</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100">In Progress</span>
                    </div>
                    <p class="text-[13px] font-medium text-slate-800 mb-1 group-hover:text-blue-600 transition-colors">Không nhận được OTP SMS</p>
                    <div class="flex items-center gap-1.5 text-[11px] text-slate-400">
                        <i class="fa-regular fa-clock"></i> 3 giờ trước
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>