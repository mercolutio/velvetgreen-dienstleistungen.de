<?php
require_once 'config.php';

$db = getDB();
$stmt = $db->query("SELECT * FROM posts WHERE status = 'publish' ORDER BY created_at DESC");
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo BLOG_TITLE; ?></title>
    <meta name="description" content="<?php echo BLOG_DESCRIPTION; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .blog-header {
            background: linear-gradient(135deg, #4a7c59 0%, #3d6b2e 100%);
            color: white;
            padding: 80px 0 60px;
            text-align: center;
        }

        .blog-header h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .blog-header p {
            font-size: 1.25rem;
            opacity: 0.9;
        }

        .blog-grid {
            max-width: 1200px;
            margin: 60px auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }

        .blog-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }

        .blog-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .blog-card-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #4a7c59 0%, #5a8d6f 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
        }

        .blog-card-content {
            padding: 25px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .blog-card-content h2 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.5rem;
            line-height: 1.4;
        }

        .blog-card-content h2 a {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s;
        }

        .blog-card-content h2 a:hover {
            color: #4a7c59;
        }

        .blog-excerpt {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
            flex: 1;
        }

        .blog-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #eee;
            font-size: 0.9rem;
            color: #999;
        }

        .blog-date {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .blog-read-more {
            color: #4a7c59;
            font-weight: bold;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: gap 0.3s;
        }

        .blog-read-more:hover {
            gap: 10px;
        }

        .back-to-home {
            text-align: center;
            margin: 40px 0;
        }

        .back-to-home a {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            background: linear-gradient(135deg, #4a7c59 0%, #3d6b2e 100%);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .back-to-home a:hover {
            background: linear-gradient(135deg, #fabe5c 0%, #f0a940 100%);
            transform: translateY(-2px);
        }

        .no-posts {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .no-posts i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .blog-grid {
                grid-template-columns: 1fr;
            }

            .blog-header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="blog-header">
        <h1><i class="fas fa-newspaper"></i> <?php echo BLOG_TITLE; ?></h1>
        <p><?php echo BLOG_DESCRIPTION; ?></p>
    </div>

    <div class="blog-grid">
        <?php if (count($posts) > 0): ?>
            <?php foreach ($posts as $post): ?>
                <?php
                    $excerpt = strip_tags($post['content']);
                    $excerpt = substr($excerpt, 0, 200) . '...';
                    $date = date('d.m.Y', strtotime($post['created_at']));
                ?>
                <article class="blog-card">
                    <div class="blog-card-image">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div class="blog-card-content">
                        <h2><a href="artikel.php?slug=<?php echo htmlspecialchars($post['slug']); ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
                        <p class="blog-excerpt"><?php echo htmlspecialchars($excerpt); ?></p>
                        <div class="blog-meta">
                            <span class="blog-date">
                                <i class="fas fa-calendar"></i>
                                <?php echo $date; ?>
                            </span>
                            <a href="artikel.php?slug=<?php echo htmlspecialchars($post['slug']); ?>" class="blog-read-more">
                                Weiterlesen <i class="fas fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-posts">
                <i class="fas fa-inbox"></i>
                <h2>Noch keine Artikel vorhanden</h2>
                <p>Schauen Sie bald wieder vorbei!</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="back-to-home">
        <a href="../index.html">
            <i class="fas fa-home"></i>
            Zurück zur Startseite
        </a>
    </div>
</body>
</html>
