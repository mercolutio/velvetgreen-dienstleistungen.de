-- SEO Keyword Import für Blog-System
-- Aufruf: sqlite3 blog/data/blog.db < import-keywords.sql

-- TIER 1: Lokale Haupt-Keywords (Hohe Priorität)
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung Salzgitter', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung Braunschweig', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung Wolfenbüttel', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung Goslar', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung Hildesheim', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung Peine', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung Gifhorn', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung Wolfsburg', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung Helmstedt', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung Bad Harzburg', 0);

INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Haushaltsauflösung Salzgitter', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Haushaltsauflösung Braunschweig', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Haushaltsauflösung Wolfenbüttel', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Haushaltsauflösung Goslar', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Haushaltsauflösung Hildesheim', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Haushaltsauflösung Peine', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Haushaltsauflösung Gifhorn', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Haushaltsauflösung Wolfsburg', 0);

INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Wohnungsauflösung Salzgitter', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Wohnungsauflösung Braunschweig', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Wohnungsauflösung Wolfenbüttel', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Wohnungsauflösung Niedersachsen', 0);

-- TIER 2: Service-Spezifische Keywords
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Kellerentrümpelung Salzgitter', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Dachbodenräumung Braunschweig', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Garagenentrümpelung Niedersachsen', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Messiewohnung entrümpeln Salzgitter', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Betriebsauflösung Braunschweig', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Geschäftsauflösung Salzgitter', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Praxisauflösung Niedersachsen', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Büroauflösung Braunschweig', 0);

INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Sperrmüll entsorgen Salzgitter', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Altmetall entsorgen Braunschweig', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Elektroschrott entsorgen Salzgitter', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Möbel entsorgen Niedersachsen', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Schrott abholen Salzgitter', 0);

INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Nachlassauflösung Salzgitter', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Wohnungsauflösung nach Todesfall Braunschweig', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Erbenermittlung Haushaltsauflösung Salzgitter', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Testamentsvollstrecker Wohnungsräumung', 0);

-- TIER 3: Long-Tail Keywords (Kaufabsicht)
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Was kostet Entrümpelung Salzgitter', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung Kosten pro m2 Niedersachsen', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Haushaltsauflösung Preise Braunschweig', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Kostenlose Besichtigung Entrümpelung Salzgitter', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Günstige Entrümpelung Niedersachsen', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung Festpreis Salzgitter', 0);

INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung kurzfristig Salzgitter', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Express Entrümpelung Braunschweig', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung am Wochenende Salzgitter', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Sofort Entrümpelung Niedersachsen', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('24h Entrümpelung Braunschweig', 0);

INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Beste Entrümpelungsfirma Salzgitter', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelungsfirma Vergleich Niedersachsen', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung Bewertungen Braunschweig', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Seriöse Entrümpelungsfirma Salzgitter', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Zuverlässige Haushaltsauflösung Braunschweig', 0);

-- TIER 4: Frage-Keywords (Voice Search)
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Wie funktioniert Entrümpelung Salzgitter', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Wie läuft Haushaltsauflösung ab', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Wie viel kostet Wohnungsauflösung', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Wie entrümple ich richtig', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Wie lange dauert Entrümpelung', 0);

INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Was kostet Entrümpelung 50 qm', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Was wird bei Haushaltsauflösung mitgenommen', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Was passiert mit Möbeln bei Entrümpelung', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Was bedeutet Wertanrechnung bei Haushaltsauflösung', 0);

INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Wann brauche ich Entrümpelung', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Wann lohnt sich Haushaltsauflösung', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Wann Entrümpelung steuerlich absetzbar', 0);

INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Wer zahlt Entrümpelung bei Todesfall', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Wer entsorgt Sperrmüll kostenlos Salzgitter', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Wer darf Entrümpelung durchführen', 0);

-- TIER 5: Stadtteil-Spezifisch
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung Salzgitter-Bad', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung Salzgitter-Lebenstedt', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung Salzgitter-Thiede', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Haushaltsauflösung Salzgitter-Gebhardshagen', 0);

INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung Braunschweig Innenstadt', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung Braunschweig Weststadt', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Haushaltsauflösung Braunschweig Querum', 0);

-- TIER 6: B2B & Gewerbe
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Gewerbliche Entrümpelung Salzgitter', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Firmenauflösung Braunschweig', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Lagerauflösung Niedersachsen', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Hotelauflösung Salzgitter', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Gastronomie Entrümpelung Braunschweig', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Baustellenräumung Salzgitter', 0);

-- TIER 7: Umwelt & Nachhaltigkeit
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Umweltgerechte Entrümpelung Salzgitter', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Nachhaltige Haushaltsauflösung Niedersachsen', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Recycling bei Entrümpelung Braunschweig', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung ohne Müll Salzgitter', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Möbel Spende Entrümpelung Braunschweig', 0);

-- TIER 8: Ratgeber & How-To
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung Checkliste', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Haushaltsauflösung planen Schritt für Schritt', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Keller entrümpeln Tipps', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Dachboden aufräumen Anleitung', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Messiewohnung richtig entrümpeln', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung nach Trennung', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung vor Umzug', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Minimalismus Entrümpelung', 0);

-- TIER 9: Saisonale Keywords
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Frühjahrsputz Entrümpelung', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung Jahresende', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Sommerschlussverkauf Möbel Entrümpelung', 0);
INSERT OR IGNORE INTO keywords (keyword, processed) VALUES ('Entrümpelung vor Weihnachten', 0);

-- Statistik anzeigen
SELECT '=======================' AS '';
SELECT '📊 KEYWORD IMPORT ABGESCHLOSSEN' AS '';
SELECT '=======================' AS '';
SELECT COUNT(*) || ' Keywords in Datenbank' AS 'Status' FROM keywords;
SELECT COUNT(*) || ' Keywords warten auf Verarbeitung' AS 'Status' FROM keywords WHERE processed = 0;
SELECT COUNT(*) || ' Keywords bereits verarbeitet' AS 'Status' FROM keywords WHERE processed = 1;
SELECT '=======================' AS '';
