<nav class="topbar">
<div class="d-flex align-items-center gap-3">
<button class="btn btn-sm d-lg-none" style="background:var(--sidebar-hover);border:none;border-radius:10px;padding:6px 10px;color:var(--muted);" onclick="document.getElementById('sidebar').classList.toggle('show')"><i class="fa-solid fa-bars"></i></button>
<form action="search.php" method="GET" class="d-none d-md-flex align-items-center" style="background:var(--bg);border:1px solid var(--border);border-radius:12px;padding:8px 14px;width:320px;gap:8px;margin:0;">
<i class="fa-solid fa-magnifying-glass" style="font-size:0.8rem;color:var(--muted);"></i>
<input type="text" name="q" placeholder="Search..." style="border:none;background:transparent;outline:none;font-size:0.82rem;width:100%;color:var(--text);">
</form>
</div>
<div class="d-flex align-items-center gap-3">
<a href="my_notifications.php" class="position-relative text-decoration-none" style="padding:8px;color:var(--muted);">
<i class="fa-solid fa-bell" style="font-size:1.1rem;"></i>
<?php $nc = unread_notifications_count($_SESSION['user_id']??0); if($nc>0): ?>
<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill" style="font-size:0.6rem;padding:3px 6px;background:var(--primary);color:#fff;"><?php echo $nc; ?></span>
<?php endif; ?>
</a>
<div class="dropdown">
<a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" style="color:var(--text);">
<img src="<?php echo !empty($user['avatar'])?htmlspecialchars($user['avatar']):'https://ui-avatars.com/api/?name='.urlencode(($user['first_name']??'U').'+'.($user['last_name']??'')).'&background=2563eb&color=fff&size=80'; ?>" class="topbar-avatar" alt="">
<div class="d-none d-md-block text-start"><div class="fw-semibold" style="font-size:0.8rem;color:var(--text);"><?php echo htmlspecialchars(($user['first_name']??'').' '.($user['last_name']??'')); ?></div><div style="font-size:0.7rem;color:var(--muted);">Employee</div></div>
</a>
<ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius:14px;min-width:200px;background:var(--card);border:1px solid var(--border);">
<li><a class="dropdown-item py-2" href="my_profile.php" style="color:var(--text);font-size:0.85rem;"><i class="fa-solid fa-user me-2" style="color:var(--primary);"></i>Profile</a></li>
<li><a class="dropdown-item py-2" href="two_factor.php" style="color:var(--text);font-size:0.85rem;"><i class="fa-solid fa-shield-halved me-2" style="color:var(--primary);"></i>Security</a></li>
<li><hr class="dropdown-divider" style="border-color:var(--border);"></li>
<li><a class="dropdown-item py-2" href="/HRSuite/process/logout.php" style="color:var(--danger);font-size:0.85rem;"><i class="fa-solid fa-right-from-bracket me-2"></i>Log Out</a></li>
</ul>
</div>
</div>
</nav>
