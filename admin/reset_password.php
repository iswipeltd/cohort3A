<?php
session_start();

include("../includes/auth_check.php");
checkRole(['admin','hr']);

include("../config/db.php");

/*
|--------------------------------------------------------------------------
| GET USER ID
|--------------------------------------------------------------------------
*/
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid user ID");
}

$user_id = (int) $_GET['id'];
$result = mysqli_query($conn, "
    SELECT id, full_name
    FROM users
    WHERE id = $user_id
");

$userData = mysqli_fetch_assoc($result);

/*
|--------------------------------------------------------------------------
| UPDATE PASSWORD
|--------------------------------------------------------------------------
*/
if (isset($_POST['reset'])) {

    $password = trim($_POST['new_password']);

    if (empty($password)) {
        die("Password cannot be empty");
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    $update = mysqli_query($conn, "
        UPDATE users 
        SET password = '$hashed'
        WHERE id = $user_id
    ");

    if (!$update) {
        die("Update failed: " . mysqli_error($conn));
    }
header("Location: users_reset.php?success=1");
exit();
}
?>

<?php include("../includes/header.php"); ?>

<div class="container mt-4">
    
<br><br><br><br>
  <h3>Reset Password for  <?php echo htmlspecialchars($userData['full_name'] ?? ''); ?></h3>

    <form method="POST">

        <input type="text" name="new_password" class="form-control mb-3" required>

        <button type="submit" name="reset" class="btn btn-primary">
            Reset Password
        </button>

    </form>

</div>

<br><br><br><br><br><br><br><br><br><br><br><br><br>

<?php include("../includes/footer.php"); ?>