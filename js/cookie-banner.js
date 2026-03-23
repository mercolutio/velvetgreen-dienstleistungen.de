/**
 * Cookie Banner & DSGVO Cookie-Verwaltung
 * Velvetgreen Dienstleistungen
 */

class CookieManager {
    constructor() {
        this.cookieSettings = {
            necessary: true, // Immer aktiv
            analytics: false,
            marketing: false
        };
        
        this.init();
    }

    init() {
        // Prüfe ob bereits eine Einwilligung existiert
        const consent = this.getConsent();
        
        if (!consent) {
            // Zeige Banner nach kurzer Verzögerung
            setTimeout(() => this.showBanner(), 500);
        } else {
            // Lade gespeicherte Einstellungen
            this.cookieSettings = consent;
            this.applyCookieSettings();
        }

        // Event Listeners
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Banner Buttons
        const acceptBtn = document.getElementById('cookie-accept-all');
        const declineBtn = document.getElementById('cookie-decline-all');
        const settingsBtn = document.getElementById('cookie-settings-btn');
        
        if (acceptBtn) {
            acceptBtn.addEventListener('click', () => this.acceptAll());
        }
        
        if (declineBtn) {
            declineBtn.addEventListener('click', () => this.declineAll());
        }
        
        if (settingsBtn) {
            settingsBtn.addEventListener('click', () => this.openSettings());
        }

        // Settings Modal
        const closeBtn = document.querySelector('.cookie-settings-close');
        const saveBtn = document.getElementById('cookie-save-settings');
        const modal = document.getElementById('cookie-settings-modal');
        
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.closeSettings());
        }
        
        if (saveBtn) {
            saveBtn.addEventListener('click', () => this.saveSettings());
        }

        // Close modal on backdrop click
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeSettings();
                }
            });
        }

        // Footer Cookie Settings Link
        const footerLink = document.querySelector('.cookie-settings-link');
        if (footerLink) {
            footerLink.addEventListener('click', (e) => {
                e.preventDefault();
                this.openSettings();
            });
        }
    }

    showBanner() {
        const banner = document.getElementById('cookie-banner');
        if (banner) {
            banner.classList.add('show');
        }
    }

    hideBanner() {
        const banner = document.getElementById('cookie-banner');
        if (banner) {
            banner.classList.remove('show');
        }
    }

    acceptAll() {
        this.cookieSettings = {
            necessary: true,
            analytics: true,
            marketing: true
        };
        
        this.saveConsent();
        this.hideBanner();
        this.applyCookieSettings();
    }

    declineAll() {
        this.cookieSettings = {
            necessary: true,
            analytics: false,
            marketing: false
        };
        
        this.saveConsent();
        this.hideBanner();
        this.applyCookieSettings();
    }

    openSettings() {
        const modal = document.getElementById('cookie-settings-modal');
        if (modal) {
            modal.classList.add('show');
            
            // Setze Toggle-Status basierend auf aktuellen Einstellungen
            const analyticsToggle = document.getElementById('cookie-analytics');
            const marketingToggle = document.getElementById('cookie-marketing');
            
            if (analyticsToggle) {
                analyticsToggle.checked = this.cookieSettings.analytics;
            }
            
            if (marketingToggle) {
                marketingToggle.checked = this.cookieSettings.marketing;
            }
        }
    }

    closeSettings() {
        const modal = document.getElementById('cookie-settings-modal');
        if (modal) {
            modal.classList.remove('show');
        }
    }

    saveSettings() {
        // Lese Toggle-Status
        const analyticsToggle = document.getElementById('cookie-analytics');
        const marketingToggle = document.getElementById('cookie-marketing');
        
        this.cookieSettings = {
            necessary: true,
            analytics: analyticsToggle ? analyticsToggle.checked : false,
            marketing: marketingToggle ? marketingToggle.checked : false
        };
        
        this.saveConsent();
        this.closeSettings();
        this.hideBanner();
        this.applyCookieSettings();
    }

    saveConsent() {
        const consent = {
            ...this.cookieSettings,
            timestamp: new Date().toISOString()
        };
        
        localStorage.setItem('cookie_consent', JSON.stringify(consent));
    }

    getConsent() {
        const consent = localStorage.getItem('cookie_consent');
        
        if (!consent) {
            return null;
        }
        
        try {
            const parsed = JSON.parse(consent);
            
            // Prüfe ob Consent älter als 6 Monate ist
            const timestamp = new Date(parsed.timestamp);
            const sixMonthsAgo = new Date();
            sixMonthsAgo.setMonth(sixMonthsAgo.getMonth() - 6);
            
            if (timestamp < sixMonthsAgo) {
                // Consent ist abgelaufen
                localStorage.removeItem('cookie_consent');
                return null;
            }
            
            return {
                necessary: parsed.necessary !== undefined ? parsed.necessary : true,
                analytics: parsed.analytics || false,
                marketing: parsed.marketing || false
            };
        } catch (e) {
            return null;
        }
    }

    applyCookieSettings() {
        // Erst alle nicht-notwendigen Cookies löschen
        this.deleteAllNonEssentialCookies();

        // Analytics Cookies (z.B. Google Analytics)
        if (this.cookieSettings.analytics) {
            this.enableAnalytics();
        } else {
            this.disableAnalytics();
        }

        // Marketing Cookies (z.B. Google Ads, Facebook Pixel)
        if (this.cookieSettings.marketing) {
            this.enableMarketing();
        } else {
            this.disableMarketing();
        }

        console.log('Cookie-Einstellungen angewendet:', this.cookieSettings);
    }

    deleteAllNonEssentialCookies() {
        // Liste aller bekannten Tracking-Cookies
        const analyticsCookies = [
            '_ga', '_gid', '_gat', '_gat_gtag_', '__utma', '__utmb', '__utmc', '__utmt', '__utmz',
            '_gcl_au', '_dc_gtm_'
        ];

        const marketingCookies = [
            '_fbp', '_fbc', 'fr', 'tr', 'ads', 'MUID', 'IDE', 'test_cookie',
            'conversion', 'drt', 'YSC', 'PREF', 'VISITOR_INFO1_LIVE'
        ];

        // Lösche Analytics Cookies wenn nicht erlaubt
        if (!this.cookieSettings.analytics) {
            analyticsCookies.forEach(cookieName => {
                this.deleteCookie(cookieName);
            });
        }

        // Lösche Marketing Cookies wenn nicht erlaubt
        if (!this.cookieSettings.marketing) {
            marketingCookies.forEach(cookieName => {
                this.deleteCookie(cookieName);
            });
        }
    }

    deleteCookie(name) {
        // Lösche Cookie auf allen möglichen Pfaden und Domains
        const domains = [
            window.location.hostname,
            '.' + window.location.hostname,
            '.velvetgreen-dienstleistungen.de'
        ];

        const paths = ['/', '/blog', '/admin'];

        domains.forEach(domain => {
            paths.forEach(path => {
                // Setze Cookie mit Ablaufdatum in der Vergangenheit
                document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=${path}; domain=${domain};`;
                document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=${path};`;
            });
        });

        // Auch ohne Domain setzen
        document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
    }

    enableAnalytics() {
        // Google Analytics (nur laden wenn erlaubt!)
        if (!window.gtag && !document.querySelector('script[src*="googletagmanager"]')) {
            // Beispiel: Google Analytics laden
            /*
            const script = document.createElement('script');
            script.async = true;
            script.src = 'https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID';
            document.head.appendChild(script);

            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            window.gtag = gtag;
            gtag('js', new Date());
            gtag('config', 'GA_MEASUREMENT_ID', {
                'anonymize_ip': true,
                'cookie_flags': 'SameSite=None;Secure'
            });
            */
        }
        console.log('✓ Analytics aktiviert');
    }

    disableAnalytics() {
        // Analytics Cookies löschen
        const analyticsCookies = [
            '_ga', '_gid', '_gat', '_gat_gtag_', '__utma', '__utmb',
            '__utmc', '__utmt', '__utmz', '_gcl_au', '_dc_gtm_'
        ];

        analyticsCookies.forEach(cookie => this.deleteCookie(cookie));

        // Google Analytics deaktivieren
        if (window.gtag) {
            window['ga-disable-GA_MEASUREMENT_ID'] = true;
        }

        console.log('✗ Analytics deaktiviert & Cookies gelöscht');
    }

    enableMarketing() {
        // Marketing Cookies aktivieren
        // Facebook Pixel, Google Ads etc. hier laden
        /*
        // Beispiel Facebook Pixel:
        if (!window.fbq) {
            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(window, document,'script',
            'https://connect.facebook.net/en_US/fbevents.js');
            fbq('init', 'YOUR_PIXEL_ID');
            fbq('track', 'PageView');
        }
        */
        console.log('✓ Marketing aktiviert');
    }

    disableMarketing() {
        // Marketing Cookies löschen
        const marketingCookies = [
            '_fbp', '_fbc', 'fr', 'tr', 'ads', 'MUID', 'IDE',
            'test_cookie', 'conversion', 'drt', 'YSC', 'PREF',
            'VISITOR_INFO1_LIVE'
        ];

        marketingCookies.forEach(cookie => this.deleteCookie(cookie));

        // Facebook Pixel deaktivieren
        if (window.fbq) {
            window.fbq = function() {};
        }

        console.log('✗ Marketing deaktiviert & Cookies gelöscht');
    }

    // Hilfsfunktion: Alle gesetzten Cookies auflisten (für Debugging)
    listAllCookies() {
        const cookies = document.cookie.split(';').map(c => c.trim().split('=')[0]);
        console.log('Aktuell gesetzte Cookies:', cookies);
        return cookies;
    }

    // Public API für externe Nutzung
    revokeConsent() {
        localStorage.removeItem('cookie_consent');
        location.reload();
    }
}

// Initialisiere Cookie Manager wenn DOM geladen ist
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.cookieManager = new CookieManager();
    });
} else {
    window.cookieManager = new CookieManager();
}
