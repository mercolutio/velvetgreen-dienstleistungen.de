<?php
require_once 'config.php';
requireLogin();

$content = loadContent();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Velvetgreen Dienstleistungen</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 1.5rem;
        }

        .header-right {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-logout {
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid white;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s;
            font-weight: bold;
        }

        .btn-logout:hover {
            background: white;
            color: #4a7c59;
        }

        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .welcome {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .welcome h2 {
            color: #4a7c59;
            margin-bottom: 10px;
        }

        .welcome p {
            color: #666;
        }

        .admin-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .admin-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            text-decoration: none;
            color: #333;
            border-top: 4px solid #4a7c59;
        }

        .admin-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }

        .admin-card .icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #4a7c59 0%, #5a8d6f 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        .admin-card .icon i {
            font-size: 1.8rem;
            color: white;
        }

        .admin-card h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .admin-card p {
            color: #666;
            font-size: 0.9rem;
        }

        .admin-card .arrow {
            margin-top: 15px;
            color: #4a7c59;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
        <div class="header-right">
            <div class="user-info">
                <i class="fas fa-user-circle" style="font-size: 1.5rem;"></i>
                <span>Hallo, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>!</span>
            </div>
            <a href="logout.php" class="btn-logout">
                <i class="fas fa-sign-out-alt"></i> Abmelden
            </a>
        </div>
    </div>

    <div class="container">
        <div class="welcome">
            <h2>Willkommen im Admin-Bereich!</h2>
            <p>Hier können Sie alle Inhalte Ihrer Website bearbeiten. Wählen Sie einen Bereich aus:</p>
        </div>

        <div class="admin-grid">
            <a href="edit-hero.php" class="admin-card">
                <div class="icon">
                    <i class="fas fa-star"></i>
                </div>
                <h3>Hero-Bereich</h3>
                <p>Hauptüberschrift und Call-to-Action Text bearbeiten</p>
                <div class="arrow">
                    Bearbeiten <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="edit-services.php" class="admin-card">
                <div class="icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <h3>Leistungen</h3>
                <p>Die 4 Service-Karten anpassen (Titel, Text, Icons)</p>
                <div class="arrow">
                    Bearbeiten <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="edit-contact.php" class="admin-card">
                <div class="icon">
                    <i class="fas fa-address-book"></i>
                </div>
                <h3>Kontaktdaten</h3>
                <p>Telefon, E-Mail und Adresse aktualisieren</p>
                <div class="arrow">
                    Bearbeiten <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="edit-calculator.php" class="admin-card">
                <div class="icon">
                    <i class="fas fa-calculator"></i>
                </div>
                <h3>Kostenrechner</h3>
                <p>Basispreise und Zuschläge anpassen</p>
                <div class="arrow">
                    Bearbeiten <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="edit-notifications.php" class="admin-card">
                <div class="icon" style="background: linear-gradient(135deg, #fabe5c 0%, #f0a940 100%);">
                    <i class="fas fa-bell"></i>
                </div>
                <h3>Social Proof Benachrichtigungen</h3>
                <p>Popup-Benachrichtigungen verwalten (unten links)</p>
                <div class="arrow">
                    Bearbeiten <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="edit-legal.php" class="admin-card">
                <div class="icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <h3>Rechtliche Seiten</h3>
                <p>Impressum, Datenschutz und AGB bearbeiten</p>
                <div class="arrow">
                    Bearbeiten <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="blog-keywords.php" class="admin-card">
                <div class="icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-tags"></i>
                </div>
                <h3>Blog Keywords</h3>
                <p>Keywords für automatische Blogartikel verwalten</p>
                <div class="arrow">
                    Verwalten <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="blog-posts.php" class="admin-card">
                <div class="icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="fas fa-newspaper"></i>
                </div>
                <h3>Blog-Artikel</h3>
                <p>Blogartikel ansehen und verwalten</p>
                <div class="arrow">
                    Verwalten <i class="fas fa-arrow-right"></i>
                </div>
            </a>

            <a href="../blog/index.php" class="admin-card" target="_blank">
                <div class="icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-blog"></i>
                </div>
                <h3>Blog anzeigen</h3>
                <p>Öffnen Sie den Blog in einem neuen Tab</p>
                <div class="arrow">
                    Ansehen <i class="fas fa-external-link-alt"></i>
                </div>
            </a>

            <a href="../index.html" class="admin-card" target="_blank">
                <div class="icon" style="background: linear-gradient(135deg, #fabe5c 0%, #f0a940 100%);">
                    <i class="fas fa-eye"></i>
                </div>
                <h3>Website anzeigen</h3>
                <p>Öffnen Sie die Website in einem neuen Tab</p>
                <div class="arrow">
                    Ansehen <i class="fas fa-external-link-alt"></i>
                </div>
            </a>
        </div>
    </div>
</body>
</html>
