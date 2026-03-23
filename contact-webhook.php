<?php
// Contact Form Handler with n8n Webhook Integration
header('Content-Type: application/json');

// Security: Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Methode nicht erlaubt.']);
    exit;
}

// Configuration
define('N8N_WEBHOOK_URL', 'https://n8n.mercolutio.eu/webhook/velvetgreen-contact');

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

// Email is optional - only validate if provided
if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
}

if (empty($phone) || strlen($phone) < 5) {
    $errors[] = 'Bitte geben Sie eine gültige Telefonnummer ein.';
}

// Message is optional - no validation required

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

// Prepare payload for n8n webhook
$payload = [
    'name' => $name,
    'email' => !empty($email) ? $email : 'Nicht angegeben',
    'phone' => $phone,
    'service' => $service,
    'serviceLabel' => $serviceLabel,
    'message' => !empty($message) ? $message : 'Keine Nachricht',
    'timestamp' => date('Y-m-d H:i:s'),
    'ip' => $_SERVER['REMOTE_ADDR'],
    'userAgent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
    'source' => 'velvetgreen-website'
];

// Send to n8n webhook via cURL
$ch = curl_init(N8N_WEBHOOK_URL);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// Log the submission
$emailLog = !empty($email) ? $email : 'keine';
$logEntry = date('Y-m-d H:i:s') . " | Name: $name | Email: $emailLog | Service: $serviceLabel | HTTP: $httpCode";
if ($curlError) {
    $logEntry .= " | Error: $curlError";
}
$logEntry .= "\n";
@file_put_contents(__DIR__ . '/webhook-log.txt', $logEntry, FILE_APPEND);

// Check if webhook call was successful
if ($httpCode >= 200 && $httpCode < 300) {
    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'Vielen Dank! Ihre Anfrage wurde erfolgreich versendet. Wir melden uns innerhalb von 24 Stunden bei Ihnen.'
    ]);
} else {
    // Error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Es gab ein Problem beim Versenden Ihrer Nachricht. Bitte versuchen Sie es später erneut oder kontaktieren Sie uns direkt per Telefon.'
    ]);
}
