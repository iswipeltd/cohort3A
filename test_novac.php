<?php
/**
 * Test Novac API Connection
 * Visit: http://localhost/HRSuite/test_novac.php
 */

require_once __DIR__ . '/config/database.php';

// Fetch the Novac key from settings
$novacKey = '';
try {
    $s = $pdo->query("SELECT setting_value FROM settings WHERE setting_key = 'payment_novac_key' LIMIT 1");
    if ($s) {
        $row = $s->fetch();
        $novacKey = $row['setting_value'] ?? '';
    }
} catch (PDOException $e) {
    echo "<p style='color:red'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h1>Novac API Test</h1>";

if (empty($novacKey)) {
    echo "<p style='color:red'>No Novac API key found in settings. Go to Admin Settings > Payments and add your key first.</p>";
    exit;
}

echo "<p>API Key found: " . substr($novacKey, 0, 10) . "...</p>";

// Test 1: Check balance
$ch = curl_init('https://api.novacpayment.com/api/v1/balance/NGN');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $novacKey,
    'Accept: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "<h2>Test 1: Check Balance (GET /api/v1/balance/NGN)</h2>";
echo "<p>HTTP Code: <strong>$httpCode</strong></p>";
if ($curlError) {
    echo "<p style='color:red'>cURL Error: " . htmlspecialchars($curlError) . "</p>";
}
if ($response) {
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
} else {
    echo "<p style='color:red'>No response</p>";
}

// Test 2: Initiate a tiny test transfer (NGN 10.00)
$testPayload = json_encode([
    'currency' => 'NGN',
    'amount' => 10.00,
    'bankCode' => '044',
    'accountNumber' => '1234567890',
    'narration' => 'Test payout from HRSuite',
    'reference' => 'HRSUITE-TEST-' . time(),
    'bankName' => 'Access Bank PLC',
    'accountName' => 'Test Account',
    'metaData' => json_encode(['test' => true])
]);

$ch = curl_init('https://api.novacpayment.com/api/v1/transfers');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $testPayload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $novacKey,
    'Content-Type: application/json',
    'Accept: application/json'
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

echo "<h2>Test 2: Initiate Transfer (POST /api/v1/transfers)</h2>";
echo "<p>Payload sent:</p>";
echo "<pre>" . htmlspecialchars($testPayload) . "</pre>";
echo "<p>HTTP Code: <strong>$httpCode</strong></p>";
if ($curlError) {
    echo "<p style='color:red'>cURL Error: " . htmlspecialchars($curlError) . "</p>";
}
if ($response) {
    echo "<p>Response:</p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
} else {
    echo "<p style='color:red'>No response</p>";
}

echo "<hr><p><strong>What these results mean:</strong></p>";
echo "<ul>";
echo "<li><strong>HTTP 200</strong> = Success</li>";
echo "<li><strong>HTTP 401</strong> = Invalid API key. Copy the correct key from your Novac dashboard.</li>";
echo "<li><strong>HTTP 400/422</strong> = Bad request. The payload format may be wrong or a required field is missing.</li>";
echo "<li><strong>HTTP 403</strong> = Forbidden. Your account may need KYC verification or may be in test mode.</li>";
echo "<li><strong>No response / cURL error</strong> = Network issue or Novac API is down.</li>";
echo "</ul>";

echo "<p><a href='/HRSuite/admin_dashboard/payroll.php'>Back to Payroll</a></p>";
echo "<p style='color:gray'>Delete this file (test_novac.php) after testing.</p>";
