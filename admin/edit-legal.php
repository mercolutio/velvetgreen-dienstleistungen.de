<?php
require_once 'config.php';
requireLogin();

$success = '';
$error = '';

// Load current content
$content = loadContent();
$legal = $content['legal'] ?? [
    'impressum' => [],
    'datenschutz' => '',
    'agb' => ''
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csrf_token'])) {
    if (verifyCSRFToken($_POST['csrf_token'])) {
        // Update Impressum
        $content['legal']['impressum'] = [
            'company' => sanitize($_POST['company'] ?? ''),
            'owner' => sanitize($_POST['owner'] ?? ''),
            'street' => sanitize($_POST['street'] ?? ''),
            'city' => sanitize($_POST['city'] ?? ''),
            'phone' => sanitize($_POST['phone'] ?? ''),
            'email' => sanitize($_POST['email'] ?? ''),
            'tax_number' => sanitize($_POST['tax_number'] ?? ''),
            'tax_id' => sanitize($_POST['tax_id'] ?? ''),
            'register' => $_POST['register'] ?? '',
            'chamber' => $_POST['chamber'] ?? '',
            'responsible' => $_POST['responsible'] ?? '',
            'dispute' => $_POST['dispute'] ?? '',
            'additional_content' => $_POST['additional_content'] ?? ''
        ];

        // Update Datenschutz & AGB (allow HTML/formatting)
        $content['legal']['datenschutz'] = $_POST['datenschutz'] ?? '';
        $content['legal']['agb'] = $_POST['agb'] ?? '';

        if (saveContent($content)) {
            $success = 'Rechtliche Seiten erfolgreich gespeichert!';
            $legal = $content['legal'];
        } else {
            $error = 'Fehler beim Speichern.';
        }
    } else {
        $error = 'Ungültiges CSRF-Token.';
    }
}

$csrf_token = generateCSRFToken();
$impressum = $legal['impressum'] ?? [];
?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rechtliche Seiten bearbeiten - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; background: #f5f5f5; color: #333; }
        
        .header {
            background: linear-gradient(135deg, #4a7c59 0%, #3d6b2e 100%);
            color: white;
            padding: 20px 40px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .header h1 { font-size: 1.5rem; }
        .header a { color: white; text-decoration: none; margin-top: 10px; display: inline-block; }
        .header a:hover { text-decoration: underline; }
        
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        
        .alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #d4edda; color: #155724; border-left: 4px solid #28a745; }
        .alert-error { background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545; }
        
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid #e9ecef;
        }
        .tab {
            padding: 15px 30px;
            background: transparent;
            border: none;
            border-bottom: 3px solid transparent;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            color: #666;
            transition: all 0.3s;
        }
        .tab:hover { color: #4a7c59; }
        .tab.active {
            color: #4a7c59;
            border-bottom-color: #4a7c59;
        }
        
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        
        .card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        .card h2 { color: #4a7c59; margin-bottom: 20px; }
        
        .form-group { margin-bottom: 20px; }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            font-family: Arial, sans-serif;
        }
        input:focus, textarea:focus {
            outline: none;
            border-color: #4a7c59;
            box-shadow: 0 0 0 3px rgba(74, 124, 89, 0.1);
        }
        textarea { min-height: 400px; line-height: 1.6; }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
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
        .btn-preview {
            background: #17a2b8;
            color: white;
            margin-left: 10px;
        }
        
        .actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 8px;
        }
        .info-box h3 { color: #1976D2; margin-bottom: 10px; }
        
        @media (max-width: 768px) {
            .form-row { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1><i class="fas fa-balance-scale"></i> Rechtliche Seiten bearbeiten</h1>
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

        <div class="tabs">
            <button class="tab active" onclick="showTab('impressum')">
                <i class="fas fa-building"></i> Impressum
            </button>
            <button class="tab" onclick="showTab('datenschutz')">
                <i class="fas fa-shield-alt"></i> Datenschutz
            </button>
            <button class="tab" onclick="showTab('agb')">
                <i class="fas fa-file-contract"></i> AGB
            </button>
        </div>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <!-- Impressum Tab -->
            <div id="impressum" class="tab-content active">
                <div class="card">
                    <h2>Impressum bearbeiten</h2>
                    
                    <div class="info-box">
                        <h3><i class="fas fa-info-circle"></i> Pflichtangaben gemäß § 5 DDG</h3>
                        <p>Das Impressum muss alle gesetzlich vorgeschriebenen Angaben enthalten. Seit Mai 2024 gilt das Digitale-Dienste-Gesetz (DDG) statt des TMG. Füllen Sie die Felder mit Ihren echten Daten aus.</p>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Firmenname / Unternehmensname *</label>
                            <input type="text" name="company" value="<?php echo htmlspecialchars($impressum['company'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Inhaber / Geschäftsführer</label>
                            <input type="text" name="owner" value="<?php echo htmlspecialchars($impressum['owner'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Straße und Hausnummer *</label>
                            <input type="text" name="street" value="<?php echo htmlspecialchars($impressum['street'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>PLZ und Stadt *</label>
                            <input type="text" name="city" value="<?php echo htmlspecialchars($impressum['city'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Telefon *</label>
                            <input type="text" name="phone" value="<?php echo htmlspecialchars($impressum['phone'] ?? ''); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>E-Mail *</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($impressum['email'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Steuernummer</label>
                        <input type="text" name="tax_number" value="<?php echo htmlspecialchars($impressum['tax_number'] ?? ''); ?>" placeholder="z.B. 51/101/26719">
                    </div>

                    <div class="form-group">
                        <label>Umsatzsteuer-ID (optional)</label>
                        <input type="text" name="tax_id" value="<?php echo htmlspecialchars($impressum['tax_id'] ?? ''); ?>" placeholder="z.B. DE123456789">
                    </div>

                    <div class="form-group">
                        <label>Registereintrag (optional)</label>
                        <textarea name="register" style="min-height: 100px;" placeholder="z.B. Handelsregister: Amtsgericht Köln HRB 12345"><?php echo htmlspecialchars($impressum['register'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Kammer / Berufsbezeichnung (optional)</label>
                        <textarea name="chamber" style="min-height: 100px;" placeholder="z.B. Zuständige Kammer, Berufsbezeichnung, etc."><?php echo htmlspecialchars($impressum['chamber'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Verantwortlich für Inhalte nach § 18 Abs. 2 MStV (optional)</label>
                        <textarea name="responsible" style="min-height: 80px;" placeholder="Name und Anschrift des Verantwortlichen"><?php echo htmlspecialchars($impressum['responsible'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Verbraucherstreitbeilegung (optional)</label>
                        <textarea name="dispute" style="min-height: 120px;" placeholder="Informationen zur Streitschlichtung, z.B.: Wir sind nicht bereit oder verpflichtet, an Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen."><?php echo htmlspecialchars($impressum['dispute'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Zusätzliche Inhalte (optional)</label>
                        <textarea name="additional_content" placeholder="Sonstige wichtige Angaben"><?php echo htmlspecialchars($impressum['additional_content'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Datenschutz Tab -->
            <div id="datenschutz" class="tab-content">
                <div class="card">
                    <h2>Datenschutzerklärung bearbeiten</h2>
                    
                    <div class="info-box">
                        <h3><i class="fas fa-info-circle"></i> DSGVO-konforme Datenschutzerklärung</h3>
                        <p>
                            Ihre Datenschutzerklärung muss alle Datenverarbeitungen beschreiben. 
                            Empfehlung: Nutzen Sie einen Generator wie 
                            <a href="https://www.e-recht24.de/muster-datenschutzerklaerung.html" target="_blank" style="color: #1976D2;">e-recht24.de</a>
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Datenschutzerklärung</label>
                        <textarea name="datenschutz" placeholder="Hier kommt Ihre vollständige Datenschutzerklärung..."><?php echo htmlspecialchars($legal['datenschutz'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- AGB Tab -->
            <div id="agb" class="tab-content">
                <div class="card">
                    <h2>AGB bearbeiten</h2>
                    
                    <div class="info-box">
                        <h3><i class="fas fa-info-circle"></i> Allgemeine Geschäftsbedingungen</h3>
                        <p>
                            Ihre AGB regeln die Geschäftsbeziehung mit Kunden. 
                            Lassen Sie diese ggf. von einem Anwalt prüfen.
                        </p>
                    </div>

                    <div class="form-group">
                        <label>Allgemeine Geschäftsbedingungen</label>
                        <textarea name="agb" placeholder="Hier kommen Ihre vollständigen AGB..."><?php echo htmlspecialchars($legal['agb'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

            <div class="actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Alle Änderungen speichern
                </button>
                <a href="../impressum.php" target="_blank" class="btn btn-preview">
                    <i class="fas fa-external-link-alt"></i> Impressum ansehen
                </a>
                <a href="../datenschutz.php" target="_blank" class="btn btn-preview">
                    <i class="fas fa-external-link-alt"></i> Datenschutz ansehen
                </a>
                <a href="../agb.php" target="_blank" class="btn btn-preview">
                    <i class="fas fa-external-link-alt"></i> AGB ansehen
                </a>
            </div>
        </form>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab').forEach(btn => {
                btn.classList.remove('active');
            });

            // Show selected tab
            document.getElementById(tabName).classList.add('active');
            event.target.classList.add('active');
        }
    </script>
</body>
</html>
