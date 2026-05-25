<?php
session_start();

include("../includes/auth_check.php");
checkRole(['admin','hr']);

include("../config/db.php");

$users = mysqli_query($conn, "
    SELECT id, full_name, email, role 
    FROM users
    ORDER BY id DESC
");
?>

<?php include("../includes/header.php"); ?>

<div class="container mt-4">
    <br><br><br><br>
    <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success mt-3">
        ✔ Password reset successful!
    </div>
    <?php endif; ?>

  <br><br><br><br>
<h3>User Management - Reset Password</h3>

<table class="table table-bordered">

<thead>
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Action</th>
</tr>
</thead>

<tbody>

<?php while($row = mysqli_fetch_assoc($users)): ?>

<tr>
    <td><?php echo $row['full_name']; ?></td>
    <td><?php echo $row['email']; ?></td>
    <td><?php echo ucfirst($row['role']); ?></td>
    <td>
        <a href="reset_password.php?id=<?php echo $row['id']; ?>" 
           class="btn btn-warning btn-sm">
            Reset Password
        </a>
    </td>
</tr>

<?php endwhile; ?>

</tbody>

</table>

</div>
<br><br><br><br><br><br>

<?php include("../includes/footer.php"); ?>