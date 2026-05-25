<?php
session_start();

include("../includes/auth_check.php");
checkRole(['admin','hr']);

include("../config/db.php");

require_once("../dompdf/vendor/autoload.php");

use Dompdf\Dompdf;

/*
|--------------------------------------------------------------------------
| FETCH ATTENDANCE DATA
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT 
        users.full_name,
        attendance.date,
        attendance.status,
        attendance.time_in,
        attendance.time_out

    FROM attendance

    LEFT JOIN users
    ON attendance.user_id = users.id

    ORDER BY attendance.date DESC
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
<h2 style='text-align:center;'>Attendance Report</h2>
<hr>

<table border='1' width='100%' cellpadding='8'>
<tr>
    <th>Employee</th>
    <th>Date</th>
    <th>Status</th>
    <th>Time In</th>
    <th>Time Out</th>
</tr>
";

while($row = mysqli_fetch_assoc($result)){

$html .= "
<tr>
    <td>{$row['full_name']}</td>
    <td>{$row['date']}</td>
    <td>{$row['status']}</td>
    <td>{$row['time_in']}</td>
    <td>{$row['time_out']}</td>
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
$dompdf->stream("attendance_report.pdf", ["Attachment" => true]);
?>