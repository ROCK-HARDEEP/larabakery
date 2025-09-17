// Bakery Shop JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all components
    initializeCart();
    initializeSearch();
    initializeFilters();
    initializeAnimations();
    initializeToast();
});

// Cart Management
function initializeCart() {
    // Cart count update
    updateCartCount();
    
    // Cart item quantity changes
    document.querySelectorAll('[data-cart-update]').forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.dataset.cartUpdate;
            const change = parseInt(this.dataset.change) || 1;
            updateCartItem(itemId, change);
        });
    });
    
    // Cart item removal
    document.querySelectorAll('[data-cart-remove]').forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.dataset.cartRemove;
            removeCartItem(itemId);
        });
    });
}

function updateCartCount(count) {
    const badge = document.getElementById('cart-count-badge') || document.querySelector('.cart-count-badge');
    const computedCount = typeof count !== 'undefined' ? parseInt(count) : parseInt(localStorage.getItem('cartCount') || 0);
    if (!badge) return;
    badge.textContent = isNaN(computedCount) ? '0' : String(computedCount);
    // Prefer Tailwind's hidden class if present; fallback to style
    if (badge.classList) {
        if (computedCount > 0) {
            badge.classList.remove('hidden');
            // Subtle pop
            badge.classList.add('scale-110');
            setTimeout(() => badge.classList.remove('scale-110'), 300);
        } else {
            badge.classList.add('hidden');
        }
    } else {
        badge.style.display = computedCount > 0 ? 'block' : 'none';
    }
}

function updateCartItem(itemId, change) {
    const formData = new FormData();
    formData.append('item_id', itemId);
    formData.append('change', change);
    formData.append('_token', getCsrfToken());
    
    fetch('/cart/update', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showToast('Error updating cart: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Error updating cart', 'error');
    });
}

function removeCartItem(itemId) {
    if (confirm('Are you sure you want to remove this item from your cart?')) {
        const formData = new FormData();
        formData.append('item_id', itemId);
        formData.append('_token', getCsrfToken());
        
        fetch('/cart/remove', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showToast('Error removing item: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error removing item', 'error');
        });
    }
}

// Search Functionality
function initializeSearch() {
    const searchInput = document.querySelector('#search');
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            
            if (query.length >= 2) {
                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 300);
            } else if (query.length === 0) {
                clearSearchResults();
            }
        });
    }
}

function performSearch(query) {
    const searchResults = document.querySelector('#search-results');
    if (!searchResults) return;
    
    // Show loading state
    searchResults.innerHTML = '<div class="text-center py-8"><div class="spinner"></div><p class="mt-2 text-gray-600">Searching...</p></div>';
    
    fetch(`/search?q=${encodeURIComponent(query)}`)
        .then(response => response.text())
        .then(html => {
            searchResults.innerHTML = html;
        })
        .catch(error => {
            console.error('Search error:', error);
            searchResults.innerHTML = '<div class="text-center py-8 text-red-600">Error performing search</div>';
        });
}

function clearSearchResults() {
    const searchResults = document.querySelector('#search-results');
    if (searchResults) {
        searchResults.innerHTML = '';
    }
}

// Filter Management
function initializeFilters() {
    // Price range filter
    const priceFilter = document.querySelector('#price_range');
    if (priceFilter) {
        priceFilter.addEventListener('change', applyFilters);
    }
    
    // Category filter
    const categoryFilter = document.querySelector('#category_filter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', applyFilters);
    }
    
    // Sort filter
    const sortFilter = document.querySelector('#sort_by');
    if (sortFilter) {
        sortFilter.addEventListener('change', applyFilters);
    }
    
    // Checkbox filters
    document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
        checkbox.addEventListener('change', applyFilters);
    });
}

function applyFilters() {
    const filters = {};
    
    // Get price range
    const priceRange = document.querySelector('#price_range')?.value;
    if (priceRange) filters.price_range = priceRange;
    
    // Get category
    const category = document.querySelector('#category_filter')?.value;
    if (category) filters.category = category;
    
    // Get sort
    const sort = document.querySelector('#sort_by')?.value;
    if (sort) filters.sort = sort;
    
    // Get checkboxes
    document.querySelectorAll('input[type="checkbox"]:checked').forEach(checkbox => {
        filters[checkbox.name] = checkbox.value || true;
    });
    
    // Apply filters via AJAX or update URL
    updateURLWithFilters(filters);
    filterProducts(filters);
}

function updateURLWithFilters(filters) {
    const url = new URL(window.location);
    Object.keys(filters).forEach(key => {
        if (filters[key]) {
            url.searchParams.set(key, filters[key]);
        } else {
            url.searchParams.delete(key);
        }
    });
    
    // Update URL without reloading
    window.history.pushState({}, '', url);
}

function filterProducts(filters) {
    const products = document.querySelectorAll('.product-card');
    
    products.forEach(product => {
        let show = true;
        
        // Apply price filter
        if (filters.price_range) {
            const price = getProductPrice(product);
            show = show && matchesPriceRange(price, filters.price_range);
        }
        
        // Apply category filter
        if (filters.category) {
            const category = product.dataset.category;
            show = show && category === filters.category;
        }
        
        // Show/hide product
        product.style.display = show ? 'block' : 'none';
    });
    
    // Update results count
    updateResultsCount();
}

function getProductPrice(product) {
    const priceElement = product.querySelector('.product-price');
    if (priceElement) {
        return parseFloat(priceElement.dataset.price || priceElement.textContent.replace(/[^\d.]/g, ''));
    }
    return 0;
}

function matchesPriceRange(price, range) {
    const [min, max] = range.split('-').map(p => p === '+' ? Infinity : parseFloat(p));
    return price >= min && (max === Infinity ? true : price <= max);
}

function updateResultsCount() {
    const visibleProducts = document.querySelectorAll('.product-card[style*="block"], .product-card:not([style*="none"])');
    const countElement = document.querySelector('#results-count');
    if (countElement) {
        countElement.textContent = visibleProducts.length;
    }
}

// Animation System
function initializeAnimations() {
    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);
    
    // Observe elements with animation classes
    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });
    
    // Hover animations
    document.querySelectorAll('.hover-lift').forEach(el => {
        el.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        el.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
}

// Toast Notification System
function initializeToast() {
    // Create toast container if it doesn't exist
    if (!document.querySelector('#toast-container')) {
        const toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        toastContainer.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(toastContainer);
    }
}

function showToast(message, type = 'info', duration = 5000) {
    const toastContainer = document.querySelector('#toast-container');
    if (!toastContainer) return;
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type} transform translate-x-full transition-transform duration-300`;
    
    const icon = getToastIcon(type);
    const bgColor = getToastColor(type);
    
    toast.innerHTML = `
        <div class="flex items-center p-4 ${bgColor} text-white rounded-lg shadow-lg">
            <div class="flex-shrink-0">
                ${icon}
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">${message}</p>
            </div>
            <div class="ml-auto pl-3">
                <button onclick="this.parentElement.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Auto remove
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 300);
    }, duration);
}

function getToastIcon(type) {
    switch (type) {
        case 'success':
            return '<i class="fas fa-check-circle"></i>';
        case 'error':
            return '<i class="fas fa-exclamation-circle"></i>';
        case 'warning':
            return '<i class="fas fa-exclamation-triangle"></i>';
        default:
            return '<i class="fas fa-info-circle"></i>';
    }
}

function getToastColor(type) {
    switch (type) {
        case 'success':
            return 'bg-green-500';
        case 'error':
            return 'bg-red-500';
        case 'warning':
            return 'bg-yellow-500';
        default:
            return 'bg-blue-500';
    }
}

// Utility Functions
function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
           document.querySelector('input[name="_token"]')?.value;
}

function formatPrice(price) {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR'
    }).format(price);
}

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

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// Product Quick Actions
function quickAddToCart(productId, quantity = 1, buttonEl = null) {
    const formData = new FormData();
    formData.append('product_id', productId);
    formData.append('qty', quantity);
    formData.append('_token', getCsrfToken());
    
    // Optimistic UI: immediately show feedback
    let originalHtml;
    if (buttonEl) {
        originalHtml = buttonEl.innerHTML;
        buttonEl.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...';
        buttonEl.disabled = true;
    }

    fetch('/cart/add', {
        method: 'POST',
        body: formData,
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        credentials: 'same-origin'
    })
    .then(async response => {
        let data = {};
        try { data = await response.json(); } catch(e) {}
        if (response.ok && data.success) {
            if (buttonEl) {
                buttonEl.innerHTML = '<i class="fas fa-check mr-2"></i>Added';
                buttonEl.classList.add('bg-green-500');
                // Show "Added" for 1.5 seconds as requested
                setTimeout(()=>{ 
                    buttonEl.innerHTML = originalHtml; 
                    buttonEl.classList.remove('bg-green-500'); 
                    buttonEl.disabled = false; 
                }, 1500);
            }
            // Fly-to-cart visual effect
            try { animateAddToCart(buttonEl); } catch(_) {}
            // Sync cart count using server value when provided
            if (typeof data.cart_count !== 'undefined') {
                localStorage.setItem('cartCount', parseInt(data.cart_count));
                updateCartCount(data.cart_count);
            } else {
                const currentCount = parseInt(localStorage.getItem('cartCount') || 0);
                localStorage.setItem('cartCount', currentCount + quantity);
                updateCartCount();
            }
        } else {
            if (buttonEl) {
                buttonEl.innerHTML = '<i class="fas fa-exclamation mr-2"></i>Limit';
                buttonEl.classList.add('bg-red-500');
                setTimeout(()=>{ 
                    buttonEl.innerHTML = originalHtml; 
                    buttonEl.classList.remove('bg-red-500'); 
                    buttonEl.disabled = false; 
                }, 1500);
            }
            const msg = data && data.message ? data.message : 'Unable to add product to cart';
            showToast(msg, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        if (buttonEl) { 
            buttonEl.innerHTML = originalHtml; 
            buttonEl.disabled = false; 
        }
        // Only show a generic error if request truly failed
        showToast('Unable to reach server. Please try again.', 'error');
    });
}

// Simple fly-to-cart animation
function animateAddToCart(buttonEl){
    const cartIcon = document.getElementById('cart-count-badge') || document.querySelector('.cart-count-badge') || document.querySelector('[data-cart-icon]');
    if (!buttonEl || !cartIcon) return;
    const rectStart = buttonEl.getBoundingClientRect();
    const rectEnd = cartIcon.getBoundingClientRect();
    const dot = document.createElement('div');
    dot.style.position = 'fixed';
    dot.style.left = rectStart.left + rectStart.width/2 + 'px';
    dot.style.top = rectStart.top + rectStart.height/2 + 'px';
    dot.style.width = '10px';
    dot.style.height = '10px';
    dot.style.borderRadius = '50%';
    dot.style.background = '#f97316';
    dot.style.zIndex = 9999;
    dot.style.transition = 'transform 600ms cubic-bezier(0.22, 1, 0.36, 1), opacity 600ms';
    document.body.appendChild(dot);
    const translateX = rectEnd.left + rectEnd.width/2 - (rectStart.left + rectStart.width/2);
    const translateY = rectEnd.top + rectEnd.height/2 - (rectStart.top + rectStart.height/2);
    requestAnimationFrame(()=>{
        dot.style.transform = `translate(${translateX}px, ${translateY}px) scale(0.2)`;
        dot.style.opacity = '0.2';
        setTimeout(()=>{ if (dot.parentElement) dot.remove(); }, 650);
    });
}

function addToWishlist(productId) {
    // Implement wishlist functionality
    showToast('Product added to wishlist!', 'success');
}

// Form Validation
function validateForm(form) {
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('border-red-500');
            isValid = false;
        } else {
            input.classList.remove('border-red-500');
        }
    });
    
    return isValid;
}

// Mobile Menu Toggle
function toggleMobileMenu() {
    const mobileMenu = document.querySelector('#mobile-menu');
    if (mobileMenu) {
        mobileMenu.classList.toggle('hidden');
    }
}

// Lazy Loading for Images
function initializeLazyLoading() {
    const images = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
}

// Initialize lazy loading when DOM is ready
document.addEventListener('DOMContentLoaded', initializeLazyLoading);

// Export functions for global use
window.BakeryShop = {
    showToast,
    quickAddToCart,
    addToWishlist,
    validateForm,
    toggleMobileMenu,
    updateCartCount
};
