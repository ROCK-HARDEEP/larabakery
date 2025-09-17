// Products page JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
    // Initialize product functionality
    initializeProductHandlers();
    initializeFilterHandlers();
});

function initializeProductHandlers() {
    // Handle quantity change buttons
    document.addEventListener('click', function(e) {
        if (e.target.matches('[data-action="change-quantity"]')) {
            const productId = e.target.dataset.productId;
            const change = parseInt(e.target.dataset.change);
            changeQuantity(productId, change);
        }
        
        if (e.target.matches('[data-action="adjust-quantity"]')) {
            const productId = e.target.dataset.productId;
            const change = parseInt(e.target.dataset.change);
            adjustQuantity(productId, change);
        }
        
        if (e.target.matches('[data-action="add-to-cart"]')) {
            const productId = e.target.dataset.productId;
            addToCart(productId);
        }
        
        if (e.target.matches('[data-action="quick-view"]')) {
            const productSlug = e.target.dataset.productSlug;
            window.location.href = `/products/${productSlug}`;
        }
    });
}

function initializeFilterHandlers() {
    // Handle filter sidebar toggle
    const filterToggle = document.getElementById('filterToggle');
    const filterSidebar = document.getElementById('filterSidebar');
    const closeSidebar = document.getElementById('closeSidebar');
    
    if (filterToggle && filterSidebar) {
        filterToggle.addEventListener('click', function() {
            filterSidebar.classList.remove('-translate-x-full');
        });
    }
    
    if (closeSidebar && filterSidebar) {
        closeSidebar.addEventListener('click', function() {
            filterSidebar.classList.add('-translate-x-full');
        });
    }
    
    // Handle price range buttons
    document.addEventListener('click', function(e) {
        if (e.target.matches('[data-action="set-price"]')) {
            const min = e.target.dataset.min;
            const max = e.target.dataset.max;
            setPrice(min, max);
        }
    });
}

function changeQuantity(productId, change) {
    const input = document.getElementById(`qty-${productId}`);
    if (input) {
        let currentValue = parseInt(input.value) || 1;
        let newValue = currentValue + change;
        
        // Ensure value is within bounds
        newValue = Math.max(1, Math.min(10, newValue));
        
        input.value = newValue;
    }
}

function adjustQuantity(productId, change) {
    const input = document.getElementById(`qty-${productId}`);
    if (input) {
        let currentValue = parseInt(input.value) || 1;
        let newValue = currentValue + change;
        
        // Ensure value is within bounds
        newValue = Math.max(1, Math.min(10, newValue));
        
        input.value = newValue;
    }
}

function addToCart(productId) {
    const input = document.getElementById(`qty-${productId}`);
    const quantity = input ? parseInt(input.value) || 1 : 1;
    
    // Show loading state
    const button = document.querySelector(`[data-action="add-to-cart"][data-product-id="${productId}"]`);
    if (button) {
        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
    }
    
    // Make AJAX request to add to cart
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        console.error('CSRF token not found');
        showNotification('Security token missing. Please refresh the page.', 'error');
        if (button) {
            button.disabled = false;
            button.innerHTML = 'Add to Cart';
        }
        return;
    }
    
    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: quantity
        })
    })
    .then(response => {
        if (!response.ok) {
            // Try to get error message from response
            return response.text().then(text => {
                try {
                    const errorData = JSON.parse(text);
                    throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
                } catch (e) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification(data.message || 'Product added to cart successfully!', 'success');
            
            // Update cart count if available
            if (data.cart_count !== undefined) {
                updateCartCount(data.cart_count);
            }
            
            // Update button to show success state
            if (button) {
                button.innerHTML = '<i class="fas fa-check"></i> Added!';
                button.classList.remove('bg-bakery-500', 'hover:bg-bakery-600');
                button.classList.add('bg-green-500');
                
                // Reset button after 2 seconds
                setTimeout(() => {
                    button.innerHTML = '<i class="fas fa-shopping-cart"></i> Add to Cart';
                    button.classList.remove('bg-green-500');
                    button.classList.add('bg-bakery-500', 'hover:bg-bakery-600');
                    button.disabled = false;
                }, 2000);
            }
        } else {
            showNotification(data.message || 'Failed to add product to cart', 'error');
            // Reset button state on error
            if (button) {
                button.disabled = false;
                button.innerHTML = 'Add to Cart';
            }
        }
    })
    .catch(error => {
        console.error('Error adding to cart:', error);
        showNotification('An error occurred while adding to cart', 'error');
        // Reset button state on error
        if (button) {
            button.disabled = false;
            button.innerHTML = 'Add to Cart';
        }
    });
}

function setPrice(min, max) {
    const minInput = document.querySelector('input[name="min"]');
    const maxInput = document.querySelector('input[name="max"]');
    
    if (minInput) minInput.value = min;
    if (maxInput) maxInput.value = max;
    
    // Auto-submit the filter form
    const form = document.getElementById('filterForm');
    if (form) form.submit();
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 transform transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    
    // Add icon based on type
    const icon = type === 'success' ? 'fas fa-check-circle' :
                 type === 'error' ? 'fas fa-exclamation-circle' :
                 'fas fa-info-circle';
    
    notification.innerHTML = `
        <div class="flex items-center space-x-2">
            <i class="${icon}"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.add('translate-y-0', 'opacity-100');
    }, 100);
    
    // Remove after 4 seconds
    setTimeout(() => {
        notification.classList.add('translate-y-2', 'opacity-0');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 300);
    }, 4000);
}

function updateCartCount(count) {
    const cartCountElement = document.getElementById('cart-count-badge');
    if (cartCountElement) {
        if (count && Number(count) > 0) {
            cartCountElement.textContent = count;
            cartCountElement.classList.remove('hidden');
            cartCountElement.classList.add('scale-110');
            setTimeout(() => cartCountElement.classList.remove('scale-110'), 300);
        } else {
            cartCountElement.textContent = '0';
            cartCountElement.classList.add('hidden');
        }
    }
}

// Mobile filter functionality
function toggleMobileFilterSidebar() {
    const sidebar = document.getElementById('mobileFilterSidebar');
    const overlay = document.getElementById('mobileFilterOverlay');
    
    if (sidebar && overlay) {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    }
}

function toggleMobileFilterSection(element) {
    const content = element.nextElementSibling;
    const icon = element.querySelector('.toggle-icon');
    
    if (content && icon) {
        content.classList.toggle('active');
        icon.classList.toggle('fa-chevron-down');
        icon.classList.toggle('fa-chevron-up');
    }
}

// Desktop filter functionality
function toggleDesktopFilterSection(element) {
    const content = element.nextElementSibling;
    const icon = element.querySelector('.toggle-icon');
    
    if (content && icon) {
        content.classList.toggle('active');
        icon.classList.toggle('fa-chevron-down');
        icon.classList.toggle('fa-chevron-up');
    }
}

// Make functions globally available
window.changeQuantity = changeQuantity;
window.adjustQuantity = adjustQuantity;
window.addToCart = addToCart;
window.setPrice = setPrice;
window.toggleMobileFilterSidebar = toggleMobileFilterSidebar;
window.toggleMobileFilterSection = toggleMobileFilterSection;
window.toggleDesktopFilterSection = toggleDesktopFilterSection;
