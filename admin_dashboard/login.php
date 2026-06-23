<?php
session_start();
require_once __DIR__ . '/../config/database.php';
$err = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Sign In | ADEEEEE Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        *{font-family:'Inter',sans-serif;}
        body{min-height:100vh;background:#0f172a;display:flex;align-items:center;justify-content:center;padding:20px;}
        .login-card{background:#1e293b;border:1px solid #334155;border-radius:16px;overflow:hidden;max-width:420px;width:100%;}
        .login-header{background:#0f172a;padding:35px 30px 25px;text-align:center;color:#fff;}
        .adeeeee-logo{display:inline-flex;align-items:center;gap:10px;flex-direction:column;}
        .adeeeee-logo-mark{width:64px;height:64px;position:relative;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;}
        .adeeeee-logo-mark svg{width:100%;height:100%;filter:drop-shadow(0 0 12px rgba(245,158,11,0.5));}
        .adeeeee-logo-text{font-family:'Poppins',sans-serif;font-weight:800;font-size:1.8rem;letter-spacing:1px;background:linear-gradient(135deg,#f59e0b 0%,#fbbf24 30%,#fff 50%,#fbbf24 70%,#f59e0b 100%);background-size:200% auto;-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;animation:shine 3s linear infinite;}
        @keyframes shine{to{background-position:200% center;}}
        .login-header p{font-size:0.85rem;opacity:0.85;margin:0;letter-spacing:2px;text-transform:uppercase;}
        .login-body{padding:30px;}
        .form-control-login{border:1.5px solid #334155;border-radius:10px;padding:12px 14px;font-size:0.9rem;background:#0f172a;color:#f1f5f9;transition:0.2s;}
        .form-control-login:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,0.15);background:#0f172a;color:#f1f5f9;outline:none;}
        .form-control-login::placeholder{color:#64748b;}
        .btn-login{background:#6366f1;border:none;color:#fff;font-weight:700;padding:13px;border-radius:10px;width:100%;transition:0.2s;font-size:0.9rem;}
        .btn-login:hover{background:#4f46e5;color:#fff;}
        .input-group-text{background:#0f172a;border:1.5px solid #334155;border-right:none;color:#64748b;border-radius:10px 0 0 10px;padding:12px 14px;}
        .form-control-login.border-start-0{border-left:none;}
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-header">
        <div class="adeeeee-logo">
            <div class="adeeeee-logo-mark">
                <svg viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="ag" x1="0%" y1="0%" x2="100%" y2="100%">
                            <stop offset="0%" style="stop-color:#f59e0b;stop-opacity:1"/>
                            <stop offset="50%" style="stop-color:#fbbf24;stop-opacity:1"/>
                            <stop offset="100%" style="stop-color:#f59e0b;stop-opacity:1"/>
                        </linearGradient>
                        <filter id="aglow">
                            <feGaussianBlur stdDeviation="2" result="b"/>
                            <feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge>
                        </filter>
                    </defs>
                    <polygon points="50,5 90,25 90,75 50,95 10,75 10,25" fill="url(#ag)" stroke="#fbbf24" stroke-width="1.5" filter="url(#aglow)"/>
                    <text x="50" y="62" text-anchor="middle" font-family="Poppins,sans-serif" font-weight="800" font-size="42" fill="#fff" style="text-shadow:0 2px 8px rgba(0,0,0,0.3);">A</text>
                </svg>
            </div>
            <div class="adeeeee-logo-text">ADEEEEE</div>
        </div>
        <p>Admin Portal</p>
    </div>
    <div class="login-body">
        <h5 class="fw-bold mb-1" style="color:#f8fafc;">Welcome back</h5>
        <p class="mb-4" style="color:#94a3b8;font-size:0.85rem;">Sign in to your admin account</p>
        <?php if ($err): ?><div class="alert alert-danger d-flex align-items-center py-2" style="font-size:0.85rem;border-radius:10px;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
        <form action="/HRSuite/process/login.php" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-semibold" style="color:#94a3b8;">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-envelope" style="font-size:0.8rem;"></i></span>
                    <input type="email" name="email" class="form-control form-control-login border-start-0" placeholder="admin@hrsuite.com" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold" style="color:#94a3b8;">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-lock" style="font-size:0.8rem;"></i></span>
                    <input type="password" name="password" class="form-control form-control-login border-start-0" placeholder="Enter password" required>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember">
                    <label class="form-check-label small" style="color:#94a3b8;" for="remember">Remember me</label>
                </div>
                <a href="/HRSuite/user-dashboard/forgot_password.php" class="small fw-semibold text-decoration-none" style="color:#6366f1;">Forgot password?</a>
            </div>
            <button type="submit" class="btn btn-login">Sign In</button>
        </form>
        <p class="text-center small mt-4 mb-0" style="color:#64748b;">Employee login? <a href="/HRSuite/user-dashboard/signin.php" class="fw-semibold text-decoration-none" style="color:#6366f1;">Sign in here</a></p>
    </div>
</div>
</body>
</html>
