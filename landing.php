<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLT Solutions - AI QA Management Platform</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #0B0E14; color: #FFFFFF; }
        .glass-panel { background: rgba(17, 24, 39, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.05); }
       .text-gradient { background: linear-gradient(to right, #60A5FA, #3B82F6); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent; }
    </style>
</head>
<body class="antialiased overflow-x-hidden">

    <!-- Header -->
    <nav class="max-w-7xl mx-auto px-6 py-6 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="text-2xl font-bold tracking-tighter">PLT</span>
            <span class="text-[10px] font-semibold text-slate-400 uppercase tracking-widest mt-1">Solutions</span>
        </div>
        <div class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-300">
            <a href="#features" class="hover:text-white transition">Tính năng</a>
            <a href="#how-it-works" class="hover:text-white transition">Cách hoạt động</a>
            <a href="demo_login.php" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2.5 rounded-lg transition shadow-[0_0_15px_rgba(37,99,235,0.5)]">
                Dùng thử ngay <i class="fa-solid fa-chevron-right text-xs ml-1"></i>
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="max-w-4xl mx-auto px-6 pt-20 pb-16 text-center flex flex-col items-center">
        <div class="border border-blue-500/30 bg-blue-500/10 text-blue-400 text-xs font-semibold px-4 py-1.5 rounded-full flex items-center gap-2 mb-8 shadow-[0_0_20px_rgba(59,130,246,0.15)]">
            <i class="fa-solid fa-wand-magic-sparkles"></i> AI QA MANAGEMENT PLATFORM
        </div>
        <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight leading-[1.1] mb-6">
            Quản lý QA <br>
            <span class="text-gradient">tích hợp AI.</span>
        </h1>
        <p class="text-lg md:text-xl text-slate-400 mb-10 max-w-2xl leading-relaxed">
            Tự động chấm Test Case, phân tích Defect và báo cáo tổng quan — giúp đội QA làm việc nhanh gấp 10 lần với độ chính xác vượt trội.
        </p>
        <div class="flex flex-col sm:flex-row items-center gap-4">
            <a href="demo_login.php" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-500 text-white px-8 py-3.5 rounded-xl font-semibold transition flex items-center justify-center gap-2 text-lg shadow-[0_0_20px_rgba(37,99,235,0.4)]">
                <i class="fa-solid fa-play text-sm"></i> Dùng thử ngay — miễn phí <i class="fa-solid fa-arrow-right text-sm ml-1"></i>
            </a>
            <a href="login.php" class="w-full sm:w-auto bg-transparent hover:bg-white/5 border border-slate-700 text-white px-8 py-3.5 rounded-xl font-semibold transition">
                Đăng nhập
            </a>
        </div>
        <p class="text-xs text-slate-500 mt-4">Không cần đăng ký · Không cần thẻ · Phiên 30 phút</p>
    </section>

    <!-- App Mockup Preview -->
    <section class="max-w-5xl mx-auto px-6 mb-24 relative z-10">
        <div class="glass-panel rounded-2xl p-2 shadow-2xl">
            <div class="bg-[#151C2C] rounded-xl border border-slate-700/50 overflow-hidden">
                <!-- Mockup Header -->
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-700/50 bg-[#1A2234]">
                    <div class="flex gap-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    </div>
                    <div class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-[10px] font-bold px-2 py-1 rounded-full flex items-center gap-1.5">
                        <div class="w-1.5 h-1.5 bg-emerald-400 rounded-full"></div> AI Online
                    </div>
                </div>
                <!-- Mockup Content -->
                <div class="p-6">
                    <div class="grid grid-cols-4 gap-4 mb-8">
                        <div><div class="text-3xl font-bold text-blue-400">248</div><div class="text-xs text-slate-400">Tổng bài nộp</div></div>
                        <div><div class="text-3xl font-bold text-emerald-400">196</div><div class="text-xs text-slate-400">Đã chấm</div></div>
                        <div><div class="text-3xl font-bold text-orange-400">52</div><div class="text-xs text-slate-400">Chưa chấm</div></div>
                        <div><div class="text-3xl font-bold text-blue-400">97.2%</div><div class="text-xs text-slate-400">AI Accuracy</div></div>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between bg-white/5 p-3 rounded-lg border border-white/5">
                            <div class="flex items-center gap-4"><span class="text-xs text-slate-500 font-mono">TC-001</span><span class="text-sm font-medium">Đăng nhập thành công với email hợp lệ</span></div>
                            <div class="flex items-center gap-4"><span class="text-xs text-emerald-400 bg-emerald-400/10 px-2 py-1 rounded font-bold">PASS</span><span class="text-sm font-bold text-blue-400">9.5</span></div>
                        </div>
                        <div class="flex items-center justify-between bg-white/5 p-3 rounded-lg border border-white/5">
                            <div class="flex items-center gap-4"><span class="text-xs text-slate-500 font-mono">TC-002</span><span class="text-sm font-medium">Kiểm tra validation form đăng ký</span></div>
                            <div class="flex items-center gap-4"><span class="text-xs text-red-400 bg-red-400/10 px-2 py-1 rounded font-bold">FAIL</span><span class="text-sm font-bold text-blue-400">4.0</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="max-w-4xl mx-auto px-6 mb-24">
        <div class="glass-panel rounded-2xl py-10 px-8 flex flex-col md:flex-row justify-around items-center gap-8 text-center divide-y md:divide-y-0 md:divide-x divide-slate-700/50">
            <div class="w-full">
                <div class="text-5xl font-bold mb-2">98%</div><div class="text-sm text-slate-400">Độ chính xác AI</div>
            </div>
            <div class="w-full pt-8 md:pt-0">
                <div class="text-5xl font-bold mb-2">10x</div><div class="text-sm text-slate-400">Nhanh hơn thủ công</div>
            </div>
            <div class="w-full pt-8 md:pt-0">
                <div class="text-5xl font-bold mb-2">500+</div><div class="text-sm text-slate-400">Test Case xử lý / ngày</div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="max-w-6xl mx-auto px-6 mb-32">
        <div class="text-center mb-16">
            <div class="text-blue-500 font-bold tracking-widest text-sm uppercase mb-2">TÍNH NĂNG</div>
            <h2 class="text-4xl font-bold mb-4">Mọi thứ đội QA cần</h2>
            <p class="text-slate-400">Ba module tích hợp chặt chẽ, được thiết kế riêng cho quy trình kiểm thử phần mềm chuyên nghiệp.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1 -->
            <div class="glass-panel p-8 rounded-2xl border-t-2 border-t-blue-500 hover:-translate-y-2 transition duration-300">
                <div class="w-12 h-12 bg-blue-500/10 text-blue-400 rounded-xl flex items-center justify-center text-xl mb-6"><i class="fa-solid fa-check-to-slot"></i></div>
                <h3 class="text-xl font-bold mb-3">Tạo & Chấm Test Case</h3>
                <p class="text-sm text-slate-400 mb-6 leading-relaxed">Nhập test case qua form hoặc import Excel. AI tự động chấm điểm, phân tích kết quả và đề xuất cải tiến theo từng tiêu chí.</p>
                <ul class="text-sm text-slate-300 space-y-2">
                    <li><i class="fa-solid fa-circle text-[6px] text-blue-500 mr-2 border-blue-500"></i> Import Excel / CSV</li>
                    <li><i class="fa-solid fa-circle text-[6px] text-blue-500 mr-2"></i> AI chấm điểm tự động</li>
                </ul>
            </div>
            <!-- Card 2 -->
            <div class="glass-panel p-8 rounded-2xl border-t-2 border-t-pink-500 hover:-translate-y-2 transition duration-300">
                <div class="w-12 h-12 bg-pink-500/10 text-pink-400 rounded-xl flex items-center justify-center text-xl mb-6"><i class="fa-solid fa-bug"></i></div>
                <h3 class="text-xl font-bold mb-3">Quản lý Defect List</h3>
                <p class="text-sm text-slate-400 mb-6 leading-relaxed">Ghi nhận và theo dõi defect đầy đủ với 15 trường thông tin: mức độ nghiêm trọng, priority, build version và đính kèm ảnh.</p>
                <ul class="text-sm text-slate-300 space-y-2">
                    <li><i class="fa-solid fa-circle text-[6px] text-pink-500 mr-2"></i> 15 trường thông tin</li>
                    <li><i class="fa-solid fa-circle text-[6px] text-pink-500 mr-2"></i> Phân loại Severity</li>
                </ul>
            </div>
            <!-- Card 3 -->
            <div class="glass-panel p-8 rounded-2xl border-t-2 border-t-emerald-500 hover:-translate-y-2 transition duration-300">
                <div class="w-12 h-12 bg-emerald-500/10 text-emerald-400 rounded-xl flex items-center justify-center text-xl mb-6"><i class="fa-solid fa-chart-simple"></i></div>
                <h3 class="text-xl font-bold mb-3">Dashboard Tổng quan</h3>
                <p class="text-sm text-slate-400 mb-6 leading-relaxed">Theo dõi 5 chỉ số chính theo thời gian thực: tổng bài nộp, đã chấm, chưa chấm, điểm trung bình và độ chính xác AI.</p>
                <ul class="text-sm text-slate-300 space-y-2">
                    <li><i class="fa-solid fa-circle text-[6px] text-emerald-500 mr-2"></i> Cập nhật thời gian thực</li>
                    <li><i class="fa-solid fa-circle text-[6px] text-emerald-500 mr-2"></i> AI Accuracy tracking</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Bottom CTA -->
    <section class="max-w-4xl mx-auto px-6 mb-20 text-center">
        <div class="bg-gradient-to-br from-blue-900/40 to-[#0B0E14] border border-blue-500/20 rounded-3xl p-12 shadow-2xl relative overflow-hidden">
            <div class="absolute inset-0 bg-blue-500/5 blur-3xl rounded-full transform scale-150"></div>
            <div class="relative z-10">
                <h2 class="text-3xl md:text-5xl font-bold mb-4">Sẵn sàng nâng cấp <br>quy trình QA của bạn?</h2>
                <p class="text-slate-400 mb-8">Trải nghiệm sức mạnh AI trong quản lý chất lượng phần mềm ngay hôm nay.</p>
                <a href="demo_login.php" class="inline-block bg-blue-600 hover:bg-blue-500 text-white px-8 py-3.5 rounded-xl font-semibold transition shadow-[0_0_20px_rgba(37,99,235,0.4)]">
                    Dùng thử miễn phí ngay <i class="fa-solid fa-arrow-right text-sm ml-1"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="border-t border-slate-800 py-8 text-center text-sm text-slate-500">
        <div class="flex items-center justify-center gap-2 mb-4 text-white opacity-50">
            <span class="text-lg font-bold tracking-tighter">PLT</span>
            <span class="text-[8px] font-semibold uppercase tracking-widest mt-1">Solutions</span>
        </div>
        <p>&copy; 2026 PLT Solutions - AI QA Management Platform.</p>
    </footer>

</body>
</html>