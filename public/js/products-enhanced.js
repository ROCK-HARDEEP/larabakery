/**
 * Enhanced Products Page JavaScript
 * Modern Filter Functionality & Interactive Features
 */

class ProductsEnhanced {
    constructor() {
        this.init();
        this.setupEventListeners();
        this.loadSavedPreferences();
    }

    init() {
        console.log('Products Enhanced initialized');
        this.setupFilterSections();
        this.setupViewControls();
        this.setupQuantityControls();
        this.setupAddToCartButtons();
        this.setupPriceRangeSync();
    }

    setupEventListeners() {
        // Mobile filter toggle
        window.toggleMobileFilterSidebar = () => this.toggleMobileFilter();
        
        // Desktop filter section toggle
        window.toggleDesktopFilterSection = (element) => this.toggleDesktopFilterSection(element);
        
        // Mobile filter section toggle
        window.toggleMobileFilterSection = (element) => this.toggleMobileFilterSection(element);
        
        // Quantity adjustment
        window.adjustQuantity = (productId, change) => this.adjustQuantity(productId, change);
        
        // Remove filter
        window.removeFilter = (filterType) => this.removeFilter(filterType);
        
        // Filter form submission
        this.setupFilterFormSubmission();
        
        // Auto-apply filters on change
        this.setupAutoFilterApply();
        
        // Keyboard shortcuts
        this.setupKeyboardShortcuts();
    }

    // Desktop Filter Section Toggle
    toggleDesktopFilterSection(element) {
        const section = element.closest('.filter-section');
        const content = section.querySelector('.filter-section-content');
        const icon = element.querySelector('.toggle-icon');
        
        section.classList.toggle('active');
        
        if (section.classList.contains('active')) {
            content.style.maxHeight = content.scrollHeight + 'px';
            icon.style.transform = 'rotate(180deg)';
        } else {
            content.style.maxHeight = '0';
            icon.style.transform = 'rotate(0deg)';
        }
    }

    // Mobile Filter Section Toggle
    toggleMobileFilterSection(element) {
        const content = element.nextElementSibling;
        const icon = element.querySelector('.toggle-icon');
        
        if (content.style.display === 'none' || content.style.display === '') {
            content.style.display = 'block';
            icon.style.transform = 'rotate(180deg)';
        } else {
            content.style.display = 'none';
            icon.style.transform = 'rotate(0deg)';
        }
    }

    // Setup Filter Sections
    setupFilterSections() {
        // Auto-expand first filter section on desktop
        const firstSection = document.querySelector('.desktop-filter-sidebar .filter-section');
        if (firstSection && window.innerWidth >= 1024) {
            firstSection.classList.add('active');
            const content = firstSection.querySelector('.filter-section-content');
            const icon = firstSection.querySelector('.toggle-icon');
            if (content && icon) {
                content.style.maxHeight = content.scrollHeight + 'px';
                icon.style.transform = 'rotate(180deg)';
            }
        }
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

    // View Controls Setup
    setupViewControls() {
        const viewControls = document.querySelectorAll('.view-control');
        const productsGrid = document.getElementById('productsGrid');
        
        viewControls.forEach(control => {
            control.addEventListener('click', () => {
                // Remove active class from all controls
                viewControls.forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked control
                control.classList.add('active');
                
                // Update grid view
                const viewType = control.getAttribute('data-view');
                if (productsGrid) {
                    if (viewType === 'list') {
                        productsGrid.classList.add('list-view');
                        productsGrid.style.gridTemplateColumns = '1fr';
                    } else {
                        productsGrid.classList.remove('list-view');
                        productsGrid.style.gridTemplateColumns = '';
                    }
                }
                
                // Save preference
                localStorage.setItem('products-view-preference', viewType);
            });
        });
    }

    // Quantity Controls
    setupQuantityControls() {
        // Handle manual input
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
            const quantitySelector = input.closest('.quantity-selector');
            if (quantitySelector) {
                quantitySelector.classList.add('updated');
                setTimeout(() => {
                    quantitySelector.classList.remove('updated');
                }, 200);
            }
        }
    }

    // Add to Cart Buttons
    setupAddToCartButtons() {
        document.querySelectorAll('.add-to-cart-btn-enhanced').forEach(button => {
            if (!button.classList.contains('disabled')) {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.handleAddToCart(button);
                });
            }
        });
    }

    handleAddToCart(button) {
        const productId = button.getAttribute('data-product-id');
        const qtyInput = document.getElementById(`qty-${productId}`);
        const quantity = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
        
        // Add loading state
        button.classList.add('loading');
        button.disabled = true;
        
        // Simulate API call (replace with actual cart functionality)
        setTimeout(() => {
            // Success animation
            this.showAddToCartSuccess(button);
            
            // Reset button
            button.classList.remove('loading');
            button.disabled = false;
            
            // Update cart count (if you have a cart counter)
            this.updateCartCount();
            
            // Show notification
            this.showNotification('Product added to cart successfully!', 'success');
            
        }, 1000);
    }

    showAddToCartSuccess(button) {
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i><span>Added!</span>';
        button.style.background = 'linear-gradient(135deg, var(--success) 0%, #059669 100%)';
        
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.style.background = '';
        }, 2000);
    }

    // Price Range Synchronization
    setupPriceRangeSync() {
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
        
        minInputs.forEach(input => input.value = min);
        maxInputs.forEach(input => input.value = max);
    }

    // Filter Form Submission
    setupFilterFormSubmission() {
        const forms = document.querySelectorAll('#desktopFilterForm, #mobileFilterForm');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                this.showFilterLoading();
            });
        });
    }

    showFilterLoading() {
        // Show loading state
        const applyButtons = document.querySelectorAll('.apply-filters-btn, .mobile-apply-btn');
        applyButtons.forEach(btn => {
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Applying...';
            btn.disabled = true;
        });
    }

    // Auto Apply Filters
    setupAutoFilterApply() {
        // Auto-submit on category change
        document.querySelectorAll('input[name="category"]').forEach(input => {
            input.addEventListener('change', () => {
                // Add small delay for better UX
                setTimeout(() => {
                    document.getElementById('desktopFilterForm')?.submit();
                }, 300);
            });
        });
    }

    // Remove Filter
    removeFilter(filterType) {
        if (filterType === 'category') {
            // Uncheck all category radios
            document.querySelectorAll('input[name="category"]').forEach(input => {
                if (input.value === '') input.checked = true;
                else input.checked = false;
            });
        }
        
        // Auto-submit form
        document.getElementById('desktopFilterForm')?.submit();
    }

    // Keyboard Shortcuts
    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            // Press 'F' to toggle filter on mobile
            if (e.key === 'f' || e.key === 'F') {
                if (window.innerWidth < 1024) {
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
        });
    }

    // Load Saved Preferences
    loadSavedPreferences() {
        // Load view preference
        const viewPreference = localStorage.getItem('products-view-preference');
        if (viewPreference) {
            const viewControl = document.querySelector(`[data-view="${viewPreference}"]`);
            if (viewControl) {
                viewControl.click();
            }
        }
    }

    // Update Cart Count
    updateCartCount() {
        // This would connect to your actual cart system
        const cartCounters = document.querySelectorAll('.cart-count');
        cartCounters.forEach(counter => {
            const current = parseInt(counter.textContent) || 0;
            counter.textContent = current + 1;
            
            // Add animation
            counter.classList.add('updated');
            setTimeout(() => {
                counter.classList.remove('updated');
            }, 500);
        });
    }

    // Show Notification
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
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
            border: 2px solid ${type === 'success' ? 'var(--success)' : 'var(--bakery-primary)'};
            border-radius: 8px;
            padding: 1rem;
            box-shadow: var(--shadow-lg);
            z-index: 10000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 300px;
        `;
        
        // Add to page
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Close button
        notification.querySelector('.notification-close').addEventListener('click', () => {
            this.removeNotification(notification);
        });
        
        // Auto remove
        setTimeout(() => {
            this.removeNotification(notification);
        }, 5000);
    }

    removeNotification(notification) {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }
}

// Enhanced Animations
const addEnhancedAnimations = () => {
    // Add CSS for enhanced animations
    const style = document.createElement('style');
    style.textContent = `
        .quantity-selector.updated {
            transform: scale(1.1);
            transition: transform 0.2s ease;
        }
        
        .cart-count.updated {
            animation: bounceIn 0.5s ease;
        }
        
        @keyframes bounceIn {
            0% { transform: scale(0.8); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }
        
        .notification-content {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--gray-800);
        }
        
        .notification-close {
            background: none;
            border: none;
            color: var(--gray-400);
            cursor: pointer;
            padding: 0.25rem;
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
        }
        
        .notification-close:hover {
            color: var(--gray-600);
        }
        
        .product-card-enhanced {
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Staggered animation for product cards */
        .product-card-enhanced:nth-child(1) { animation-delay: 0.1s; }
        .product-card-enhanced:nth-child(2) { animation-delay: 0.2s; }
        .product-card-enhanced:nth-child(3) { animation-delay: 0.3s; }
        .product-card-enhanced:nth-child(4) { animation-delay: 0.4s; }
        .product-card-enhanced:nth-child(5) { animation-delay: 0.5s; }
        .product-card-enhanced:nth-child(6) { animation-delay: 0.6s; }
        .product-card-enhanced:nth-child(7) { animation-delay: 0.7s; }
        .product-card-enhanced:nth-child(8) { animation-delay: 0.8s; }
    `;
    document.head.appendChild(style);
};

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new ProductsEnhanced();
    addEnhancedAnimations();
});

// Handle window resize
window.addEventListener('resize', () => {
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
});

// Export for external use
window.ProductsEnhanced = ProductsEnhanced;