<?php
/**
 * Landing Page Generator für lokale SEO
 *
 * Generiert automatisch optimierte Landing Pages für alle Städte und Stadtteile
 * Aufruf: php generate-landing-pages.php
 */

// Städte-Konfiguration mit GPS-Koordinaten
$cities = [
    'salzgitter' => [
        'name' => 'Salzgitter',
        'lat' => '52.15',
        'lon' => '10.33',
        'plz' => '38226',
        'stadtteil' => ['Bad', 'Lebenstedt', 'Thiede', 'Gebhardshagen', 'Salder', 'Ringelheim'],
        'beschreibung' => 'Salzgitter und allen Stadtteilen'
    ],
    'braunschweig' => [
        'name' => 'Braunschweig',
        'lat' => '52.2689',
        'lon' => '10.5268',
        'plz' => '38100',
        'stadtteil' => ['Innenstadt', 'Weststadt', 'Querum', 'Lehndorf', 'Rüningen', 'Viewegsgarten-Bebelhof', 'Östliches Ringgebiet', 'Volkmarode', 'Wenden', 'Stöckheim', 'Melverode', 'Heidberg'],
        'beschreibung' => 'Braunschweig und allen Stadtteilen'
    ],
    'wolfenbuettel' => [
        'name' => 'Wolfenbüttel',
        'lat' => '52.1642',
        'lon' => '10.5372',
        'plz' => '38300',
        'stadtteil' => ['Stadtgebiet', 'Linden', 'Fümmelse', 'Ahlum', 'Leinde'],
        'beschreibung' => 'Wolfenbüttel und Umgebung'
    ],
    'hildesheim' => [
        'name' => 'Hildesheim',
        'lat' => '52.1561',
        'lon' => '9.9500',
        'plz' => '31134',
        'stadtteil' => ['Innenstadt', 'Nordstadt', 'Weststadt', 'Südstadt', 'Oststadt', 'Drispenstedt', 'Ochtersum'],
        'beschreibung' => 'Hildesheim und allen Stadtteilen'
    ],
    'goslar' => [
        'name' => 'Goslar',
        'lat' => '51.9059',
        'lon' => '10.4289',
        'plz' => '38640',
        'stadtteil' => ['Altstadt', 'Jürgenohl', 'Kramerswinkel', 'Georgenberg', 'Jerstedt', 'Hahnenklee'],
        'beschreibung' => 'Goslar und Umgebung'
    ],
    'peine' => [
        'name' => 'Peine',
        'lat' => '52.3203',
        'lon' => '10.2345',
        'plz' => '31224',
        'stadtteil' => ['Kernstadt', 'Stederdorf', 'Vöhrum', 'Woltorf', 'Dungelbeck'],
        'beschreibung' => 'Peine und Umgebung'
    ],
    'gifhorn' => [
        'name' => 'Gifhorn',
        'lat' => '52.4889',
        'lon' => '10.5506',
        'plz' => '38518',
        'stadtteil' => ['Kernstadt', 'Gamsen', 'Kästorf', 'Neubokel', 'Wilsche'],
        'beschreibung' => 'Gifhorn und Umgebung'
    ],
    'wolfsburg' => [
        'name' => 'Wolfsburg',
        'lat' => '52.4226',
        'lon' => '10.7865',
        'plz' => '38440',
        'stadtteil' => ['Innenstadt', 'Westhagen', 'Detmerode', 'Vorsfelde', 'Fallersleben', 'Reislingen', 'Kreuzheide'],
        'beschreibung' => 'Wolfsburg und allen Stadtteilen'
    ],
    'helmstedt' => [
        'name' => 'Helmstedt',
        'lat' => '52.2277',
        'lon' => '11.0103',
        'plz' => '38350',
        'stadtteil' => ['Kernstadt', 'Barmke', 'Büddenstedt', 'Emmerstedt'],
        'beschreibung' => 'Helmstedt und Umgebung'
    ],
    'bad-harzburg' => [
        'name' => 'Bad Harzburg',
        'lat' => '51.8797',
        'lon' => '10.5597',
        'plz' => '38667',
        'stadtteil' => ['Kernstadt', 'Bündheim', 'Göttingerode', 'Harlingerode', 'Schlewecke'],
        'beschreibung' => 'Bad Harzburg und Umgebung'
    ]
];

// Template laden
$template = file_get_contents(__DIR__ . '/entruempelung-braunschweig.html');

echo "🚀 Landing Page Generator gestartet...\n\n";

$generated = 0;
$skipped = 0;

foreach ($cities as $slug => $city) {
    // Dateiname generieren
    $filename = "entruempelung-{$slug}.html";
    $filepath = __DIR__ . '/' . $filename;

    // Prüfen ob Datei bereits existiert
    if (file_exists($filepath) && $slug === 'braunschweig') {
        echo "⏭️  Übersprungen (Vorlage): {$filename}\n";
        $skipped++;
        continue;
    }

    // Platzhalter ersetzen
    $content = $template;

    // Stadt-spezifische Replacements
    $replacements = [
        // Meta-Tags
        'Braunschweig' => $city['name'],
        'braunschweig' => $slug,
        '52.2689' => $city['lat'],
        '10.5268' => $city['lon'],

        // URLs
        'entruempelung-braunschweig.html' => "entruempelung-{$slug}.html",

        // Texte
        'in Braunschweig und allen Stadtteilen' => "in {$city['beschreibung']}",
        'in Braunschweig und Umgebung' => "in {$city['beschreibung']}",
        'nach Braunschweig' => "nach {$city['name']}",
        'von Braunschweig' => "von {$city['name']}",
        'in Braunschweig' => "in {$city['name']}",
        'Braunschweig für' => "{$city['name']} für",
    ];

    foreach ($replacements as $search => $replace) {
        $content = str_replace($search, $replace, $content);
    }

    // Stadtteile ersetzen
    $stadtteilHTML = '';
    foreach ($city['stadtteil'] as $stadtteil) {
        $stadtteilHTML .= "                <div class=\"area-item\"><i class=\"fas fa-map-marker-alt\"></i> {$stadtteil}</div>\n";
    }

    // Stadtteile-Section ersetzen
    $content = preg_replace(
        '/<div class="area-item">.*?<\/div>\s*<\/div>/s',
        $stadtteilHTML . "            </div>",
        $content,
        1
    );

    // Datei speichern
    file_put_contents($filepath, $content);

    echo "✅ Erstellt: {$filename} ({$city['name']})\n";
    $generated++;
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 GENERIERUNG ABGESCHLOSSEN\n";
echo str_repeat("=", 60) . "\n";
echo "✅ Erfolgreich generiert: {$generated} Landing Pages\n";
echo "⏭️  Übersprungen: {$skipped} Dateien\n";
echo "📝 Gesamt: " . count($cities) . " Städte\n";
echo str_repeat("=", 60) . "\n\n";

// Zusätzlich: Stadtteile von Salzgitter als separate Landing Pages
echo "🏘️  SALZGITTER STADTTEILE...\n\n";

$salzgitter_stadtteile = [
    'salzgitter-bad' => [
        'name' => 'Salzgitter-Bad',
        'lat' => '52.0686',
        'lon' => '10.3406',
        'parent' => 'Salzgitter'
    ],
    'salzgitter-lebenstedt' => [
        'name' => 'Salzgitter-Lebenstedt',
        'lat' => '52.1547',
        'lon' => '10.4086',
        'parent' => 'Salzgitter'
    ],
    'salzgitter-thiede' => [
        'name' => 'Salzgitter-Thiede',
        'lat' => '52.1089',
        'lon' => '10.4378',
        'parent' => 'Salzgitter'
    ],
    'salzgitter-gebhardshagen' => [
        'name' => 'Salzgitter-Gebhardshagen',
        'lat' => '52.1344',
        'lon' => '10.3256',
        'parent' => 'Salzgitter'
    ]
];

$generated_stadtteile = 0;

foreach ($salzgitter_stadtteile as $slug => $stadtteil) {
    $filename = "entruempelung-{$slug}.html";
    $filepath = __DIR__ . '/' . $filename;

    // Template laden und anpassen
    $content = $template;

    $replacements = [
        'Braunschweig' => $stadtteil['name'],
        'braunschweig' => $slug,
        '52.2689' => $stadtteil['lat'],
        '10.5268' => $stadtteil['lon'],
        'entruempelung-braunschweig.html' => "entruempelung-{$slug}.html",
        'in Braunschweig und allen Stadtteilen' => "in {$stadtteil['name']}",
        'in Braunschweig und Umgebung' => "in {$stadtteil['name']} und Umgebung",
        'nach Braunschweig' => "nach {$stadtteil['name']}",
        'von Braunschweig' => "von {$stadtteil['name']}",
        'in Braunschweig' => "in {$stadtteil['name']}",
    ];

    foreach ($replacements as $search => $replace) {
        $content = str_replace($search, $replace, $content);
    }

    file_put_contents($filepath, $content);

    echo "✅ Erstellt: {$filename} ({$stadtteil['name']})\n";
    $generated_stadtteile++;
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 STADTTEILE GENERIERT\n";
echo str_repeat("=", 60) . "\n";
echo "✅ Salzgitter Stadtteile: {$generated_stadtteile}\n";
echo str_repeat("=", 60) . "\n\n";

$total = $generated + $generated_stadtteile;

echo "🎉 GESAMT: {$total} Landing Pages erfolgreich erstellt!\n\n";
echo "📋 NÄCHSTE SCHRITTE:\n";
echo "1. Alle HTML-Dateien auf Server hochladen\n";
echo "2. sitemap.xml aktualisieren (siehe update-sitemap.php)\n";
echo "3. In Google Search Console neue URLs einreichen\n";
echo "4. Interne Verlinkung prüfen\n\n";

echo "🚀 SEO-Dominanz: READY TO LAUNCH!\n";
