/**
 * Products Vertical Sidebar - JavaScript Functionality
 * Professional E-commerce Filter Interface
 */

class ProductsVerticalSidebar {
    constructor() {
        this.init();
        this.bindEvents();
        this.loadPreferences();
    }

    init() {
        console.log('Products Vertical Sidebar initialized');
        this.setupFilterSections();
        this.setupViewToggle();
        this.setupQuantityControls();
        this.setupAddToCart();
        this.setupPriceSync();
        this.setupAutoSubmit();
    }

    bindEvents() {
        // Global functions for inline onclick handlers
        window.toggleFilterSection = (element) => this.toggleFilterSection(element);
        window.toggleMobileFilter = () => this.toggleMobileFilter();
        window.toggleView = (element, view) => this.toggleView(element, view);
        window.adjustQuantity = (productId, change) => this.adjustQuantity(productId, change);
        window.addToCart = (productId) => this.addToCart(productId);

        // Keyboard shortcuts
        this.setupKeyboardShortcuts();
        
        // Window resize handler
        window.addEventListener('resize', () => this.handleResize());
    }

    // Setup Filter Sections
    setupFilterSections() {
        // Auto-expand first section on desktop
        const firstSection = document.querySelector('.filter-section');
        if (firstSection && window.innerWidth >= 1024) {
            firstSection.classList.add('active');
        }

        // Setup section toggles
        document.querySelectorAll('.filter-section-header').forEach(header => {
            header.addEventListener('click', () => this.toggleFilterSection(header));
        });
    }

    // Toggle Filter Section
    toggleFilterSection(headerElement) {
        const section = headerElement.closest('.filter-section');
        const content = section.querySelector('.filter-section-content');
        const toggle = headerElement.querySelector('.section-toggle');
        
        section.classList.toggle('active');
        
        if (section.classList.contains('active')) {
            content.style.maxHeight = content.scrollHeight + 'px';
            toggle.style.transform = 'rotate(180deg)';
        } else {
            content.style.maxHeight = '0px';
            toggle.style.transform = 'rotate(0deg)';
        }
        
        // Add visual feedback
        headerElement.style.backgroundColor = 'var(--bakery-cream)';
        setTimeout(() => {
            headerElement.style.backgroundColor = '';
        }, 200);
    }

    // Mobile Filter Toggle
    toggleMobileFilter() {
        const sidebar = document.getElementById('mobileFilterSidebar');
        const overlay = document.getElementById('mobileFilterOverlay');
        
        if (sidebar && overlay) {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
            
            // Prevent body scroll when filter is open
            document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        }
    }

    // View Toggle Setup
    setupViewToggle() {
        const viewButtons = document.querySelectorAll('.view-toggle-btn');
        const productsGrid = document.getElementById('productsGrid');
        
        viewButtons.forEach(button => {
            button.addEventListener('click', () => {
                this.toggleView(button, button.dataset.view);
            });
        });
    }

    toggleView(element, viewType) {
        const viewButtons = document.querySelectorAll('.view-toggle-btn');
        const productsGrid = document.getElementById('productsGrid');
        
        // Update button states
        viewButtons.forEach(btn => btn.classList.remove('active'));
        element.classList.add('active');
        
        // Update grid layout
        if (productsGrid) {
            if (viewType === 'list') {
                productsGrid.style.gridTemplateColumns = '1fr';
                productsGrid.classList.add('list-view');
                
                // Modify product cards for list view
                document.querySelectorAll('.product-card-modern').forEach(card => {
                    card.style.display = 'flex';
                    card.style.flexDirection = 'row';
                    card.style.height = '200px';
                });
            } else {
                productsGrid.style.gridTemplateColumns = '';
                productsGrid.classList.remove('list-view');
                
                // Reset product cards for grid view
                document.querySelectorAll('.product-card-modern').forEach(card => {
                    card.style.display = '';
                    card.style.flexDirection = '';
                    card.style.height = '';
                });
            }
        }
        
        // Save preference
        localStorage.setItem('products-view-preference', viewType);
        
        // Visual feedback
        element.style.transform = 'scale(0.95)';
        setTimeout(() => {
            element.style.transform = '';
        }, 150);
    }

    // Quantity Controls Setup
    setupQuantityControls() {
        document.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('change', (e) => {
                let value = parseInt(e.target.value);
                if (isNaN(value) || value < 1) value = 1;
                if (value > 10) value = 10;
                e.target.value = value;
            });
        });
    }

    adjustQuantity(productId, change) {
        const input = document.getElementById(`qty-${productId}`);
        if (input) {
            let currentValue = parseInt(input.value) || 1;
            let newValue = currentValue + change;
            
            // Ensure value is within bounds
            if (newValue < 1) newValue = 1;
            if (newValue > 10) newValue = 10;
            
            input.value = newValue;
            
            // Add visual feedback
            const controls = input.closest('.quantity-controls');
            if (controls) {
                controls.style.transform = 'scale(1.05)';
                controls.style.backgroundColor = 'var(--bakery-cream)';
                setTimeout(() => {
                    controls.style.transform = '';
                    controls.style.backgroundColor = '';
                }, 200);
            }
        }
    }

    // Add to Cart Setup
    setupAddToCart() {
        document.querySelectorAll('.add-cart-btn:not(.disabled)').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const productId = button.getAttribute('onclick')?.match(/\d+/)?.[0];
                if (productId) {
                    this.addToCart(parseInt(productId));
                }
            });
        });
    }

    addToCart(productId) {
        const button = document.querySelector(`[onclick*="${productId}"]`);
        const qtyInput = document.getElementById(`qty-${productId}`);
        const quantity = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
        
        if (button && !button.disabled) {
            // Add loading state
            const originalContent = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Adding...</span>';
            button.disabled = true;
            button.style.cursor = 'not-allowed';
            
            // Simulate API call (replace with actual cart functionality)
            setTimeout(() => {
                // Success state
                button.innerHTML = '<i class="fas fa-check"></i><span>Added!</span>';
                button.style.background = 'linear-gradient(135deg, var(--success) 0%, #059669 100%)';
                
                // Show notification
                this.showNotification(`${quantity} item(s) added to cart!`, 'success');
                
                // Update cart count (if you have a cart counter)
                this.updateCartCount(quantity);
                
                // Reset button after delay
                setTimeout(() => {
                    button.innerHTML = originalContent;
                    button.style.background = '';
                    button.disabled = false;
                    button.style.cursor = '';
                }, 2000);
                
            }, 1000);
        }
    }

    // Price Range Synchronization
    setupPriceSync() {
        // Handle price range radio buttons
        document.querySelectorAll('input[name="price_range"]').forEach(radio => {
            radio.addEventListener('change', (e) => {
                if (e.target.checked) {
                    this.syncPriceInputs(e.target.value);
                }
            });
        });
        
        // Handle manual price input
        document.querySelectorAll('input[name="min"], input[name="max"]').forEach(input => {
            input.addEventListener('input', () => {
                // Clear price range radio selection when manually typing
                document.querySelectorAll('input[name="price_range"]').forEach(radio => {
                    radio.checked = false;
                });
            });
        });
    }

    syncPriceInputs(priceRange) {
        const minInputs = document.querySelectorAll('input[name="min"]');
        const maxInputs = document.querySelectorAll('input[name="max"]');
        
        let min = '', max = '';
        
        switch(priceRange) {
            case '0-50':
                min = '0';
                max = '50';
                break;
            case '50-100':
                min = '50';
                max = '100';
                break;
            case '100-200':
                min = '100';
                max = '200';
                break;
            case '200+':
                min = '200';
                max = '';
                break;
        }
        
        minInputs.forEach(input => {
            input.value = min;
            // Add visual feedback
            input.style.borderColor = 'var(--bakery-primary)';
            setTimeout(() => {
                input.style.borderColor = '';
            }, 1000);
        });
        
        maxInputs.forEach(input => {
            input.value = max;
            // Add visual feedback
            input.style.borderColor = 'var(--bakery-primary)';
            setTimeout(() => {
                input.style.borderColor = '';
            }, 1000);
        });
    }

    // Auto Submit Setup
    setupAutoSubmit() {
        // Auto-submit on category change (optional - can be disabled)
        document.querySelectorAll('input[name="category"]').forEach(input => {
            input.addEventListener('change', () => {
                // Add small delay for better UX
                setTimeout(() => {
                    if (this.shouldAutoSubmit()) {
                        document.getElementById('filterForm')?.submit();
                    }
                }, 300);
            });
        });
    }

    shouldAutoSubmit() {
        // Check if auto-submit is enabled (you can make this configurable)
        return localStorage.getItem('auto-submit-filters') === 'true';
    }

    // Keyboard Shortcuts
    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Press 'F' to toggle filter on mobile
            if (e.key === 'f' || e.key === 'F') {
                if (window.innerWidth < 1024 && !e.target.matches('input, textarea')) {
                    e.preventDefault();
                    this.toggleMobileFilter();
                }
            }
            
            // Press 'Escape' to close mobile filter
            if (e.key === 'Escape') {
                const sidebar = document.getElementById('mobileFilterSidebar');
                if (sidebar && sidebar.classList.contains('active')) {
                    this.toggleMobileFilter();
                }
            }
            
            // Press 'G' to toggle between grid and list view
            if (e.key === 'g' || e.key === 'G') {
                if (!e.target.matches('input, textarea')) {
                    e.preventDefault();
                    const currentView = document.querySelector('.view-toggle-btn.active');
                    const nextView = currentView?.dataset.view === 'grid' ? 
                        document.querySelector('[data-view="list"]') : 
                        document.querySelector('[data-view="grid"]');
                    if (nextView) {
                        nextView.click();
                    }
                }
            }
        });
    }

    // Handle Window Resize
    handleResize() {
        // Close mobile filter if switching to desktop
        if (window.innerWidth >= 1024) {
            const sidebar = document.getElementById('mobileFilterSidebar');
            const overlay = document.getElementById('mobileFilterOverlay');
            if (sidebar && sidebar.classList.contains('active')) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        }
    }

    // Load Saved Preferences
    loadPreferences() {
        // Load view preference
        const viewPreference = localStorage.getItem('products-view-preference');
        if (viewPreference) {
            const viewButton = document.querySelector(`[data-view="${viewPreference}"]`);
            if (viewButton) {
                setTimeout(() => {
                    viewButton.click();
                }, 100);
            }
        }
    }

    // Update Cart Count
    updateCartCount(quantity = 1) {
        const cartCounters = document.querySelectorAll('.cart-count, [data-cart-count]');
        cartCounters.forEach(counter => {
            const current = parseInt(counter.textContent) || 0;
            counter.textContent = current + quantity;
            
            // Add animation
            counter.style.transform = 'scale(1.3)';
            counter.style.color = 'var(--bakery-primary)';
            setTimeout(() => {
                counter.style.transform = '';
                counter.style.color = '';
            }, 500);
        });
    }

    // Show Notification
    showNotification(message, type = 'info') {
        // Remove existing notifications
        document.querySelectorAll('.notification').forEach(notif => {
            notif.remove();
        });
        
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${this.getNotificationIcon(type)}"></i>
                <span>${message}</span>
            </div>
            <button class="notification-close">
                <i class="fas fa-times"></i>
            </button>
        `;
        
        // Add styles
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            border-left: 4px solid ${this.getNotificationColor(type)};
            border-radius: 8px;
            padding: 1rem 1.5rem;
            box-shadow: var(--shadow-lg);
            z-index: 10000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 350px;
            min-width: 300px;
        `;
        
        // Add notification styles to head if not already added
        if (!document.querySelector('#notification-styles')) {
            const style = document.createElement('style');
            style.id = 'notification-styles';
            style.textContent = `
                .notification-content {
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    color: var(--gray-800);
                    font-weight: 500;
                }
                
                .notification-close {
                    position: absolute;
                    top: 0.5rem;
                    right: 0.5rem;
                    background: none;
                    border: none;
                    color: var(--gray-400);
                    cursor: pointer;
                    padding: 0.25rem;
                    border-radius: 4px;
                    transition: var(--transition-fast);
                }
                
                .notification-close:hover {
                    color: var(--gray-600);
                    background: var(--gray-100);
                }
            `;
            document.head.appendChild(style);
        }
        
        // Add to page
        document.body.appendChild(notification);
        
        // Animate in
        requestAnimationFrame(() => {
            notification.style.transform = 'translateX(0)';
        });
        
        // Close button
        notification.querySelector('.notification-close').addEventListener('click', () => {
            this.removeNotification(notification);
        });
        
        // Auto remove
        setTimeout(() => {
            if (notification.parentNode) {
                this.removeNotification(notification);
            }
        }, 5000);
    }

    getNotificationIcon(type) {
        const icons = {
            success: 'check-circle',
            warning: 'exclamation-triangle',
            error: 'exclamation-circle',
            info: 'info-circle'
        };
        return icons[type] || icons.info;
    }

    getNotificationColor(type) {
        const colors = {
            success: 'var(--success)',
            warning: 'var(--warning)',
            error: 'var(--error)',
            info: 'var(--bakery-primary)'
        };
        return colors[type] || colors.info;
    }

    removeNotification(notification) {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }

    // Utility Methods
    debounce(func, wait) {
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

    // Smooth scroll to element
    scrollToElement(selector) {
        const element = document.querySelector(selector);
        if (element) {
            element.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new ProductsVerticalSidebar();
});

// Add enhanced loading animations
const addLoadingAnimations = () => {
    const style = document.createElement('style');
    style.textContent = `
        .product-card-modern {
            animation: fadeInUp 0.6s ease-out forwards;
            opacity: 0;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Staggered animation for product cards */
        .product-card-modern:nth-child(1) { animation-delay: 0.1s; }
        .product-card-modern:nth-child(2) { animation-delay: 0.2s; }
        .product-card-modern:nth-child(3) { animation-delay: 0.3s; }
        .product-card-modern:nth-child(4) { animation-delay: 0.4s; }
        .product-card-modern:nth-child(5) { animation-delay: 0.5s; }
        .product-card-modern:nth-child(6) { animation-delay: 0.6s; }
        .product-card-modern:nth-child(7) { animation-delay: 0.7s; }
        .product-card-modern:nth-child(8) { animation-delay: 0.8s; }
        
        .filter-section {
            animation: slideInLeft 0.5s ease-out forwards;
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    `;
    document.head.appendChild(style);
};

// Initialize animations when page loads
window.addEventListener('load', () => {
    addLoadingAnimations();
});

// Export for external access
window.ProductsVerticalSidebar = ProductsVerticalSidebar;