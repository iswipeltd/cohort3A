<?php
require_once __DIR__ . '/../config/session.php';
require_admin();
$user = current_user();
$err='';$success='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $fn=trim($_POST['first_name']??'');$ln=trim($_POST['last_name']??'');$ph=trim($_POST['phone']??'');
  if($fn&&$ln){$pdo->prepare("UPDATE users SET first_name=?,last_name=?,phone=? WHERE id=?")->execute([$fn,$ln,$ph,$_SESSION['user_id']]);$success='Profile updated.';$user=current_user();}
  if(!empty($_FILES['avatar']['tmp_name'])&&in_array($_FILES['avatar']['type'],['image/jpeg','image/png','image/gif','image/webp'])){
    $ext=pathinfo($_FILES['avatar']['name'],PATHINFO_EXTENSION);$fn='avatar_'.$_SESSION['user_id'].'_'.time().'.'.$ext;
    if(move_uploaded_file($_FILES['avatar']['tmp_name'],__DIR__.'/../uploads/avatars/'.$fn)){
      $pdo->prepare("UPDATE users SET avatar=? WHERE id=?")->execute(['/HRSuite/uploads/avatars/'.$fn,$_SESSION['user_id']]);$success='Avatar updated.';$user=current_user();
    }
  }
}
?><?php include 'includes/head.php'; ?><body><?php include 'includes/sidebar.php'; ?><div class="main-content"><?php include 'includes/navbar.php'; ?>
<?php $err=$_SESSION['error']??''; $success=$_SESSION['success']??''; unset($_SESSION['error'],$_SESSION['success']); ?>
<?php if($err):?><div class="alert alert-danger d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success d-flex align-items-center py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>

<div class="page-header"><div><h1 class="page-title">Profile Settings</h1><p class="page-subtitle mb-0">Manage your account</p></div></div>
<div class="px-4 pb-4">
<?php if($err):?><div class="alert alert-danger py-2 mb-3" style="border-radius:10px;font-size:.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<?php if($success):?><div class="alert alert-success py-2 mb-3" style="border-radius:10px;font-size:.85rem;"><i class="fa-solid fa-circle-check me-2"></i><?php echo htmlspecialchars($success);?></div><?php endif;?>
<div class="row g-3">
<div class="col-lg-4"><div class="card-modern text-center"><div class="card-body-modern">
<img src="<?php echo $user['avatar']?htmlspecialchars($user['avatar']):'https://ui-avatars.com/api/?name='.urlencode($user['first_name'].'+'.$user['last_name']).'&background=6366f1&color=fff&size=200';?>" style="width:120px;height:120px;border-radius:50%;object-fit:cover;border:4px solid var(--primary);margin-bottom:16px;" alt="">
<h5 class="fw-bold mb-1"><?php echo htmlspecialchars($user['first_name'].' '.$user['last_name']);?></h5>
<p class="text-muted small mb-3"><?php echo ucfirst($user['role']);?></p>
<p class="text-muted small mb-0"><i class="fa-solid fa-envelope me-1"></i><?php echo htmlspecialchars($user['email']);?></p>
</div></div></div>
<div class="col-lg-8"><div class="card-modern"><div class="card-header-modern"><h6 class="fw-bold mb-0">Edit Profile</h6></div><div class="card-body-modern">
<form method="POST" enctype="multipart/form-data"><div class="row g-3">
<div class="col-md-6"><label class="form-label-mod">First Name</label><input type="text" name="first_name" value="<?php echo htmlspecialchars($user['first_name']);?>" class="form-control form-control-mod"></div>
<div class="col-md-6"><label class="form-label-mod">Last Name</label><input type="text" name="last_name" value="<?php echo htmlspecialchars($user['last_name']);?>" class="form-control form-control-mod"></div>
<div class="col-md-6"><label class="form-label-mod">Email</label><input type="email" value="<?php echo htmlspecialchars($user['email']);?>" class="form-control form-control-mod" disabled></div>
<div class="col-md-6"><label class="form-label-mod">Phone</label><input type="tel" name="phone" value="<?php echo htmlspecialchars($user['phone']??'');?>" class="form-control form-control-mod"></div>
<div class="col-md-12"><label class="form-label-mod">Profile Photo</label><input type="file" name="avatar" accept="image/*" class="form-control form-control-mod"></div>
</div><div class="mt-3"><button type="submit" class="btn btn-primary-mod">Save Changes</button></div></form>
</div></div></div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
