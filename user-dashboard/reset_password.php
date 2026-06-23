<?php
session_start();
require_once __DIR__ . '/../config/database.php';

$token = $_GET['token'] ?? '';
$email = strtolower(trim($_GET['email'] ?? ''));
$err = $_SESSION['error'] ?? '';
unset($_SESSION['error']);

$valid = false;
$userId = null;

if ($token && $email) {
    $stmt = $pdo->prepare("SELECT pr.*, u.id as user_id FROM password_resets pr JOIN users u ON pr.user_id = u.id WHERE u.email = ? AND pr.used = 0 AND pr.expires_at > NOW() ORDER BY pr.created_at DESC LIMIT 1");
    $stmt->execute([$email]);
    $row = $stmt->fetch();
    if ($row && password_verify($token, $row['token'])) {
        $valid = true;
        $userId = $row['user_id'];
    }
}
?>
<!DOCTYPE html><html lang="en">
<head><meta charset="utf-8"><title>Reset Password | ADEEEEE</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
*{font-family:'Inter',sans-serif;}
body{min-height:100vh;background:linear-gradient(135deg,#f0f9ff,#e0e7ff,#f5f3ff);display:flex;align-items:center;justify-content:center;padding:20px;}
.login-card{background:rgba(255,255,255,0.98);backdrop-filter:blur(20px);border-radius:24px;box-shadow:0 25px 80px rgba(0,0,0,0.08);overflow:hidden;max-width:420px;width:100%;animation:fadeIn 0.5s ease;}
.login-header{background:linear-gradient(135deg,#4f46e5,#6366f1);padding:40px 30px 30px;text-align:center;color:#fff;position:relative;}
.login-header i{position:absolute;bottom:-14px;left:50%;transform:translateX(-50%);font-size:1.6rem;color:#4f46e5;z-index:2;background:#fff;width:48px;height:48px;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,0.1);}
.login-body{padding:40px 30px 30px;}
.form-control-login{border:1.5px solid #e5e7eb;border-radius:12px;padding:12px 16px;font-size:0.9rem;transition:0.2s;}
.form-control-login:focus{border-color:#4f46e5;box-shadow:0 0 0 4px rgba(79,70,229,0.1);}
.btn-login{background:linear-gradient(135deg,#4f46e5,#6366f1);border:none;color:#fff;font-weight:700;padding:14px;border-radius:12px;width:100%;transition:0.2s;}
.btn-login:hover{transform:translateY(-1px);box-shadow:0 10px 30px rgba(79,70,229,0.35);color:#fff;}
@keyframes fadeIn{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
.adeeeee-logo{display:inline-flex;align-items:center;gap:10px;flex-direction:column;}
        .adeeeee-logo-mark{width:64px;height:64px;position:relative;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;}
        .adeeeee-logo-mark svg{width:100%;height:100%;filter:drop-shadow(0 0 12px rgba(245,158,11,0.5));}
        .adeeeee-logo-text{font-family:'Poppins',sans-serif;font-weight:800;font-size:1.8rem;letter-spacing:1px;background:linear-gradient(135deg,#f59e0b 0%,#fbbf24 30%,#f59e0b 50%,#fbbf24 70%,#f59e0b 100%);background-size:200% auto;-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;animation:shine 3s linear infinite;}
        @keyframes shine{to{background-position:200% center;}}
    </style></head><body>
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
    </div>
<div class="login-body">
<?php if(!$valid):?>
<h5 class="fw-bold mb-1 text-danger">Invalid Link</h5>
<p class="text-muted small mb-4">This password reset link is expired or invalid.</p>
<a href="forgot_password.php" class="btn btn-login">Request New Link</a>
<?php else:?>
<h5 class="fw-bold mb-1">Create new password</h5>
<p class="text-muted small mb-4">Enter a new secure password below</p>
<?php if($err):?><div class="alert alert-danger py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<form action="/HRSuite/process/reset_password.php" method="POST">
<input type="hidden" name="token" value="<?php echo htmlspecialchars($token);?>">
<input type="hidden" name="email" value="<?php echo htmlspecialchars($email);?>">
<div class="mb-3"><label class="form-label small fw-semibold text-muted">New Password</label><input type="password" name="new_password" class="form-control form-control-login" placeholder="Min 8 characters" required></div>
<div class="mb-4"><label class="form-label small fw-semibold text-muted">Confirm Password</label><input type="password" name="confirm_password" class="form-control form-control-login" placeholder="Repeat password" required></div>
<button type="submit" class="btn btn-login">Reset Password <i class="fa-solid fa-check ms-2"></i></button>
</form>
<?php endif;?>
</div>
</div>
</body></html>
