/**
 * Stunning Bakery Frontend JavaScript
 * Modern, Interactive, and Extremely Attractive
 */

class BakeryFrontend {
    constructor() {
        this.scrollPosition = 0;
        this.ticking = false;
        this.animations = [];
        
        this.init();
    }

    init() {
        this.initializeScrollEffects();
        this.initializeAnimations();
        this.initializeInteractions();
        this.initializeParallax();
        this.initializeCartFunctionality();
        this.initializeLazyLoading();
        this.initializeToasts();
        
        // Initialize when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                this.bindEvents();
                this.startAnimations();
            });
        } else {
            this.bindEvents();
            this.startAnimations();
        }
    }

    initializeScrollEffects() {
        // Header scroll effect
        let lastScrollTop = 0;
        const header = document.querySelector('header');
        
        const handleScroll = () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            // Add scrolled class for glassmorphism effect
            if (scrollTop > 50) {
                header?.classList.add('scrolled');
            } else {
                header?.classList.remove('scrolled');
            }
            
            // Hide/show header on scroll
            if (scrollTop > lastScrollTop && scrollTop > 100) {
                header?.style.setProperty('transform', 'translateY(-100%)');
            } else {
                header?.style.setProperty('transform', 'translateY(0)');
            }
            
            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
        };

        // Throttle scroll events
        let scrollTimeout;
        window.addEventListener('scroll', () => {
            if (scrollTimeout) {
                clearTimeout(scrollTimeout);
            }
            scrollTimeout = setTimeout(handleScroll, 10);
        }, { passive: true });
    }

    initializeAnimations() {
        // Intersection Observer for scroll animations
        const observerOptions = {
            root: null,
            rootMargin: '0px 0px -100px 0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    
                    // Animate counters
                    if (entry.target.classList.contains('stats-number')) {
                        this.animateCounter(entry.target);
                    }
                    
                    // Stagger animations for product cards
                    if (entry.target.classList.contains('product-card')) {
                        const delay = Array.from(entry.target.parentNode.children).indexOf(entry.target) * 100;
                        setTimeout(() => {
                            entry.target.style.animation = `fadeInUp 0.6s ease forwards`;
                        }, delay);
                    }
                }
            });
        }, observerOptions);

        // Observe elements
        document.querySelectorAll('.scroll-animate, .product-card, .feature-card, .category-card, .stats-number').forEach(el => {
            observer.observe(el);
        });
    }

    initializeInteractions() {
        // Enhanced hover effects for product cards
        document.querySelectorAll('.product-card').forEach(card => {
            const overlay = card.querySelector('.product-overlay');
            const image = card.querySelector('.product-image img');
            
            card.addEventListener('mouseenter', (e) => {
                this.handleProductHover(e, true);
            });
            
            card.addEventListener('mouseleave', (e) => {
                this.handleProductHover(e, false);
            });
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(anchor.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Enhanced search with live suggestions
        const searchInput = document.querySelector('input[name="q"]');
        if (searchInput) {
            this.initializeSearch(searchInput);
        }
    }

    handleProductHover(event, isEntering) {
        const card = event.currentTarget;
        const image = card.querySelector('.product-image img');
        const overlay = card.querySelector('.product-overlay');
        
        if (isEntering) {
            // Add magnetic effect
            card.addEventListener('mousemove', this.magneticEffect);
            
            // Image zoom effect
            if (image) {
                image.style.transform = 'scale(1.1) rotate(2deg)';
            }
            
            // Show overlay with animation
            if (overlay) {
                overlay.style.opacity = '1';
                overlay.style.transform = 'scale(1)';
            }
            
            // Add glow effect
            card.style.boxShadow = '0 25px 50px rgba(210, 105, 30, 0.3), 0 0 0 1px rgba(210, 105, 30, 0.1)';
            
        } else {
            // Remove magnetic effect
            card.removeEventListener('mousemove', this.magneticEffect);
            card.style.transform = '';
            
            // Reset image
            if (image) {
                image.style.transform = '';
            }
            
            // Hide overlay
            if (overlay) {
                overlay.style.opacity = '0';
                overlay.style.transform = 'scale(0.9)';
            }
            
            // Remove glow
            card.style.boxShadow = '';
        }
    }

    magneticEffect(event) {
        const card = event.currentTarget;
        const rect = card.getBoundingClientRect();
        const x = event.clientX - rect.left - rect.width / 2;
        const y = event.clientY - rect.top - rect.height / 2;
        
        const intensity = 0.1;
        const transformX = x * intensity;
        const transformY = y * intensity;
        
        card.style.transform = `translate(${transformX}px, ${transformY}px) rotateX(${-y * 0.05}deg) rotateY(${x * 0.05}deg)`;
    }



    initializeParallax() {
        // Simple parallax for hero sections
        const parallaxElements = document.querySelectorAll('.hero-slide img, .parallax-element');
        
        if (parallaxElements.length === 0) return;
        
        const handleParallax = () => {
            const scrolled = window.pageYOffset;
            const rate = scrolled * -0.5;
            
            parallaxElements.forEach(element => {
                if (element.getBoundingClientRect().top < window.innerHeight) {
                    element.style.transform = `translateY(${rate}px)`;
                }
            });
        };

        let parallaxTicking = false;
        window.addEventListener('scroll', () => {
            if (!parallaxTicking) {
                requestAnimationFrame(() => {
                    handleParallax();
                    parallaxTicking = false;
                });
                parallaxTicking = true;
            }
        }, { passive: true });
    }

    initializeCartFunctionality() {
        // Enhanced add to cart with animations
        document.addEventListener('click', (e) => {
            if (e.target.matches('.add-to-cart-btn, .quick-add-btn')) {
                this.handleAddToCart(e);
            }
        });

        // Cart count animation
        this.observeCartChanges();
    }

    handleAddToCart(event) {
        event.preventDefault();
        const button = event.target;
        const productCard = button.closest('.product-card');
        
        // Add loading state
        this.setButtonLoading(button, true);
        
        // Get product data
        const productId = button.dataset.pid || button.getAttribute('data-pid');
        const qtySelect = button.dataset.select ? document.querySelector(button.dataset.select) : null;
        const qty = qtySelect ? parseInt(qtySelect.value) || 1 : 1;
        
        // Create form data
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.content || '');
        formData.append('product_id', productId);
        formData.append('qty', qty);
        
        // Send request
        fetch('/cart/add', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            this.setButtonLoading(button, false);
            
            if (data.success) {
                this.showSuccessAnimation(button, productCard);
                this.updateCartCount(data.cart_count);
                this.showToast('Added to cart successfully!', 'success');
            } else {
                this.showErrorAnimation(button);
                this.showToast(data.message || 'Could not add to cart', 'error');
            }
        })
        .catch(error => {
            this.setButtonLoading(button, false);
            this.showErrorAnimation(button);
            this.showToast('Something went wrong', 'error');
        });
    }

    setButtonLoading(button, loading) {
        if (loading) {
            button.dataset.originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...';
            button.disabled = true;
            button.classList.add('loading');
        } else {
            button.innerHTML = button.dataset.originalText || 'Add to Cart';
            button.disabled = false;
            button.classList.remove('loading');
        }
    }

    showSuccessAnimation(button, productCard) {
        // Button success state
        button.classList.add('btn-success');
        button.innerHTML = '<i class="fas fa-check mr-2"></i>Added!';
        
        // Particle effect
        this.createParticleEffect(button);
        
        // Product card flash
        if (productCard) {
            productCard.style.animation = 'pulse 0.6s ease';
        }
        
        // Reset after delay
        setTimeout(() => {
            button.classList.remove('btn-success');
            button.innerHTML = button.dataset.originalText || 'Add to Cart';
            if (productCard) {
                productCard.style.animation = '';
            }
        }, 2000);
    }

    showErrorAnimation(button) {
        button.classList.add('btn-error');
        button.innerHTML = '<i class="fas fa-exclamation mr-2"></i>Error!';
        
        setTimeout(() => {
            button.classList.remove('btn-error');
            button.innerHTML = button.dataset.originalText || 'Add to Cart';
        }, 2000);
    }

    createParticleEffect(element) {
        const rect = element.getBoundingClientRect();
        const centerX = rect.left + rect.width / 2;
        const centerY = rect.top + rect.height / 2;
        
        for (let i = 0; i < 12; i++) {
            const particle = document.createElement('div');
            particle.style.cssText = `
                position: fixed;
                width: 6px;
                height: 6px;
                background: linear-gradient(45deg, #d2691e, #daa520);
                border-radius: 50%;
                pointer-events: none;
                z-index: 9999;
                left: ${centerX}px;
                top: ${centerY}px;
            `;
            
            document.body.appendChild(particle);
            
            // Animate particle
            const angle = (i / 12) * Math.PI * 2;
            const velocity = 50 + Math.random() * 50;
            const x = Math.cos(angle) * velocity;
            const y = Math.sin(angle) * velocity;
            
            particle.animate([
                { transform: 'translate(0, 0) scale(1)', opacity: 1 },
                { transform: `translate(${x}px, ${y}px) scale(0)`, opacity: 0 }
            ], {
                duration: 800,
                easing: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)'
            }).addEventListener('finish', () => {
                particle.remove();
            });
        }
    }

    updateCartCount(count) {
        const badge = document.getElementById('cart-count-badge');
        if (!badge) return;
        
        if (count && count > 0) {
            badge.textContent = count;
            badge.classList.remove('hidden');
            
            // Animate badge
            badge.animate([
                { transform: 'scale(1)' },
                { transform: 'scale(1.4)' },
                { transform: 'scale(1)' }
            ], {
                duration: 300,
                easing: 'ease-out'
            });
        } else {
            badge.classList.add('hidden');
        }
    }

    observeCartChanges() {
        // Listen for custom cart update events
        window.addEventListener('cart-updated', (e) => {
            this.updateCartCount(e.detail.count);
        });
    }

    initializeLazyLoading() {
        // Lazy load images with fade-in effect
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const src = img.dataset.src || img.src;
                    
                    if (src) {
                        const tempImg = new Image();
                        tempImg.onload = () => {
                            img.src = src;
                            img.classList.add('loaded');
                            img.animate([
                                { opacity: 0, filter: 'blur(5px)' },
                                { opacity: 1, filter: 'blur(0)' }
                            ], {
                                duration: 600,
                                easing: 'ease-out'
                            });
                        };
                        tempImg.src = src;
                    }
                    
                    imageObserver.unobserve(img);
                }
            });
        }, { rootMargin: '50px' });

        document.querySelectorAll('img[data-src], .product-image img').forEach(img => {
            imageObserver.observe(img);
        });
    }

    initializeToasts() {
        // Create toast container if it doesn't exist
        if (!document.getElementById('toast-container')) {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 10000;
                display: flex;
                flex-direction: column;
                gap: 12px;
                max-width: 400px;
            `;
            document.body.appendChild(container);
        }
    }

    showToast(message, type = 'info', duration = 4000) {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        
        const colors = {
            success: { bg: '#28a745', icon: 'check-circle' },
            error: { bg: '#dc3545', icon: 'exclamation-circle' },
            warning: { bg: '#ffc107', icon: 'exclamation-triangle' },
            info: { bg: '#17a2b8', icon: 'info-circle' }
        };
        
        const color = colors[type] || colors.info;
        
        toast.style.cssText = `
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-left: 4px solid ${color.bg};
            border-radius: 12px;
            padding: 16px 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateX(100%);
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            cursor: pointer;
        `;
        
        toast.innerHTML = `
            <i class="fas fa-${color.icon}" style="color: ${color.bg}; font-size: 18px;"></i>
            <span style="color: #2d3748; font-weight: 500; flex: 1;">${message}</span>
            <button onclick="this.parentElement.remove()" style="background: none; border: none; color: #a0aec0; cursor: pointer; padding: 0;">
                <i class="fas fa-times"></i>
            </button>
        `;

        container.appendChild(toast);

        // Show animation
        requestAnimationFrame(() => {
            toast.style.transform = 'translateX(0)';
        });

        // Auto remove
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 300);
        }, duration);

        // Remove on click
        toast.addEventListener('click', () => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 300);
        });
    }

    initializeSearch(input) {
        let searchTimeout;
        let suggestionBox;
        
        input.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            
            if (query.length < 2) {
                this.hideSuggestions();
                return;
            }
            
            searchTimeout = setTimeout(() => {
                this.fetchSearchSuggestions(query, input);
            }, 300);
        });
        
        input.addEventListener('blur', () => {
            setTimeout(() => this.hideSuggestions(), 200);
        });
    }

    fetchSearchSuggestions(query, input) {
        // Mock suggestions - replace with actual API call
        const suggestions = [
            { name: 'Chocolate Cake', category: 'Cakes' },
            { name: 'Vanilla Cupcakes', category: 'Cupcakes' },
            { name: 'Croissant', category: 'Pastries' },
            { name: 'Blueberry Muffin', category: 'Muffins' }
        ].filter(item => item.name.toLowerCase().includes(query.toLowerCase()));
        
        this.showSuggestions(suggestions, input);
    }

    showSuggestions(suggestions, input) {
        this.hideSuggestions();
        
        if (suggestions.length === 0) return;
        
        const suggestionBox = document.createElement('div');
        suggestionBox.id = 'search-suggestions';
        suggestionBox.style.cssText = `
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(210, 105, 30, 0.2);
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            max-height: 300px;
            overflow-y: auto;
        `;
        
        suggestions.forEach((suggestion, index) => {
            const item = document.createElement('div');
            item.style.cssText = `
                padding: 12px 16px;
                cursor: pointer;
                border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                transition: background-color 0.2s ease;
            `;
            item.innerHTML = `
                <div style="font-weight: 500; color: #2d3748;">${suggestion.name}</div>
                <div style="font-size: 0.8rem; color: #718096;">${suggestion.category}</div>
            `;
            
            item.addEventListener('mouseenter', () => {
                item.style.backgroundColor = 'rgba(210, 105, 30, 0.1)';
            });
            
            item.addEventListener('mouseleave', () => {
                item.style.backgroundColor = 'transparent';
            });
            
            item.addEventListener('click', () => {
                input.value = suggestion.name;
                this.hideSuggestions();
                input.closest('form')?.submit();
            });
            
            suggestionBox.appendChild(item);
        });
        
        input.parentNode.style.position = 'relative';
        input.parentNode.appendChild(suggestionBox);
    }

    hideSuggestions() {
        const existing = document.getElementById('search-suggestions');
        if (existing) {
            existing.remove();
        }
    }

    animateCounter(element) {
        const target = parseInt(element.textContent) || 0;
        const duration = 2000;
        const start = performance.now();
        
        const animate = (currentTime) => {
            const elapsed = currentTime - start;
            const progress = Math.min(elapsed / duration, 1);
            const easeProgress = 1 - Math.pow(1 - progress, 3); // ease-out cubic
            
            const currentValue = Math.floor(easeProgress * target);
            element.textContent = currentValue + (element.textContent.includes('+') ? '+' : '');
            
            if (progress < 1) {
                requestAnimationFrame(animate);
            }
        };
        
        requestAnimationFrame(animate);
    }

    bindEvents() {
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + K for search focus
            if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                e.preventDefault();
                const searchInput = document.querySelector('input[name="q"]');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
        });

        // Smooth page transitions
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href]');
            if (link && link.hostname === window.location.hostname && !link.hasAttribute('target')) {
                this.handlePageTransition(e, link);
            }
        });
    }

    handlePageTransition(event, link) {
        // Add smooth page transition effect
        const href = link.getAttribute('href');
        
        // Skip if it's an anchor link or external
        if (href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')) {
            return;
        }
        
        // Add loading state to the page
        document.body.style.opacity = '0.8';
        document.body.style.transition = 'opacity 0.3s ease';
        
        // Allow the default navigation to proceed
        setTimeout(() => {
            document.body.style.opacity = '';
            document.body.style.transition = '';
        }, 300);
    }

    startAnimations() {
        // Start any continuous animations
        this.startFloatingAnimation();
        this.startParticleBackground();
    }

    startFloatingAnimation() {
        const floatingElements = document.querySelectorAll('.float-animation');
        floatingElements.forEach((element, index) => {
            const delay = index * 200;
            const duration = 3000 + Math.random() * 2000;
            
            setTimeout(() => {
                element.style.animation = `float ${duration}ms ease-in-out infinite`;
            }, delay);
        });
    }

    startParticleBackground() {
        // Create subtle floating particles in the background
        const particleContainer = document.createElement('div');
        particleContainer.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
            overflow: hidden;
        `;
        
        document.body.appendChild(particleContainer);
        
        for (let i = 0; i < 20; i++) {
            this.createFloatingParticle(particleContainer);
        }
    }

    createFloatingParticle(container) {
        const particle = document.createElement('div');
        const size = Math.random() * 4 + 2;
        
        particle.style.cssText = `
            position: absolute;
            width: ${size}px;
            height: ${size}px;
            background: radial-gradient(circle, rgba(210, 105, 30, 0.3) 0%, rgba(218, 165, 32, 0.1) 100%);
            border-radius: 50%;
            left: ${Math.random() * 100}%;
            top: ${Math.random() * 100}%;
            animation: particleFloat ${10 + Math.random() * 20}s linear infinite;
        `;
        
        container.appendChild(particle);
        
        // Remove and recreate particle after animation
        setTimeout(() => {
            particle.remove();
            this.createFloatingParticle(container);
        }, (10 + Math.random() * 20) * 1000);
    }

    // Public API for other scripts
    static getInstance() {
        if (!window.bakeryFrontendInstance) {
            window.bakeryFrontendInstance = new BakeryFrontend();
        }
        return window.bakeryFrontendInstance;
    }
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes particleFloat {
        0% {
            transform: translateY(100vh) translateX(-50px) rotate(0deg);
            opacity: 0;
        }
        10% {
            opacity: 1;
        }
        90% {
            opacity: 1;
        }
        100% {
            transform: translateY(-100px) translateX(50px) rotate(360deg);
            opacity: 0;
        }
    }
    
    @keyframes float {
        0%, 100% {
            transform: translateY(0) rotate(0deg);
        }
        33% {
            transform: translateY(-10px) rotate(1deg);
        }
        66% {
            transform: translateY(-5px) rotate(-1deg);
        }
    }
    
    .loaded {
        opacity: 1 !important;
        filter: blur(0) !important;
    }
`;
document.head.appendChild(style);

// Initialize the frontend
const bakeryFrontend = BakeryFrontend.getInstance();

// Export for use in other scripts
if (typeof window !== 'undefined') {
    window.BakeryFrontend = BakeryFrontend;
    window.bakeryFrontend = bakeryFrontend;
}

// Quick add to cart function for global use
window.quickAddToCart = function(productId, qty = 1, buttonElement = null) {
    if (window.bakeryFrontend) {
        const fakeEvent = {
            target: buttonElement || document.createElement('button'),
            preventDefault: () => {}
        };
        fakeEvent.target.dataset = { pid: productId };
        window.bakeryFrontend.handleAddToCart(fakeEvent);
    }
};