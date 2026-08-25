<?php 
if (!isset($page)) { 
    $page = isset($_GET['page']) ? $_GET['page'] : 'dashboard'; 
}
$isGuest = (!isset($_SESSION['user']) && isset($_SESSION['role']) && $_SESSION['role'] === 'guest');
?>
<aside class="sidebar flex flex-col h-screen border-r border-slate-800">
    <div class="flex items-center justify-center py-6 border-b border-slate-800/80 mb-2">
        <a href="index.php" class="block w-32 hover:scale-105 transition-transform duration-300">
            <img src="assets/images/Cardmoi_PLT_Trang.png" alt="PLT Solutions" class="w-full h-auto object-contain">
        </a>
    </div>

    <ul class="mt-4 flex-1 px-3 space-y-1">
        <li>
            <a href="index.php?page=dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[14px] font-medium transition <?= ($page == 'dashboard') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white'; ?>">
                <i class="fa-solid fa-border-all w-5 text-center"></i> Tổng quan
            </a>
        </li>
        <li>
            <a href="index.php?page=testcase" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[14px] font-medium transition <?= ($page == 'testcase') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white'; ?>">
                <i class="fa-regular fa-square-check w-5 text-center"></i> Viết Test Case
            </a>
        </li>
        <li>
            <a href="index.php?page=defect" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[14px] font-medium transition <?= ($page == 'defect') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white'; ?>">
                <i class="fa-solid fa-bug w-5 text-center"></i> Defect List
            </a>
        </li>
        <li>
            <a href="index.php?page=settings" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[14px] font-medium transition <?= ($page == 'settings') ? 'bg-blue-600 text-white shadow-md' : 'text-slate-400 hover:bg-slate-800 hover:text-white'; ?>">
                <i class="fa-solid fa-gear w-5 text-center"></i> <?= $isGuest ? 'Cài đặt AI' : 'Cài đặt'; ?>
            </a>
        </li>
    </ul>

    <?php if ($isGuest): ?>
        <div class="m-4 p-5 rounded-xl bg-slate-800/80 border border-slate-700 text-center shadow-lg">
            <h4 class="text-amber-400 font-bold text-[13px] mb-2 flex items-center justify-center gap-1.5"><i class="fa-solid fa-wand-magic-sparkles"></i> Pro AI</h4>
            <p class="text-[12px] text-slate-400 mb-4 leading-relaxed">Chấm điểm tự động không giới hạn.</p>
            <a href="landing.php" class="block w-full py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg text-[13px] font-semibold transition">Nâng cấp ngay</a>
        </div>
    <?php else: ?>
        <div class="p-4 border-t border-slate-800/80">
            <a href="logout.php" onclick="return confirm('Bạn có chắc chắn muốn đăng xuất?');" class="flex items-center gap-3 px-4 py-3 rounded-xl text-red-400 hover:bg-red-500/10 hover:text-red-500 transition">
                <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center"></i>
                <span class="text-[14px] font-medium">Đăng xuất</span>
            </a>
        </div>
    <?php endif; ?>
</aside>