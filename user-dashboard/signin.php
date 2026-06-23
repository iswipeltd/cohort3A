<?php
session_start();
$err = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Sign In | ADEEEEE Employee</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        *{font-family:'Inter',sans-serif;}
        body{min-height:100vh;background:#f8fafc;display:flex;align-items:center;justify-content:center;padding:20px;}
        .login-card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;max-width:420px;width:100%;box-shadow:0 4px 20px rgba(0,0,0,0.06);}
        .login-header{background:#fff;padding:35px 30px 25px;text-align:center;color:#fff;}
        .adeeeee-logo{display:inline-flex;align-items:center;gap:10px;flex-direction:column;}
        .adeeeee-logo-mark{width:64px;height:64px;position:relative;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;}
        .adeeeee-logo-mark svg{width:100%;height:100%;filter:drop-shadow(0 0 12px rgba(245,158,11,0.5));}
        .adeeeee-logo-text{font-family:'Poppins',sans-serif;font-weight:800;font-size:1.8rem;letter-spacing:1px;background:linear-gradient(135deg,#f59e0b 0%,#fbbf24 30%,#f59e0b 50%,#fbbf24 70%,#f59e0b 100%);background-size:200% auto;-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;animation:shine 3s linear infinite;}
        @keyframes shine{to{background-position:200% center;}}
        .login-header p{font-size:0.85rem;opacity:0.85;margin:0;letter-spacing:2px;text-transform:uppercase;color:#64748b;}
        .login-body{padding:30px;}
        .form-control-login{border:1.5px solid #e2e8f0;border-radius:10px;padding:12px 14px;font-size:0.9rem;transition:0.2s;}
        .form-control-login:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(37,99,235,0.12);outline:none;}
        .btn-login{background:#2563eb;border:none;color:#fff;font-weight:700;padding:13px;border-radius:10px;width:100%;transition:0.2s;font-size:0.9rem;}
        .btn-login:hover{background:#1d4ed8;color:#fff;}
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
        <p>Employee Portal</p>
    </div>
    <div class="login-body">
        <h5 class="fw-bold mb-1">Welcome back</h5>
        <p class="text-muted mb-4" style="font-size:0.85rem;">Sign in to your employee account</p>
        <?php if ($err): ?><div class="alert alert-danger d-flex align-items-center py-2" style="font-size:0.85rem;border-radius:10px;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
        <form action="/HRSuite/process/login.php" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border-radius:10px 0 0 10px;"><i class="fa-solid fa-envelope text-muted" style="font-size:0.8rem;"></i></span>
                    <input type="email" name="email" class="form-control form-control-login border-start-0" style="border-radius:0 10px 10px 0;" placeholder="john.doe@hrsuite.com" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-semibold text-muted">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0" style="border-radius:10px 0 0 10px;"><i class="fa-solid fa-lock text-muted" style="font-size:0.8rem;"></i></span>
                    <input type="password" name="password" class="form-control form-control-login border-start-0" style="border-radius:0 10px 10px 0;" placeholder="Enter password" required>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember">
                    <label class="form-check-label small text-muted" for="remember">Remember me</label>
                </div>
                <a href="/HRSuite/user-dashboard/forgot_password.php" class="small fw-semibold text-decoration-none" style="color:#2563eb;">Forgot password?</a>
            </div>
            <input type="hidden" name="redirect" value="/HRSuite/user-dashboard/index.php">
            <button type="submit" class="btn btn-login">Sign In</button>
        </form>
        <p class="text-center text-muted small mt-4 mb-0">Don't have an account? <a href="/HRSuite/user-dashboard/signup.php" class="fw-semibold text-decoration-none" style="color:#2563eb;">Create Account</a></p>
        <p class="text-center text-muted small mt-2 mb-0">Admin login? <a href="/HRSuite/admin_dashboard/login.php" class="fw-semibold text-decoration-none" style="color:#2563eb;">Go to Admin Portal</a></p>
    </div>
</div>
</body>
</html>
