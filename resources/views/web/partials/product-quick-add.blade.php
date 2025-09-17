<!-- Quick Add Modal -->
<div id="quickAddModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto">
        <div class="p-6">
            <!-- Modal Header -->
            <div class="flex justify-between items-center mb-4">
                <h3 id="modalProductName" class="text-xl font-semibold"></h3>
                <button onclick="closeQuickAdd()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- Product Image and Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <img id="modalProductImage" src="" alt="" class="w-full h-64 object-cover rounded-lg">
                </div>
                
                <div>
                    <div id="modalProductPrice" class="text-2xl font-bold text-bakery-600 mb-4"></div>
                    <p id="modalProductDescription" class="text-gray-600 mb-4"></p>
                    
                    <!-- Variants Section -->
                    <div id="modalVariants" class="mb-6 hidden">
                        <h4 class="font-semibold mb-3">Select Options:</h4>
                        <div id="modalVariantsList"></div>
                        
                        <!-- Selected Variant Info -->
                        <div id="modalVariantInfo" class="mt-4 p-3 bg-gray-50 rounded-lg hidden">
                            <div class="flex justify-between items-center">
                                <div>
                                    <span class="text-sm text-gray-600">Selected: </span>
                                    <span id="modalSelectedVariant" class="font-medium"></span>
                                </div>
                                <div>
                                    <span class="text-sm text-gray-600">Stock: </span>
                                    <span id="modalSelectedStock" class="font-medium"></span>
                                </div>
                            </div>
                            <div class="mt-2">
                                <span class="text-sm text-gray-600">Price: </span>
                                <span id="modalSelectedPrice" class="text-lg font-bold text-bakery-600"></span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quantity Selector -->
                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex items-center border border-gray-300 rounded-lg">
                            <button onclick="modalDecrementQuantity()" class="px-3 py-2 text-gray-600 hover:bg-gray-100">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" id="modalQuantity" value="1" min="1" max="10" class="w-16 text-center border-0 focus:ring-0">
                            <button onclick="modalIncrementQuantity()" class="px-3 py-2 text-gray-600 hover:bg-gray-100">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        
                        <button id="modalAddToCartBtn" onclick="modalAddToCart()" class="flex-1 bg-bakery-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-bakery-700 transition-colors">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Add to Cart
                        </button>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="flex gap-2">
                        <a id="modalViewProduct" href="#" class="flex-1 border border-gray-300 py-2 px-4 rounded-lg text-center font-medium hover:bg-gray-50 transition-colors">
                            View Details
                        </a>
                        <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-heart"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentProduct = null;
let modalSelectedVariant = null;

function showQuickAdd(productId) {
    // Fetch product details
    fetch(`/product-quick-view/${productId}`)
        .then(response => response.json())
        .then(data => {
            currentProduct = data.product;
            
            // Populate modal
            document.getElementById('modalProductName').textContent = data.product.name;
            document.getElementById('modalProductDescription').textContent = data.product.description;
            document.getElementById('modalProductPrice').textContent = '₹' + parseFloat(data.product.base_price).toFixed(2);
            document.getElementById('modalViewProduct').href = `/p/${data.product.slug}`;
            
            // Set product image
            const productImage = data.product.images_path && data.product.images_path.length > 0 
                ? `/storage/${data.product.images_path[0]}`
                : '/img/placeholder-product.jpg';
            document.getElementById('modalProductImage').src = productImage;
            document.getElementById('modalProductImage').alt = data.product.name;
            
            // Handle variants
            if (data.variants && data.variants.length > 0) {
                displayModalVariants(data.variants);
                document.getElementById('modalVariants').classList.remove('hidden');
            } else {
                document.getElementById('modalVariants').classList.add('hidden');
                modalSelectedVariant = null;
            }
            
            // Reset quantity
            document.getElementById('modalQuantity').value = 1;
            
            // Show modal
            document.getElementById('quickAddModal').classList.remove('hidden');
        })
        .catch(error => {
            console.error('Error fetching product details:', error);
            alert('Error loading product details');
        });
}

function closeQuickAdd() {
    document.getElementById('quickAddModal').classList.add('hidden');
    currentProduct = null;
    modalSelectedVariant = null;
}

function displayModalVariants(variants) {
    const variantsList = document.getElementById('modalVariantsList');
    const variantTypes = {};
    
    // Group variants by type
    variants.forEach(variant => {
        if (!variantTypes[variant.variant_type]) {
            variantTypes[variant.variant_type] = [];
        }
        variantTypes[variant.variant_type].push(variant);
    });
    
    let html = '';
    Object.keys(variantTypes).forEach(type => {
        html += `<div class="mb-3">`;
        html += `<label class="block text-sm font-medium text-gray-700 mb-2">${type.charAt(0).toUpperCase() + type.slice(1)}:</label>`;
        html += `<div class="flex flex-wrap gap-2">`;
        
        variantTypes[type].forEach(variant => {
            const priceOffset = variant.price - currentProduct.base_price;
            const priceText = priceOffset > 0 ? `(+₹${priceOffset.toFixed(2)})` : priceOffset < 0 ? `(-₹${Math.abs(priceOffset).toFixed(2)})` : '';
            
            html += `
                <button onclick="selectModalVariant(${variant.id}, ${variant.price}, '${variant.variant_value}', ${variant.stock_quantity || variant.stock || 0})"
                        class="modal-variant-option px-3 py-2 border-2 border-gray-300 rounded-lg hover:border-bakery-500 focus:border-bakery-500 focus:outline-none transition-colors"
                        data-variant-id="${variant.id}">
                    <span class="font-medium">${variant.variant_value}</span>
                    ${priceText ? `<span class="text-xs text-gray-500 ml-1">${priceText}</span>` : ''}
                </button>`;
        });
        
        html += `</div></div>`;
    });
    
    variantsList.innerHTML = html;
}

function selectModalVariant(variantId, price, variantValue, stock) {
    modalSelectedVariant = variantId;
    
    // Update UI
    document.querySelectorAll('.modal-variant-option').forEach(btn => {
        btn.classList.remove('border-bakery-500', 'bg-bakery-50');
        if (btn.dataset.variantId == variantId) {
            btn.classList.add('border-bakery-500', 'bg-bakery-50');
        }
    });
    
    // Show variant info
    document.getElementById('modalVariantInfo').classList.remove('hidden');
    document.getElementById('modalSelectedVariant').textContent = variantValue;
    document.getElementById('modalSelectedStock').textContent = stock + ' units';
    document.getElementById('modalSelectedPrice').textContent = '₹' + price.toFixed(2);
    
    // Update quantity max
    document.getElementById('modalQuantity').max = Math.min(stock, 10);
}

function modalIncrementQuantity() {
    const input = document.getElementById('modalQuantity');
    const max = parseInt(input.max);
    const current = parseInt(input.value);
    if (current < max) {
        input.value = current + 1;
    }
}

function modalDecrementQuantity() {
    const input = document.getElementById('modalQuantity');
    const current = parseInt(input.value);
    if (current > 1) {
        input.value = current - 1;
    }
}

function modalAddToCart() {
    if (!currentProduct) return;
    
    const quantity = document.getElementById('modalQuantity').value;
    
    // Check if variant is required but not selected
    if (currentProduct.variants && currentProduct.variants.length > 0 && !modalSelectedVariant) {
        alert('Please select a variant');
        return;
    }
    
    // Add to cart
    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            product_id: currentProduct.id,
            variant_id: modalSelectedVariant,
            quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart count
            updateCartCount(data.cart_count);
            
            // Show success message
            showSuccessMessage('Product added to cart!');
            
            // Close modal
            closeQuickAdd();
        } else {
            alert(data.message || 'Error adding to cart');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding to cart');
    });
}

function showSuccessMessage(message) {
    // Create a temporary success message
    const successDiv = document.createElement('div');
    successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all duration-300';
    successDiv.textContent = message;
    
    document.body.appendChild(successDiv);
    
    // Fade in
    setTimeout(() => successDiv.style.transform = 'translateX(0)', 100);
    
    // Fade out and remove
    setTimeout(() => {
        successDiv.style.transform = 'translateX(100%)';
        setTimeout(() => document.body.removeChild(successDiv), 300);
    }, 3000);
}

// Close modal when clicking outside
document.getElementById('quickAddModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeQuickAdd();
    }
});
</script>