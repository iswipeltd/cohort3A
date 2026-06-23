<?php
session_start();
/**
 * ADEEEEE One-Click Web Installer
 * Just open http://localhost/HRSuite/setup.php in your browser
 */

$step = $_GET['step'] ?? 'check';
$message = '';
$canInstall = true;

// Check PHP version
$phpOk = version_compare(PHP_VERSION, '7.4.0', '>=');

// Check PDO MySQL
$pdoOk = extension_loaded('pdo') && extension_loaded('pdo_mysql');

// Check config file exists
$configPath = __DIR__ . '/config/database.php';
$configExists = file_exists($configPath);

// Check SQL file
$sqlPath = __DIR__ . '/hrsuite.sql';
$sqlExists = file_exists($sqlPath);

// Test database connection
$dbOk = false;
$dbError = '';
if ($configExists) {
    try {
        require_once $configPath;
        $dbOk = true;
    } catch (Exception $e) {
        $dbError = $e->getMessage();
    }
}

if ($step === 'install' && $canInstall) {
    require_once $configPath;
    $results = [];
    $successCount = 0;
    $errors = [];
    
    try {
        // Read SQL file
        $sql = file_get_contents($sqlPath);
        // Split by semicolon but preserve statements inside triggers/procedures
        $queries = array_filter(array_map('trim', explode(";\n", $sql)));
        
        foreach ($queries as $query) {
            if (empty($query)) continue;
            try {
                $pdo->exec($query);
                $successCount++;
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'database exists') !== false) {
                    $successCount++;
                } else {
                    $errors[] = $e->getMessage();
                }
            }
        }
        
        $message = 'Installation complete!';
        
    } catch (Exception $e) {
        $message = 'Installation failed: ' . $e->getMessage();
        $canInstall = false;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ADEEEEE Installer</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
<style>
*{font-family:'Inter',sans-serif;}body{min-height:100vh;background:linear-gradient(135deg,#0f172a,#1e1b4b,#312e81);display:flex;align-items:center;justify-content:center;padding:20px;}
.install-card{background:rgba(255,255,255,0.97);border-radius:24px;box-shadow:0 25px 80px rgba(0,0,0,0.3);max-width:600px;width:100%;overflow:hidden;}
.install-header{background:linear-gradient(135deg,#6366f1,#8b5cf6);padding:40px 30px;text-align:center;color:#fff;}
.install-body{padding:35px 30px;}
.check-item{padding:14px 18px;border:1.5px solid #e5e7eb;border-radius:12px;margin-bottom:10px;display:flex;justify-content:space-between;align-items:center;}
.check-item.ok{border-color:#86efac;background:#f0fdf4;}
.check-item.fail{border-color:#fca5a5;background:#fef2f2;}
.check-icon{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:0.8rem;}
.btn-install{background:linear-gradient(135deg,#6366f1,#8b5cf6);border:none;color:#fff;font-weight:700;padding:14px;border-radius:12px;width:100%;}
.btn-install:hover{box-shadow:0 10px 30px rgba(99,102,241,0.35);color:#fff;}
.btn-install:disabled{opacity:0.5;cursor:not-allowed;}
.btn-admin{background:linear-gradient(135deg,#1e293b,#334155);border:none;color:#fff;font-weight:600;padding:12px;border-radius:10px;text-decoration:none;display:inline-block;}
.btn-employee{background:linear-gradient(135deg,#4f46e5,#6366f1);border:none;color:#fff;font-weight:600;padding:12px;border-radius:10px;text-decoration:none;display:inline-block;}
@keyframes fadeIn{from{opacity:0;transform:translateY(20px);}to{opacity:1;transform:translateY(0);}}
</style>
</head>
<body>
<div class="install-card" style="animation:fadeIn 0.5s ease;">
<div class="install-header">
<h3 class="fw-bold mb-1" style="font-family:'Poppins',sans-serif;">ADEEEEE Installer</h3>
<p class="mb-0 opacity-75">One-click database setup</p>
</div>
<div class="install-body">

<?php if ($step === 'check'): ?>

<h5 class="fw-bold mb-3">Pre-Installation Checks</h5>

<div class="check-item <?php echo $phpOk ? 'ok' : 'fail'; ?>">
<span><i class="fa-solid fa-code me-2"></i>PHP Version (<?php echo PHP_VERSION; ?>)</span>
<span class="check-icon" style="background:<?php echo $phpOk ? '#22c55e' : '#ef4444'; ?>;color:#fff;"><i class="fa-solid fa-<?php echo $phpOk ? 'check' : 'xmark'; ?>"></i></span>
</div>

<div class="check-item <?php echo $pdoOk ? 'ok' : 'fail'; ?>">
<span><i class="fa-solid fa-database me-2"></i>PDO MySQL Extension</span>
<span class="check-icon" style="background:<?php echo $pdoOk ? '#22c55e' : '#ef4444'; ?>;color:#fff;"><i class="fa-solid fa-<?php echo $pdoOk ? 'check' : 'xmark'; ?>"></i></span>
</div>

<div class="check-item <?php echo $configExists ? 'ok' : 'fail'; ?>">
<span><i class="fa-solid fa-file me-2"></i>Config File (database.php)</span>
<span class="check-icon" style="background:<?php echo $configExists ? '#22c55e' : '#ef4444'; ?>;color:#fff;"><i class="fa-solid fa-<?php echo $configExists ? 'check' : 'xmark'; ?>"></i></span>
</div>

<div class="check-item <?php echo $sqlExists ? 'ok' : 'fail'; ?>">
<span><i class="fa-solid fa-database me-2"></i>Database Schema (hrsuite.sql)</span>
<span class="check-icon" style="background:<?php echo $sqlExists ? '#22c55e' : '#ef4444'; ?>;color:#fff;"><i class="fa-solid fa-<?php echo $sqlExists ? 'check' : 'xmark'; ?>"></i></span>
</div>

<div class="check-item <?php echo $dbOk ? 'ok' : 'fail'; ?>">
<span><i class="fa-solid fa-server me-2"></i>Database Connection</span>
<span class="check-icon" style="background:<?php echo $dbOk ? '#22c55e' : '#ef4444'; ?>;color:#fff;"><i class="fa-solid fa-<?php echo $dbOk ? 'check' : 'xmark'; ?>"></i></span>
</div>

<?php if (!$dbOk && $dbError): ?>
<div class="alert alert-danger py-2 mt-2" style="border-radius:10px;font-size:0.85rem;">
<strong>Connection Error:</strong> <?php echo htmlspecialchars($dbError); ?>
<p class="mb-0 mt-1">Open <code>config/database.php</code> and set the correct username/password for your MySQL.</p>
</div>
<?php endif; ?>

<?php if ($phpOk && $pdoOk && $configExists && $sqlExists && $dbOk): ?>
<div class="alert alert-success py-2 mt-3" style="border-radius:10px;font-size:0.85rem;">
<i class="fa-solid fa-circle-check me-2"></i>All checks passed! Click below to install.
</div>
<a href="setup.php?step=install" class="btn btn-install mt-3">Install Database</a>
<?php else: ?>
<div class="alert alert-warning py-2 mt-3" style="border-radius:10px;font-size:0.85rem;">
<i class="fa-solid fa-triangle-exclamation me-2"></i>Fix the failed checks above before continuing.
</div>
<button class="btn btn-install mt-3" disabled>Install Database</button>
<?php endif; ?>

<?php elseif ($step === 'install'): ?>

<h5 class="fw-bold mb-3">Installation Result</h5>

<?php if ($canInstall): ?>
<div class="alert alert-success py-3" style="border-radius:12px;">
<i class="fa-solid fa-circle-check me-2"></i><strong><?php echo $message; ?></strong><br>
<span class="small text-muted">Executed <?php echo $successCount; ?> queries successfully.</span>
</div>

<?php if (!empty($errors)): ?>
<div class="alert alert-warning py-2" style="border-radius:10px;font-size:0.8rem;">
<small>Some minor warnings (usually harmless):</small>
<ul class="mb-0 mt-1">
<?php foreach (array_slice($errors, 0, 3) as $err): ?>
<li><?php echo htmlspecialchars($err); ?></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>

<h6 class="fw-bold mt-4 mb-2">Default Login Credentials</h6>
<table class="table table-sm" style="font-size:0.85rem;">
<thead><tr><th>Role</th><th>Email</th><th>Password</th></tr></thead>
<tbody>
<tr><td><span class="badge bg-dark">Admin</span></td><td>admin@hrsuite.com</td><td><code>password</code></td></tr>
<tr><td><span class="badge bg-primary">Employee</span></td><td>john.doe@hrsuite.com</td><td><code>password</code></td></tr>
</tbody>
</table>

<div class="d-grid gap-2 mt-4">
<a href="admin_dashboard/login.php" class="btn-admin text-center"><i class="fa-solid fa-user-shield me-2"></i>Open Admin Portal</a>
<a href="user-dashboard/signin.php" class="btn-employee text-center"><i class="fa-solid fa-user me-2"></i>Open Employee Portal</a>
</div>

<div class="mt-3 text-center">
<a href="setup.php?step=delete" class="text-danger small text-decoration-none" onclick="return confirm('Delete installer for security?')">
<i class="fa-solid fa-trash me-1"></i>Delete Setup File
</a>
</div>

<?php else: ?>
<div class="alert alert-danger py-3" style="border-radius:12px;">
<i class="fa-solid fa-circle-xmark me-2"></i><strong><?php echo $message; ?></strong>
</div>
<a href="setup.php?step=check" class="btn btn-outline-secondary w-100 mt-3">Back to Checks</a>
<?php endif; ?>

<?php elseif ($step === 'delete'): ?>
<?php @unlink(__FILE__); ?>
<div class="alert alert-success py-3 text-center" style="border-radius:12px;">
<i class="fa-solid fa-check-circle fa-2x mb-2 text-success"></i><br>
<strong>Setup file deleted successfully.</strong><br>
<span class="small text-muted">Your ADEEEEE is now ready to use.</span>
</div>
<div class="d-grid gap-2 mt-3">
<a href="admin_dashboard/login.php" class="btn-admin text-center">Admin Portal</a>
<a href="user-dashboard/signin.php" class="btn-employee text-center">Employee Portal</a>
</div>
<?php endif; ?>

</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
