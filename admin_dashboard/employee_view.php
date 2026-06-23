<?php require_once __DIR__.'/../config/session.php'; require_admin(); $user=current_user(); $id=(int)($_GET['id']??0); $stmt=$pdo->prepare("SELECT e.*,u.first_name,u.last_name,u.email,u.phone,u.avatar,d.name as dept,r.name as role_name,CONCAT(mu.first_name,' ',mu.last_name) as manager FROM employees e JOIN users u ON e.user_id=u.id LEFT JOIN departments d ON e.department_id=d.id LEFT JOIN roles r ON e.role_id=r.id LEFT JOIN employees me ON e.manager_id=me.id LEFT JOIN users mu ON me.user_id=mu.id WHERE e.id=?"); $stmt->execute([$id]); $emp=$stmt->fetch(); if(!$emp){header('Location: employees.php');exit;} ?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div class="d-flex justify-content-between"><div><h1 class="page-title">Employee Profile</h1><p class="page-subtitle mb-0"><?php echo htmlspecialchars($emp['first_name'].' '.$emp['last_name']); ?></p></div><a href="employees.php" class="btn btn-outline-mod"><i class="fa-solid fa-arrow-left me-2"></i>Back</a></div></div>
<div class="px-4 pb-4"><div class="row g-3">
<div class="col-lg-4"><div class="card-modern text-center"><div class="card-body-modern">
<img src="<?php echo $emp['avatar']?htmlspecialchars($emp['avatar']):'https://ui-avatars.com/api/?name='.urlencode($emp['first_name'].'+'.$emp['last_name']).'&background=6366f1&color=fff&size=200'; ?>" style="width:110px;height:110px;border-radius:50%;object-fit:cover;border:4px solid var(--primary);margin-bottom:14px;" alt="">
<h4 class="fw-bold mb-1"><?php echo htmlspecialchars($emp['first_name'].' '.$emp['last_name']); ?></h4>
<p class="text-muted small mb-2"><?php echo htmlspecialchars($emp['employee_code']); ?> &middot; <?php echo htmlspecialchars($emp['role_name']??'-'); ?></p>
<span class="status-badge <?php echo $emp['status']=='active'?'status-active':($emp['status']=='on_leave'?'status-pending':($emp['status']=='probation'?'status-probation':'status-inactive')); ?>"><?php echo ucfirst($emp['status']); ?></span>
</div></div></div>
<div class="col-lg-8"><div class="card-modern mb-3"><div class="card-header-modern"><h6 class="fw-bold mb-0">Employment Details</h6></div><div class="card-body-modern"><div class="row g-3">
<div class="col-md-6"><label class="form-label-mod">Department</label><p class="mb-0 fw-semibold"><?php echo htmlspecialchars($emp['dept']??'-'); ?></p></div>
<div class="col-md-6"><label class="form-label-mod">Manager</label><p class="mb-0 fw-semibold"><?php echo htmlspecialchars($emp['manager']??'-'); ?></p></div>
<div class="col-md-6"><label class="form-label-mod">Employment Type</label><p class="mb-0 fw-semibold"><?php echo ucfirst($emp['employment_type']??'-'); ?></p></div>
<div class="col-md-6"><label class="form-label-mod">Start Date</label><p class="mb-0 fw-semibold"><?php echo $emp['start_date']?date('M j, Y',strtotime($emp['start_date'])):'-'; ?></p></div>
<div class="col-md-6"><label class="form-label-mod">Salary</label><p class="mb-0 fw-semibold"><?php echo format_currency($emp['salary']); ?></p></div>
<div class="col-md-6"><label class="form-label-mod">Email</label><p class="mb-0 fw-semibold"><?php echo htmlspecialchars($emp['email']); ?></p></div>
<div class="col-md-6"><label class="form-label-mod">Phone</label><p class="mb-0 fw-semibold"><?php echo htmlspecialchars($emp['phone']??'-'); ?></p></div>
<div class="col-md-6"><label class="form-label-mod">Address</label><p class="mb-0 fw-semibold"><?php echo htmlspecialchars($emp['address']??'-'); ?></p></div>
</div></div></div></div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
