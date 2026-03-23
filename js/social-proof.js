/**
 * Social Proof Notifications System
 * Velvetgreen Dienstleistungen
 */

class SocialProofNotifications {
    constructor() {
        this.notifications = [];
        this.currentIndex = 0;
        this.isVisible = false;
        this.container = null;
        this.interval = null;
        
        this.init();
    }

    async init() {
        // Lade Notifications aus content.json
        await this.loadNotifications();
        
        // Erstelle Container
        this.createContainer();
        
        // Starte Rotation nach 5 Sekunden
        setTimeout(() => {
            this.startRotation();
        }, 5000);
    }

    async loadNotifications() {
        // Hardcoded notifications - keine externe JSON-Datei mehr nötig
        this.notifications = [
            {
                name: "Michael K.",
                action: "hat gerade eine Wohnungsauflösung gebucht",
                time: "vor 12 Minuten",
                enabled: true
            },
            {
                name: "Sarah M.",
                action: "hat eine Entrümpelung angefragt",
                time: "vor 23 Minuten",
                enabled: true
            },
            {
                name: "Thomas B.",
                action: "hat gerade einen Umzug gebucht",
                time: "vor 1 Stunde",
                enabled: true
            },
            {
                name: "Julia S.",
                action: "hat Gartenarbeiten beauftragt",
                time: "vor 2 Stunden",
                enabled: true
            }
        ];

        console.log('✓ Social Proof Notifications geladen:', this.notifications.length);
    }

    createContainer() {
        this.container = document.createElement('div');
        this.container.className = 'social-proof-notification';
        this.container.innerHTML = `
            <div class="social-proof-avatar">
                <i class="fas fa-home"></i>
            </div>
            <div class="social-proof-content">
                <p class="social-proof-name"></p>
                <p class="social-proof-action"></p>
                <p class="social-proof-time"></p>
            </div>
            <button class="social-proof-close" aria-label="Schließen">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        document.body.appendChild(this.container);
        
        // Close Button Event
        this.container.querySelector('.social-proof-close').addEventListener('click', () => {
            this.hide();
        });
    }

    startRotation() {
        if (this.notifications.length === 0) return;
        
        // Zeige erste Notification
        this.show();
        
        // Rotiere alle 15 Sekunden
        this.interval = setInterval(() => {
            this.currentIndex = (this.currentIndex + 1) % this.notifications.length;
            this.show();
        }, 15000);
    }

    show() {
        if (this.notifications.length === 0) return;
        
        const notification = this.notifications[this.currentIndex];
        
        // Update Content
        const nameEl = this.container.querySelector('.social-proof-name');
        const actionEl = this.container.querySelector('.social-proof-action');
        const timeEl = this.container.querySelector('.social-proof-time');
        const avatarEl = this.container.querySelector('.social-proof-avatar i');
        
        nameEl.textContent = notification.name;
        actionEl.textContent = notification.action;
        timeEl.textContent = notification.time;
        
        // Icon basierend auf Action
        if (notification.action.includes('Wohnung') || notification.action.includes('Haushalt')) {
            avatarEl.className = 'fas fa-home';
        } else if (notification.action.includes('Entrümp')) {
            avatarEl.className = 'fas fa-box-open';
        } else if (notification.action.includes('Umzug')) {
            avatarEl.className = 'fas fa-truck-moving';
        } else if (notification.action.includes('Garten')) {
            avatarEl.className = 'fas fa-leaf';
        } else {
            avatarEl.className = 'fas fa-check-circle';
        }
        
        // Zeige Notification
        this.container.classList.add('show');
        this.isVisible = true;
        
        // Verstecke nach 8 Sekunden
        setTimeout(() => {
            this.hide();
        }, 8000);
    }

    hide() {
        this.container.classList.remove('show');
        this.container.classList.add('hide');
        this.isVisible = false;
        
        setTimeout(() => {
            this.container.classList.remove('hide');
        }, 400);
    }

    stop() {
        if (this.interval) {
            clearInterval(this.interval);
        }
        this.hide();
    }
}

// Initialisiere nach DOM-Load
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.socialProof = new SocialProofNotifications();
    });
} else {
    window.socialProof = new SocialProofNotifications();
}
