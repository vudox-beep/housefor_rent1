<?php
// Simple and reliable Gmail SMTP implementation using PHP mail() function
// This approach works better with Gmail's requirements

if (!function_exists('send_email_simple')) {
    function send_email_simple(string $to, string $subject, string $html, string $fromName = 'TasteBud', string $fromEmail = 'chisalaluckyk5@gmail.com', ?string $replyTo = null): bool
    {
        $logDir = __DIR__ . '/../storage/logs';
        if (!is_dir($logDir)) @mkdir($logDir, 0777, true);
        $logFile = $logDir . '/mailer.log';
        $ts = date('Y-m-d H:i:s');

        @file_put_contents($logFile, "[$ts] Simple mailer attempt to=$to subject=" . substr($subject, 0, 120) . "\n", FILE_APPEND);

        // Load configuration
        $cfg = [];
        $cfgFile = __DIR__ . '/smtp.local.php';
        if (is_file($cfgFile)) {
            $loaded = require $cfgFile;
            if (is_array($loaded)) $cfg = $loaded;
        }

        // Override with config values if available
        $host = $cfg['host'] ?? 'smtp.gmail.com';
        $port = (int)($cfg['port'] ?? 587);
        $user = $cfg['user'] ?? 'chisalaluckyk5@gmail.com';
        $pass = $cfg['pass'] ?? '';
        $fromEnv = $cfg['from'] ?? '';
        $fromNameEnv = $cfg['from_name'] ?? '';

        if ($fromEnv) $fromEmail = $fromEnv;
        if ($fromNameEnv) $fromName = $fromNameEnv;

        // Clean app password (remove spaces)
        $cleanPass = str_replace(' ', '', $pass);

        try {
            // Set PHP mail configuration for Gmail
            ini_set('SMTP', $host);
            ini_set('smtp_port', $port);
            ini_set('sendmail_from', $fromEmail);
            ini_set('smtp_username', $user);
            ini_set('smtp_password', $cleanPass);

            // Build headers
            $headers = [];
            $headers[] = "From: $fromName <$fromEmail>";
            $headers[] = "Reply-To: " . ($replyTo ?: $fromEmail);
            $headers[] = "MIME-Version: 1.0";
            $headers[] = "Content-Type: text/html; charset=UTF-8";
            $headers[] = "X-Mailer: PHP/" . phpversion();

            // For Gmail, we need to add additional authentication headers
            $headers[] = "X-Google-Original-From: $fromEmail";

            $full_headers = implode("\r\n", $headers);

            @file_put_contents($logFile, "[$ts] Attempting mail() function...\n", FILE_APPEND);

            // Try to send using PHP's mail() function
            $sent = @mail($to, $subject, $html, $full_headers, "-f$fromEmail");

            if ($sent) {
                @file_put_contents($logFile, "[$ts] SUCCESS: Email sent via mail() function\n", FILE_APPEND);
                return true;
            } else {
                @file_put_contents($logFile, "[$ts] mail() function failed\n", FILE_APPEND);

                // If mail() fails, try using a more direct approach
                return send_email_socket($to, $subject, $html, $fromName, $fromEmail, $replyTo, $host, $port, $user, $cleanPass);
            }
        } catch (Exception $e) {
            @file_put_contents($logFile, "[$ts] ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }
}

if (!function_exists('send_email_socket')) {
    function send_email_socket(string $to, string $subject, string $html, string $fromName, string $fromEmail, ?string $replyTo, string $host, int $port, string $user, string $pass): bool
    {
        $logFile = __DIR__ . '/../storage/logs/mailer.log';
        $ts = date('Y-m-d H:i:s');

        @file_put_contents($logFile, "[$ts] Socket attempt: $host:$port\n", FILE_APPEND);

        try {
            // Try direct SMTP connection with better error handling
            $timeout = 30;
            $errno = 0;
            $errstr = '';

            // Try TLS first (port 587)
            $fp = @fsockopen($host, 587, $errno, $errstr, $timeout);

            if (!$fp) {
                @file_put_contents($logFile, "[$ts] Connection failed: $errstr ($errno)\n", FILE_APPEND);
                return false;
            }

            // Read greeting
            $greeting = fgets($fp, 512);
            @file_put_contents($logFile, "[$ts] Greeting: " . trim($greeting) . "\n", FILE_APPEND);

            if (strpos($greeting, '220') !== 0) {
                fclose($fp);
                return false;
            }

            // Send EHLO
            fwrite($fp, "EHLO localhost\r\n");
            $ehlo_response = '';
            while ($line = fgets($fp, 512)) {
                $ehlo_response .= $line;
                if (substr($line, 3, 1) === ' ') break;
            }
            @file_put_contents($logFile, "[$ts] EHLO response received\n", FILE_APPEND);

            // Start TLS
            fwrite($fp, "STARTTLS\r\n");
            $starttls_response = fgets($fp, 512);
            @file_put_contents($logFile, "[$ts] STARTTLS: " . trim($starttls_response) . "\n", FILE_APPEND);

            if (strpos($starttls_response, '220') !== 0) {
                fclose($fp);
                return false;
            }

            // Enable TLS
            if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT)) {
                @file_put_contents($logFile, "[$ts] TLS enable failed\n", FILE_APPEND);
                fclose($fp);
                return false;
            }

            // Re-send EHLO after TLS
            fwrite($fp, "EHLO localhost\r\n");
            $ehlo_tls_response = '';
            while ($line = fgets($fp, 512)) {
                $ehlo_tls_response .= $line;
                if (substr($line, 3, 1) === ' ') break;
            }

            // Authenticate
            fwrite($fp, "AUTH LOGIN\r\n");
            $auth1_response = fgets($fp, 512);
            @file_put_contents($logFile, "[$ts] AUTH LOGIN: " . trim($auth1_response) . "\n", FILE_APPEND);

            if (strpos($auth1_response, '334') !== 0) {
                fclose($fp);
                return false;
            }

            fwrite($fp, base64_encode($user) . "\r\n");
            $auth2_response = fgets($fp, 512);
            @file_put_contents($logFile, "[$ts] Username: " . trim($auth2_response) . "\n", FILE_APPEND);

            if (strpos($auth2_response, '334') !== 0) {
                fclose($fp);
                return false;
            }

            fwrite($fp, base64_encode($pass) . "\r\n");
            $auth3_response = fgets($fp, 512);
            @file_put_contents($logFile, "[$ts] Password: " . trim($auth3_response) . "\n", FILE_APPEND);

            if (strpos($auth3_response, '235') !== 0) {
                @file_put_contents($logFile, "[$ts] Authentication failed\n", FILE_APPEND);
                fclose($fp);
                return false;
            }

            @file_put_contents($logFile, "[$ts] Authentication successful!\n", FILE_APPEND);

            // Send email
            fwrite($fp, "MAIL FROM: <$fromEmail>\r\n");
            $mail_from_response = fgets($fp, 512);

            fwrite($fp, "RCPT TO: <$to>\r\n");
            $rcpt_to_response = fgets($fp, 512);

            fwrite($fp, "DATA\r\n");
            $data_response = fgets($fp, 512);

            if (strpos($data_response, '354') !== 0) {
                fclose($fp);
                return false;
            }

            // Build email content
            $email_content = "From: $fromName <$fromEmail>\r\n";
            $email_content .= "To: $to\r\n";
            $email_content .= "Subject: $subject\r\n";
            $email_content .= "MIME-Version: 1.0\r\n";
            $email_content .= "Content-Type: text/html; charset=UTF-8\r\n";
            $email_content .= "Date: " . date(DATE_RFC2822) . "\r\n";
            $email_content .= "\r\n";
            $email_content .= $html;
            $email_content .= "\r\n.\r\n";

            fwrite($fp, $email_content);
            $send_response = fgets($fp, 512);

            fwrite($fp, "QUIT\r\n");
            fclose($fp);

            if (strpos($send_response, '250') !== 0) {
                @file_put_contents($logFile, "[$ts] Email send failed: " . trim($send_response) . "\n", FILE_APPEND);
                return false;
            }

            @file_put_contents($logFile, "[$ts] Email sent successfully via socket!\n", FILE_APPEND);
            return true;
        } catch (Exception $e) {
            @file_put_contents($logFile, "[$ts] Socket error: " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }
}

// Override the original send_email function with our improved version
if (function_exists('send_email')) {
    // Store original function name if needed
    if (!function_exists('send_email_original')) {
        function send_email_original(string $to, string $subject, string $html, string $fromName = 'TasteBud', string $fromEmail = 'chisalaluckyk5@gmail.com', ?string $replyTo = null): bool
        {
            return false;
        }
    }
}

// Define our improved send_email function
if (!function_exists('send_email')) {
    function send_email(string $to, string $subject, string $html, string $fromName = 'TasteBud', string $fromEmail = 'chisalaluckyk5@gmail.com', ?string $replyTo = null): bool
    {
        return send_email_simple($to, $subject, $html, $fromName, $fromEmail, $replyTo);
    }
}
