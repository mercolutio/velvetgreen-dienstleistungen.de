<?php
require_once 'config.php';

// Get slug from URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($slug)) {
    header('Location: index.php');
    exit;
}

// Get post from database
$db = getDB();
$stmt = $db->prepare("SELECT * FROM posts WHERE slug = ? AND status = 'publish'");
$stmt->execute([$slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    header('HTTP/1.0 404 Not Found');
    echo '404 - Artikel nicht gefunden';
    exit;
}

$title = htmlspecialchars($post['title']);
$metaDescription = $post['meta_description'] ?: substr(strip_tags($post['content']), 0, 160);
$date = date('d.m.Y', strtotime($post['created_at']));
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?> | <?php echo BLOG_TITLE; ?></title>
    <meta name="description" content="<?php echo htmlspecialchars($metaDescription); ?>">

    <!-- Open Graph / Social Media -->
    <meta property="og:type" content="article">
    <meta property="og:title" content="<?php echo $title; ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($metaDescription); ?>">
    <meta property="og:url" content="https://velvetgreen-dienstleistungen.de/blog/artikel.php?slug=<?php echo $slug; ?>">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .article-header {
            background: linear-gradient(135deg, #4a7c59 0%, #3d6b2e 100%);
            color: white;
            padding: 100px 0 60px;
            text-align: center;
        }

        .article-title {
            font-size: 2.5rem;
            max-width: 900px;
            margin: 0 auto 20px;
            line-height: 1.3;
            padding: 0 20px;
        }

        .article-meta {
            display: flex;
            gap: 30px;
            justify-content: center;
            align-items: center;
            opacity: 0.9;
            font-size: 1rem;
        }

        .article-meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .article-container {
            max-width: 900px;
            margin: 60px auto;
            padding: 0 20px;
        }

        .article-content {
            background: white;
            padding: 50px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            line-height: 1.8;
            font-size: 1.1rem;
            color: #333;
        }

        .article-content h1,
        .article-content h2,
        .article-content h3 {
            color: #4a7c59;
            margin-top: 40px;
            margin-bottom: 20px;
            line-height: 1.3;
        }

        .article-content h1 {
            font-size: 2.5rem;
            border-bottom: 3px solid #fabe5c;
            padding-bottom: 15px;
        }

        .article-content h2 {
            font-size: 2rem;
        }

        .article-content h3 {
            font-size: 1.5rem;
        }

        .article-content p {
            margin-bottom: 20px;
        }

        .article-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 30px 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .article-content ul,
        .article-content ol {
            margin: 25px 0;
            padding-left: 30px;
        }

        .article-content li {
            margin-bottom: 12px;
        }

        .article-content blockquote {
            background: #f8f9fa;
            border-left: 4px solid #4a7c59;
            padding: 20px 30px;
            margin: 30px 0;
            border-radius: 8px;
            font-style: italic;
        }

        .article-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }

        .article-content table th,
        .article-content table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .article-content table th {
            background: #4a7c59;
            color: white;
            font-weight: bold;
        }

        .article-content table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .article-content a {
            color: #4a7c59;
            text-decoration: underline;
        }

        .article-content a:hover {
            color: #fabe5c;
        }

        .article-navigation {
            display: flex;
            gap: 20px;
            justify-content: center;
            margin: 40px 0;
        }

        .article-navigation a {
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

        .article-navigation a:hover {
            background: linear-gradient(135deg, #fabe5c 0%, #f0a940 100%);
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .article-title {
                font-size: 1.8rem;
            }

            .article-content {
                padding: 30px 20px;
                font-size: 1rem;
            }

            .article-content h1 {
                font-size: 1.8rem;
            }

            .article-content h2 {
                font-size: 1.5rem;
            }

            .article-meta {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="article-header">
        <h1 class="article-title"><?php echo $title; ?></h1>
        <div class="article-meta">
            <span class="article-meta-item">
                <i class="fas fa-calendar"></i>
                <?php echo $date; ?>
            </span>
            <span class="article-meta-item">
                <i class="fas fa-clock"></i>
                <?php echo ceil(str_word_count(strip_tags($post['content'])) / 200); ?> Min. Lesezeit
            </span>
        </div>
    </div>

    <div class="article-container">
        <article class="article-content">
            <?php echo $post['content']; ?>
        </article>

        <div class="article-navigation">
            <a href="index.php">
                <i class="fas fa-list"></i>
                Alle Artikel
            </a>
            <a href="../index.html">
                <i class="fas fa-home"></i>
                Zur Startseite
            </a>
        </div>
    </div>
</body>
</html>
