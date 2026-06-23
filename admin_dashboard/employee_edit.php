<?php require_once __DIR__.'/../config/session.php'; require_admin(); require_once __DIR__.'/../config/nigerian_banks.php'; $user=current_user(); $id=(int)($_GET['id']??0); $stmt=$pdo->prepare("SELECT e.*,u.first_name,u.last_name,u.email,u.phone FROM employees e JOIN users u ON e.user_id=u.id WHERE e.id=?"); $stmt->execute([$id]); $emp=$stmt->fetch(); if(!$emp){header('Location: employees.php');exit;} $bankCategories=getBankCategories(); ?>
<?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php
// Warn if bank columns are missing (required by Novac payment)
$empCols = $pdo->query("SHOW COLUMNS FROM employees")->fetchAll(PDO::FETCH_COLUMN, 0);
$missingBankCols = [];
if (!in_array('bank_name', $empCols)) $missingBankCols[] = 'bank_name';
if (!in_array('bank_account', $empCols)) $missingBankCols[] = 'bank_account';
if (!in_array('bank_code', $empCols)) $missingBankCols[] = 'bank_code';
if (!empty($missingBankCols)): ?>
<div class="alert alert-warning d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;">
    <i class="fa-solid fa-triangle-exclamation me-2"></i>
    <div>Your database is missing columns required for Novac payments: <code><?php echo implode(', ', $missingBankCols); ?></code>. <a href="/HRSuite/setup_database_fix.php" class="alert-link">Click here to fix the database</a>, then re-save this employee's bank details.</div>
</div>
<?php endif; ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div class="d-flex justify-content-between"><div><h1 class="page-title">Edit Employee</h1><p class="page-subtitle mb-0"><?php echo htmlspecialchars($emp['first_name'].' '.$emp['last_name']); ?></p></div><a href="employees.php" class="btn btn-outline-mod"><i class="fa-solid fa-arrow-left me-2"></i>Back</a></div></div>
<div class="px-4 pb-4">
<div class="card-modern"><div class="card-body-modern">
<form action="../process/edit_employee.php" method="POST"><div class="row g-3">
<input type="hidden" name="id" value="<?php echo $emp['id']; ?>">
<input type="hidden" name="department_id" value="<?php echo $emp['department_id']; ?>">
<input type="hidden" name="role_id" value="<?php echo $emp['role_id']; ?>">
<input type="hidden" name="manager_id" value="<?php echo $emp['manager_id']; ?>">
<input type="hidden" name="employment_type" value="<?php echo htmlspecialchars($emp['employment_type']??'full-time'); ?>">
<div class="col-md-6"><label class="form-label-mod">First Name</label><input type="text" value="<?php echo htmlspecialchars($emp['first_name']); ?>" class="form-control form-control-mod" disabled></div>
<div class="col-md-6"><label class="form-label-mod">Last Name</label><input type="text" value="<?php echo htmlspecialchars($emp['last_name']); ?>" class="form-control form-control-mod" disabled></div>
<div class="col-md-6"><label class="form-label-mod">Phone</label><input type="tel" name="phone" value="<?php echo htmlspecialchars($emp['phone']??''); ?>" class="form-control form-control-mod"></div>
<div class="col-md-6"><label class="form-label-mod">Email</label><input type="email" value="<?php echo htmlspecialchars($emp['email']); ?>" class="form-control form-control-mod" disabled></div>
<div class="col-md-6"><label class="form-label-mod">Salary</label><input type="number" name="salary" step="0.01" value="<?php echo $emp['salary']; ?>" class="form-control form-control-mod"></div>
<div class="col-md-6"><label class="form-label-mod">Status</label><select name="status" class="form-select"><option value="active" <?php echo ($emp['status']??'')=='active'?'selected':''; ?>>Active</option><option value="inactive" <?php echo ($emp['status']??'')=='inactive'?'selected':''; ?>>Inactive</option><option value="on_leave" <?php echo ($emp['status']??'')=='on_leave'?'selected':''; ?>>On Leave</option><option value="probation" <?php echo ($emp['status']??'')=='probation'?'selected':''; ?>>Probation</option></select></div>

<div class="col-md-6"><label class="form-label-mod">Select Bank <span class="text-danger">*</span></label>
<select id="bank_select" name="bank_code" class="form-select" required onchange="updateBankName(this)">
<option value="">-- Select Nigerian Bank --</option>
<?php foreach($bankCategories as $cat => $banks): ?>
<optgroup label="<?php echo htmlspecialchars($cat); ?>">
<?php foreach($nigerian_banks as $b): if(in_array($b['name'], $banks)): $selected=(string)($emp['bank_code']??'')===(string)$b['code']?'selected':''; ?>
<option value="<?php echo htmlspecialchars($b['code']); ?>" data-name="<?php echo htmlspecialchars($b['name']); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($b['name'] . ' (' . $b['code'] . ')'); ?></option>
<?php endif; endforeach; ?>
</optgroup>
<?php endforeach; ?>
</select>
</div>

<div class="col-md-6"><label class="form-label-mod">Bank Account Number <span class="text-danger">*</span></label><input type="text" name="bank_account" value="<?php echo htmlspecialchars($emp['bank_account']??''); ?>" class="form-control form-control-mod" placeholder="e.g. 1234567890" required></div>

<!-- Hidden bank_name populated by JS from dropdown -->
<input type="hidden" name="bank_name" id="bank_name_hidden" value="<?php echo htmlspecialchars($emp['bank_name']??''); ?>">

<input type="hidden" name="address" value="<?php echo htmlspecialchars($emp['address']??''); ?>">
<input type="hidden" name="city" value="<?php echo htmlspecialchars($emp['city']??''); ?>">
<input type="hidden" name="country" value="<?php echo htmlspecialchars($emp['country']??''); ?>">
</div>
<div class="mt-3"><button type="submit" class="btn btn-primary-mod">Save Changes</button> <a href="employees.php" class="btn btn-outline-mod">Cancel</a></div>
</form>
</div></div></div>

<script>
function updateBankName(sel) {
    var opt = sel.options[sel.selectedIndex];
    var name = opt.getAttribute('data-name') || '';
    document.getElementById('bank_name_hidden').value = name;
}
// On page load, if a bank is already selected, populate bank_name
document.addEventListener('DOMContentLoaded', function() {
    var sel = document.getElementById('bank_select');
    if (sel && sel.selectedIndex > 0) {
        updateBankName(sel);
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
