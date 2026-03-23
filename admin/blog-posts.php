<?php
require_once 'config.php';
require_once '../blog/config.php';
requireLogin();

$success = '';
$error = '';

$blogDb = getDB();

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (verifyCSRFToken($_POST['csrf_token'])) {
        $postId = intval($_POST['post_id']);
        $stmt = $blogDb->prepare("DELETE FROM posts WHERE id = ?");
        if ($stmt->execute([$postId])) {
            $success = 'Blogartikel erfolgreich gelöscht!';
        } else {
            $error = 'Fehler beim Löschen des Artikels.';
        }
    } else {
        $error = 'Ungültiges CSRF Token.';
    }
}

// Get all posts
$stmt = $blogDb->query("SELECT * FROM posts ORDER BY created_at DESC");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog-Artikel verwalten - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
        }

        .header {
            background: linear-gradient(135deg, #4a7c59 0%, #3d6b2e 100%);
            color: white;
            padding: 20px 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 1.5rem;
        }

        .header a {
            color: white;
            text-decoration: none;
            margin-top: 10px;
            display: inline-block;
        }

        .header a:hover {
            text-decoration: underline;
        }

        .container {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .posts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 20px;
        }

        .post-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }

        .post-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .post-header {
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .post-header h3 {
            font-size: 1.25rem;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .post-meta {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .post-content {
            padding: 20px;
        }

        .post-excerpt {
            color: #666;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .post-stats {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: #999;
        }

        .post-stat {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
        }

        .status-publish {
            background: #d4edda;
            color: #155724;
        }

        .status-draft {
            background: #fff3cd;
            color: #856404;
        }

        .post-actions {
            display: flex;
            gap: 10px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }

        .btn {
            padding: 10px 18px;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            flex: 1;
            justify-content: center;
        }

        .btn-view {
            background: #4a7c59;
            color: white;
        }

        .btn-view:hover {
            background: #3d6b2e;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .no-posts {
            text-align: center;
            padding: 60px 20px;
            color: #666;
            background: white;
            border-radius: 12px;
        }

        .no-posts i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .posts-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-newspaper"></i> Blog-Artikel verwalten</h1>
        <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Zurück zum Dashboard</a>
    </div>

    <div class="container">
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <?php if (count($posts) > 0): ?>
            <div class="posts-grid">
                <?php foreach ($posts as $post): ?>
                    <?php
                        $excerpt = strip_tags($post['content']);
                        $excerpt = substr($excerpt, 0, 150) . '...';
                        $wordCount = str_word_count(strip_tags($post['content']));
                        $readTime = ceil($wordCount / 200);
                    ?>
                    <div class="post-card">
                        <div class="post-header">
                            <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                            <div class="post-meta">
                                <i class="fas fa-calendar"></i>
                                <?php echo date('d.m.Y H:i', strtotime($post['created_at'])); ?>
                            </div>
                        </div>
                        <div class="post-content">
                            <p class="post-excerpt"><?php echo htmlspecialchars($excerpt); ?></p>

                            <div class="post-stats">
                                <span class="post-stat">
                                    <i class="fas fa-file-word"></i>
                                    <?php echo number_format($wordCount); ?> Wörter
                                </span>
                                <span class="post-stat">
                                    <i class="fas fa-clock"></i>
                                    ~<?php echo $readTime; ?> Min
                                </span>
                                <span class="status-badge status-<?php echo $post['status']; ?>">
                                    <?php echo $post['status'] === 'publish' ? 'Veröffentlicht' : 'Entwurf'; ?>
                                </span>
                            </div>

                            <div class="post-actions">
                                <a href="../blog/artikel.php?slug=<?php echo htmlspecialchars($post['slug']); ?>"
                                   class="btn btn-view"
                                   target="_blank">
                                    <i class="fas fa-eye"></i>
                                    Ansehen
                                </a>

                                <form method="POST" style="flex: 1;" onsubmit="return confirm('Wirklich löschen?');">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" class="btn btn-danger" style="width: 100%;">
                                        <i class="fas fa-trash"></i>
                                        Löschen
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-posts">
                <i class="fas fa-inbox"></i>
                <h2>Noch keine Blogartikel vorhanden</h2>
                <p>Artikel werden automatisch durch den n8n Workflow erstellt.<br>
                Fügen Sie Keywords hinzu, um den Prozess zu starten.</p>
                <a href="blog-keywords.php" class="btn btn-view" style="margin-top: 20px; display: inline-flex;">
                    <i class="fas fa-tags"></i>
                    Zu den Keywords
                </a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
