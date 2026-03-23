<?php
require_once 'config.php';
require_once '../blog/config.php';
requireLogin();

$success = '';
$error = '';

$blogDb = getDB();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add' && isset($_POST['csrf_token'])) {
        if (verifyCSRFToken($_POST['csrf_token'])) {
            $keyword = trim($_POST['keyword']);

            if (!empty($keyword)) {
                $stmt = $blogDb->prepare("INSERT INTO keywords (keyword) VALUES (?)");
                if ($stmt->execute([$keyword])) {
                    $success = 'Keyword erfolgreich hinzugefügt!';
                } else {
                    $error = 'Fehler beim Speichern des Keywords.';
                }
            } else {
                $error = 'Bitte geben Sie ein Keyword ein.';
            }
        } else {
            $error = 'Ungültiges CSRF Token.';
        }
    }

    if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['csrf_token'])) {
        if (verifyCSRFToken($_POST['csrf_token'])) {
            $keywordId = intval($_POST['keyword_id']);
            $stmt = $blogDb->prepare("DELETE FROM keywords WHERE id = ?");
            if ($stmt->execute([$keywordId])) {
                $success = 'Keyword erfolgreich gelöscht!';
            } else {
                $error = 'Fehler beim Löschen des Keywords.';
            }
        }
    }
}

// Get all keywords
$stmt = $blogDb->query("SELECT * FROM keywords ORDER BY created_at DESC");
$keywords = $stmt->fetchAll(PDO::FETCH_ASSOC);

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Keywords verwalten - Admin</title>
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
            max-width: 1200px;
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

        .add-keyword-form {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        input[type="text"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #4a7c59;
            box-shadow: 0 0 0 3px rgba(74, 124, 89, 0.1);
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4a7c59 0%, #3d6b2e 100%);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #fabe5c 0%, #f0a940 100%);
            transform: translateY(-2px);
        }

        .btn-danger {
            background: #dc3545;
            color: white;
            padding: 8px 16px;
            font-size: 0.9rem;
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .keywords-table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #4a7c59;
            color: white;
        }

        th, td {
            padding: 15px 20px;
            text-align: left;
        }

        tbody tr {
            border-bottom: 1px solid #eee;
        }

        tbody tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-processed {
            background: #d4edda;
            color: #155724;
        }

        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 8px;
        }

        .info-box h3 {
            color: #1976D2;
            margin-bottom: 10px;
        }

        .info-box p {
            color: #555;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            table {
                font-size: 0.9rem;
            }

            th, td {
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-tags"></i> Blog Keywords verwalten</h1>
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

        <div class="info-box">
            <h3><i class="fas fa-info-circle"></i> So funktioniert's</h3>
            <p>
                Fügen Sie Keywords hinzu, für die automatisch SEO-Blogartikel erstellt werden sollen.
                Der n8n Workflow holt sich täglich die unverarbeiteten Keywords und erstellt professionelle Blogartikel mit ca. 1.200 Wörtern.
            </p>
        </div>

        <div class="add-keyword-form">
            <h2>Neues Keyword hinzufügen</h2>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="action" value="add">

                <div class="form-group">
                    <label for="keyword">
                        <i class="fas fa-key"></i> Keyword oder Thema
                    </label>
                    <input type="text"
                           id="keyword"
                           name="keyword"
                           placeholder="z.B. Haushaltsauflösung Berlin, Entrümpelung Tipps, etc."
                           required>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Keyword hinzufügen
                </button>
            </form>
        </div>

        <div class="keywords-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Keyword</th>
                        <th>Status</th>
                        <th>Erstellt am</th>
                        <th>Verarbeitet am</th>
                        <th>Aktion</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($keywords) > 0): ?>
                        <?php foreach ($keywords as $kw): ?>
                            <tr>
                                <td><?php echo $kw['id']; ?></td>
                                <td><strong><?php echo htmlspecialchars($kw['keyword']); ?></strong></td>
                                <td>
                                    <?php if ($kw['processed']): ?>
                                        <span class="status-badge status-processed">
                                            <i class="fas fa-check"></i> Verarbeitet
                                        </span>
                                    <?php else: ?>
                                        <span class="status-badge status-pending">
                                            <i class="fas fa-clock"></i> Ausstehend
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo date('d.m.Y H:i', strtotime($kw['created_at'])); ?></td>
                                <td>
                                    <?php echo $kw['processed_at'] ? date('d.m.Y H:i', strtotime($kw['processed_at'])) : '-'; ?>
                                </td>
                                <td>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Wirklich löschen?');">
                                        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="keyword_id" value="<?php echo $kw['id']; ?>">
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
                                <i class="fas fa-inbox" style="font-size: 3rem; display: block; margin-bottom: 15px;"></i>
                                Noch keine Keywords vorhanden. Fügen Sie oben ein neues Keyword hinzu.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
