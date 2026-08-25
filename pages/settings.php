<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Xử lý khi người dùng nhấn nút "Lưu thay đổi"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action_update_settings'])) {
    // Ở đây bạn sẽ viết code SQL UPDATE để cập nhật vào CSDL.
    // Ví dụ: $pdo->prepare("UPDATE users SET fullname = ?, email = ? WHERE id = ?")...

    // Giả lập lưu thành công và trả về thông báo
    $_SESSION['toast_msg'] = "Đã cập nhật cài đặt thành công!";
    $_SESSION['toast_type'] = "success";
    
    // Tải lại trang để tránh lỗi gửi lại form
    header("Location: index.php?page=settings");
    exit;
}
?>

<!-- Header nội dung -->
<header class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-semibold text-slate-800">Cài đặt hệ thống</h1>
        <p class="text-[13px] text-slate-500 mt-1">Quản lý thông tin tài khoản và tùy chọn giao diện của bạn.</p>
    </div>
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

<div class="relative">
    <!-- Hiển thị Toast Message từ PHP Session -->
    <?php if (isset($_SESSION['toast_msg'])): ?>
        <div id="php-toast" class="absolute -top-16 right-0 <?= $_SESSION['toast_type'] === 'success' ? 'bg-slate-800' : 'bg-red-600' ?> text-white px-4 py-2 rounded-lg text-sm shadow-lg transition-opacity pointer-events-none z-50 flex items-center gap-2">
            <i class="fa-solid <?= $_SESSION['toast_type'] === 'success' ? 'fa-circle-check text-emerald-400' : 'fa-circle-exclamation text-white' ?>"></i> 
            <span><?= $_SESSION['toast_msg'] ?></span>
        </div>
        <?php unset($_SESSION['toast_msg']); unset($_SESSION['toast_type']); ?>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 items-start">
        
        <!-- Cột Trái: Menu Điều Hướng Cài Đặt -->
        <div class="lg:col-span-1 bg-white rounded-xl shadow-sm border border-slate-200 p-2 sticky top-6">
            <nav class="space-y-1">
                <a href="#profile" class="flex items-center gap-3 px-4 py-2.5 bg-blue-50 text-blue-700 rounded-lg text-[13px] font-semibold transition">
                    <i class="fa-regular fa-user w-5 text-center text-blue-600"></i> Thông tin cá nhân
                </a>
                <a href="#security" class="flex items-center gap-3 px-4 py-2.5 text-slate-600 hover:bg-slate-50 rounded-lg text-[13px] font-medium transition">
                    <i class="fa-solid fa-shield-halved w-5 text-center text-slate-400"></i> Bảo mật tài khoản
                </a>
                <a href="#preferences" class="flex items-center gap-3 px-4 py-2.5 text-slate-600 hover:bg-slate-50 rounded-lg text-[13px] font-medium transition">
                    <i class="fa-solid fa-sliders w-5 text-center text-slate-400"></i> Tùy chọn hệ thống
                </a>
                <a href="#notifications" class="flex items-center gap-3 px-4 py-2.5 text-slate-600 hover:bg-slate-50 rounded-lg text-[13px] font-medium transition">
                    <i class="fa-regular fa-bell w-5 text-center text-slate-400"></i> Thông báo
                </a>
            </nav>
        </div>

        <!-- Cột Phải: Các Form Nội Dung -->
        <div class="lg:col-span-3 space-y-6">
            <form method="POST" action="index.php?page=settings">
                
                <!-- CARD 1: THÔNG TIN CÁ NHÂN -->
                <div id="profile" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden scroll-mt-6">
                    <div class="p-5 border-b border-slate-100">
                        <h2 class="text-[15px] font-bold text-slate-800">Thông tin cá nhân</h2>
                        <p class="text-[13px] text-slate-500 mt-0.5">Cập nhật hình đại diện và thông tin liên hệ của bạn.</p>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center gap-6 mb-8">
                            <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 text-2xl font-bold border-2 border-white shadow-sm overflow-hidden">
                                <!-- Chữ cái đầu của tên hoặc ảnh đại diện -->
                                <span>T</span>
                            </div>
                            <div>
                                <button type="button" class="px-4 py-2 bg-white border border-slate-300 rounded-lg text-[13px] font-medium text-slate-700 hover:bg-slate-50 transition shadow-sm mb-2">
                                    Đổi ảnh đại diện
                                </button>
                                <p class="text-[12px] text-slate-500">Định dạng JPG, GIF hoặc PNG. Tối đa 2MB.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Họ và tên</label>
                                <input type="text" name="fullname" value="Bùi Lê Nguyệt Trân" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 bg-slate-50 focus:bg-white transition-colors">
                            </div>
                            <div>
                                <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Vai trò / Chức vụ</label>
                                <input type="text" name="role" value="QA / Tester Intern" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 bg-slate-50 focus:bg-white transition-colors" readonly>
                            </div>
                            <div>
                                <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Email</label>
                                <input type="email" name="email" value="nguyettran.qa@example.com" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 bg-slate-50 focus:bg-white transition-colors">
                            </div>
                            <div>
                                <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Số điện thoại</label>
                                <input type="text" name="phone" value="0901234567" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 bg-slate-50 focus:bg-white transition-colors">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CARD 2: BẢO MẬT TÀI KHOẢN -->
                <div id="security" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mt-6 scroll-mt-6">
                    <div class="p-5 border-b border-slate-100">
                        <h2 class="text-[15px] font-bold text-slate-800">Bảo mật tài khoản</h2>
                        <p class="text-[13px] text-slate-500 mt-0.5">Đảm bảo tài khoản của bạn đang sử dụng một mật khẩu mạnh và an toàn.</p>
                    </div>
                    <div class="p-6 space-y-5">
                        <div class="max-w-md">
                            <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Mật khẩu hiện tại</label>
                            <input type="password" name="current_password" placeholder="••••••••" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 bg-slate-50 focus:bg-white transition-colors">
                        </div>
                        <div class="max-w-md">
                            <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Mật khẩu mới</label>
                            <input type="password" name="new_password" placeholder="Tối thiểu 8 ký tự" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 bg-slate-50 focus:bg-white transition-colors">
                        </div>
                        <div class="max-w-md">
                            <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Xác nhận mật khẩu mới</label>
                            <input type="password" name="confirm_password" placeholder="Nhập lại mật khẩu mới" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-slate-800 bg-slate-50 focus:bg-white transition-colors">
                        </div>
                    </div>
                </div>

                <!-- CARD 3: TÙY CHỌN HỆ THỐNG & THÔNG BÁO -->
                <div id="preferences" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden mt-6 scroll-mt-6 mb-6">
                    <div class="p-5 border-b border-slate-100">
                        <h2 class="text-[15px] font-bold text-slate-800">Tùy chọn hệ thống</h2>
                    </div>
                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Ngôn ngữ</label>
                            <select class="w-full border border-slate-300 rounded-lg px-3 py-2 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 cursor-pointer">
                                <option value="vi" selected>Tiếng Việt</option>
                                <option value="en">English (US)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Khu vực (Timezone)</label>
                            <select class="w-full border border-slate-300 rounded-lg px-3 py-2 text-[13px] focus:outline-none focus:ring-2 focus:ring-blue-500 bg-slate-50 cursor-pointer">
                                <option value="HCM" selected>(GMT+07:00) Ho Chi Minh City</option>
                            </select>
                        </div>
                    </div>
                    
                    <div id="notifications" class="p-6 border-t border-slate-100 scroll-mt-6">
                        <h3 class="text-[14px] font-bold text-slate-800 mb-4">Nhận thông báo qua Email</h3>
                        
                        <div class="space-y-4">
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" checked class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500 cursor-pointer">
                                <span class="text-[13px] text-slate-700 group-hover:text-slate-900 transition">Thông báo khi có tài khoản gán Defect mới cho tôi</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" checked class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500 cursor-pointer">
                                <span class="text-[13px] text-slate-700 group-hover:text-slate-900 transition">Thông báo khi trạng thái Defect được cập nhật</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer group">
                                <input type="checkbox" class="w-4 h-4 text-blue-600 rounded border-slate-300 focus:ring-blue-500 cursor-pointer">
                                <span class="text-[13px] text-slate-700 group-hover:text-slate-900 transition">Email nhắc nhở Test Case chưa hoàn thành hàng tuần</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- NÚT LƯU CỐ ĐỊNH Ở DƯỚI -->
                <div class="flex items-center justify-end gap-3 sticky bottom-4">
                    <button type="button" onclick="window.location.reload();" class="px-5 py-2 border border-slate-300 bg-white rounded-lg text-[13px] font-semibold text-slate-700 hover:bg-slate-50 transition shadow-sm">
                        Hủy thay đổi
                    </button>
                    <button type="submit" name="action_update_settings" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-[13px] font-semibold shadow-sm transition flex items-center gap-2">
                        <i class="fa-regular fa-floppy-disk"></i> Lưu cài đặt
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Tự động ẩn thông báo Toast PHP sau 3 giây
    document.addEventListener("DOMContentLoaded", function() {
        const toast = document.getElementById('php-toast');
        if(toast) {
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
        
        // Kịch bản JS: Nhấn vào menu bên trái sẽ Highlight màu menu đó
        const navLinks = document.querySelectorAll('nav a');
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                // Xóa màu active của tất cả các nút
                navLinks.forEach(l => {
                    l.className = 'flex items-center gap-3 px-4 py-2.5 text-slate-600 hover:bg-slate-50 rounded-lg text-[13px] font-medium transition';
                    l.querySelector('i').className = l.querySelector('i').className.replace('text-blue-600', 'text-slate-400');
                });
                
                // Set màu active cho nút được nhấn
                this.className = 'flex items-center gap-3 px-4 py-2.5 bg-blue-50 text-blue-700 rounded-lg text-[13px] font-semibold transition';
                this.querySelector('i').className = this.querySelector('i').className.replace('text-slate-400', 'text-blue-600');
            });
        });
    });
</script>