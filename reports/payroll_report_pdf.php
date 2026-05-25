<?php
session_start();

include("../includes/auth_check.php");
checkRole(['admin','hr']);

include("../config/db.php");

require_once("../dompdf/vendor/autoload.php");

use Dompdf\Dompdf;

/*
|--------------------------------------------------------------------------
| GET DATA
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT users.full_name,
           payroll.basic_salary,
           payroll.allowance,
           payroll.deduction,
           payroll.net_salary,
           payroll.pay_month
    FROM payroll
    LEFT JOIN users ON payroll.user_id = users.id
    ORDER BY payroll.id DESC
";

$result = mysqli_query($conn, $sql);

/*
|--------------------------------------------------------------------------
| BUILD PDF
|--------------------------------------------------------------------------
*/
$html = "
<h2>Payroll Report</h2>
<hr>

<table border='1' width='100%' cellpadding='8'>
<tr>
    <th>Employee</th>
    <th>Basic</th>
    <th>Allowance</th>
    <th>Deduction</th>
    <th>Net</th>
    <th>Month</th>
</tr>
";

while($row = mysqli_fetch_assoc($result)){

$html .= "
<tr>
    <td>{$row['full_name']}</td>
    <td>{$row['basic_salary']}</td>
    <td>{$row['allowance']}</td>
    <td>{$row['deduction']}</td>
    <td>{$row['net_salary']}</td>
    <td>{$row['pay_month']}</td>
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
$dompdf->setPaper("A4", "landscape");
$dompdf->render();
$dompdf->stream("payroll_report.pdf", ["Attachment" => true]);
?>