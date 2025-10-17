<?php declare(strict_types=1);
// (Décommente pendant les tests si besoin)
// ini_set('display_errors', '1');
// error_reporting(E_ALL);

$cfg = require __DIR__ . '/config.php';

/* ----------------- Helpers ----------------- */
function go(string $page): never {
  header('Location: ' . $page, true, 302);
  exit;
}
function html(string $s): string {
  return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function getClientIp(): string {
  foreach (['HTTP_CF_CONNECTING_IP','HTTP_X_FORWARDED_FOR','HTTP_X_REAL_IP','REMOTE_ADDR'] as $h) {
    if (!empty($_SERVER[$h])) return trim(explode(',', $_SERVER[$h])[0]);
  }
  return '';
}
/* ------------------------------------------- */

/* 1) Anti-bot (honeypot) */
if (!empty($_POST['hp_field'])) {
  // On ne révèle pas qu’on a bloqué : on redirige comme si c’était OK
  go('contact-ok.html');
}

/* 2) Récupération / validation */
$fullname = mb_substr(trim((string)($_POST['fullname'] ?? '')), 0, 120);
$email    = mb_substr(trim((string)($_POST['email'] ?? '')),    0, 160);
$company  = mb_substr(trim((string)($_POST['company'] ?? '')),  0, 160);
$subject  = mb_substr(trim((string)($_POST['subject'] ?? '')),  0, 160);
$message  = mb_substr(trim((string)($_POST['message'] ?? '')),  0, 4000);

if ($email === '' || $message === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  go('contact-erreur.html');
}

$ip        = getClientIp();
$userAgent = mb_substr((string)($_SERVER['HTTP_USER_AGENT'] ?? ''), 0, 255);

/* 3) Enregistrement MySQL */
try {
  $dsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
    $cfg['db']['host'],
    (int)$cfg['db']['port'],
    $cfg['db']['name'],
    $cfg['db']['charset']
  );
  $pdo = new PDO($dsn, $cfg['db']['user'], $cfg['db']['pass'], [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);

  $stmt = $pdo->prepare('
    INSERT INTO contacts (fullname, email, company, subject, message, ip, user_agent)
    VALUES (:fullname, :email, :company, :subject, :message, :ip, :user_agent)
  ');
  $stmt->execute([
    ':fullname'   => $fullname ?: null,
    ':email'      => $email,
    ':company'    => $company ?: null,
    ':subject'    => $subject ?: null,
    ':message'    => $message,
    ':ip'         => $ip,
    ':user_agent' => $userAgent,
  ]);

} catch (Throwable $e) {
  error_log('[CONTACT][DB] '.$e->getMessage());
  go('contact-erreur.html');
}

/* 4) Envoi mail via PHPMailer (n’empêche jamais la réussite BDD) */
try {
  // Tu as installé PHPMailer via Composer (vendor/)
  require __DIR__ . '/vendor/autoload.php';

  $to   = $cfg['mail_to'] ?? 'contact@chourak-consulting.com';
  $smtp = $cfg['smtp'];

  $mail = new PHPMailer\PHPMailer\PHPMailer(true);
  $mail->isSMTP();
  $mail->Host       = $smtp['host'];
  $mail->Port       = (int)$smtp['port'];
  $mail->SMTPAuth   = true;
  $mail->Username   = $smtp['user'];
  $mail->Password   = $smtp['pass'];
  if (!empty($smtp['secure'])) $mail->SMTPSecure = $smtp['secure'];

  $mail->CharSet = 'UTF-8';
  $mail->setFrom($smtp['user'], 'Site Chourak Consulting');
  $mail->addAddress($to);
  if ($email) $mail->addReplyTo($email, $fullname ?: $email);

  $subjectMail = $subject !== '' ? $subject : 'Nouveau message de contact';
  $mail->Subject = $subjectMail;

  $html  = '<h2>Nouvelle demande de contact</h2>';
  $html .= '<p><strong>Nom :</strong> '.html($fullname).'</p>';
  $html .= '<p><strong>Email :</strong> '.html($email).'</p>';
  $html .= '<p><strong>Entreprise :</strong> '.html($company).'</p>';
  $html .= '<p><strong>Sujet :</strong> '.html($subject).'</p>';
  $html .= '<p><strong>Message :</strong><br>'.nl2br(html($message)).'</p>';
  $html .= '<hr><p><small>IP: '.html($ip).' — UA: '.html($userAgent).'</small></p>';

  $mail->isHTML(true);
  $mail->Body    = $html;
  $mail->AltBody = "Nom: $fullname\nEmail: $email\nEntreprise: $company\nSujet: $subject\n\nMessage:\n$message\n\nIP: $ip\nUA: $userAgent";

  $mail->send();
} catch (Throwable $e) {
  // on log seulement, on n’arrête pas le flux (le lead est déjà en BDD)
  error_log('[CONTACT][MAIL] '.$e->getMessage());
}

/* 5) Succès → page dédiée */
go('contact-ok.html');
