/**
 * LANDINGPAGE JAVASCRIPT
 * Conversion-Optimiert für Google Ads
 * Velvetgreen Dienstleistungen - Entrümpelung
 */

// ==================== DOM READY ====================
document.addEventListener('DOMContentLoaded', function() {
    console.log('🌿 Velvetgreen Landingpage loaded');

    // Initialize all components
    initScrollProgressBar();
    initFAQ();
    initContactForm();
    initScrollTracking();
    initClickTracking();
});

// ==================== SCROLL PROGRESS BAR ====================
function initScrollProgressBar() {
    const progressBar = document.querySelector('.scroll-progress-bar');
    if (!progressBar) return;

    window.addEventListener('scroll', () => {
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;
        const scrollTop = window.scrollY;
        const scrollPercent = (scrollTop / (documentHeight - windowHeight)) * 100;

        progressBar.style.width = `${Math.min(scrollPercent, 100)}%`;
    });
}

// ==================== FAQ ACCORDION ====================
function initFAQ() {
    const faqItems = document.querySelectorAll('.lp-faq-item');

    faqItems.forEach(item => {
        const question = item.querySelector('.lp-faq-item__question');

        question.addEventListener('click', () => {
            // Close all other items
            faqItems.forEach(otherItem => {
                if (otherItem !== item && otherItem.classList.contains('active')) {
                    otherItem.classList.remove('active');
                }
            });

            // Toggle current item
            item.classList.toggle('active');

            // Track FAQ click (for Google Analytics)
            const questionText = question.textContent.trim();
            trackEvent('faq_click', {
                question: questionText
            });
        });
    });
}

// ==================== CONTACT FORM ====================
function initContactForm() {
    const form = document.getElementById('lp-contact-form');
    if (!form) return;

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        // Get form data
        const formData = {
            name: document.getElementById('lp-name').value.trim(),
            phone: document.getElementById('lp-phone').value.trim(),
            plz: document.getElementById('lp-plz').value.trim(),
            message: document.getElementById('lp-message').value.trim(),
            privacy: document.getElementById('lp-privacy').checked
        };

        // Validation
        const errors = validateForm(formData);

        if (errors.length > 0) {
            showNotification(errors.join(' '), 'error');
            return;
        }

        // Track form start (if not already tracked)
        trackEvent('form_start', {
            form_name: 'entrümpelung_landingpage'
        });

        // Show loading state
        const submitBtn = form.querySelector('.lp-form__submit');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Wird gesendet...';
        submitBtn.disabled = true;

        // Prepare data for webhook
        const webhookData = new FormData();
        webhookData.append('name', formData.name);
        webhookData.append('phone', formData.phone);
        webhookData.append('service', 'entruempelung');
        webhookData.append('message', `PLZ: ${formData.plz}\n\n${formData.message}`);
        webhookData.append('privacy', 'true');

        try {
            const response = await fetch('contact-webhook.php', {
                method: 'POST',
                body: webhookData
            });

            const data = await response.json();

            if (data.success) {
                // Success!
                // Track conversion
                trackEvent('form_submit_success', {
                    form_name: 'entrümpelung_landingpage',
                    service: 'entruempelung'
                });

                // Redirect to Thank You page after short delay
                setTimeout(() => {
                    window.location.href = 'danke.html';
                }, 500);

            } else {
                showNotification(data.message || 'Es gab ein Problem. Bitte versuchen Sie es erneut oder rufen Sie uns direkt an.', 'error');

                trackEvent('form_submit_error', {
                    form_name: 'entrümpelung_landingpage',
                    error: data.message
                });
            }

        } catch (error) {
            console.error('Form submission error:', error);
            showNotification('Es gab ein Problem beim Versenden. Bitte rufen Sie uns direkt an: +49 5341 2884770', 'error');

            trackEvent('form_submit_error', {
                form_name: 'entrümpelung_landingpage',
                error: error.message
            });
        } finally {
            // Reset button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    });
}

// ==================== FORM VALIDATION ====================
function validateForm(formData) {
    const errors = [];

    if (!formData.name || formData.name.length < 2) {
        errors.push('Bitte geben Sie Ihren Namen ein.');
    }

    if (!formData.phone || formData.phone.length < 5) {
        errors.push('Bitte geben Sie eine gültige Telefonnummer ein.');
    }

    if (!formData.plz || !/^\d{5}$/.test(formData.plz)) {
        errors.push('Bitte geben Sie eine gültige PLZ ein (5 Ziffern).');
    }

    // Nachricht ist optional
    // (keine Validierung nötig)

    if (!formData.privacy) {
        errors.push('Bitte akzeptieren Sie die Datenschutzerklärung.');
    }

    return errors;
}

// ==================== NOTIFICATION SYSTEM ====================
function showNotification(message, type = 'success') {
    // Remove existing notification
    const existing = document.querySelector('.lp-notification');
    if (existing) {
        existing.remove();
    }

    // Create notification
    const notification = document.createElement('div');
    notification.className = `lp-notification lp-notification--${type}`;
    notification.innerHTML = `
        <strong>${type === 'success' ? '✓ Erfolg!' : '⚠ Hinweis'}</strong>
        <p style="margin: 0.5rem 0 0 0;">${message}</p>
    `;

    document.body.appendChild(notification);

    // Auto-remove after 5 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 500);
    }, 5000);
}

// ==================== SCROLL TRACKING ====================
function initScrollTracking() {
    let scrollDepths = {
        25: false,
        50: false,
        75: false,
        100: false
    };

    window.addEventListener('scroll', function() {
        const scrollPercent = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;

        Object.keys(scrollDepths).forEach(depth => {
            if (scrollPercent >= parseInt(depth) && !scrollDepths[depth]) {
                scrollDepths[depth] = true;
                trackEvent('scroll_depth', {
                    percent: depth
                });
            }
        });
    });
}

// ==================== CLICK TRACKING ====================
function initClickTracking() {
    // Track phone clicks
    const phoneLinks = document.querySelectorAll('a[href^="tel:"]');
    phoneLinks.forEach(link => {
        link.addEventListener('click', function() {
            trackEvent('phone_click', {
                location: this.dataset.location || 'unknown',
                phone: this.href.replace('tel:', '')
            });

            // Google Ads Conversion for Phone Clicks
            // TODO: Add your phone click conversion tracking
            // gtag('event', 'conversion', {
            //     'send_to': 'AW-XXXXXXXXX/XXXXXX'
            // });
        });
    });

    // Track all CTA button clicks
    const ctaButtons = document.querySelectorAll('[data-cta]');
    ctaButtons.forEach(button => {
        button.addEventListener('click', function() {
            trackEvent('cta_click', {
                cta_name: this.dataset.cta,
                cta_text: this.textContent.trim()
            });
        });
    });
}

// ==================== EVENT TRACKING (Google Analytics 4) ====================
/**
 * Track custom events
 * This function is prepared for Google Analytics 4
 *
 * To activate:
 * 1. Add Google Analytics gtag.js to your page
 * 2. Uncomment the gtag() call below
 */
function trackEvent(eventName, eventParams = {}) {
    console.log('📊 Track Event:', eventName, eventParams);

    // Google Analytics 4 Event Tracking
    // Uncomment when GA4 is set up:
    /*
    if (typeof gtag !== 'undefined') {
        gtag('event', eventName, eventParams);
    }
    */

    // Facebook Pixel Tracking (optional)
    // Uncomment when Facebook Pixel is set up:
    /*
    if (typeof fbq !== 'undefined') {
        fbq('trackCustom', eventName, eventParams);
    }
    */
}

// ==================== SMOOTH SCROLL ====================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href === '#') return;

        e.preventDefault();

        const target = document.querySelector(href);
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// ==================== FORM FIELD FOCUS TRACKING ====================
// Track which form fields users interact with
document.querySelectorAll('.lp-form__input, .lp-form__textarea').forEach(field => {
    field.addEventListener('focus', function() {
        trackEvent('form_field_focus', {
            field_name: this.id || this.name
        });
    });
});

// ==================== EXIT INTENT (Optional) ====================
/**
 * Detects when user is about to leave the page
 * Can be used to show a last-chance offer or reminder
 */
function initExitIntent() {
    let hasShownExitIntent = false;

    document.addEventListener('mouseout', function(e) {
        if (e.clientY < 0 && !hasShownExitIntent) {
            hasShownExitIntent = true;

            // Track exit intent
            trackEvent('exit_intent');

            // Optional: Show exit-intent popup or message
            // Example:
            // showExitIntentPopup();
        }
    });
}

// Uncomment to enable exit intent tracking
// initExitIntent();

// ==================== PAGE VISIBILITY TRACKING ====================
/**
 * Track when user switches tabs or minimizes browser
 * Useful for understanding engagement
 */
document.addEventListener('visibilitychange', function() {
    if (document.hidden) {
        trackEvent('page_hidden');
    } else {
        trackEvent('page_visible');
    }
});

// ==================== TIME ON PAGE TRACKING ====================
let timeOnPage = 0;
const timeTrackingInterval = setInterval(() => {
    timeOnPage += 30;

    // Track every 30 seconds
    if (timeOnPage % 30 === 0) {
        trackEvent('time_on_page', {
            seconds: timeOnPage
        });
    }
}, 30000); // Every 30 seconds

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    clearInterval(timeTrackingInterval);
    trackEvent('page_exit', {
        total_time: timeOnPage
    });
});

// ==================== CONSOLE MESSAGE ====================
console.log('%c🌿 Velvetgreen Dienstleistungen', 'font-size: 20px; font-weight: bold; color: #4a7c59;');
console.log('%cConversion-Optimierte Landingpage', 'font-size: 14px; color: #666;');
console.log('%cBereit für Google Ads Tracking', 'font-size: 12px; color: #fabe5c;');
