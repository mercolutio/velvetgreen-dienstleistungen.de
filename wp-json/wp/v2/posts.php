<?php
// WordPress Compatible Posts API
require_once __DIR__ . '/../../../blog/config.php';

header('Content-Type: application/json');
checkAPIAuth();

$db = getDB();
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

// POST /wp-json/wp/v2/posts (create new post)
if ($method === 'POST' && !preg_match('/\/posts\/\d+/', $path)) {
    // Support both JSON and Form-Data
    $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
    if (strpos($contentType, 'application/json') !== false) {
        $input = json_decode(file_get_contents('php://input'), true);
    } else {
        $input = $_POST;
    }

    if (!isset($input['title']) || empty($input['title'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Title is required']);
        exit;
    }

    if (!isset($input['content']) || empty($input['content'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Content is required']);
        exit;
    }

    $title = $input['title'];
    $content = $input['content'];
    $status = isset($input['status']) ? $input['status'] : 'draft';
    $slug = generateSlug($title);

    // Extract meta description from HTML comment in content
    $metaDescription = '';
    if (preg_match('/<!-- meta description: (.+?) -->/', $content, $matches)) {
        $metaDescription = $matches[1];
        // Remove meta description comment from content
        $content = preg_replace('/<!-- meta description: .+? -->/', '', $content);
    }

    $stmt = $db->prepare("INSERT INTO posts (title, content, slug, meta_description, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $content, $slug, $metaDescription, $status]);

    $postId = $db->lastInsertId();

    echo json_encode([
        'id' => $postId,
        'title' => $title,
        'slug' => $slug,
        'status' => $status,
        'link' => 'https://velvetgreen-dienstleistungen.de/blog/artikel.php?slug=' . $slug
    ]);
    exit;
}

// POST /wp-json/wp/v2/posts/{id} (update post - for Yoast meta description)
if ($method === 'POST' && preg_match('/\/posts\/(\d+)/', $path, $matches)) {
    $postId = $matches[1];

    // Get query parameters
    $yoastDescription = isset($_GET['yoast_description']) ? $_GET['yoast_description'] : '';

    if ($yoastDescription) {
        $stmt = $db->prepare("UPDATE posts SET meta_description = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$yoastDescription, $postId]);
    }

    // Get updated post
    $stmt = $db->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$postId]);
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'id' => $post['id'],
        'title' => $post['title'],
        'slug' => $post['slug'],
        'status' => $post['status'],
        'meta_description' => $post['meta_description']
    ]);
    exit;
}

// GET /wp-json/wp/v2/posts (get all posts)
if ($method === 'GET') {
    $stmt = $db->query("SELECT * FROM posts WHERE status = 'publish' ORDER BY created_at DESC");
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $result = array_map(function($post) {
        return [
            'id' => $post['id'],
            'title' => $post['title'],
            'slug' => $post['slug'],
            'excerpt' => substr(strip_tags($post['content']), 0, 200) . '...',
            'created_at' => $post['created_at'],
            'link' => 'https://velvetgreen-dienstleistungen.de/blog/artikel.php?slug=' . $post['slug']
        ];
    }, $posts);

    echo json_encode($result);
    exit;
}

http_response_code(404);
echo json_encode(['error' => 'Endpoint not found']);
