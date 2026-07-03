<?php
session_start();

// Nếu người dùng đã đăng nhập rồi thì tự động chuyển hướng vào trang trong (index.php)
if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}

$error = '';

// Xử lý khi người dùng nhấn nút Đăng nhập
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'includes/database.php'; // Kết nối CSDL
    
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (!empty($username) && !empty($password)) {
        try {
            // Truy vấn kiểm tra user (Lưu ý: Trong dữ liệu mẫu, mật khẩu đang lưu dạng plain text '123456')
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND password_hash = :password AND status = 'ACTIVE' LIMIT 1");
            $stmt->execute([
                ':username' => $username,
                ':password' => $password
            ]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Đăng nhập thành công -> Lưu thông tin vào Session
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'full_name' => $user['full_name'],
                    'username' => $user['username'],
                    'role_id' => $user['role_id']
                ];
                
                // Chuyển hướng vào Dashboard
                header("Location: index.php");
                exit();
            } else {
                $error = 'Tài khoản hoặc mật khẩu không chính xác!';
            }
        } catch (Exception $e) {
            $error = 'Lỗi hệ thống: ' . $e->getMessage();
        }
    } else {
        $error = 'Vui lòng nhập đầy đủ tài khoản và mật khẩu!';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - PLT Solutions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <!-- Khung Form Đăng Nhập -->
    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 w-full max-w-md overflow-hidden">
        
        <!-- Header / Logo -->
        <div class="bg-[#0F172A] p-8 text-center relative overflow-hidden">
            <!-- Background AI Sparkle Effect -->
            <div class="absolute top-[-20px] right-[-20px] text-white/5 text-8xl rotate-12">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
            </div>
            
            <div class="relative z-10 flex flex-col items-center">
                <h2 class="text-3xl font-bold tracking-wide text-white">PLT</h2>
                <span class="text-[11px] font-semibold tracking-[0.25em] text-blue-400 mt-1">SOLUTIONS</span>
                <p class="text-slate-400 text-sm mt-3">AI Test Management System</p>
            </div>
        </div>

        <!-- Form Nhập Liệu -->
        <div class="p-8">
            <h3 class="text-xl font-bold text-slate-800 mb-6 text-center">Đăng nhập hệ thống</h3>
            
            <!-- Hiển thị thông báo lỗi nếu có -->
            <?php if (!empty($error)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-3 mb-6 rounded-r-lg">
                    <p class="text-sm text-red-700 font-medium"><i class="fa-solid fa-circle-exclamation mr-1"></i> <?php echo $error; ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="space-y-5">
                
                <!-- Input Tài khoản -->
                <div>
                    <label class="block text-[13px] font-semibold text-slate-700 mb-1.5">Tên đăng nhập</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-regular fa-user text-slate-400"></i>
                        </div>
                        <input type="text" name="username" required placeholder="Nhập tài khoản..." 
                               class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors bg-slate-50 focus:bg-white text-slate-800">
                    </div>
                </div>

                <!-- Input Mật khẩu -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-[13px] font-semibold text-slate-700">Mật khẩu</label>
                        <a href="#" class="text-[12px] font-medium text-blue-600 hover:text-blue-800 transition-colors">Quên mật khẩu?</a>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-lock text-slate-400"></i>
                        </div>
                        <input type="password" name="password" required placeholder="••••••••" 
                               class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors bg-slate-50 focus:bg-white text-slate-800">
                    </div>
                </div>

                <!-- Ghi nhớ đăng nhập -->
                <div class="flex items-center">
                    <input type="checkbox" id="remember" class="w-4 h-4 text-blue-600 bg-slate-100 border-slate-300 rounded focus:ring-blue-500 cursor-pointer">
                    <label for="remember" class="ml-2 text-[13px] font-medium text-slate-600 cursor-pointer">Ghi nhớ đăng nhập</label>
                </div>

                <!-- Nút Đăng nhập -->
                <button type="submit" class="w-full bg-[#2563EB] hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg text-sm shadow-md shadow-blue-500/30 transition-all flex justify-center items-center gap-2 mt-2">
                    Đăng nhập <i class="fa-solid fa-arrow-right"></i>
                </button>
            </form>

        </div>
        
        <!-- Footer Form -->
        <div class="bg-slate-50 p-4 text-center border-t border-slate-100">
            <p class="text-[12px] text-slate-500">
                Chưa có tài khoản? <a href="#" class="font-semibold text-blue-600 hover:text-blue-800">Liên hệ Quản trị viên</a>
            </p>
        </div>
    </div>

</body>
</html>