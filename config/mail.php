<?php
/**
 * ADEEEEE Mail Helper
 * Supports SMTP (recommended) or PHP mail() fallback
 * 
 * Configure SMTP in settings table or edit defaults below.
 */

function getMailConfig() {
    global $pdo;
    $defaults = [
        'mail_driver'     => 'php',      // 'smtp' or 'php'
        'smtp_host'       => 'smtp.gmail.com',
        'smtp_port'       => '587',
        'smtp_encryption' => 'tls',      // 'tls', 'ssl', or ''
        'smtp_username'   => '',
        'smtp_password'   => '',
        'mail_from'       => 'hrsuite@company.com',
        'mail_from_name'  => 'ADEEEEE HR System',
    ];
    
    try {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings WHERE setting_group = 'mail'");
        $dbSettings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        return array_merge($defaults, $dbSettings);
    } catch (Exception $e) {
        return $defaults;
    }
}

/**
 * Send email via SMTP using raw socket (no external dependencies)
 */
function sendSmtpMail($to, $subject, $body, $config) {
    $host = $config['smtp_host'];
    $port = (int) $config['smtp_port'];
    $user = $config['smtp_username'];
    $pass = $config['smtp_password'];
    $from = $config['mail_from'];
    $fromName = $config['mail_from_name'];
    $encryption = $config['smtp_encryption'];
    
    if (empty($host) || empty($user) || empty($pass)) {
        error_log("SMTP not configured. Falling back to PHP mail.");
        return sendPhpMail($to, $subject, $body, $from, $fromName);
    }
    
    $timeout = 30;
    $errno = 0;
    $errstr = '';
    
    $prefix = ($encryption === 'ssl') ? 'ssl://' : '';
    $socket = fsockopen($prefix . $host, $port, $errno, $errstr, $timeout);
    
    if (!$socket) {
        error_log("SMTP connection failed: {$errstr} ({$errno})");
        return sendPhpMail($to, $subject, $body, $from, $fromName);
    }
    
    stream_set_timeout($socket, $timeout);
    
    $reply = fgets($socket, 512);
    if (substr($reply, 0, 3) !== '220') { fclose($socket); return false; }
    
    $ehloHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
    fputs($socket, "EHLO {$ehloHost}\r\n");
    $reply = fgets($socket, 512);
    
    if ($encryption === 'tls') {
        fputs($socket, "STARTTLS\r\n");
        fgets($socket, 512);
        stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
        fputs($socket, "EHLO {$ehloHost}\r\n");
        fgets($socket, 512);
    }
    
    // AUTH LOGIN
    fputs($socket, "AUTH LOGIN\r\n");
    fgets($socket, 512);
    fputs($socket, base64_encode($user) . "\r\n");
    fgets($socket, 512);
    fputs($socket, base64_encode($pass) . "\r\n");
    $reply = fgets($socket, 512);
    if (substr($reply, 0, 3) !== '235') {
        error_log("SMTP authentication failed: " . trim($reply));
        fclose($socket);
        return false;
    }
    
    // MAIL FROM
    fputs($socket, "MAIL FROM:<{$from}>\r\n");
    fgets($socket, 512);
    
    // RCPT TO
    fputs($socket, "RCPT TO:<{$to}>\r\n");
    fgets($socket, 512);
    
    // DATA
    fputs($socket, "DATA\r\n");
    fgets($socket, 512);
    
    $boundary = md5(time());
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: {$fromName} <{$from}>\r\n";
    $headers .= "To: {$to}\r\n";
    $headers .= "Subject: {$subject}\r\n";
    
    $message = $headers . "\r\n" . $body . "\r\n.\r\n";
    fputs($socket, $message);
    fgets($socket, 512);
    
    fputs($socket, "QUIT\r\n");
    fclose($socket);
    
    return true;
}

function sendPhpMail($to, $subject, $body, $from, $fromName) {
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: {$fromName} <{$from}>\r\n";
    return mail($to, $subject, $body, $headers);
}

/**
 * Unified send email function
 */
function sendEmail($to, $subject, $body, $template = null, $vars = []) {
    $config = getMailConfig();
    
    // Apply template if provided
    if ($template) {
        $body = renderEmailTemplate($template, array_merge($vars, ['subject' => $subject, 'body' => $body]));
    } else {
        $body = wrapInHtmlTemplate($subject, $body);
    }
    
    if ($config['mail_driver'] === 'smtp') {
        return sendSmtpMail($to, $subject, $body, $config);
    }
    return sendPhpMail($to, $subject, $body, $config['mail_from'], $config['mail_from_name']);
}

function wrapInHtmlTemplate($subject, $body) {
    return '<!DOCTYPE html>
<html><head><meta charset="UTF-8"><title>' . htmlspecialchars($subject) . '</title>
<style>body{font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:20px;}
.container{max-width:600px;margin:0 auto;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,0.1);}
.header{background:#0d6efd;color:#fff;padding:20px;text-align:center;}
.content{padding:30px;line-height:1.6;color:#333;}
.footer{background:#f8f9fa;padding:15px;text-align:center;font-size:12px;color:#666;}</style>
</head><body>
<div class="container">
<div class="header"><h2>ADEEEEE</h2></div>
<div class="content">' . $body . '</div>
<div class="footer">This is an automated message from ADEEEEE HR Management System.</div>
</div>
</body></html>';
}

function renderEmailTemplate($template, $vars) {
    $templates = [
        'welcome' => '<h3>Welcome to ADEEEEE, {{name}}!</h3>
            <p>Your account has been created successfully.</p>
            <p><strong>Email:</strong> {{email}}<br><strong>Temporary Password:</strong> {{password}}</p>
            <p>Please log in and change your password immediately.</p>',
            
        'leave_approved' => '<h3>Leave Request Approved</h3>
            <p>Hi {{name}},</p>
            <p>Your <strong>{{leave_type}}</strong> request from <strong>{{start_date}}</strong> to <strong>{{end_date}}</strong> ({{days}} days) has been <span style="color:green"><strong>APPROVED</strong></span>.</p>',
            
        'leave_rejected' => '<h3>Leave Request Rejected</h3>
            <p>Hi {{name}},</p>
            <p>Your <strong>{{leave_type}}</strong> request from <strong>{{start_date}}</strong> to <strong>{{end_date}}</strong> ({{days}} days) has been <span style="color:red"><strong>REJECTED</strong></span>.</p>
            <p><strong>Reason:</strong> {{reason}}</p>',
            
        'payroll_processed' => '<h3>Payroll Processed</h3>
            <p>Hi {{name}},</p>
            <p>Your payroll for <strong>{{period}}</strong> has been processed.</p>
            <p><strong>Gross Pay:</strong> {{gross}}<br><strong>Net Pay:</strong> {{net}}</p>
            <p>Your payslip is available in the employee portal.</p>',
            
        'expense_status' => '<h3>Expense Claim Update</h3>
            <p>Hi {{name}},</p>
            <p>Your expense claim of <strong>{{amount}}</strong> for <strong>{{type}}</strong> has been <strong>{{status}}</strong>.</p>',
            
        'new_employee' => '<h3>New Employee Onboarded</h3>
            <p>A new employee <strong>{{name}}</strong> has been added to the system.</p>
            <p><strong>Department:</strong> {{department}}<br><strong>Role:</strong> {{role}}<br><strong>Start Date:</strong> {{start_date}}</p>',
            
        'password_reset' => '<h3>Password Reset Request</h3>
            <p>Hi {{name}},</p>
            <p>You requested a password reset. Click the link below to reset your password:</p>
            <p><a href="{{reset_link}}" style="background:#0d6efd;color:#fff;padding:10px 20px;text-decoration:none;border-radius:5px;">Reset Password</a></p>
            <p>If you did not request this, please ignore this email.</p>',
    ];
    
    $html = $templates[$template] ?? $vars['body'] ?? '';
    foreach ($vars as $key => $val) {
        $html = str_replace('{{' . $key . '}}', htmlspecialchars($val), $html);
    }
    return wrapInHtmlTemplate($vars['subject'] ?? 'ADEEEEE Notification', $html);
}
