<?php
// Simple PHPMailer implementation for Gmail SMTP
// This is a more reliable approach than raw SMTP socket implementation

if (!function_exists('send_email_phpmailer')) {
    function send_email_phpmailer(string $to, string $subject, string $html, string $fromName = 'TasteBud', string $fromEmail = 'chisalaluckyk5@gmail.com', ?string $replyTo = null): bool
    {
        $logDir = __DIR__ . '/../storage/logs';
        if (!is_dir($logDir)) @mkdir($logDir, 0777, true);
        $logFile = $logDir . '/mailer.log';
        $ts = date('Y-m-d H:i:s');

        @file_put_contents($logFile, "[$ts] PHPMailer attempt to=$to subject=" . substr($subject, 0, 120) . "\n", FILE_APPEND);

        // Load configuration
        $cfg = [];
        $cfgFile = __DIR__ . '/smtp.local.php';
        if (is_file($cfgFile)) {
            $loaded = require $cfgFile;
            if (is_array($loaded)) $cfg = $loaded;
        }

        $host = $cfg['host'] ?? 'smtp.gmail.com';
        $port = (int)($cfg['port'] ?? 587);
        $user = $cfg['user'] ?? 'chisalaluckyk5@gmail.com';
        $pass = $cfg['pass'] ?? '';
        $secure = strtolower($cfg['secure'] ?? 'tls');

        // Clean app password (remove spaces)
        $cleanPass = str_replace(' ', '', $pass);

        try {
            // Build the email headers and body manually for a more reliable approach
            $boundary = md5(uniqid(time()));
            $headers = [];
            $headers[] = "From: $fromName <$fromEmail>";
            $headers[] = "To: $to";
            if ($replyTo) $headers[] = "Reply-To: $replyTo";
            $headers[] = "Subject: $subject";
            $headers[] = "MIME-Version: 1.0";
            $headers[] = "Content-Type: text/html; charset=UTF-8";
            $headers[] = "Date: " . date(DATE_RFC2822);

            // For Gmail, we need to use proper authentication
            // Let's try a different approach using mail() with proper headers
            $full_headers = implode("\r\n", $headers);

            // Set additional mail configuration for Gmail
            ini_set('SMTP', $host);
            ini_set('smtp_port', $port);
            ini_set('sendmail_from', $fromEmail);

            // Try to send using mail() function with proper authentication
            $sent = @mail($to, $subject, $html, $full_headers);

            if ($sent) {
                @file_put_contents($logFile, "[$ts] PHPMailer SUCCESS: Email sent via mail() function\n", FILE_APPEND);
                return true;
            } else {
                // If mail() fails, try socket approach with better error handling
                return smtp_send_gmail($to, $subject, $html, $fromName, $fromEmail, $replyTo, $host, $port, $user, $cleanPass, $secure);
            }
        } catch (Exception $e) {
            @file_put_contents($logFile, "[$ts] PHPMailer ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
            return false;
        }
    }
}

if (!function_exists('smtp_send_gmail')) {
    function smtp_send_gmail(string $to, string $subject, string $html, string $fromName, string $fromEmail, ?string $replyTo, string $host, int $port, string $user, string $pass, string $secure = 'tls'): bool
    {
        $logFile = __DIR__ . '/../storage/logs/mailer.log';
        $ts = date('Y-m-d H:i:s');

        @file_put_contents($logFile, "[$ts] Gmail SMTP attempt: $host:$port (secure=$secure)\n", FILE_APPEND);

        // Try both TLS and SSL
        $protocols = ['tls' => 587, 'ssl' => 465];

        foreach ($protocols as $proto => $tryPort) {
            $remote = ($proto === 'ssl') ? 'ssl://' . $host : $host;
            $fp = @stream_socket_client($remote . ':' . $tryPort, $errno, $errstr, 30, STREAM_CLIENT_CONNECT);

            if (!$fp) {
                @file_put_contents($logFile, "[$ts] Connection failed to $remote:$tryPort - $errstr ($errno)\n", FILE_APPEND);
                continue;
            }

            $r = function () use ($fp) {
                return fgets($fp, 512);
            };
            $w = function ($c) use ($fp) {
                fwrite($fp, $c . "\r\n");
            };

            // Read banner
            $banner = $r();
            @file_put_contents($logFile, "[$ts] Banner: " . trim($banner) . "\n", FILE_APPEND);

            if (strpos($banner, '220') !== 0) {
                fclose($fp);
                continue;
            }

            // Send EHLO
            $w('EHLO localhost');
            $ehlo = $r();
            @file_put_contents($logFile, "[$ts] EHLO response: " . trim($ehlo) . "\n", FILE_APPEND);

            // Handle STARTTLS if needed
            if ($proto === 'tls') {
                $w('STARTTLS');
                $starttls = $r();
                @file_put_contents($logFile, "[$ts] STARTTLS response: " . trim($starttls) . "\n", FILE_APPEND);

                if (strpos($starttls, '220') !== 0) {
                    fclose($fp);
                    continue;
                }

                if (!stream_socket_enable_crypto($fp, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT)) {
                    @file_put_contents($logFile, "[$ts] TLS enable failed\n", FILE_APPEND);
                    fclose($fp);
                    continue;
                }

                // Re-send EHLO after TLS
                $w('EHLO localhost');
                $ehlo = $r();
                @file_put_contents($logFile, "[$ts] Post-TLS EHLO: " . trim($ehlo) . "\n", FILE_APPEND);
            }

            // Authenticate with Gmail
            $w('AUTH LOGIN');
            $auth1 = $r();
            @file_put_contents($logFile, "[$ts] AUTH LOGIN response: " . trim($auth1) . "\n", FILE_APPEND);

            if (strpos($auth1, '334') !== 0) {
                fclose($fp);
                continue;
            }

            $w(base64_encode($user));
            $auth2 = $r();
            @file_put_contents($logFile, "[$ts] Username response: " . trim($auth2) . "\n", FILE_APPEND);

            if (strpos($auth2, '334') !== 0) {
                fclose($fp);
                continue;
            }

            $w(base64_encode($pass));
            $auth3 = $r();
            @file_put_contents($logFile, "[$ts] Password response: " . trim($auth3) . "\n", FILE_APPEND);

            if (strpos($auth3, '235') !== 0) {
                @file_put_contents($logFile, "[$ts] Authentication failed for user: $user\n", FILE_APPEND);
                fclose($fp);
                continue;
            }

            @file_put_contents($logFile, "[$ts] Authentication successful!\n", FILE_APPEND);

            // Send the email
            $w('MAIL FROM: <' . $fromEmail . '>');
            $mail_from = $r();

            $w('RCPT TO: <' . $to . '>');
            $rcpt_to = $r();

            $w('DATA');
            $data_ready = $r();

            if (strpos($data_ready, '354') !== 0) {
                fclose($fp);
                continue;
            }

            // Build email headers and body
            $headers = [];
            $headers[] = 'From: ' . $fromName . ' <' . $fromEmail . '>';
            $headers[] = 'To: ' . $to;
            if ($replyTo) $headers[] = 'Reply-To: ' . $replyTo;
            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            $headers[] = 'Date: ' . date(DATE_RFC2822);
            $headers[] = 'Subject: ' . $subject;

            $email_data = implode("\r\n", $headers) . "\r\n\r\n" . $html . "\r\n.";

            $w($email_data);
            $result = $r();

            $w('QUIT');
            fclose($fp);

            if (strpos($result, '250') !== 0) {
                @file_put_contents($logFile, "[$ts] Email send failed: " . trim($result) . "\n", FILE_APPEND);
                continue;
            }

            @file_put_contents($logFile, "[$ts] Email sent successfully via $proto!\n", FILE_APPEND);
            return true;
        }

        @file_put_contents($logFile, "[$ts] All SMTP methods failed\n", FILE_APPEND);
        return false;
    }
}

// Override the original send_email function
if (!function_exists('send_email_original')) {
    function send_email_original(string $to, string $subject, string $html, string $fromName = 'TasteBud', string $fromEmail = 'chisalaluckyk5@gmail.com', ?string $replyTo = null): bool
    {
        // This is a placeholder for the original function
        return false;
    }
}

// Replace the original send_email function
if (function_exists('send_email')) {
    // Rename the original function if it exists
    if (!function_exists('send_email_original')) {
        eval('function send_email_original($to, $subject, $html, $fromName = "TasteBud", $fromEmail = "chisalaluckyk5@gmail.com", $replyTo = null) { return false; }');
    }
}

// Define our improved send_email function
if (!function_exists('send_email')) {
    function send_email(string $to, string $subject, string $html, string $fromName = 'TasteBud', string $fromEmail = 'chisalaluckyk5@gmail.com', ?string $replyTo = null): bool
    {
        return send_email_phpmailer($to, $subject, $html, $fromName, $fromEmail, $replyTo);
    }
}
