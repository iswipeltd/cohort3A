<?php
session_start();

include("../includes/auth_check.php");
checkRole(['admin','hr']);

include("../config/db.php");

require_once("../dompdf/vendor/autoload.php");

use Dompdf\Dompdf;

/*
|--------------------------------------------------------------------------
| FETCH LEAVE DATA
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT 
        users.full_name,
        leaves.leave_type,
        leaves.start_date,
        leaves.end_date,
        leaves.reason,
        leaves.status,
        leaves.created_at

    FROM leaves

    LEFT JOIN users
    ON leaves.user_id = users.id

    ORDER BY leaves.id DESC
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
<h2 style='text-align:center;'>Leave Report</h2>
<hr>

<table border='1' width='100%' cellpadding='6'>
<tr>
    <th>Employee</th>
    <th>Leave Type</th>
    <th>Start Date</th>
    <th>End Date</th>
    <th>Reason</th>
    <th>Status</th>
    <th>Date Applied</th>
</tr>
";

while($row = mysqli_fetch_assoc($result)){

$html .= "
<tr>
    <td>{$row['full_name']}</td>
    <td>{$row['leave_type']}</td>
    <td>{$row['start_date']}</td>
    <td>{$row['end_date']}</td>
    <td>{$row['reason']}</td>
    <td>{$row['status']}</td>
    <td>{$row['created_at']}</td>
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
$dompdf->stream("leave_report.pdf", ["Attachment" => true]);
?>