<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
require_once __DIR__ . '/../config/nigerian_banks.php';
$user = current_user();

$depts = $pdo->query("SELECT id, name FROM departments ORDER BY name")->fetchAll();
$roles = $pdo->query("SELECT id, name FROM roles ORDER BY name")->fetchAll();
$empUsers = $pdo->query("SELECT e.id, CONCAT(u.first_name,' ',u.last_name) as name FROM employees e JOIN users u ON e.user_id=u.id WHERE e.status='active' ORDER BY u.last_name")->fetchAll();
$bankCategories = getBankCategories();

$err = $_SESSION['error'] ?? ''; $success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<?php include 'includes/head.php'; ?>
<body>
<?php include 'includes/sidebar.php'; ?>
<div class="main-content">
<?php include 'includes/navbar.php'; ?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="page-title">Add New Employee</h1>
            <p class="page-subtitle mb-0">Create a new employee record and user account</p>
        </div>
        <a href="employees.php" class="btn btn-outline-mod"><i class="fa-solid fa-arrow-left me-2"></i>Back</a>
    </div>
</div>

<div class="px-4 pb-4">
    <?php if($err): ?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err); ?></div><?php endif; ?>
    <?php if($success): ?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

    <form action="/HRSuite/process/add_employee.php" method="POST" enctype="multipart/form-data">
        <div class="row g-3">
            <div class="col-lg-8">
                <div class="card-modern mb-3">
                    <div class="card-header-modern"><h6 class="fw-bold mb-0"><i class="fa-solid fa-user me-2 text-primary"></i>Personal Information</h6></div>
                    <div class="card-body-modern">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label-mod">First Name *</label><input type="text" name="first_name" class="form-control form-control-mod" required></div>
                            <div class="col-md-6"><label class="form-label-mod">Last Name *</label><input type="text" name="last_name" class="form-control form-control-mod" required></div>
                            <div class="col-md-6"><label class="form-label-mod">Email *</label><input type="email" name="email" class="form-control form-control-mod" required></div>
                            <div class="col-md-6"><label class="form-label-mod">Phone</label><input type="tel" name="phone" class="form-control form-control-mod"></div>
                            <div class="col-md-6"><label class="form-label-mod">Date of Birth</label><input type="date" name="date_of_birth" class="form-control form-control-mod"></div>
                            <div class="col-md-6"><label class="form-label-mod">Gender</label><select name="gender" class="form-select"><option value="">Select</option><option value="male">Male</option><option value="female">Female</option><option value="other">Other</option></select></div>
                        </div>
                    </div>
                </div>

                <div class="card-modern mb-3">
                    <div class="card-header-modern"><h6 class="fw-bold mb-0"><i class="fa-solid fa-briefcase me-2 text-primary"></i>Employment Details</h6></div>
                    <div class="card-body-modern">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label-mod">Department *</label><select name="department_id" class="form-select" required><option value="">Select</option><?php foreach($depts as $d): ?><option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['name']); ?></option><?php endforeach; ?></select></div>
                            <div class="col-md-6"><label class="form-label-mod">Role *</label><select name="role_id" class="form-select" required><option value="">Select</option><?php foreach($roles as $r): ?><option value="<?php echo $r['id']; ?>"><?php echo htmlspecialchars($r['name']); ?></option><?php endforeach; ?></select></div>
                            <div class="col-md-6"><label class="form-label-mod">Employment Type</label><select name="employment_type" class="form-select"><option value="full-time">Full Time</option><option value="part-time">Part Time</option><option value="contract">Contract</option><option value="intern">Intern</option></select></div>
                            <div class="col-md-6"><label class="form-label-mod">Start Date *</label><input type="date" name="start_date" class="form-control form-control-mod" required></div>
                            <div class="col-md-6"><label class="form-label-mod">Manager</label><select name="manager_id" class="form-select"><option value="">None</option><?php foreach($empUsers as $eu): ?><option value="<?php echo $eu['id']; ?>"><?php echo htmlspecialchars($eu['name']); ?></option><?php endforeach; ?></select></div>
                            <div class="col-md-6"><label class="form-label-mod">Salary</label><input type="number" name="salary" step="0.01" class="form-control form-control-mod"></div>
                            <div class="col-md-6"><label class="form-label-mod">City</label><input type="text" name="city" class="form-control form-control-mod"></div>
                            <div class="col-md-6"><label class="form-label-mod">Country</label><input type="text" name="country" class="form-control form-control-mod"></div>
                            <div class="col-md-12"><label class="form-label-mod">Address</label><textarea name="address" class="form-control form-control-mod" rows="2"></textarea></div>
                        </div>
                    </div>
                </div>

                <div class="card-modern mb-3">
                    <div class="card-header-modern"><h6 class="fw-bold mb-0"><i class="fa-solid fa-heart-pulse me-2 text-primary"></i>Emergency Contact</h6></div>
                    <div class="card-body-modern">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label-mod">Contact Name</label><input type="text" name="emergency_name" class="form-control form-control-mod"></div>
                            <div class="col-md-6"><label class="form-label-mod">Contact Phone</label><input type="tel" name="emergency_phone" class="form-control form-control-mod"></div>
                        </div>
                    </div>
                </div>

                <div class="card-modern mb-3">
                    <div class="card-header-modern"><h6 class="fw-bold mb-0"><i class="fa-solid fa-building-columns me-2 text-primary"></i>Bank & Tax</h6></div>
                    <div class="card-body-modern">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label-mod">Select Bank <span class="text-danger">*</span></label>
                                <select id="bank_select_add" name="bank_code" class="form-select" required onchange="updateBankNameAdd(this)">
                                    <option value="">-- Select Nigerian Bank --</option>
                                    <?php foreach($bankCategories as $cat => $banks): ?>
                                    <optgroup label="<?php echo htmlspecialchars($cat); ?>">
                                    <?php foreach($nigerian_banks as $b): if(in_array($b['name'], $banks)): ?>
                                    <option value="<?php echo htmlspecialchars($b['code']); ?>" data-name="<?php echo htmlspecialchars($b['name']); ?>"><?php echo htmlspecialchars($b['name'] . ' (' . $b['code'] . ')'); ?></option>
                                    <?php endif; endforeach; ?>
                                    </optgroup>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6"><label class="form-label-mod">Bank Account Number <span class="text-danger">*</span></label><input type="text" name="bank_account" class="form-control form-control-mod" placeholder="e.g. 1234567890" required></div>
                            <input type="hidden" name="bank_name" id="bank_name_add" value="">
                            <div class="col-md-6"><label class="form-label-mod">Tax ID</label><input type="text" name="tax_id" class="form-control form-control-mod"></div>
                            <div class="col-md-6"><label class="form-label-mod">Employee Status</label><select name="status" class="form-select"><option value="active">Active</option><option value="probation">Probation</option><option value="inactive">Inactive</option></select></div>
                        </div>
                    </div>
                </div>

                <div class="card-modern mb-3">
                    <div class="card-header-modern"><h6 class="fw-bold mb-0"><i class="fa-solid fa-file-lines me-2 text-primary"></i>Documents</h6></div>
                    <div class="card-body-modern">
                        <label class="form-label-mod">Profile Photo</label>
                        <input type="file" name="photo" accept="image/*" class="form-control form-control-mod">
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card-modern mb-3" style="position:sticky;top:80px;">
                    <div class="card-header-modern"><h6 class="fw-bold mb-0">Actions</h6></div>
                    <div class="card-body-modern">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary-mod"><i class="fa-solid fa-check me-2"></i>Create Employee</button>
                            <a href="employees.php" class="btn btn-outline-mod">Cancel</a>
                        </div>
                        <hr class="my-3">
                        <div class="text-muted small">
                            <p class="mb-1"><i class="fa-solid fa-circle-info me-1 text-primary"></i> A user account will be created automatically.</p>
                            <p class="mb-0"><i class="fa-solid fa-lock me-1 text-primary"></i> Default password: <strong>Employee@123</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function updateBankNameAdd(sel) {
    var opt = sel.options[sel.selectedIndex];
    var name = opt.getAttribute('data-name') || '';
    document.getElementById('bank_name_add').value = name;
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
