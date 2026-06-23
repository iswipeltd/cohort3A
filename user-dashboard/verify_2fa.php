<?php
session_start();

// Must have pending 2FA user
if (empty($_SESSION['pending_2fa_user_id'])) {
    header('Location: signin.php');
    exit;
}

require_once __DIR__ . '/../config/totp.php';

$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    
    require_once __DIR__ . '/../config/database.php';
    $stmt = $pdo->prepare("SELECT two_factor_secret FROM users WHERE id = ? AND two_factor_enabled = 1");
    $stmt->execute([$_SESSION['pending_2fa_user_id']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        $err = 'Two-factor setup is incomplete. Please contact HR to reset your 2FA.';
    } elseif (empty($user['two_factor_secret'])) {
        $err = 'Two-factor secret is missing. Please contact HR to reset your 2FA.';
    } elseif (TOTP::verify($user['two_factor_secret'], $code)) {
        // Check if admin needs onboarding
        $uStmt = $pdo->prepare("SELECT avatar FROM users WHERE id = ?");
        $uStmt->execute([$_SESSION['pending_2fa_user_id']]);
        $uData = $uStmt->fetch();
        
        $isAdmin = in_array($_SESSION['pending_2fa_role'], ['admin','hr']);
        if ($isAdmin && empty($uData['avatar'])) {
            $_SESSION['pending_onboarding_user_id'] = $_SESSION['pending_2fa_user_id'];
            unset($_SESSION['pending_2fa_user_id'], $_SESSION['pending_2fa_email'], $_SESSION['pending_2fa_role'], $_SESSION['pending_2fa_name'], $_SESSION['pending_2fa_redirect']);
            header('Location: /HRSuite/admin_dashboard/onboarding.php');
            exit;
        }
        
        // Complete login
        $_SESSION['user_id'] = $_SESSION['pending_2fa_user_id'];
        $_SESSION['email'] = $_SESSION['pending_2fa_email'];
        $_SESSION['role'] = $_SESSION['pending_2fa_role'];
        $_SESSION['full_name'] = $_SESSION['pending_2fa_name'];
        $_SESSION['onboarding_completed'] = true;
        
        $redirect = $_SESSION['pending_2fa_redirect'] ?? '';
        unset($_SESSION['pending_2fa_user_id'], $_SESSION['pending_2fa_email'], $_SESSION['pending_2fa_role'], $_SESSION['pending_2fa_name'], $_SESSION['pending_2fa_redirect']);
        
        $upd = $pdo->prepare("UPDATE users SET last_login = NOW(), last_login_ip = ? WHERE id = ?");
        $upd->execute([$_SERVER['REMOTE_ADDR'] ?? '0.0.0.0', $_SESSION['user_id']]);
        
        log_activity($_SESSION['user_id'], 'LOGIN', 'Auth', $_SESSION['user_id'], 'Successful login with 2FA');
        
        if ($redirect) {
            header('Location: ' . $redirect);
            exit;
        }
        if ($isAdmin) {
            header('Location: /HRSuite/admin_dashboard/welcome.php');
        } else {
            header('Location: /HRSuite/user-dashboard/index.php');
        }
        exit;
    } else {
        $err = 'Invalid verification code. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Verify Two-Factor - ADEEEEE</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body{min-height:100vh;background:linear-gradient(135deg,#f0f9ff,#e0e7ff,#f5f3ff);display:flex;align-items:center;justify-content:center;padding:20px;font-family:'Inter',sans-serif;}
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
    </div>
        <div class="login-body">
            <h5 class="fw-bold mb-1">Enter 6-digit Code</h5>
            <p class="text-muted small mb-4">Open your authenticator app and enter the current code</p>
            <?php if($err):?><div class="alert alert-danger py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label small fw-semibold text-muted">Verification Code</label>
                    <input type="text" name="code" class="form-control form-control-login" maxlength="6" pattern="[0-9]{6}" placeholder="123456" required autofocus>
                </div>
                <button type="submit" class="btn btn-login">Verify <i class="fa-solid fa-check ms-2"></i></button>
            </form>
            <div class="text-center mt-3">
                <a href="signin.php" class="text-muted small">Back to Sign In</a>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
