<?php
session_start();

include("../includes/auth_check.php");
checkRole(['admin','hr']);

include("../config/db.php");

require_once("../dompdf/vendor/autoload.php");

use Dompdf\Dompdf;

/*
|--------------------------------------------------------------------------
| GET EMPLOYEES
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT id, full_name, email, role
    FROM users
    ORDER BY id DESC
";

$result = mysqli_query($conn, $sql);

if(!$result){
    die("Query Error: " . mysqli_error($conn));
}

/*
|--------------------------------------------------------------------------
| BUILD PDF
|--------------------------------------------------------------------------
*/
$html = "
<h2 style='text-align:center;'>Employee Report</h2>
<hr>

<table border='1' width='100%' cellpadding='8'>
<tr>
    <th>ID</th>
    <th>Full Name</th>
    <th>Email</th>
    <th>Role</th>
</tr>
";

while($row = mysqli_fetch_assoc($result)){

$html .= "
<tr>
    <td>{$row['id']}</td>
    <td>{$row['full_name']}</td>
    <td>{$row['email']}</td>
    <td>{$row['role']}</td>
</tr>
";

}

$html .= "</table>";

/*
|--------------------------------------------------------------------------
| GENERATE PDF
|--------------------------------------------------------------------------
*/
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();
$dompdf->stream("employee_report.pdf", ["Attachment" => true]);
?>