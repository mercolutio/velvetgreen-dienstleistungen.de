<?php
/**
 * Keyword Import Script für SEO Blog-System
 *
 * Dieses Script lädt alle strategischen Keywords in die Datenbank
 * Aufruf: php import-keywords.php
 * Oder im Browser: http://localhost:8000/import-keywords.php
 */

require_once __DIR__ . '/blog/config.php';

// SEO Keywords - Tier-basierte Strategie
$keywords = [
    // TIER 1: Lokale Haupt-Keywords (Hohe Priorität)
    'Entrümpelung Salzgitter',
    'Entrümpelung Braunschweig',
    'Entrümpelung Wolfenbüttel',
    'Entrümpelung Goslar',
    'Entrümpelung Hildesheim',
    'Entrümpelung Peine',
    'Entrümpelung Gifhorn',
    'Entrümpelung Wolfsburg',
    'Entrümpelung Helmstedt',
    'Entrümpelung Bad Harzburg',

    'Haushaltsauflösung Salzgitter',
    'Haushaltsauflösung Braunschweig',
    'Haushaltsauflösung Wolfenbüttel',
    'Haushaltsauflösung Goslar',
    'Haushaltsauflösung Hildesheim',
    'Haushaltsauflösung Peine',
    'Haushaltsauflösung Gifhorn',
    'Haushaltsauflösung Wolfsburg',

    'Wohnungsauflösung Salzgitter',
    'Wohnungsauflösung Braunschweig',
    'Wohnungsauflösung Wolfenbüttel',
    'Wohnungsauflösung Niedersachsen',

    // TIER 2: Service-Spezifische Keywords
    'Kellerentrümpelung Salzgitter',
    'Dachbodenräumung Braunschweig',
    'Garagenentrümpelung Niedersachsen',
    'Messiewohnung entrümpeln Salzgitter',
    'Betriebsauflösung Braunschweig',
    'Geschäftsauflösung Salzgitter',
    'Praxisauflösung Niedersachsen',
    'Büroauflösung Braunschweig',

    'Sperrmüll entsorgen Salzgitter',
    'Altmetall entsorgen Braunschweig',
    'Elektroschrott entsorgen Salzgitter',
    'Möbel entsorgen Niedersachsen',
    'Schrott abholen Salzgitter',

    'Nachlassauflösung Salzgitter',
    'Wohnungsauflösung nach Todesfall Braunschweig',
    'Erbenermittlung Haushaltsauflösung Salzgitter',
    'Testamentsvollstrecker Wohnungsräumung',

    // TIER 3: Long-Tail Keywords (Kaufabsicht)
    'Was kostet Entrümpelung Salzgitter',
    'Entrümpelung Kosten pro m2 Niedersachsen',
    'Haushaltsauflösung Preise Braunschweig',
    'Kostenlose Besichtigung Entrümpelung Salzgitter',
    'Günstige Entrümpelung Niedersachsen',
    'Entrümpelung Festpreis Salzgitter',

    'Entrümpelung kurzfristig Salzgitter',
    'Express Entrümpelung Braunschweig',
    'Entrümpelung am Wochenende Salzgitter',
    'Sofort Entrümpelung Niedersachsen',
    '24h Entrümpelung Braunschweig',

    'Beste Entrümpelungsfirma Salzgitter',
    'Entrümpelungsfirma Vergleich Niedersachsen',
    'Entrümpelung Bewertungen Braunschweig',
    'Seriöse Entrümpelungsfirma Salzgitter',
    'Zuverlässige Haushaltsauflösung Braunschweig',

    // TIER 4: Frage-Keywords (Voice Search)
    'Wie funktioniert Entrümpelung Salzgitter',
    'Wie läuft Haushaltsauflösung ab',
    'Wie viel kostet Wohnungsauflösung',
    'Wie entrümple ich richtig',
    'Wie lange dauert Entrümpelung',

    'Was kostet Entrümpelung 50 qm',
    'Was wird bei Haushaltsauflösung mitgenommen',
    'Was passiert mit Möbeln bei Entrümpelung',
    'Was bedeutet Wertanrechnung bei Haushaltsauflösung',

    'Wann brauche ich Entrümpelung',
    'Wann lohnt sich Haushaltsauflösung',
    'Wann Entrümpelung steuerlich absetzbar',

    'Wer zahlt Entrümpelung bei Todesfall',
    'Wer entsorgt Sperrmüll kostenlos Salzgitter',
    'Wer darf Entrümpelung durchführen',

    // TIER 5: Stadtteil-Spezifisch
    'Entrümpelung Salzgitter-Bad',
    'Entrümpelung Salzgitter-Lebenstedt',
    'Entrümpelung Salzgitter-Thiede',
    'Haushaltsauflösung Salzgitter-Gebhardshagen',

    'Entrümpelung Braunschweig Innenstadt',
    'Entrümpelung Braunschweig Weststadt',
    'Haushaltsauflösung Braunschweig Querum',

    // TIER 6: B2B & Gewerbe
    'Gewerbliche Entrümpelung Salzgitter',
    'Firmenauflösung Braunschweig',
    'Lagerauflösung Niedersachsen',
    'Hotelauflösung Salzgitter',
    'Gastronomie Entrümpelung Braunschweig',
    'Baustellenräumung Salzgitter',

    // TIER 7: Umwelt & Nachhaltigkeit
    'Umweltgerechte Entrümpelung Salzgitter',
    'Nachhaltige Haushaltsauflösung Niedersachsen',
    'Recycling bei Entrümpelung Braunschweig',
    'Entrümpelung ohne Müll Salzgitter',
    'Möbel Spende Entrümpelung Braunschweig',

    // TIER 8: Ratgeber & How-To
    'Entrümpelung Checkliste',
    'Haushaltsauflösung planen Schritt für Schritt',
    'Keller entrümpeln Tipps',
    'Dachboden aufräumen Anleitung',
    'Messiewohnung richtig entrümpeln',
    'Entrümpelung nach Trennung',
    'Entrümpelung vor Umzug',
    'Minimalismus Entrümpelung',

    // TIER 9: Saisonale Keywords
    'Frühjahrsputz Entrümpelung',
    'Entrümpelung Jahresende',
    'Sommerschlussverkauf Möbel Entrümpelung',
    'Entrümpelung vor Weihnachten',
];

// Datenbank verbinden
try {
    $db = getDB();

    echo "🚀 Keyword-Import gestartet...\n\n";

    $imported = 0;
    $skipped = 0;
    $errors = 0;

    foreach ($keywords as $keyword) {
        try {
            // Prüfen ob Keyword bereits existiert
            $stmt = $db->prepare("SELECT id FROM keywords WHERE keyword = ?");
            $stmt->execute([$keyword]);

            if ($stmt->fetch()) {
                echo "⏭️  Übersprungen (existiert): $keyword\n";
                $skipped++;
                continue;
            }

            // Keyword einfügen
            $stmt = $db->prepare("INSERT INTO keywords (keyword, processed) VALUES (?, 0)");
            $stmt->execute([$keyword]);

            echo "✅ Importiert: $keyword\n";
            $imported++;

        } catch (Exception $e) {
            echo "❌ Fehler bei '$keyword': " . $e->getMessage() . "\n";
            $errors++;
        }
    }

    echo "\n" . str_repeat("=", 60) . "\n";
    echo "📊 IMPORT ABGESCHLOSSEN\n";
    echo str_repeat("=", 60) . "\n";
    echo "✅ Erfolgreich importiert: $imported Keywords\n";
    echo "⏭️  Übersprungen (Duplikate): $skipped Keywords\n";
    echo "❌ Fehler: $errors Keywords\n";
    echo "📝 Gesamt: " . count($keywords) . " Keywords\n";
    echo str_repeat("=", 60) . "\n\n";

    // Statistik anzeigen
    $stmt = $db->query("SELECT COUNT(*) as total FROM keywords");
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    $stmt = $db->query("SELECT COUNT(*) as processed FROM keywords WHERE processed = 1");
    $processed = $stmt->fetch(PDO::FETCH_ASSOC)['processed'];

    $pending = $total - $processed;

    echo "📈 DATENBANK-STATUS\n";
    echo str_repeat("=", 60) . "\n";
    echo "📝 Keywords gesamt: $total\n";
    echo "✅ Bereits verarbeitet: $processed\n";
    echo "⏳ Warten auf Verarbeitung: $pending\n";
    echo str_repeat("=", 60) . "\n\n";

    if ($pending > 0) {
        $days = ceil($pending / 1); // 1 Artikel pro Tag
        echo "⏰ Geschätzte Zeit bis Fertigstellung: ~$days Tage\n";
        echo "📅 Fertigstellung ca.: " . date('d.m.Y', strtotime("+$days days")) . "\n\n";
    }

    echo "🎯 NÄCHSTE SCHRITTE:\n";
    echo "1. n8n Workflow aktivieren (täglich 4:50 Uhr)\n";
    echo "2. Google Search Console einrichten\n";
    echo "3. Google Analytics verbinden\n";
    echo "4. Nach 7 Tagen: Erste Rankings prüfen\n";
    echo "5. Nach 30 Tagen: Traffic-Analyse\n\n";

    echo "🚀 SEO-Dominanz startet JETZT!\n\n";

} catch (Exception $e) {
    echo "❌ KRITISCHER FEHLER: " . $e->getMessage() . "\n";
    exit(1);
}
