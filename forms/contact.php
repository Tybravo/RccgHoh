<?php
  header('Content-Type: text/plain; charset=utf-8');

  if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Method Not Allowed';
    exit;
  }

  $config = [];
  $configFile = __DIR__ . '/mail-config.php';
  if (file_exists($configFile)) {
    $loaded = include $configFile;
    if (is_array($loaded)) {
      $config = $loaded; 
    }
  }

  $name = trim((string)($_POST['name'] ?? ''));
  $email = trim((string)($_POST['email'] ?? ''));
  $subject = trim((string)($_POST['subject'] ?? ''));
  $message = trim((string)($_POST['message'] ?? ''));

  if ($name === '' || $email === '' || $subject === '' || $message === '') {
    http_response_code(400);
    echo 'Please fill in all required fields.';
    exit;
  }

  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo 'Please enter a valid email address.';
    exit;
  }

  if (mb_strlen($name) > 200 || mb_strlen($email) > 254 || mb_strlen($subject) > 200) {
    http_response_code(400);
    echo 'One or more fields are too long.';
    exit;
  }

  if (mb_strlen($message) > 5000) {
    http_response_code(400);
    echo 'Message is too long.';
    exit;
  }

  $to = (string)($config['to'] ?? 'info@rccghabitationofhope.org');
  $from = (string)($config['from'] ?? 'info@rccghabitationofhope.org');
  $fromName = (string)($config['from_name'] ?? '');
  $siteName = $fromName !== '' ? $fromName : 'Website';
  $displayFromName = $name . ' via ' . $siteName;

  $safeSubject = preg_replace("/[\r\n]+/", ' ', $subject);

  $bodyLines = [
    'You have a new contact form submission.',
    '',
    'Name: ' . $name,
    'Email: ' . $email,
    'Subject: ' . $safeSubject,
    '',
    'Message:',
    $message,
  ];
  $body = implode("\n", $bodyLines);
  $body = wordwrap($body, 70);

  $transport = strtolower((string)($config['transport'] ?? 'mail'));
  if ($transport === 'smtp') {
    $smtpHost = (string)($config['host'] ?? '');
    $smtpUsername = (string)($config['username'] ?? '');
    $smtpPassword = (string)($config['password'] ?? '');
    $smtpPort = (int)($config['port'] ?? 0);
    $smtpEncryption = strtolower((string)($config['encryption'] ?? ''));

    if ($smtpHost !== '' && $smtpUsername !== '' && $smtpPassword !== '' && $smtpPort > 0) {
      $phpMailerPath = dirname(__DIR__) . '/class.phpmailer.php';
      $smtpPath = dirname(__DIR__) . '/class.smtp.php';

      if (file_exists($phpMailerPath) && file_exists($smtpPath)) {
        require_once $phpMailerPath;
        require_once $smtpPath;

        $mail = new PHPMailer();
        $mail->CharSet = 'UTF-8';
        $mail->IsSMTP();
        $mail->Host = $smtpHost;
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUsername;
        $mail->Password = $smtpPassword;

        if ($smtpEncryption !== '') {
          $mail->SMTPSecure = $smtpEncryption;
        }

        $mail->Port = $smtpPort;

        $mail->From = $from;
        $mail->FromName = $displayFromName;
        $mail->AddAddress($to);
        $mail->AddReplyTo($email, $name);

        $mail->Subject = $safeSubject;
        $mail->Body = $body;

        if ($mail->Send()) {
          echo 'OK';
          exit;
        }

        http_response_code(500);
        echo $mail->ErrorInfo !== '' ? $mail->ErrorInfo : 'Failed to send email.';
        exit;
      }
    }
  }

  $headers = [];
  $headers[] = 'From: ' . $displayFromName . ' <' . $from . '>';
  $headers[] = 'Reply-To: ' . $email;
  $headers[] = 'MIME-Version: 1.0';
  $headers[] = 'Content-Type: text/plain; charset=UTF-8';

  $ok = @mail($to, $safeSubject, $body, implode("\r\n", $headers));

  if ($ok) {
    echo 'OK';
    exit;
  }

  http_response_code(500);
  echo 'Failed to send email.';
?>
