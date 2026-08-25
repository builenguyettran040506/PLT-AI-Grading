<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$isGuest = (!isset($_SESSION['user']) && isset($_SESSION['role']) && $_SESSION['role'] === 'guest');

$timeLeft = 1800; 
if ($isGuest && isset($_SESSION['trial_start'])) {
    $timeLeft = 1800 - (time() - $_SESSION['trial_start']);
    if ($timeLeft < 0) $timeLeft = 0;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PLT Solutions - AI Test Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- CSS CỐT LÕI ÉP BUỘC LAYOUT (Chống vỡ giao diện) -->
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; margin: 0; padding: 0; }
        
        .wrapper { display: flex; width: 100%; min-height: 100vh; }
        .sidebar { width: 260px; height: 100vh; background: #0F172A; position: fixed; left: 0; top: 0; color: white; z-index: 50; }
        .main-content { flex: 1; margin-left: 260px; width: calc(100% - 260px); padding: 32px; box-sizing: border-box; }
        
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #94A3B8; }
    </style>
</head>
<body>

<?php if ($isGuest): ?>
<!-- BANNER DÙNG THỬ -->
<div class="w-full bg-gradient-to-r from-indigo-600 to-blue-500 text-white px-6 py-2 flex items-center justify-between text-sm z-[60] relative shadow-md">
    <div class="flex items-center gap-2 font-bold tracking-wider text-[12px] uppercase">
        <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span> Phiên dùng thử
    </div>
    <div class="flex items-center gap-4">
        <div class="flex items-center gap-2 font-mono text-[15px] font-bold">
            <i class="fa-regular fa-clock"></i> <span id="trial-timer">--:--</span>
        </div>
        <span class="hidden md:inline text-[13px] opacity-90 font-medium">Dữ liệu chỉ lưu trong phiên này</span>
    </div>
    <a href="landing.php" class="bg-white text-blue-600 hover:bg-blue-50 px-4 py-1 rounded-full font-bold text-[12px] flex items-center gap-1.5 shadow-sm transition">
        Đăng ký <i class="fa-solid fa-arrow-right"></i>
    </a>
</div>

<script>
    let timeLeft = <?= $timeLeft ?>;
    const timerEl = document.getElementById('trial-timer');
    const timerInterval = setInterval(() => {
        if(timeLeft <= 0) {
            clearInterval(timerInterval);
            window.location.href = 'index.php'; // Hết giờ, ép F5 để Backend đá ra
            return;
        }
        timeLeft--;
        let m = Math.floor(timeLeft / 60);
        let s = timeLeft % 60;
        if(timerEl) timerEl.innerText = (m < 10 ? '0'+m : m) + ':' + (s < 10 ? '0'+s : s);
    }, 1000);
</script>
<?php endif; ?>