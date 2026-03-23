// ===================================
// Velvetgreen Dienstleistungen
// Interactive JavaScript Features
// ===================================

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {

    // ===================================
    // Set Current Year in Footer
    // ===================================
    const currentYearElement = document.getElementById('current-year');
    if (currentYearElement) {
        currentYearElement.textContent = new Date().getFullYear();
    }

    // ===================================
    // Scroll Progress Indicator
    // ===================================
    const scrollProgressBar = document.querySelector('.scroll-progress-bar');

    function updateScrollProgress() {
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight - windowHeight;
        const scrolled = window.pageYOffset;
        const progress = (scrolled / documentHeight) * 100;

        if (scrollProgressBar) {
            scrollProgressBar.style.width = progress + '%';
        }
    }

    window.addEventListener('scroll', updateScrollProgress);
    updateScrollProgress();

    // Custom cursor removed - using default cursor

    // ===================================
    // Particle Effect for Hero
    // ===================================
    function createParticles() {
        const particlesContainer = document.getElementById('particles-hero');
        if (!particlesContainer) return;

        const particleCount = 50;

        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.style.position = 'absolute';
            particle.style.width = Math.random() * 4 + 1 + 'px';
            particle.style.height = particle.style.width;
            particle.style.background = Math.random() > 0.5 ? 'var(--color-primary)' : 'var(--color-green-light)';
            particle.style.borderRadius = '50%';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.top = Math.random() * 100 + '%';
            particle.style.opacity = Math.random() * 0.5 + 0.2;
            particle.style.animation = `floatParticle ${Math.random() * 10 + 15}s linear infinite`;
            particle.style.animationDelay = Math.random() * 5 + 's';
            particle.style.filter = 'blur(1px)';

            particlesContainer.appendChild(particle);
        }

        // Add particle animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes floatParticle {
                0% {
                    transform: translateY(0) translateX(0) scale(1);
                    opacity: 0;
                }
                10% {
                    opacity: 0.6;
                }
                90% {
                    opacity: 0.6;
                }
                100% {
                    transform: translateY(-100vh) translateX(${Math.random() * 100 - 50}px) scale(0.5);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    }
    createParticles();

    // ===================================
    // 3D Tilt Effect on Cards
    // ===================================
    const cards = document.querySelectorAll('.service-card, .testimonial-card');
    cards.forEach(card => {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;

            const centerX = rect.width / 2;
            const centerY = rect.height / 2;

            const rotateX = (y - centerY) / 10;
            const rotateY = (centerX - x) / 10;

            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-8px) scale(1.02)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0) rotateY(0) translateY(0) scale(1)';
        });
    });

    // ===================================
    // Parallax Scroll Effects
    // ===================================
    function parallaxScroll() {
        const scrolled = window.pageYOffset;

        // Parallax for blobs
        const blobs = document.querySelectorAll('.blob');
        blobs.forEach((blob, index) => {
            const speed = 0.3 + (index * 0.1);
            const yPos = -(scrolled * speed);
            blob.style.transform = `translateY(${yPos}px)`;
        });

        // Parallax for section headers
        const sectionHeaders = document.querySelectorAll('.section-header');
        sectionHeaders.forEach(header => {
            const rect = header.getBoundingClientRect();
            if (rect.top < window.innerHeight && rect.bottom > 0) {
                const scrollPercentage = 1 - (rect.top / window.innerHeight);
                header.style.transform = `translateY(${scrollPercentage * 30}px)`;
                header.style.opacity = 0.3 + (scrollPercentage * 0.7);
            }
        });
    }

    window.addEventListener('scroll', parallaxScroll);
    parallaxScroll(); // Initial call

    // ===================================
    // Mobile Menu Toggle
    // ===================================
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    const navLinks = document.querySelectorAll('.nav-menu a');

    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            this.classList.toggle('active');
        });

        // Close mobile menu when a link is clicked
        navLinks.forEach(link => {
            link.addEventListener('click', function() {
                navMenu.classList.remove('active');
                mobileMenuToggle.classList.remove('active');
            });
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.navbar')) {
                navMenu.classList.remove('active');
                mobileMenuToggle.classList.remove('active');
            }
        });
    }

    // ===================================
    // Header Scroll Effect
    // ===================================
    const header = document.querySelector('.header');
    let lastScroll = 0;

    window.addEventListener('scroll', function() {
        const currentScroll = window.pageYOffset;

        if (currentScroll > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }

        lastScroll = currentScroll;
    });

    // ===================================
    // Smooth Scroll for Anchor Links
    // ===================================
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const href = this.getAttribute('href');

            // Don't prevent default for # links without target
            if (href === '#') return;

            e.preventDefault();

            const target = document.querySelector(href);
            if (target) {
                const headerHeight = document.querySelector('.header').offsetHeight;
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight;

                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });

    // ===================================
    // Animated Counter for Stats
    // ===================================
    const statsSection = document.querySelector('.stats');
    let statsAnimated = false;

    function animateCounter(element, target, duration) {
        const start = 0;
        const increment = target / (duration / 16); // 60 FPS
        let current = start;

        const timer = setInterval(function() {
            current += increment;
            if (current >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 16);
    }

    function checkStatsVisibility() {
        if (!statsSection || statsAnimated) return;

        const rect = statsSection.getBoundingClientRect();
        const isVisible = rect.top < window.innerHeight && rect.bottom >= 0;

        if (isVisible) {
            statsAnimated = true;

            const statNumbers = document.querySelectorAll('.stat-number');
            statNumbers.forEach(stat => {
                const target = parseInt(stat.getAttribute('data-target'));
                animateCounter(stat, target, 2000);
            });
        }
    }

    window.addEventListener('scroll', checkStatsVisibility);
    checkStatsVisibility(); // Check on page load

    // ===================================
    // Scroll to Top Button
    // ===================================
    const scrollToTopBtn = document.getElementById('scrollToTop');

    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollToTopBtn.classList.add('visible');
        } else {
            scrollToTopBtn.classList.remove('visible');
        }
    });

    if (scrollToTopBtn) {
        scrollToTopBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // ===================================
    // Intersection Observer for Animations
    // ===================================
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe service cards
    const serviceCards = document.querySelectorAll('.service-card');
    serviceCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });

    // Observe process steps
    const processSteps = document.querySelectorAll('.step');
    processSteps.forEach((step, index) => {
        step.style.opacity = '0';
        step.style.transform = 'translateY(30px)';
        step.style.transition = `opacity 0.6s ease ${index * 0.15}s, transform 0.6s ease ${index * 0.15}s`;
        observer.observe(step);
    });

    // Observe testimonial cards
    const testimonialCards = document.querySelectorAll('.testimonial-card');
    testimonialCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
        observer.observe(card);
    });

    // ===================================
    // Contact Form Handling
    // ===================================
    const contactForm = document.getElementById('contactForm');

    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // Get form data
            const formData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                phone: document.getElementById('phone').value,
                service: document.getElementById('service').value,
                message: document.getElementById('message').value,
                privacy: document.getElementById('privacy').checked
            };

            // Basic validation
            if (!formData.name || !formData.email || !formData.phone || !formData.message) {
                showNotification('Bitte füllen Sie alle Pflichtfelder aus.', 'error');
                return;
            }

            if (!formData.privacy) {
                showNotification('Bitte akzeptieren Sie die Datenschutzerklärung.', 'error');
                return;
            }

            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(formData.email)) {
                showNotification('Bitte geben Sie eine gültige E-Mail-Adresse ein.', 'error');
                return;
            }

            // Show loading state
            const submitBtn = contactForm.querySelector('.btn-submit');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Wird gesendet...';
            submitBtn.disabled = true;

            // Send form data to PHP handler
            const formDataToSend = new FormData();
            formDataToSend.append('name', formData.name);
            formDataToSend.append('email', formData.email);
            formDataToSend.append('phone', formData.phone);
            formDataToSend.append('service', formData.service);
            formDataToSend.append('message', formData.message);
            formDataToSend.append('privacy', formData.privacy);

            fetch('contact-webhook.php', {
                method: 'POST',
                body: formDataToSend
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message, 'success');
                    contactForm.reset();
                } else {
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Es gab ein Problem beim Versenden Ihrer Nachricht. Bitte versuchen Sie es später erneut.', 'error');
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    // ===================================
    // Notification System
    // ===================================
    function showNotification(message, type = 'info') {
        // Remove existing notifications
        const existingNotifications = document.querySelectorAll('.notification');
        existingNotifications.forEach(notification => notification.remove());

        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;

        // Set icon based on type
        let icon = 'fa-info-circle';
        if (type === 'success') icon = 'fa-check-circle';
        if (type === 'error') icon = 'fa-exclamation-circle';
        if (type === 'warning') icon = 'fa-exclamation-triangle';

        notification.innerHTML = `
            <i class="fas ${icon}"></i>
            <span>${message}</span>
            <button class="notification-close" aria-label="Schließen">
                <i class="fas fa-times"></i>
            </button>
        `;

        // Add styles
        notification.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            background-color: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#3b82f6'};
            color: white;
            padding: 16px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 12px;
            max-width: 400px;
            z-index: 10000;
            animation: slideInRight 0.3s ease;
        `;

        // Add to document
        document.body.appendChild(notification);

        // Close button handler
        const closeBtn = notification.querySelector('.notification-close');
        closeBtn.style.cssText = `
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            padding: 0;
            margin-left: auto;
            font-size: 1.25rem;
        `;

        closeBtn.addEventListener('click', function() {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        });

        // Auto remove after 5 seconds
        setTimeout(function() {
            if (notification.parentElement) {
                notification.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }
        }, 5000);
    }

    // Add notification animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideInRight {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }

        .notification-close:hover {
            opacity: 0.8;
        }
    `;
    document.head.appendChild(style);

    // ===================================
    // Form Input Focus Effects
    // ===================================
    const formInputs = document.querySelectorAll('.form-group input, .form-group textarea, .form-group select');

    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');

            // Add filled class if input has value
            if (this.value) {
                this.parentElement.classList.add('filled');
            } else {
                this.parentElement.classList.remove('filled');
            }
        });
    });

    // ===================================
    // Lazy Loading for Background Images
    // ===================================
    const lazyBackgrounds = document.querySelectorAll('[data-bg]');

    const bgObserver = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;
                element.style.backgroundImage = `url('${element.dataset.bg}')`;
                bgObserver.unobserve(element);
            }
        });
    });

    lazyBackgrounds.forEach(bg => bgObserver.observe(bg));

    // ===================================
    // Active Navigation Link Highlighting
    // ===================================
    function setActiveNavLink() {
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.nav-menu a[href^="#"]');

        let currentSection = '';

        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            const scrollPosition = window.pageYOffset + 200;

            if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                currentSection = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${currentSection}`) {
                link.classList.add('active');
            }
        });
    }

    window.addEventListener('scroll', setActiveNavLink);
    setActiveNavLink(); // Set on page load

    // Add active link styles
    const navStyle = document.createElement('style');
    navStyle.textContent = `
        .nav-menu a.active:not(.btn-cta) {
            color: var(--color-primary);
            font-weight: bold;
        }
    `;
    document.head.appendChild(navStyle);

    // ===================================
    // Performance: Debounce Scroll Events
    // ===================================
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Wrap expensive scroll handlers with debounce
    const debouncedSetActiveNav = debounce(setActiveNavLink, 100);
    window.addEventListener('scroll', debouncedSetActiveNav);

    // ===================================
    // Console Welcome Message
    // ===================================
    console.log('%c🌿 Velvetgreen Dienstleistungen', 'font-size: 24px; font-weight: bold; color: #fabe5c;');
    console.log('%cProfessionelle Entrümpelung - Schnell. Zuverlässig. Fair.', 'font-size: 14px; color: #666;');
    console.log('%cWebsite designed with ❤️', 'font-size: 12px; color: #999;');

    // ===================================
    // Accessibility: Focus Visible
    // ===================================
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Tab') {
            document.body.classList.add('user-is-tabbing');
        }
    });

    document.addEventListener('mousedown', function() {
        document.body.classList.remove('user-is-tabbing');
    });

    // Add focus styles
    const a11yStyle = document.createElement('style');
    a11yStyle.textContent = `
        body.user-is-tabbing *:focus {
            outline: 3px solid var(--color-primary) !important;
            outline-offset: 2px;
        }
    `;
    document.head.appendChild(a11yStyle);

    // ===================================
    // Cost Calculator
    // ===================================
    const calculateBtn = document.getElementById('calculateBtn');
    const calculatorResult = document.getElementById('calculatorResult');
    const priceValue = document.getElementById('priceValue');
    const breakdownList = document.getElementById('breakdownList');

    // Show/hide additional size input based on checkboxes
    const additionalCheckboxes = ['hasCellar', 'hasAttic', 'hasGarage', 'hasStorage'];
    const additionalSizeGroup = document.getElementById('additionalSizeGroup');

    additionalCheckboxes.forEach(id => {
        const checkbox = document.getElementById(id);
        if (checkbox) {
            checkbox.addEventListener('change', function() {
                const anyChecked = additionalCheckboxes.some(id =>
                    document.getElementById(id).checked
                );
                additionalSizeGroup.style.display = anyChecked ? 'flex' : 'none';
            });
        }
    });

    // Calculate cost function
    function calculateCost(showErrors = false) {
        // Get all values
        const objectSize = parseFloat(document.getElementById('objectSize').value) || 0;
        const fillLevel = parseFloat(document.getElementById('fillLevel').value) || 0;
        const floor = parseInt(document.getElementById('floor').value) || 0;
        const parkingDistance = parseInt(document.getElementById('parkingDistance').value) || 0;

        // Bodenbeläge
        const carpetFullGlued = parseFloat(document.getElementById('carpetFullGlued').value) || 0;
        const carpetPartialGlued = parseFloat(document.getElementById('carpetPartialGlued').value) || 0;
        const laminateGlued = parseFloat(document.getElementById('laminateGlued').value) || 0;
        const laminateClick = parseFloat(document.getElementById('laminateClick').value) || 0;
        const pvcGlued = parseFloat(document.getElementById('pvcGlued').value) || 0;
        const pvcLaid = parseFloat(document.getElementById('pvcLaid').value) || 0;
        const baseboards = parseFloat(document.getElementById('baseboards').value) || 0;

        // Weitere Leistungen
        const wallpaper = parseFloat(document.getElementById('wallpaper').value) || 0;
        const lamps = parseInt(document.getElementById('lamps').value) || 0;
        const drywall = parseFloat(document.getElementById('drywall').value) || 0;

        // Validation
        if (objectSize < 1) {
            if (showErrors) {
                showNotification('Bitte geben Sie eine gültige Objektgröße ein (mindestens 1 m²).', 'error');
            }
            return;
        }

        if (fillLevel === 0) {
            if (showErrors) {
                showNotification('Bitte wählen Sie einen Befüllungsgrad aus.', 'error');
            }
            return;
        }

        // Base calculation
        let totalCost = 0;
        let breakdown = [];

        // Grundpreis: Quadratmeter × Befüllungsgrad
        const baseCost = objectSize * fillLevel;
        totalCost += baseCost;

        let fillLevelLabel = '';
        if (fillLevel === 20) fillLevelLabel = 'Normal';
        else if (fillLevel === 30) fillLevelLabel = 'Stark';
        else if (fillLevel === 45) fillLevelLabel = 'Extrem';

        breakdown.push({
            label: `Entrümpelung (${objectSize} m² × ${fillLevelLabel})`,
            value: baseCost
        });

        // Etagen-Aufpreis
        if (floor > 0) {
            const floorCost = floor * 50;
            totalCost += floorCost;
            breakdown.push({
                label: `Etagen-Aufpreis (${floor}. ${floor === 1 ? 'Stock' : 'Stockwerke'})`,
                value: floorCost
            });
        }

        // Parkplatz-Entfernung
        if (parkingDistance > 0) {
            totalCost += parkingDistance;
            let distanceLabel = '';
            if (parkingDistance === 100) {
                distanceLabel = '16-30 Meter';
            } else if (parkingDistance === 200) {
                distanceLabel = '31-50 Meter';
            }
            breakdown.push({
                label: `Parkplatz-Entfernung (${distanceLabel})`,
                value: parkingDistance
            });
        }

        // Bodenbeläge
        if (carpetFullGlued > 0) {
            const cost = carpetFullGlued * 20;
            totalCost += cost;
            breakdown.push({
                label: `Teppich vollverklebt (${carpetFullGlued} m²)`,
                value: cost
            });
        }

        if (carpetPartialGlued > 0) {
            const cost = carpetPartialGlued * 13;
            totalCost += cost;
            breakdown.push({
                label: `Teppich teilverklebt (${carpetPartialGlued} m²)`,
                value: cost
            });
        }

        if (laminateGlued > 0) {
            const cost = laminateGlued * 20;
            totalCost += cost;
            breakdown.push({
                label: `Laminat verklebt (${laminateGlued} m²)`,
                value: cost
            });
        }

        if (laminateClick > 0) {
            const cost = laminateClick * 8;
            totalCost += cost;
            breakdown.push({
                label: `Laminat Klick (${laminateClick} m²)`,
                value: cost
            });
        }

        if (pvcGlued > 0) {
            const cost = pvcGlued * 20;
            totalCost += cost;
            breakdown.push({
                label: `PVC verklebt (${pvcGlued} m²)`,
                value: cost
            });
        }

        if (pvcLaid > 0) {
            const cost = pvcLaid * 5;
            totalCost += cost;
            breakdown.push({
                label: `PVC verlegt (${pvcLaid} m²)`,
                value: cost
            });
        }

        if (baseboards > 0) {
            const cost = baseboards * 2;
            totalCost += cost;
            breakdown.push({
                label: `Sockelleisten entfernen (${baseboards} m)`,
                value: cost
            });
        }

        // Weitere Leistungen
        if (wallpaper > 0) {
            const cost = wallpaper * 10;
            totalCost += cost;
            breakdown.push({
                label: `Tapeten entfernen (${wallpaper} m²)`,
                value: cost
            });
        }

        if (lamps > 0) {
            const cost = lamps * 10;
            totalCost += cost;
            breakdown.push({
                label: `Lampen demontieren (${lamps} Stück)`,
                value: cost
            });
        }

        if (drywall > 0) {
            const cost = drywall * 30;
            totalCost += cost;
            breakdown.push({
                label: `Trockenbauwand entfernen (${drywall} m²)`,
                value: cost
            });
        }

        // Display result
        priceValue.textContent = totalCost.toLocaleString('de-DE', {
            style: 'currency',
            currency: 'EUR'
        });

        // Display breakdown
        breakdownList.innerHTML = '';
        breakdown.forEach(item => {
            const div = document.createElement('div');
            div.className = 'breakdown-item';
            div.innerHTML = `
                <span>${item.label}</span>
                <span>${item.value.toLocaleString('de-DE', { style: 'currency', currency: 'EUR' })}</span>
            `;
            breakdownList.appendChild(div);
        });

        // Add total
        const totalDiv = document.createElement('div');
        totalDiv.className = 'breakdown-item';
        totalDiv.style.marginTop = 'var(--spacing-sm)';
        totalDiv.style.paddingTop = 'var(--spacing-sm)';
        totalDiv.style.borderTop = '2px solid rgba(255, 255, 255, 0.3)';
        totalDiv.innerHTML = `
            <span style="font-weight: bold; font-size: 1rem;">Gesamtsumme</span>
            <span style="font-size: 1.25rem;">${totalCost.toLocaleString('de-DE', { style: 'currency', currency: 'EUR' })}</span>
        `;
        breakdownList.appendChild(totalDiv);

        // Result is always visible, no animation needed
    }

    // Calculate button click handler
    if (calculateBtn) {
        calculateBtn.addEventListener('click', function(e) {
            e.preventDefault();

            // Validate required fields
            const objectSize = document.getElementById('objectSize').value;
            const fillLevel = document.getElementById('fillLevel').value;

            if (!objectSize || objectSize < 1) {
                showNotification('Bitte geben Sie eine gültige Objektgröße ein (mindestens 1 m²).', 'error');
                document.getElementById('objectSize').focus();
                return;
            }

            if (!fillLevel) {
                showNotification('Bitte wählen Sie einen Befüllungsgrad aus.', 'error');
                document.getElementById('fillLevel').focus();
                return;
            }

            calculateCost();
        });
    }

    // Real-time calculation on input change
    const calcInputs = document.querySelectorAll('#costCalculator input, #costCalculator select');
    calcInputs.forEach(input => {
        input.addEventListener('change', function() {
            // Auto-calculate on every change
            const objectType = document.getElementById('objectType').value;
            const objectSize = document.getElementById('objectSize').value;

            if (objectType && objectSize >= 10) {
                calculateCost();
            }
        });
    });

    // Initial calculation on page load
    calculateCost();

    // ===================================
    // Before/After Slider
    // ===================================
    const beforeAfterSlider = document.getElementById('beforeAfterSlider');
    const afterImage = document.querySelector('.after-image');
    const sliderLine = document.querySelector('.slider-line');

    if (beforeAfterSlider && afterImage && sliderLine) {
        beforeAfterSlider.addEventListener('input', function() {
            const value = this.value;
            afterImage.style.clipPath = `inset(0 ${100 - value}% 0 0)`;
            sliderLine.style.left = value + '%';
        });
    }

    // ===================================
    // FAQ Accordion
    // ===================================
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');

        question.addEventListener('click', () => {
            const isActive = item.classList.contains('active');

            // Close all items
            faqItems.forEach(faqItem => {
                faqItem.classList.remove('active');
            });

            // Open clicked item if it wasn't active
            if (!isActive) {
                item.classList.add('active');
            }
        });
    });


    // ===================================
    // Social Proof Notifications
    // ===================================
    const notifications = [
        { name: 'Maria S.', action: 'hat gerade eine Entrümpelung gebucht', icon: 'fa-check-circle' },
        { name: 'Thomas M.', action: 'hat uns mit 5 Sternen bewertet', icon: 'fa-star' },
        { name: 'Sarah W.', action: 'hat einen Kostenvoranschlag angefordert', icon: 'fa-file-invoice-dollar' },
        { name: 'Michael K.', action: 'hat gerade eine Wohnungsauflösung gebucht', icon: 'fa-home' },
        { name: 'Lisa B.', action: 'empfiehlt uns weiter', icon: 'fa-heart' }
    ];

    let notificationIndex = 0;
    let notificationTimeout;

    function showSocialProof() {
        // Create notification if doesn't exist
        let notification = document.querySelector('.social-proof');

        if (!notification) {
            notification = document.createElement('div');
            notification.className = 'social-proof';
            notification.innerHTML = `
                <div class="social-proof-icon">
                    <i class="fas"></i>
                </div>
                <div class="social-proof-content">
                    <h4></h4>
                    <p></p>
                </div>
                <button class="social-proof-close" aria-label="Schließen">
                    <i class="fas fa-times"></i>
                </button>
            `;
            document.body.appendChild(notification);

            // Close button
            notification.querySelector('.social-proof-close').addEventListener('click', () => {
                notification.classList.remove('show');
            });
        }

        const data = notifications[notificationIndex];
        notification.querySelector('.social-proof-icon i').className = `fas ${data.icon}`;
        notification.querySelector('.social-proof-content h4').textContent = data.name;
        notification.querySelector('.social-proof-content p').textContent = data.action;

        // Show notification
        setTimeout(() => {
            notification.classList.add('show');
        }, 500);

        // Hide after 5 seconds
        clearTimeout(notificationTimeout);
        notificationTimeout = setTimeout(() => {
            notification.classList.remove('show');
        }, 5000);

        // Next notification
        notificationIndex = (notificationIndex + 1) % notifications.length;
    }

    // Show first notification after 5 seconds, then every 15 seconds
    setTimeout(showSocialProof, 5000);
    setInterval(showSocialProof, 15000);

    // ===================================
    // Scroll Reveal Animations
    // ===================================
    const revealElements = document.querySelectorAll('.service-card, .testimonial-card, .step, .faq-item');

    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry, index) => {
            if (entry.isIntersecting) {
                setTimeout(() => {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, index * 100);
                revealObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    revealElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        revealObserver.observe(el);
    });

});

// ===================================
// Service Worker Registration (Optional)
// ===================================
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        // Uncomment to enable service worker
        // navigator.serviceWorker.register('/sw.js')
        //     .then(registration => console.log('Service Worker registered'))
        //     .catch(err => console.log('Service Worker registration failed'));
    });
}
