<?php require_once __DIR__ . '/../../config/session.php'; $u = current_user(); $notifCount = unread_notifications_count($_SESSION['user_id'] ?? 0); ?>
<nav class="topbar">
    <div class="d-flex align-items-center gap-3">
        <button class="btn btn-sm d-lg-none" id="sidebarToggle" style="background:var(--sidebar-hover);border:none;border-radius:10px;padding:8px 12px;color:var(--text2);"><i class="fa-solid fa-bars"></i></button>
        <form action="search.php" method="GET" class="topbar-search d-none d-md-flex" style="margin:0;">
            <i class="fa-solid fa-magnifying-glass" style="font-size:0.8rem;color:var(--text2);"></i>
            <input type="text" name="q" placeholder="Search employees, reports...">
        </form>
    </div>
    <div class="d-flex align-items-center gap-3">
        <a href="leave_requests.php" class="position-relative text-decoration-none" style="padding:8px;color:var(--text2);">
            <i class="fa-solid fa-bell" style="font-size:1.1rem;"></i>
            <?php if ($notifCount > 0): ?><span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="font-size:0.6rem;padding:3px 6px;background:var(--primary);color:#fff;"><?php echo $notifCount; ?></span><?php endif; ?>
        </a>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" style="color:var(--text);">
                <img src="<?php echo $u['avatar'] ? htmlspecialchars($u['avatar']) : 'https://ui-avatars.com/api/?name=' . urlencode($u['first_name'] . '+' . $u['last_name']) . '&background=6366f1&color=fff&size=80'; ?>" class="topbar-avatar" alt="">
                <div class="d-none d-md-block text-start">
                    <div class="fw-semibold" style="font-size:0.8rem;color:var(--text);"><?php echo htmlspecialchars($u['first_name'] . ' ' . $u['last_name']); ?></div>
                    <div style="font-size:0.7rem;color:var(--text2);"><?php echo ucfirst($u['role']); ?></div>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius:14px;min-width:200px;background:var(--card);border:1px solid var(--border);">
                <li><a class="dropdown-item py-2" href="settings.php" style="color:var(--text);font-size:0.85rem;"><i class="fa-solid fa-user me-2" style="color:var(--primary);"></i>Profile</a></li>
                <li><a class="dropdown-item py-2" href="settings.php" style="color:var(--text);font-size:0.85rem;"><i class="fa-solid fa-gear me-2" style="color:var(--primary);"></i>Settings</a></li>
                <li><hr class="dropdown-divider" style="border-color:var(--border);"></li>
                <li><a class="dropdown-item py-2" href="/HRSuite/process/logout.php" style="color:#f87171;font-size:0.85rem;"><i class="fa-solid fa-right-from-bracket me-2"></i>Log Out</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="document.getElementById('sidebar').classList.remove('show');this.style.display='none'" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:1035;"></div>
<script>
document.getElementById('sidebarToggle').addEventListener('click',function(){var s=document.getElementById('sidebar'),b=document.getElementById('sidebarBackdrop');s.classList.toggle('show');b.style.display=s.classList.contains('show')?'block':'none';});
</script>
