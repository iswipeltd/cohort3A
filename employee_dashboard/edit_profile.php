<?php
session_start();

include("../includes/auth_check.php");
checkRole(['employee']);

include("../config/db.php");

/*
|--------------------------------------------------------------------------
| GET LOGGED IN USER
|--------------------------------------------------------------------------
*/
$user_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| FETCH USER DATA
|--------------------------------------------------------------------------
*/
$sql = "SELECT * FROM users WHERE id='$user_id' LIMIT 1";

$result = mysqli_query($conn, $sql);

if(!$result){
    die("Query Failed: " . mysqli_error($conn));
}

$userData = mysqli_fetch_assoc($result);

/*
|--------------------------------------------------------------------------
| UPDATE PROFILE
|--------------------------------------------------------------------------
*/
if(isset($_POST['update_profile'])){

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);

    if(empty($full_name) || empty($email)){
        die("All fields are required");
    }

    /*
    |--------------------------------------------------------------------------
    | HANDLE PROFILE IMAGE
    |--------------------------------------------------------------------------
    */
    $profile_image = $userData['profile_image'];

    if(isset($_FILES['profile_image']) && $_FILES['profile_image']['name'] != ''){

        $image_name = time() . "_" . $_FILES['profile_image']['name'];

        $tmp_name = $_FILES['profile_image']['tmp_name'];

        $upload_path = "../uploads/profile/" . $image_name;

        move_uploaded_file($tmp_name, $upload_path);

        $profile_image = $image_name;
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE USER
    |--------------------------------------------------------------------------
    */
    $update = mysqli_query($conn, "
        UPDATE users

        SET
        full_name='$full_name',
        email='$email',
        profile_image='$profile_image'

        WHERE id='$user_id'
    ");

    if(!$update){
        die("Update Failed: " . mysqli_error($conn));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE SESSION NAME
    |--------------------------------------------------------------------------
    */
    $_SESSION['name'] = $full_name;

    header("Location: profile.php?updated=1");
    exit();
}
?>

<?php include("../includes/header.php"); ?>

<div id="layoutSidenav">

<?php include("../includes/sidebar.php"); ?>

<div id="layoutSidenav_content">

<main>

<div class="container-fluid px-4">

<h1 class="mt-4">Edit Profile</h1>

<div class="card mb-4">

<div class="card-body">

<form method="POST" enctype="multipart/form-data">

<div class="mb-3">

<label>Full Name</label>

<input type="text"
       name="full_name"
       class="form-control"
       value="<?php echo $userData['full_name']; ?>"
       required>

</div>

<div class="mb-3">

<label>Email</label>

<input type="email"
       name="email"
       class="form-control"
       value="<?php echo $userData['email']; ?>"
       required>

</div>

<div class="mb-3">

<label class="form-label">
    Profile Image
</label>

<input type="file"
       name="profile_image"
       class="form-control">

</div>

<button type="submit"
        name="update_profile"
        class="btn btn-primary">

    Update Profile

</button>

</form>

</div>

</div>

</div>

</main>

<?php include("../includes/footer.php"); ?>

</div>
</div>