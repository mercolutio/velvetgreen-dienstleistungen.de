# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Velvet Green Dienstleistungen — a German local SEO website for junk removal and household cleanup services in the Braunschweig/Niedersachsen region. Pure PHP (no framework), vanilla JS, Apache-served.

## Tech Stack

- **Backend:** PHP 7.x+, SQLite (blog), JSON (content storage)
- **Frontend:** Static HTML, vanilla CSS/JS, Font Awesome 6.4.0 (CDN)
- **Server:** Apache with `.htaccess` security headers and caching
- **Integrations:** n8n (workflow automation), OpenAI GPT-4o-mini (content generation), Pexels API (images)
- **No build tools** — no npm, no Composer, no bundler. Files are served directly.

## Architecture

### Content Management
- **Admin panel** (`admin/`) — PHP-based CMS behind session auth (bcrypt, CSRF tokens, 30-min timeout)
- **Content stored in** `admin/data/content.json` — hero section, services, contact info, calculator pricing
- **Blog stored in** `blog/data/blog.db` — SQLite with `posts` and `keywords` tables

### Landing Page Generation
- `generate-landing-pages.php` generates 400+ city/district-specific HTML pages from `entruempelung-braunschweig.html` as template
- Template replacement: city names, GPS coordinates, postal codes, district lists
- Output files follow pattern: `{service}-{city}-{district}.html`
- Service categories: Entrümpelung, Gewerbe, Haushaltsauflösung, Kellerentrümpelung, Messiewohnung
- **Do not edit generated landing pages directly** — edit the template or generator script instead

### Contact Form
- `contact-handler.php` — sends email via PHP `mail()`
- `contact-webhook.php` — posts to n8n webhook for automation
- Both use: POST-only, honeypot spam protection, input validation, JSON responses

### Blog System
- `blog/index.php` — listing page; `blog/artikel.php` — article viewer (slug-based URLs)
- Keywords feed into n8n workflow for automated article generation
- `wp-json/` provides REST API endpoints mimicking WordPress structure for n8n integration

### REST API (`wp-json/`)
- `wp-json/wp/v2/posts.php` — create/list blog posts (basic auth)
- `wp-json/keyword-manager/v1/keywords.php` — keyword CRUD (basic auth)
- Auth credentials defined in `blog/config.php`

## Key Files

| File | Purpose |
|------|---------|
| `admin/config.php` | Auth, session mgmt, content helpers, default content structure |
| `blog/config.php` | SQLite init, API auth, slug generation |
| `admin/data/content.json` | All CMS-managed content (hero, services, contact, calculator) |
| `generate-landing-pages.php` | Bulk landing page generator from template |
| `.htaccess` | Security headers, file protection, GZIP, caching rules |
| `n8n-workflow.json` | n8n automation workflow config |

## Themenauswahl (überschreibt Schritt 1 im System-Prompt)

### ⚠️ Reihenfolge ist zwingend: ZUERST `--list-done` → DANN Service-Rotation → DANN WebSearch

**1. Done-Liste laden:**
```
./generate-single-landing-page.sh --list-done
```

**2. Service per Rotation wählen** – zähle Einträge pro Service und wähle den mit der **niedrigsten Anzahl**. Bei Gleichstand: `entruempelung` → `haushaltsaufloesung` → `gewerbe` → `kellerentruempelung` → `messiewohnung`.

**3. Neue Stadt per WebSearch finden** – Ziel ist eine Stadt, die **noch gar nicht** in der Done-Liste vorkommt (für keinen Service). Bevorzuge Orte im ~80-km-Radius um Salzgitter, die bisher keine einzige Velvetgreen-Seite haben. Beispiele: Seesen, Bad Gandersheim, Alfeld (Leine), Bockenem, Clausthal-Zellerfeld, Liebenburg, Langelsheim, Lutter am Barenberge, Einbeck, Dassel, Greene, Holzminden, Northeim, Osterode am Harz.

**4. Kombination = Service aus Schritt 2 × Stadt aus Schritt 3.** Prüfe kurz, dass diese genaue Kombination nicht in der Done-Liste steht.

---

## Benachrichtigungssystem (überschreibt Schritt 6 im System-Prompt)

**WICHTIG:** Curl-Webhooks an n8n.mercolutio.eu sind in dieser Umgebung geblockt.
Stattdessen gilt für Schritt 6:

1. Lies `pending-notifications.json` aus dem Projekt-Root
2. Füge einen neuen Eintrag ans Ende des Arrays an:
   ```json
   {
     "id": "[service]-[stadt]",
     "subject": "Neue Landing Page: [Service] [Stadt]",
     "html_body": "...vollständiges HTML der Benachrichtigungs-E-Mail..."
   }
   ```
3. Die `html_body` enthält dieselben Inhalte wie zuvor beschrieben (Ansprache an Deniz, Live-Link, SERP-Preview, Keyword-Tabelle, lokale Details, Unterschrift "Mercolutio Agent")
4. `pending-notifications.json` mit committen (git add)
5. n8n pollt die Datei stündlich und sendet alle noch nicht gesendeten Einträge automatisch

Die `id` muss eindeutig sein (z.B. `messiewohnung-hildesheim`). Bereits gesendete IDs werden von n8n in Static Data gespeichert und nicht erneut gesendet.

## Deployment

Direct file upload to Apache server — no build step, no CI/CD pipeline. PHP files execute server-side; HTML/CSS/JS served as static assets.
