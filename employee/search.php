<?php
session_start();

include("../includes/auth_check.php");
checkRole(['admin','hr']);

include("../config/db.php");

/*
|--------------------------------------------------------------------------
| SEARCH VALUE
|--------------------------------------------------------------------------
*/
$search = "";

if(isset($_GET['search'])){
    $search = trim($_GET['search']);
}

/*
|--------------------------------------------------------------------------
| SEARCH QUERY
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT *
    FROM users

    WHERE
    full_name LIKE '%$search%'
    OR
    email LIKE '%$search%'
    OR
    role LIKE '%$search%'

    ORDER BY id DESC
";

$result = mysqli_query($conn, $sql);

if(!$result){
    die("Search Failed: " . mysqli_error($conn));
}
?>

<?php include("../includes/header.php"); ?>

<div id="layoutSidenav">

<?php include("../includes/sidebar.php"); ?>

<div id="layoutSidenav_content">

<main>

<div class="container-fluid px-4">

<h1 class="mt-4">Search Results</h1>

<div class="card mb-4">

<div class="card-header">
Search: "<?php echo htmlspecialchars($search); ?>"
</div>

<div class="card-body">

<table class="table table-bordered table-striped">

<thead class="table-dark">

<tr>
    <th>ID</th>
    <th>Full Name</th>
    <th>Email</th>
    <th>Role</th>
</tr>

</thead>

<tbody>

<?php if(mysqli_num_rows($result) > 0): ?>

<?php while($userData = mysqli_fetch_assoc($result)): ?>

<tr>

<td><?php echo $userData['id']; ?></td>

<td><?php echo $userData['full_name']; ?></td>

<td><?php echo $userData['email']; ?></td>

<td><?php echo ucfirst($userData['role']); ?></td>

</tr>

<?php endwhile; ?>

<?php else: ?>

<tr>

<td colspan="4" class="text-center text-danger">
No matching users found
</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</div>

</main>

<?php include("../includes/footer.php"); ?>

</div>
</div>