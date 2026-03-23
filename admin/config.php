<?php
// Admin Configuration
session_start();

// Security: Prevent direct access
define('ADMIN_ACCESS', true);

// Admin Credentials (Password is hashed with PHP password_hash)
define('ADMIN_USERNAME', 'deniz');
define('ADMIN_PASSWORD_HASH', '$2y$10$O/pDgN/NVTuA/KVyWPbV8eXF59879bxy4Azs9sj8xhd5R5Pv.gel2');

// Paths
define('CONTENT_FILE', __DIR__ . '/data/content.json');
define('UPLOAD_DIR', __DIR__ . '/../images/');

// Session timeout (30 minutes)
define('SESSION_TIMEOUT', 1800);

// Check if user is logged in
function isLoggedIn() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        return false;
    }

    // Check session timeout
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_unset();
        session_destroy();
        return false;
    }

    $_SESSION['last_activity'] = time();
    return true;
}

// Redirect to login if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}

// Load content from JSON
function loadContent() {
    if (!file_exists(CONTENT_FILE)) {
        return getDefaultContent();
    }
    $json = file_get_contents(CONTENT_FILE);
    return json_decode($json, true);
}

// Save content to JSON
function saveContent($data) {
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    return file_put_contents(CONTENT_FILE, $json);
}

// Get default content structure
function getDefaultContent() {
    return [
        'hero' => [
            'title' => 'Professionelle Entrümpelung & Haushaltsauflösung',
            'subtitle' => 'Schnell, zuverlässig und fair – Ihr Partner für Entrümpelung in ganz Deutschland',
            'cta_text' => 'Jetzt Angebot einholen'
        ],
        'services' => [
            [
                'icon' => 'fa-box-open',
                'title' => 'Entrümpelung',
                'description' => 'Professionelle Entrümpelung von Kellern, Dachböden, Garagen und Wohnungen.'
            ],
            [
                'icon' => 'fa-home',
                'title' => 'Haushaltsauflösung',
                'description' => 'Komplette Wohnungsauflösung mit Wertanrechnung und fachgerechter Entsorgung.'
            ],
            [
                'icon' => 'fa-leaf',
                'title' => 'Gartenarbeiten',
                'description' => 'Gartenpflege, Heckenschnitt, Rasen mähen und Grünschnittentsorgung.'
            ],
            [
                'icon' => 'fa-truck-moving',
                'title' => 'Umzüge',
                'description' => 'Unkomplizierte Umzüge mit professionellem Team und modernem Equipment.'
            ]
        ],
        'contact' => [
            'phone' => '+49 123 456 7890',
            'email' => 'info@velvetgreen-dienstleistungen.de',
            'address' => 'Musterstraße 123, 50321 Brühl'
        ],
        'calculator' => [
            'base_price_per_sqm' => 8,
            'floor_surcharge' => 50,
            'appliance_price' => 15,
            'additional_space_price' => 6,
            'kitchen_keep_surcharge' => 100
        ]
    ];
}

// Sanitize input
function sanitize($input) {
    return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
}

// Generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
