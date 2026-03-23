<?php
// Blog Configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database Configuration
define('DB_FILE', __DIR__ . '/data/blog.db');
define('BLOG_TITLE', 'Velvetgreen Dienstleistungen Blog');
define('BLOG_DESCRIPTION', 'Tipps und Ratgeber zu Entrümpelung, Haushaltsauflösung und mehr');

// API Authentication
define('API_USERNAME', 'api_user');
// Hash für "Bedburg181." - generiert mit password_hash()
define('API_PASSWORD_HASH', '$2y$10$O/pDgN/NVTuA/KVyWPbV8eXF59879bxy4Azs9sj8xhd5R5Pv.gel2');

// Initialize Database
function initDatabase() {
    if (!file_exists(dirname(DB_FILE))) {
        mkdir(dirname(DB_FILE), 0755, true);
    }

    $db = new PDO('sqlite:' . DB_FILE);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create posts table
    $db->exec("CREATE TABLE IF NOT EXISTS posts (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        content TEXT NOT NULL,
        slug TEXT UNIQUE NOT NULL,
        meta_description TEXT,
        status TEXT DEFAULT 'draft',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Create keywords table
    $db->exec("CREATE TABLE IF NOT EXISTS keywords (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        keyword TEXT NOT NULL,
        processed INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        processed_at DATETIME
    )");

    return $db;
}

// Get database connection
function getDB() {
    static $db = null;
    if ($db === null) {
        $db = initDatabase();
    }
    return $db;
}

// Sanitize input
if (!function_exists('sanitize')) {
    function sanitize($input) {
        return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
    }
}

// Generate slug from title
function generateSlug($title) {
    $slug = strtolower($title);
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    $slug = trim($slug, '-');

    // Ensure uniqueness
    $db = getDB();
    $originalSlug = $slug;
    $counter = 1;

    while (true) {
        $stmt = $db->prepare("SELECT id FROM posts WHERE slug = ?");
        $stmt->execute([$slug]);

        if (!$stmt->fetch()) {
            break;
        }

        $slug = $originalSlug . '-' . $counter;
        $counter++;
    }

    return $slug;
}

// Check API authentication
function checkAPIAuth() {
    // Apache might pass Authorization header differently in CGI/FastCGI mode
    if (!isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $auth = base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6));
        list($_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW']) = explode(':', $auth, 2);
    }

    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
        header('WWW-Authenticate: Basic realm="Blog API"');
        http_response_code(401);
        echo json_encode(['error' => 'Authentication required']);
        exit;
    }

    if ($_SERVER['PHP_AUTH_USER'] !== API_USERNAME ||
        !password_verify($_SERVER['PHP_AUTH_PW'], API_PASSWORD_HASH)) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
        exit;
    }
}

// Check admin login
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: ../admin/login.php');
        exit;
    }
}
