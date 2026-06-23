<?php
/**
 * ADEEEEE Export Helper
 * Generates CSV and HTML (Excel-compatible) exports for reports
 */

function exportToCsv($filename, $headers, $rows) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
    fputcsv($output, $headers);
    foreach ($rows as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}

function exportToHtmlExcel($filename, $title, $headers, $rows) {
    header('Content-Type: application/vnd.ms-excel; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
    <head><meta charset="utf-8"><style>table{border-collapse:collapse;}th,td{border:1px solid #ccc;padding:8px;}th{background:#0d6efd;color:#fff;}</style></head>
    <body><h2>' . htmlspecialchars($title) . '</h2><table><tr>';
    foreach ($headers as $h) {
        echo '<th>' . htmlspecialchars($h) . '</th>';
    }
    echo '</tr>';
    foreach ($rows as $row) {
        echo '<tr>';
        foreach ($row as $cell) {
            echo '<td>' . htmlspecialchars($cell) . '</td>';
        }
        echo '</tr>';
    }
    echo '</table></body></html>';
    exit;
}
