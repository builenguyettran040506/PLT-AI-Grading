<aside class="sidebar">
    <div class="logo">
        <a href="index.php" class="logo-link">
            <h2 class="text-2xl font-bold tracking-wide text-white">PLT</h2>
            <span class="text-[10px] font-semibold tracking-[0.2em] text-slate-400 mt-1">SOLUTIONS</span>
        </a>
    </div>

    <ul class="mt-4">
        <li>
            <a href="index.php?page=dashboard" class="<?php echo ($page == 'dashboard') ? 'active' : ''; ?>">
                <i class="fa-solid fa-border-all w-5 text-center"></i> Tổng quan
            </a>
        </li>
        <li>
            <a href="index.php?page=testcase" class="<?php echo ($page == 'testcase') ? 'active' : ''; ?>">
                <i class="fa-regular fa-square-check w-5 text-center"></i> Viết Test Case
            </a>
        </li>
        <li>
            <a href="index.php?page=defect" class="<?php echo ($page == 'defect') ? 'active' : ''; ?>">
                <i class="fa-solid fa-bug w-5 text-center"></i> Defect List
            </a>
        </li>
        <li>
            <a href="index.php?page=settings" class="<?php echo ($page == 'settings') ? 'active' : ''; ?>">
                <i class="fa-solid fa-gear w-5 text-center"></i> Cài đặt AI
            </a>
        </li>
    </ul>

    <div class="pro-card">
        <h3 class="flex items-center gap-2 text-yellow-500 font-semibold text-sm">
            <i class="fa-solid fa-wand-magic-sparkles"></i> Pro AI Tiers
        </h3>
        <p>Nâng cấp để chấm điểm tự động không giới hạn.</p>
        <button class="hover:bg-blue-600 transition">Nâng cấp ngay</button>
    </div>
</aside>