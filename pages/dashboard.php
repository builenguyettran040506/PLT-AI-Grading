<?php
$db_path = file_exists('includes/database.php') ? 'includes/database.php' : '../includes/database.php';
if (file_exists($db_path)) { require_once $db_path; }

if (session_status() === PHP_SESSION_NONE) { session_start(); }
$isGuest = (!isset($_SESSION['user']) && isset($_SESSION['role']) && $_SESSION['role'] === 'guest');

// 1. TRUY VẤN DỮ LIỆU
$total_tc = $pdo->query("SELECT COUNT(*) FROM test_cases")->fetchColumn();
$passed_tc = $pdo->query("SELECT COUNT(*) FROM test_cases WHERE result_status = 'Passed'")->fetchColumn();
$failed_tc = $pdo->query("SELECT COUNT(*) FROM test_cases WHERE result_status = 'Failed'")->fetchColumn();
$untested_tc = $pdo->query("SELECT COUNT(*) FROM test_cases WHERE result_status = 'Untested' OR result_status IS NULL")->fetchColumn();
$tested_tc = $passed_tc + $failed_tc;

$pct_pass = $total_tc > 0 ? round(($passed_tc / $total_tc) * 100) : 0;
$pct_fail = $total_tc > 0 ? round(($failed_tc / $total_tc) * 100) : 0;
$pct_untested = $total_tc > 0 ? round(($untested_tc / $total_tc) * 100) : 0;

$crit_bug = $pdo->query("SELECT COUNT(*) FROM defects WHERE severity = 'Critical'")->fetchColumn();
$high_bug = $pdo->query("SELECT COUNT(*) FROM defects WHERE severity = 'High'")->fetchColumn();
$med_bug = $pdo->query("SELECT COUNT(*) FROM defects WHERE severity = 'Medium'")->fetchColumn();
$low_bug = $pdo->query("SELECT COUNT(*) FROM defects WHERE severity = 'Low'")->fetchColumn();
$max_bug = max($crit_bug, $high_bug, $med_bug, $low_bug);
$max_bug = $max_bug > 0 ? $max_bug : 1;

$h_crit = round(($crit_bug / $max_bug) * 100);
$h_high = round(($high_bug / $max_bug) * 100);
$h_med  = round(($med_bug / $max_bug) * 100);
$h_low  = round(($low_bug / $max_bug) * 100);

$recent_defects = $pdo->query("SELECT defect_code, title, status, created_at FROM defects ORDER BY created_at DESC LIMIT 3")->fetchAll(PDO::FETCH_ASSOC);
$ai_score = 8.5;
$ai_accuracy = 98.2;
?>

<!-- Header đồng bộ -->
<header class="flex flex-wrap items-center justify-between mb-6 gap-4">
    <h1 class="text-2xl font-bold text-slate-800">Tổng quan Dự án</h1>
    
    <div class="flex items-center gap-4">
        <a href="landing.php" class="flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-slate-200 bg-white hover:bg-slate-50 text-[13px] font-semibold text-slate-600 transition shadow-sm">
            <i class="fa-solid fa-arrow-left-long text-slate-400"></i> Trang chủ
        </a>

        <div class="flex items-center gap-2 bg-slate-200/50 px-3 py-1.5 rounded-full border border-slate-200">
            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
            <span class="text-sm font-medium text-slate-600">AI Engine: Online</span>
        </div>
        
        <?php if ($isGuest): ?>
            <a href="landing.php" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full font-bold text-[13px] shadow-md shadow-indigo-500/30 transition">
                Đăng ký tài khoản
            </a>
        <?php else: ?>
            <div class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-sm shadow-md cursor-pointer">QA</div>
        <?php endif; ?>
    </div>
</header>

<div class="h-full overflow-y-auto custom-scrollbar pb-10">
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 flex flex-col justify-center">
            <div class="flex justify-between items-start mb-2"><div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500"><i class="fa-regular fa-file-lines"></i></div></div>
            <h3 class="text-2xl font-bold text-slate-800"><?= number_format($total_tc) ?></h3><p class="text-[13px] text-slate-500 mt-1">Tổng bài nộp</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 flex flex-col justify-center">
            <div class="flex justify-between items-start mb-2"><div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-500"><i class="fa-regular fa-circle-check"></i></div></div>
            <h3 class="text-2xl font-bold text-slate-800"><?= number_format($tested_tc) ?></h3><p class="text-[13px] text-slate-500 mt-1">Đã chấm</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 flex flex-col justify-center">
            <div class="flex justify-between items-start mb-2"><div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center text-orange-400"><i class="fa-regular fa-circle-question"></i></div></div>
            <h3 class="text-2xl font-bold text-slate-800"><?= number_format($untested_tc) ?></h3><p class="text-[13px] text-slate-500 mt-1">Chưa chấm</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100 flex flex-col justify-center">
            <div class="flex justify-between items-start mb-2"><div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center text-purple-500"><i class="fa-regular fa-star"></i></div></div>
            <h3 class="text-2xl font-bold text-slate-800"><?= $ai_score ?><span class="text-sm font-medium text-slate-400">/10</span></h3><p class="text-[13px] text-slate-500 mt-1">Điểm TB</p>
        </div>
        <div class="bg-gradient-to-br from-[#4F46E5] to-[#2563EB] rounded-xl p-5 shadow-md flex flex-col justify-center relative overflow-hidden">
            <div class="absolute top-2 right-3 text-white/20 text-4xl"><i class="fa-solid fa-wand-magic-sparkles"></i></div>
            <div class="flex justify-between items-start mb-2 relative z-10"><div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center text-white backdrop-blur-sm"><i class="fa-solid fa-bullseye"></i></div></div>
            <h3 class="text-3xl font-bold text-white relative z-10"><?= $ai_accuracy ?>%</h3><p class="text-[13px] text-blue-100 mt-1 font-medium relative z-10">AI Accuracy</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 flex flex-col">
            <h3 class="text-[15px] font-bold text-slate-800 mb-8">Trạng thái Test Case</h3>
            <div class="flex-1 flex flex-col items-center justify-center">
                <div class="relative w-48 h-48 mb-8">
                    <svg viewBox="0 0 36 36" class="w-full h-full transform -rotate-90">
                        <path class="text-slate-400 transition-all duration-1000" stroke-width="6" stroke="currentColor" fill="none" stroke-dasharray="<?= $pct_untested ?>, 100" stroke-dashoffset="0" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <path class="text-red-500 transition-all duration-1000" stroke-width="6" stroke="currentColor" fill="none" stroke-dasharray="<?= $pct_fail ?>, 100" stroke-dashoffset="<?= -($pct_untested) ?>" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        <path class="text-emerald-500 transition-all duration-1000" stroke-width="6" stroke="currentColor" fill="none" stroke-dasharray="<?= $pct_pass ?>, 100" stroke-dashoffset="<?= -($pct_untested + $pct_fail) ?>" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                    </svg>
                    <div class="absolute inset-0 rounded-full border-[3px] border-white pointer-events-none scale-105"></div>
                    <div class="absolute inset-0 rounded-full border-[3px] border-white pointer-events-none scale-75"></div>
                </div>
                <div class="flex flex-wrap items-center justify-center gap-4 text-[12px] text-slate-600 font-medium w-full">
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Pass (<?= $passed_tc ?>)</div>
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-red-500"></span> Fail (<?= $failed_tc ?>)</div>
                    <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-slate-400"></span> Untested (<?= $untested_tc ?>)</div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 flex flex-col">
            <h3 class="text-[15px] font-bold text-slate-800 mb-8">Mức độ nghiêm trọng Defect (Severity)</h3>
            <div class="flex-1 relative mt-2">
                <div class="absolute inset-0 flex flex-col justify-between text-[11px] text-slate-400 pb-6 z-0">
                    <div class="flex items-center gap-3 w-full border-b border-dashed border-slate-200 pb-1"><span><?= $max_bug ?></span></div>
                    <div class="flex items-center gap-3 w-full border-b border-dashed border-slate-200 pb-1"><span><?= round($max_bug * 0.75) ?></span></div>
                    <div class="flex items-center gap-3 w-full border-b border-dashed border-slate-200 pb-1"><span><?= round($max_bug * 0.5) ?></span></div>
                    <div class="flex items-center gap-3 w-full border-b border-dashed border-slate-200 pb-1"><span><?= round($max_bug * 0.25) ?></span></div>
                    <div class="flex items-center gap-3 w-full border-b border-dashed border-slate-200 pb-1"><span>0</span></div>
                </div>
                <div class="absolute inset-0 pl-10 pb-6 flex items-end justify-around z-10">
                    <div class="w-12 sm:w-16 bg-[#EF4444] rounded-t-sm transition-all duration-1000 group relative" style="height: <?= $h_crit ?>%;"></div>
                    <div class="w-12 sm:w-16 bg-[#F97316] rounded-t-sm transition-all duration-1000 group relative" style="height: <?= $h_high ?>%;"></div>
                    <div class="w-12 sm:w-16 bg-[#3B82F6] rounded-t-sm transition-all duration-1000 group relative" style="height: <?= $h_med ?>%;"></div>
                    <div class="w-12 sm:w-16 bg-[#64748B] rounded-t-sm transition-all duration-1000 group relative" style="height: <?= $h_low ?>%;"></div>
                </div>
                <div class="absolute bottom-0 left-0 w-full pl-10 flex items-center justify-around text-[12px] text-slate-500 font-medium">
                    <div class="w-12 sm:w-16 text-center">Critical</div><div class="w-12 sm:w-16 text-center">High</div>
                    <div class="w-12 sm:w-16 text-center">Medium</div><div class="w-12 sm:w-16 text-center">Low</div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-[16px] font-bold text-slate-800 flex items-center gap-2"><i class="fa-solid fa-wand-magic-sparkles text-indigo-500"></i> Đánh giá chất lượng từ AI</h3>
                <span class="px-3 py-1 bg-emerald-50 text-emerald-600 border border-emerald-200 rounded-full text-xs font-semibold">Dự án Tốt (85/100)</span>
            </div>
            <div class="space-y-4">
                <div class="flex gap-4 p-5 rounded-xl bg-orange-50/50 border border-orange-100">
                    <div class="mt-0.5 text-orange-500 text-lg"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <div>
                        <h4 class="text-[14px] font-semibold text-slate-800 mb-1">Cảnh báo phân hệ</h4>
                        <p class="text-[13px] text-slate-600 leading-relaxed">Tỷ lệ lỗi đang có xu hướng tăng. AI đề xuất viết thêm Test Case cho các luồng xử lý chính.</p>
                    </div>
                </div>
                <div class="flex gap-4 p-5 rounded-xl bg-emerald-50/50 border border-emerald-100">
                    <div class="mt-0.5 text-emerald-500 text-lg"><i class="fa-regular fa-circle-check"></i></div>
                    <div>
                        <h4 class="text-[14px] font-semibold text-slate-800 mb-1">Độ phủ kịch bản (Test Coverage)</h4>
                        <p class="text-[13px] text-slate-600 leading-relaxed">Hệ thống ghi nhận <b><?= $passed_tc ?></b> Test Case đã Pass trên tổng số <b><?= $total_tc ?></b> kịch bản.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 flex flex-col h-full">
            <h3 class="text-[15px] font-bold text-slate-800 mb-4">Defect mới cập nhật</h3>
            <div class="flex-1 flex flex-col">
                <?php if (count($recent_defects) > 0): ?>
                    <?php foreach ($recent_defects as $bug): ?>
                        <div class="py-3 border-b border-slate-100 last:border-0">
                            <div class="flex items-center gap-2 mb-1.5"><span class="text-[13px] font-bold text-slate-700"><?= htmlspecialchars($bug['defect_code']) ?></span></div>
                            <p class="text-[13px] font-medium text-slate-800 mb-1 truncate"><?= htmlspecialchars($bug['title']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="text-center text-[13px] text-slate-400 py-8">Chưa có lỗi nào.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>