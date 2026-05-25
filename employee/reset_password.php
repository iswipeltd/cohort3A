<?php
session_start();

include("../includes/auth_check.php");
checkRole(['admin','hr']);

include("../config/db.php");

if(!isset($_GET['id'])){
    die("No user selected");
}

$user_id = intval($_GET['id']);

$result = mysqli_query($conn, "SELECT full_name FROM users WHERE id='$user_id' LIMIT 1");

if(!$result || mysqli_num_rows($result) == 0){
    die("User not found");
}

$user = mysqli_fetch_assoc($result);

if(isset($_POST['reset'])){

    $password = $_POST['new_password'];

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    mysqli_query($conn, "
        UPDATE users 
        SET password='$hashed'
        WHERE id='$user_id'
    ");

    header("Location: index.php?reset=success");
    exit();
}
?>

<?php include("../includes/header.php"); ?>

<div class="container mt-4">

<h3>Reset Password for <?php echo $user['full_name']; ?></h3>

<form method="POST">

<input type="text" name="new_password" class="form-control mb-3" required>

<button class="btn btn-primary" name="reset">
Reset Password
</button>

</form>

</div>

<?php include("../includes/footer.php"); ?>