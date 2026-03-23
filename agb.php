<?php
// Load content
$contentFile = __DIR__ . '/admin/data/content.json';
$content = json_decode(file_get_contents($contentFile), true);
$agb = $content['legal']['agb'] ?? '';
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AGB - Velvetgreen Dienstleistungen</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .legal-page {
            max-width: 900px;
            margin: 100px auto 60px;
            padding: 40px 20px;
        }
        .legal-page h1 {
            color: #4a7c59;
            font-size: 2.5rem;
            margin-bottom: 30px;
        }
        .legal-content {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .legal-content h2 {
            color: #4a7c59;
            margin: 30px 0 15px 0;
            font-size: 1.5rem;
        }
        .legal-content p {
            line-height: 1.8;
            margin-bottom: 15px;
            color: #555;
            white-space: pre-wrap;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 30px;
            color: #4a7c59;
            text-decoration: none;
            font-weight: 600;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="legal-page">
        <a href="index.html" class="back-link">
            <i class="fas fa-arrow-left"></i> Zurück zur Startseite
        </a>
        
        <h1>Allgemeine Geschäftsbedingungen (AGB)</h1>
        
        <div class="legal-content">
            <p><?php echo nl2br(htmlspecialchars($agb)); ?></p>
        </div>
    </div>
</body>
</html>
