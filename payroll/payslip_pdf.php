<?php
session_start();

include("../includes/auth_check.php");
checkRole(['admin','hr','employee']);

include("../config/db.php");

require_once("../dompdf/vendor/autoload.php");

use Dompdf\Dompdf;

$role = $_SESSION['role'];
$session_id = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| DETERMINE USER
|--------------------------------------------------------------------------
*/

// Admin/HR can view ANY employee via URL
if(($role == 'admin' || $role == 'hr') && isset($_GET['id'])){
    $user_id = intval($_GET['id']);
} else {
    $user_id = $session_id;
}

/*
|--------------------------------------------------------------------------
| GET LATEST PAYROLL FOR USER
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT payroll.*, users.full_name
    FROM payroll
    LEFT JOIN users ON payroll.user_id = users.id
    WHERE payroll.user_id = '$user_id'
    ORDER BY payroll.id DESC
    LIMIT 1
";

$result = mysqli_query($conn, $sql);

if(!$result){
    die("Query Error: " . mysqli_error($conn));
}

if(mysqli_num_rows($result) == 0){
    die("No payroll record found for this employee");
}

$data = mysqli_fetch_assoc($result);

/*
|--------------------------------------------------------------------------
| PDF DESIGN
|--------------------------------------------------------------------------
*/
$html = "
<style>
body { font-family: Arial; font-size: 12px; }
.header { text-align:center; margin-bottom:20px; }
table { width:100%; border-collapse: collapse; }
td, th { border:1px solid #ddd; padding:8px; }
th { background:#f4f4f4; }
</style>

<div class='header'>
    <h2>PAYSLIP</h2>
    <p>Name: {$data['full_name']}</p>
    <p>Month: {$data['pay_month']}</p>
</div>

<table>
<tr><th>Item</th><th>Amount</th></tr>
<tr><td>Basic Salary</td><td>{$data['basic_salary']}</td></tr>
<tr><td>Allowance</td><td>{$data['allowance']}</td></tr>
<tr><td>Deduction</td><td>{$data['deduction']}</td></tr>
<tr><td><b>Net Salary</b></td><td><b>{$data['net_salary']}</b></td></tr>
</table>
";

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();
$dompdf->stream("payslip.pdf", ["Attachment" => true]);
?>