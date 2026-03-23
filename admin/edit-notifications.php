<?php
require_once 'config.php';
requireLogin();

$success = '';
$error = '';

// Load current content
$content = loadContent();
$notifications = $content['notifications'] ?? [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token'])) {
    if (verifyCSRFToken($_POST['csrf_token'])) {
        // Update notifications
        $updatedNotifications = [];
        
        // Get count of notifications
        $count = isset($_POST['notification_name']) ? count($_POST['notification_name']) : 0;
        
        for ($i = 0; $i < $count; $i++) {
            if (!empty($_POST['notification_name'][$i]) && !empty($_POST['notification_action'][$i])) {
                $updatedNotifications[] = [
                    'name' => sanitize($_POST['notification_name'][$i]),
                    'action' => sanitize($_POST['notification_action'][$i]),
                    'time' => sanitize($_POST['notification_time'][$i] ?? 'vor wenigen Minuten'),
                    'enabled' => isset($_POST['notification_enabled'][$i])
                ];
            }
        }
        
        $content['notifications'] = $updatedNotifications;
        
        if (saveContent($content)) {
            $success = 'Benachrichtigungen erfolgreich gespeichert!';
            $notifications = $updatedNotifications;
        } else {
            $error = 'Fehler beim Speichern der Benachrichtigungen.';
        }
    } else {
        $error = 'Ungültiges CSRF-Token.';
    }
}

$csrf_token = generateCSRFToken();
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Social Proof Benachrichtigungen - Admin</title>
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

        .card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .card h2 {
            color: #4a7c59;
            margin-bottom: 20px;
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

        .notification-item {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            position: relative;
        }

        .notification-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .notification-number {
            background: #4a7c59;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
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

        .toggle-switch {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .toggle-switch input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
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

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-add {
            background: #28a745;
            color: white;
            margin-bottom: 20px;
        }

        .btn-add:hover {
            background: #218838;
        }

        .actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-bell"></i> Social Proof Benachrichtigungen</h1>
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
            <h3><i class="fas fa-info-circle"></i> Was sind Social Proof Benachrichtigungen?</h3>
            <p>
                Diese kleinen Benachrichtigungen erscheinen unten links auf der Website und zeigen Besuchern,
                dass andere Kunden kürzlich Dienstleistungen gebucht haben. Dies schafft Vertrauen und erhöht
                die Conversion-Rate. Die Benachrichtigungen rotieren automatisch alle 15 Sekunden.
            </p>
        </div>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <div class="card">
                <h2>Benachrichtigungen verwalten</h2>

                <button type="button" class="btn btn-add" onclick="addNotification()">
                    <i class="fas fa-plus"></i> Neue Benachrichtigung hinzufügen
                </button>

                <div id="notifications-container">
                    <?php foreach ($notifications as $index => $notification): ?>
                        <div class="notification-item">
                            <div class="notification-header">
                                <div class="notification-number"><?php echo $index + 1; ?></div>
                                <div class="toggle-switch">
                                    <input type="checkbox" 
                                           name="notification_enabled[<?php echo $index; ?>]" 
                                           id="enabled_<?php echo $index; ?>"
                                           <?php echo $notification['enabled'] ? 'checked' : ''; ?>>
                                    <label for="enabled_<?php echo $index; ?>" style="margin: 0;">Aktiv</label>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Name</label>
                                    <input type="text" 
                                           name="notification_name[<?php echo $index; ?>]" 
                                           value="<?php echo htmlspecialchars($notification['name']); ?>"
                                           placeholder="z.B. Michael K.">
                                </div>

                                <div class="form-group">
                                    <label>Aktion</label>
                                    <input type="text" 
                                           name="notification_action[<?php echo $index; ?>]" 
                                           value="<?php echo htmlspecialchars($notification['action']); ?>"
                                           placeholder="z.B. hat eine Wohnungsauflösung gebucht">
                                </div>

                                <div class="form-group">
                                    <label>Zeit</label>
                                    <input type="text" 
                                           name="notification_time[<?php echo $index; ?>]" 
                                           value="<?php echo htmlspecialchars($notification['time']); ?>"
                                           placeholder="z.B. vor 12 Minuten">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (empty($notifications)): ?>
                        <p style="text-align: center; color: #999; padding: 40px;">
                            <i class="fas fa-inbox" style="font-size: 3rem; display: block; margin-bottom: 15px;"></i>
                            Noch keine Benachrichtigungen vorhanden. Füge oben eine neue hinzu.
                        </p>
                    <?php endif; ?>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Änderungen speichern
                    </button>
                    <a href="dashboard.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Abbrechen
                    </a>
                </div>
            </div>
        </form>
    </div>

    <script>
        let notificationCount = <?php echo count($notifications); ?>;

        function addNotification() {
            const container = document.getElementById('notifications-container');
            const index = notificationCount;
            
            const notification = document.createElement('div');
            notification.className = 'notification-item';
            notification.innerHTML = `
                <div class="notification-header">
                    <div class="notification-number">${index + 1}</div>
                    <div class="toggle-switch">
                        <input type="checkbox" 
                               name="notification_enabled[${index}]" 
                               id="enabled_${index}"
                               checked>
                        <label for="enabled_${index}" style="margin: 0;">Aktiv</label>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" 
                               name="notification_name[${index}]" 
                               placeholder="z.B. Michael K.">
                    </div>

                    <div class="form-group">
                        <label>Aktion</label>
                        <input type="text" 
                               name="notification_action[${index}]" 
                               placeholder="z.B. hat eine Wohnungsauflösung gebucht">
                    </div>

                    <div class="form-group">
                        <label>Zeit</label>
                        <input type="text" 
                               name="notification_time[${index}]" 
                               placeholder="z.B. vor 12 Minuten">
                    </div>
                </div>
            `;
            
            container.appendChild(notification);
            notificationCount++;
        }
    </script>
</body>
</html>
