<?php
// Contact Form Handler
header('Content-Type: application/json');

// Security: Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Methode nicht erlaubt.']);
    exit;
}

// Configuration
define('RECIPIENT_EMAIL', 'd.schloesser@mercolutio.com'); // Ändern Sie dies zu Ihrer echten E-Mail-Adresse
define('SUBJECT_PREFIX', 'Kontaktanfrage von Website');

// Mail configuration - Verbesserte Einstellungen für verschiedene Server
ini_set('sendmail_from', 'noreply@velvetgreen-dienstleistungen.de');

// Get and sanitize input data
$name = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
$email = isset($_POST['email']) ? trim(strip_tags($_POST['email'])) : '';
$phone = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : '';
$service = isset($_POST['service']) ? trim(strip_tags($_POST['service'])) : 'Nicht angegeben';
$message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';
$privacy = isset($_POST['privacy']) ? $_POST['privacy'] === 'true' : false;

// Validation
$errors = [];

if (empty($name) || strlen($name) < 2) {
    $errors[] = 'Bitte geben Sie einen gültigen Namen ein.';
}

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
}

if (empty($phone) || strlen($phone) < 5) {
    $errors[] = 'Bitte geben Sie eine gültige Telefonnummer ein.';
}

if (empty($message) || strlen($message) < 10) {
    $errors[] = 'Bitte geben Sie eine Nachricht ein (mindestens 10 Zeichen).';
}

if (!$privacy) {
    $errors[] = 'Bitte akzeptieren Sie die Datenschutzerklärung.';
}

// Return errors if validation failed
if (!empty($errors)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => implode(' ', $errors)
    ]);
    exit;
}

// Simple spam protection: Check for honeypot field (optional)
if (isset($_POST['website']) && !empty($_POST['website'])) {
    // Honeypot field filled - likely spam
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Spam erkannt.'
    ]);
    exit;
}

// Service types mapping
$serviceTypes = [
    'haushaltsaufloesung' => 'Haushaltsauflösung',
    'entruempelung' => 'Entrümpelung',
    'entsorgung' => 'Entsorgung',
    'reinigung' => 'Reinigung',
    'transport' => 'Transport',
    'gewerbe' => 'Gewerbe'
];

$serviceLabel = isset($serviceTypes[$service]) ? $serviceTypes[$service] : $service;

// Prepare email content
$emailSubject = SUBJECT_PREFIX . ' - ' . $serviceLabel;

$emailBody = "Neue Kontaktanfrage über die Website\n\n";
$emailBody .= "═══════════════════════════════════════\n\n";
$emailBody .= "Name: " . $name . "\n";
$emailBody .= "E-Mail: " . $email . "\n";
$emailBody .= "Telefon: " . $phone . "\n";
$emailBody .= "Gewünschte Leistung: " . $serviceLabel . "\n\n";
$emailBody .= "Nachricht:\n" . $message . "\n\n";
$emailBody .= "═══════════════════════════════════════\n\n";
$emailBody .= "Eingegangen am: " . date('d.m.Y H:i:s') . "\n";
$emailBody .= "IP-Adresse: " . $_SERVER['REMOTE_ADDR'] . "\n";

// Email headers - Verbessert für bessere Zustellbarkeit
$headers = [];
$headers[] = 'From: Velvetgreen Dienstleistungen <noreply@velvetgreen-dienstleistungen.de>';
$headers[] = 'Reply-To: ' . $email;
$headers[] = 'X-Mailer: PHP/' . phpversion();
$headers[] = 'Content-Type: text/plain; charset=UTF-8';
$headers[] = 'MIME-Version: 1.0';

// Additional parameters for mail function
$additional_parameters = '-f noreply@velvetgreen-dienstleistungen.de';

// Send email
$mailSent = @mail(
    RECIPIENT_EMAIL,
    $emailSubject,
    $emailBody,
    implode("\r\n", $headers),
    $additional_parameters
);

if ($mailSent) {
    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'Vielen Dank! Ihre Anfrage wurde erfolgreich versendet. Wir melden uns innerhalb von 24 Stunden bei Ihnen.'
    ]);

    // Optional: Log successful submissions
    $logEntry = date('Y-m-d H:i:s') . " - Kontaktanfrage von: $name ($email)\n";
    @file_put_contents(__DIR__ . '/contact-log.txt', $logEntry, FILE_APPEND);

} else {
    // Error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Es gab ein Problem beim Versenden Ihrer Nachricht. Bitte versuchen Sie es später erneut oder kontaktieren Sie uns direkt per Telefon.'
    ]);
}
