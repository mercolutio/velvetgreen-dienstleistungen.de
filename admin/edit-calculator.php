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
        $content['calculator'] = [
            'filling_normal' => floatval($_POST['filling_normal']),
            'filling_strong' => floatval($_POST['filling_strong']),
            'filling_extreme' => floatval($_POST['filling_extreme']),
            'floor_surcharge' => floatval($_POST['floor_surcharge']),
            'carpet_glued_full' => floatval($_POST['carpet_glued_full']),
            'carpet_glued_partial' => floatval($_POST['carpet_glued_partial']),
            'laminate_glued' => floatval($_POST['laminate_glued']),
            'laminate_click' => floatval($_POST['laminate_click']),
            'pvc_glued' => floatval($_POST['pvc_glued']),
            'pvc_laid' => floatval($_POST['pvc_laid']),
            'baseboards_per_meter' => floatval($_POST['baseboards_per_meter']),
            'appliances_removal' => floatval($_POST['appliances_removal']),
            'wallpaper_removal' => floatval($_POST['wallpaper_removal']),
            'lamp_removal' => floatval($_POST['lamp_removal']),
            'drywall_removal' => floatval($_POST['drywall_removal'])
        ];

        if (saveContent($content)) {
            $success = 'Kostenrechner-Preise erfolgreich aktualisiert!';
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
    <title>Kostenrechner bearbeiten - Admin</title>
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
            max-width: 900px;
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
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px 20px;
            margin-bottom: 30px;
            border-radius: 8px;
        }

        .info-box h3 {
            color: #1976D2;
            margin-bottom: 10px;
            font-size: 1.1rem;
        }

        .info-box p {
            color: #555;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .price-section {
            background: #f8f9fa;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            border-left: 4px solid #4a7c59;
        }

        .price-section h3 {
            color: #4a7c59;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
            font-size: 1rem;
        }

        label i {
            color: #4a7c59;
            margin-right: 5px;
        }

        .input-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        input {
            flex: 1;
            padding: 14px 18px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            font-family: Arial, sans-serif;
        }

        input:focus {
            outline: none;
            border-color: #4a7c59;
            box-shadow: 0 0 0 3px rgba(74, 124, 89, 0.1);
        }

        .currency {
            font-weight: bold;
            color: #666;
            font-size: 1.1rem;
        }

        .helper-text {
            font-size: 0.9rem;
            color: #666;
            margin-top: 5px;
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
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-calculator"></i> Kostenrechner bearbeiten</h1>
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
            <h3><i class="fas fa-info-circle"></i> Hinweis</h3>
            <p>Diese Preise werden für die automatische Kostenberechnung auf der Website verwendet. Änderungen werden sofort auf der Website wirksam.</p>
        </div>

        <div class="form-container">
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <div class="price-section">
                    <h3>Grundpreise Entrümpelung</h3>

                    <div class="form-group">
                        <label for="filling_normal">
                            <i class="fas fa-boxes"></i> Normale Befüllung (€/m²)
                        </label>
                        <div class="input-group">
                            <input type="number" id="filling_normal" name="filling_normal"
                                   value="<?php echo htmlspecialchars($content['calculator']['filling_normal']); ?>"
                                   step="0.01" min="0" required>
                            <span class="currency">€ pro m²</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="filling_strong">
                            <i class="fas fa-boxes"></i> Starke Befüllung (€/m²)
                        </label>
                        <div class="input-group">
                            <input type="number" id="filling_strong" name="filling_strong"
                                   value="<?php echo htmlspecialchars($content['calculator']['filling_strong']); ?>"
                                   step="0.01" min="0" required>
                            <span class="currency">€ pro m²</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="filling_extreme">
                            <i class="fas fa-boxes"></i> Extreme Befüllung (€/m²)
                        </label>
                        <div class="input-group">
                            <input type="number" id="filling_extreme" name="filling_extreme"
                                   value="<?php echo htmlspecialchars($content['calculator']['filling_extreme']); ?>"
                                   step="0.01" min="0" required>
                            <span class="currency">€ pro m²</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="floor_surcharge">
                            <i class="fas fa-stairs"></i> Etagen-Aufpreis
                        </label>
                        <div class="input-group">
                            <input type="number" id="floor_surcharge" name="floor_surcharge"
                                   value="<?php echo htmlspecialchars($content['calculator']['floor_surcharge']); ?>"
                                   step="0.01" min="0" required>
                            <span class="currency">€ pro Etage</span>
                        </div>
                    </div>
                </div>

                <div class="price-section">
                    <h3>Bodenbeläge entfernen</h3>

                    <div class="form-group">
                        <label for="carpet_glued_full">
                            <i class="fas fa-grip-horizontal"></i> Teppich vollverklebt (€/m²)
                        </label>
                        <div class="input-group">
                            <input type="number" id="carpet_glued_full" name="carpet_glued_full"
                                   value="<?php echo htmlspecialchars($content['calculator']['carpet_glued_full']); ?>"
                                   step="0.01" min="0" required>
                            <span class="currency">€ pro m²</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="carpet_glued_partial">
                            <i class="fas fa-grip-horizontal"></i> Teppich teilverklebt (€/m²)
                        </label>
                        <div class="input-group">
                            <input type="number" id="carpet_glued_partial" name="carpet_glued_partial"
                                   value="<?php echo htmlspecialchars($content['calculator']['carpet_glued_partial']); ?>"
                                   step="0.01" min="0" required>
                            <span class="currency">€ pro m²</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="laminate_glued">
                            <i class="fas fa-th-large"></i> Laminat verklebt (€/m²)
                        </label>
                        <div class="input-group">
                            <input type="number" id="laminate_glued" name="laminate_glued"
                                   value="<?php echo htmlspecialchars($content['calculator']['laminate_glued']); ?>"
                                   step="0.01" min="0" required>
                            <span class="currency">€ pro m²</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="laminate_click">
                            <i class="fas fa-th-large"></i> Laminat Klick/verlegt (€/m²)
                        </label>
                        <div class="input-group">
                            <input type="number" id="laminate_click" name="laminate_click"
                                   value="<?php echo htmlspecialchars($content['calculator']['laminate_click']); ?>"
                                   step="0.01" min="0" required>
                            <span class="currency">€ pro m²</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pvc_glued">
                            <i class="fas fa-border-all"></i> PVC verklebt (€/m²)
                        </label>
                        <div class="input-group">
                            <input type="number" id="pvc_glued" name="pvc_glued"
                                   value="<?php echo htmlspecialchars($content['calculator']['pvc_glued']); ?>"
                                   step="0.01" min="0" required>
                            <span class="currency">€ pro m²</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="pvc_laid">
                            <i class="fas fa-border-all"></i> PVC verlegt (€/m²)
                        </label>
                        <div class="input-group">
                            <input type="number" id="pvc_laid" name="pvc_laid"
                                   value="<?php echo htmlspecialchars($content['calculator']['pvc_laid']); ?>"
                                   step="0.01" min="0" required>
                            <span class="currency">€ pro m²</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="baseboards_per_meter">
                            <i class="fas fa-minus"></i> Sockelleisten entfernen (€/Meter)
                        </label>
                        <div class="input-group">
                            <input type="number" id="baseboards_per_meter" name="baseboards_per_meter"
                                   value="<?php echo htmlspecialchars($content['calculator']['baseboards_per_meter']); ?>"
                                   step="0.01" min="0" required>
                            <span class="currency">€ pro Meter</span>
                        </div>
                    </div>
                </div>

                <div class="price-section">
                    <h3>Weitere Leistungen</h3>

                    <div class="form-group">
                        <label for="appliances_removal">
                            <i class="fas fa-plug"></i> Elektrogeräte entfernen (€/Stück)
                        </label>
                        <div class="input-group">
                            <input type="number" id="appliances_removal" name="appliances_removal"
                                   value="<?php echo htmlspecialchars($content['calculator']['appliances_removal']); ?>"
                                   step="0.01" min="0" required>
                            <span class="currency">€ pro Stück</span>
                        </div>
                        <p class="helper-text">Aktuell 0€ (kostenlos im Rahmen der Entrümpelung)</p>
                    </div>

                    <div class="form-group">
                        <label for="wallpaper_removal">
                            <i class="fas fa-paint-roller"></i> Tapeten entfernen (€/m²)
                        </label>
                        <div class="input-group">
                            <input type="number" id="wallpaper_removal" name="wallpaper_removal"
                                   value="<?php echo htmlspecialchars($content['calculator']['wallpaper_removal']); ?>"
                                   step="0.01" min="0" required>
                            <span class="currency">€ pro m²</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="lamp_removal">
                            <i class="fas fa-lightbulb"></i> Lampen demontieren (€/Stück)
                        </label>
                        <div class="input-group">
                            <input type="number" id="lamp_removal" name="lamp_removal"
                                   value="<?php echo htmlspecialchars($content['calculator']['lamp_removal']); ?>"
                                   step="0.01" min="0" required>
                            <span class="currency">€ pro Stück</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="drywall_removal">
                            <i class="fas fa-vector-square"></i> Trockenbauwand entfernen (€/m²)
                        </label>
                        <div class="input-group">
                            <input type="number" id="drywall_removal" name="drywall_removal"
                                   value="<?php echo htmlspecialchars($content['calculator']['drywall_removal']); ?>"
                                   step="0.01" min="0" required>
                            <span class="currency">€ pro m²</span>
                        </div>
                    </div>
                </div>

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
