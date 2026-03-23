<?php
// Keyword Manager API - WordPress Compatible Endpoint
require_once __DIR__ . '/../../../blog/config.php';

header('Content-Type: application/json');
checkAPIAuth();

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

// GET /wp-json/keyword-manager/v1/keywords/unprocessed
if ($method === 'GET' && strpos($path, '/unprocessed') !== false) {
    $stmt = $db->prepare("SELECT * FROM keywords WHERE processed = 0 ORDER BY created_at ASC LIMIT 1");
    $stmt->execute();
    $keywords = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($keywords);
    exit;
}

// POST /wp-json/keyword-manager/v1/keywords/{id}/process
if ($method === 'POST' && preg_match('/\/keywords\/(\d+)\/process/', $path, $matches)) {
    $keywordId = $matches[1];

    $stmt = $db->prepare("UPDATE keywords SET processed = 1, processed_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->execute([$keywordId]);

    echo json_encode(['success' => true, 'message' => 'Keyword marked as processed']);
    exit;
}

// GET /wp-json/keyword-manager/v1/keywords (all keywords)
if ($method === 'GET') {
    $stmt = $db->query("SELECT * FROM keywords ORDER BY created_at DESC");
    $keywords = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($keywords);
    exit;
}

// POST /wp-json/keyword-manager/v1/keywords (create new keyword)
if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['keyword']) || empty($input['keyword'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Keyword is required']);
        exit;
    }

    $stmt = $db->prepare("INSERT INTO keywords (keyword) VALUES (?)");
    $stmt->execute([$input['keyword']]);

    echo json_encode([
        'id' => $db->lastInsertId(),
        'keyword' => $input['keyword'],
        'processed' => 0
    ]);
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'Endpoint not found']);
