<?php
session_start();

include("../includes/auth_check.php");
checkRole(['employee']);

include("../config/db.php");

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM users WHERE id='$user_id'";

$result = mysqli_query($conn, $sql);

$userData = mysqli_fetch_assoc($result);
?>

<?php include("../includes/header.php"); ?>

<div id="layoutSidenav">

<?php include("../includes/sidebar.php"); ?>

<div id="layoutSidenav_content">

<main>

<div class="container-fluid px-4">

<h1 class="mt-4">My Profile</h1>

<div class="row">

<!-- PROFILE CARD -->

<div class="col-xl-4">

<div class="card shadow mb-4">

<div class="card-body text-center">

<?php if(!empty($userData['profile_image'])): ?>

<img src="../uploads/profile/<?php echo $userData['profile_image']; ?>"
     class="rounded-circle mb-3"
     width="150"
     height="150"
     style="object-fit:cover;">

<?php else: ?>

<img src="https://via.placeholder.com/150"
     class="rounded-circle mb-3">

<?php endif; ?>

<h3>
<?php echo $userData['full_name']; ?>
</h3>

<span class="badge bg-primary">
<?php echo ucfirst($userData['role']); ?>
</span>

</div>

</div>

</div>

<!-- PROFILE DETAILS -->

<div class="col-xl-8">

<div class="card shadow mb-4">

<div class="card-header">
Employee Information
</div>

<div class="card-body">

<table class="table table-bordered">

<tr>
<th>Full Name</th>
<td><?php echo $userData['full_name']; ?></td>
</tr>

<tr>
<th>Email</th>
<td><?php echo $userData['email']; ?></td>
</tr>

<tr>
<th>Role</th>
<td><?php echo ucfirst($userData['role']); ?></td>
</tr>

<?php if(isset($userData['phone'])): ?>
<tr>
<th>Phone</th>
<td><?php echo $userData['phone']; ?></td>
</tr>
<?php endif; ?>

<?php if(isset($userData['address'])): ?>
<tr>
<th>Address</th>
<td><?php echo $userData['address']; ?></td>
</tr>
<?php endif; ?>

<tr>
<th>Account Created</th>
<td><?php echo $userData['created_at']; ?></td>
</tr>

</table>

<a href="edit_profile.php"
   class="btn btn-primary">

    Edit Profile

</a>

</div>

</div>

</div>

</div>

</div>

</main>

<?php include("../includes/footer.php"); ?>

</div>

</div>