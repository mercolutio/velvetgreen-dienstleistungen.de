<?php
require_once 'config.php';
requireLogin();

$success = '';
$error = '';

// Load current content
$content = loadContent();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token'])) {
    if (verifyCSRFToken($_POST['csrf_token'])) {
        // Update services
        for ($i = 0; $i < 4; $i++) {
            if (isset($_POST['service_' . $i . '_title'])) {
                $content['services'][$i] = [
                    'icon' => sanitize($_POST['service_' . $i . '_icon']),
                    'title' => sanitize($_POST['service_' . $i . '_title']),
                    'description' => sanitize($_POST['service_' . $i . '_description'])
                ];
            }
        }

        if (saveContent($content)) {
            $success = 'Leistungen erfolgreich aktualisiert!';
        } else {
            $error = 'Fehler beim Speichern der Änderungen.';
        }
    } else {
        $error = 'Ungültiges CSRF Token.';
    }
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leistungen bearbeiten - Admin</title>
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
            max-width: 1000px;
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

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .service-block {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #4a7c59;
        }

        .service-block h3 {
            color: #4a7c59;
            margin-bottom: 20px;
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

        input, textarea, select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            font-family: Arial, sans-serif;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #4a7c59;
            box-shadow: 0 0 0 3px rgba(74, 124, 89, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .icon-preview {
            margin-top: 10px;
            padding: 15px;
            background: white;
            border-radius: 8px;
            display: inline-block;
        }

        .icon-preview i {
            font-size: 2rem;
            color: #4a7c59;
        }

        .btn {
            padding: 14px 30px;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #4a7c59 0%, #3d6b2e 100%);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #fabe5c 0%, #f0a940 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(250, 190, 92, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
            margin-left: 10px;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .button-group {
            margin-top: 30px;
        }

        .icon-help {
            font-size: 0.9rem;
            color: #666;
            margin-top: 5px;
        }

        .icon-help a {
            color: #4a7c59;
            text-decoration: none;
        }

        .icon-help a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-briefcase"></i> Leistungen bearbeiten</h1>
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

        <div class="form-container">
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <?php foreach ($content['services'] as $index => $service): ?>
                    <div class="service-block">
                        <h3>Leistung <?php echo ($index + 1); ?></h3>

                        <div class="form-group">
                            <label for="service_<?php echo $index; ?>_title">Titel</label>
                            <input type="text"
                                   id="service_<?php echo $index; ?>_title"
                                   name="service_<?php echo $index; ?>_title"
                                   value="<?php echo htmlspecialchars($service['title']); ?>"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="service_<?php echo $index; ?>_description">Beschreibung</label>
                            <textarea id="service_<?php echo $index; ?>_description"
                                      name="service_<?php echo $index; ?>_description"
                                      required><?php echo htmlspecialchars($service['description']); ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="service_<?php echo $index; ?>_icon">Font Awesome Icon Klasse</label>
                            <input type="text"
                                   id="service_<?php echo $index; ?>_icon"
                                   name="service_<?php echo $index; ?>_icon"
                                   value="<?php echo htmlspecialchars($service['icon']); ?>"
                                   required>
                            <p class="icon-help">
                                Beispiele: fa-box-open, fa-home, fa-leaf, fa-truck-moving<br>
                                <a href="https://fontawesome.com/icons" target="_blank">
                                    <i class="fas fa-external-link-alt"></i> Font Awesome Icons suchen
                                </a>
                            </p>
                            <div class="icon-preview">
                                <i class="fas <?php echo htmlspecialchars($service['icon']); ?>"></i>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="button-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Änderungen speichern
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Abbrechen
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
