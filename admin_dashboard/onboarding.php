<?php
session_start();
if(empty($_SESSION['pending_onboarding_user_id'])){header('Location: login.php');exit;}
require_once __DIR__.'/../config/database.php';
$err='';$success='';
$stmt=$pdo->prepare("SELECT id,email,first_name,last_name FROM users WHERE id=?");
$stmt->execute([$_SESSION['pending_onboarding_user_id']]);
$u=$stmt->fetch();
if(!$u){header('Location: login.php');exit;}

if($_SERVER['REQUEST_METHOD']==='POST'){
  $fn=trim($_POST['first_name']??'');$ln=trim($_POST['last_name']??'');
  if($fn&&$ln){
    $pdo->prepare("UPDATE users SET first_name=?,last_name=? WHERE id=?")->execute([$fn,$ln,$u['id']]);
    $u['first_name']=$fn;$u['last_name']=$ln;
  }
  if(!empty($_FILES['avatar']['tmp_name'])&&in_array($_FILES['avatar']['type'],['image/jpeg','image/png','image/gif','image/webp'])){
    $ext=pathinfo($_FILES['avatar']['name'],PATHINFO_EXTENSION);$fn='avatar_'.$u['id'].'_'.time().'.'.$ext;
    if(move_uploaded_file($_FILES['avatar']['tmp_name'],__DIR__.'/../uploads/avatars/'.$fn)){
      $pdo->prepare("UPDATE users SET avatar=? WHERE id=?")->execute(['/HRSuite/uploads/avatars/'.$fn,$u['id']]);
      $_SESSION['user_id']=$u['id'];$_SESSION['email']=$u['email'];$_SESSION['full_name']=$fn.' '.$ln;
      $pdo->prepare("UPDATE users SET last_login=NOW(),last_login_ip=? WHERE id=?")->execute([$_SERVER['REMOTE_ADDR']??'0.0.0.0',$u['id']]);
      log_activity($u['id'],'LOGIN','Auth',$u['id'],'First login with profile setup');
      unset($_SESSION['pending_onboarding_user_id']);
      header('Location: welcome.php');exit;
    }else{$err='Failed to upload photo.';}
  }else{$err='Please upload a profile photo.';}
}
?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome | ADEEEEE</title><link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>*{font-family:'Inter',sans-serif;}body{min-height:100vh;background:#0a0a0f;display:flex;align-items:center;justify-content:center;padding:20px;}
.onboard-card{background:#13131f;border:1.5px solid #252540;border-radius:16px;box-shadow:0 20px 60px rgba(0,0,0,0.5);max-width:520px;width:100%;overflow:hidden;}
.onboard-header{background:#6366f1;padding:40px 30px 30px;text-align:center;color:#fff;}
.onboard-header h2{font-family:'Poppins';font-weight:700;margin-bottom:4px;}
.onboard-body{padding:35px 30px 30px;}
.avatar-upload{width:130px;height:130px;border-radius:50%;border:3px dashed #334155;background:#0a0a0f;display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;transition:0.2s;margin:0 auto 20px;position:relative;overflow:hidden;}
.avatar-upload:hover{border-color:#6366f1;background:#1a1a2e;}
.avatar-upload img{width:100%;height:100%;object-fit:cover;position:absolute;top:0;left:0;display:none;}
.avatar-upload.has-img img{display:block;}
.avatar-upload.has-img i,.avatar-upload.has-img span{display:none;}
.avatar-upload i{color:#6366f1;font-size:1.5rem;}
.avatar-upload span{color:#64748b;}
.form-control-onboard{border:1.5px solid #252540;border-radius:12px;padding:12px 16px;background:#0a0a0f;color:#f8fafc;}
.form-control-onboard:focus{border-color:#6366f1;box-shadow:0 0 0 4px rgba(99,102,241,0.15);background:#0a0a0f;color:#f8fafc;outline:none;}
.form-control-onboard::placeholder{color:#64748b;}
.btn-onboard{background:#6366f1;border:none;color:#fff;font-weight:700;padding:14px;border-radius:12px;width:100%;transition:0.2s;}
.btn-onboard:hover{background:#4f46e5;box-shadow:0 10px 30px rgba(99,102,241,0.3);}
label{color:#94a3b8;font-weight:600;text-transform:uppercase;font-size:0.75rem;letter-spacing:0.3px;}
</style></head><body>
<div class="onboard-card animate-fade">
<div class="onboard-header">
<i class="fa-solid fa-sparkles fa-2x mb-2" style="opacity:0.8;"></i>
<h2>Welcome to ADEEEEE!</h2>
<p class="mb-0 opacity-75">Let's set up your admin profile</p>
</div>
<div class="onboard-body">
<?php if($err):?><div class="alert alert-danger py-2 mb-3" style="border-radius:10px;font-size:0.85rem;"><i class="fa-solid fa-circle-exclamation me-2"></i><?php echo htmlspecialchars($err);?></div><?php endif;?>
<form method="POST" enctype="multipart/form-data" id="onboardForm">
<div class="text-center mb-3">
<div class="avatar-upload" id="avatarUpload" onclick="document.getElementById('avatarInput').click()">
<img id="avatarPreview" alt=""><i class="fa-solid fa-camera"></i>
<span class="small mt-1">Upload photo</span>
</div>
<input type="file" name="avatar" id="avatarInput" accept="image/*" style="display:none;" onchange="previewAvatar(this)">
</div>
<div class="row g-2 mb-3">
<div class="col-6"><label class="form-label">First Name</label><input type="text" name="first_name" value="<?php echo htmlspecialchars($u['first_name']??'');?>" class="form-control form-control-onboard" required></div>
<div class="col-6"><label class="form-label">Last Name</label><input type="text" name="last_name" value="<?php echo htmlspecialchars($u['last_name']??'');?>" class="form-control form-control-onboard" required></div>
</div>
<button type="submit" class="btn btn-onboard">Complete Setup <i class="fa-solid fa-arrow-right ms-2"></i></button>
</form>
</div></div>
<script>function previewAvatar(input){var f=input.files[0];if(f){var r=new FileReader();r.onload=function(e){var p=document.getElementById('avatarPreview');p.src=e.target.result;p.parentElement.classList.add('has-img');};r.readAsDataURL(f);}}</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>
