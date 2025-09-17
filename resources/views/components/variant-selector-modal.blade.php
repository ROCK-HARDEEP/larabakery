<!-- Variant Selector Modal -->
<div id="variantModal" class="variant-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background: rgba(0,0,0,0.5); z-index: 99999; align-items: center; justify-content: center;">
    <div class="variant-modal-content" style="background: white; border-radius: 16px; max-width: 500px; width: 90%; max-height: 80vh; overflow-y: auto; position: relative; margin: auto;">
        <!-- Modal Header -->
        <div style="padding: 20px 24px; border-bottom: 1px solid #e0e0e0; display: flex; justify-content: space-between; align-items: center;">
            <h3 id="modalProductName" style="font-size: 20px; font-weight: 600; color: #333; margin: 0;">Select Variant</h3>
            <button onclick="closeVariantModal()" style="background: none; border: none; font-size: 24px; color: #666; cursor: pointer; padding: 8px; line-height: 1; transition: color 0.2s;" onmouseover="this.style.color='#333'" onmouseout="this.style.color='#666'">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Product Image and Info -->
        <div style="padding: 20px 24px; border-bottom: 1px solid #e0e0e0;">
            <div style="display: flex; gap: 16px; align-items: center;">
                <img id="modalProductImage" src="" alt="Product" style="width: 80px; height: 80px; object-fit: cover; border-radius: 8px; background: #f5f5f5;">
                <div>
                    <h4 id="modalProductTitle" style="font-size: 16px; font-weight: 600; color: var(--skc-black); margin: 0 0 4px 0;"></h4>
                    <p id="modalProductPrice" style="font-size: 18px; font-weight: 700; color: var(--skc-orange); margin: 0;"></p>
                </div>
            </div>
        </div>

        <!-- Variant Selection -->
        <div id="modalVariantContent" style="padding: 20px 24px;">
            <!-- Dynamic variant content will be loaded here -->
        </div>

        <!-- Modal Footer -->
        <div style="padding: 16px 24px; border-top: 1px solid #e0e0e0; display: flex; gap: 12px;">
            <div style="display: flex; align-items: center; border: 2px solid #e0e0e0; border-radius: 8px; overflow: hidden;">
                <button onclick="decrementModalQty()" style="padding: 12px 16px; background: white; border: none; cursor: pointer; transition: background 0.2s;">
                    <i class="fas fa-minus" style="color: var(--skc-medium-gray);"></i>
                </button>
                <input type="number" id="modalQuantity" value="1" min="1" max="10"
                       style="width: 60px; text-align: center; border: none; border-left: 2px solid #e0e0e0; border-right: 2px solid #e0e0e0; padding: 12px 0; font-weight: 600; font-size: 16px;">
                <button onclick="incrementModalQty()" style="padding: 12px 16px; background: white; border: none; cursor: pointer; transition: background 0.2s;">
                    <i class="fas fa-plus" style="color: var(--skc-medium-gray);"></i>
                </button>
            </div>

            <button onclick="addToCartFromModal()" id="modalAddToCartBtn"
                    style="flex: 1; padding: 16px 24px; background: var(--skc-black); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px;">
                <i class="fas fa-shopping-cart"></i>
                Add to Cart
            </button>
        </div>
    </div>
</div>

<style>
.variant-modal {
    display: none !important;
}

.variant-modal.show {
    display: flex !important;
}

.variant-option-modal {
    padding: 12px 16px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    text-align: center;
    transition: all 0.3s;
    background: white;
    cursor: pointer;
    min-width: 120px;
}

.variant-option-modal:hover {
    border-color: var(--skc-orange);
    background: rgba(246, 157, 28, 0.05);
}

.variant-option-modal.selected {
    border-color: var(--skc-orange);
    background: rgba(246, 157, 28, 0.1);
}

.variant-group-modal {
    margin-bottom: 20px;
}

.variant-group-modal h4 {
    font-size: 16px;
    font-weight: 600;
    color: var(--skc-black);
    margin: 0 0 12px 0;
}

.variant-options-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.hierarchical-variant-group {
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 15px;
    background: white;
    margin-bottom: 15px;
}

.hierarchical-variant-group h4 {
    font-size: 16px;
    font-weight: 600;
    color: var(--skc-black);
    margin: 0 0 12px 0;
}
</style>

<script>
let currentModalProduct = null;
let selectedModalVariant = null;

function openVariantModal(productId, productName, productImage, productPrice) {
    console.log('Opening modal for product:', productId, productName);

    currentModalProduct = productId;
    selectedModalVariant = null;

    // Set product info
    document.getElementById('modalProductName').textContent = productName;
    document.getElementById('modalProductTitle').textContent = productName;
    document.getElementById('modalProductImage').src = productImage;
    document.getElementById('modalProductPrice').textContent = productPrice;
    document.getElementById('modalQuantity').value = 1;

    // Load variants for this product
    loadProductVariants(productId);

    // Show modal
    const modal = document.getElementById('variantModal');
    modal.classList.add('show');
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';

    console.log('Modal opened');
}

function closeVariantModal() {
    console.log('Closing modal');

    const modal = document.getElementById('variantModal');
    modal.classList.remove('show');
    modal.style.display = 'none';
    document.body.style.overflow = 'auto';
    currentModalProduct = null;
    selectedModalVariant = null;

    console.log('Modal closed');
}

function loadProductVariants(productId) {
    console.log('Loading variants for product:', productId);

    // Find the product element with variants data
    const productElement = document.querySelector(`[data-product-id="${productId}"]`);
    console.log('Product element found:', productElement);

    if (!productElement) {
        console.error('Product element not found for ID:', productId);
        document.getElementById('modalVariantContent').innerHTML = '<p style="text-align: center; color: red;">Product not found</p>';
        return;
    }

    const variantsDataRaw = productElement.dataset.variants;
    console.log('Raw variants data:', variantsDataRaw);

    let variantsData = [];
    try {
        variantsData = variantsDataRaw ? JSON.parse(variantsDataRaw) : [];
        console.log('Parsed variants data:', variantsData);
    } catch (e) {
        console.error('Error parsing variants data:', e);
        document.getElementById('modalVariantContent').innerHTML = '<p style="text-align: center; color: red;">Error loading variants</p>';
        return;
    }

    renderModalVariants(variantsData);
}

function renderModalVariants(variants) {
    console.log('Rendering variants:', variants);
    const container = document.getElementById('modalVariantContent');

    if (!variants || variants.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #666; padding: 20px;">No variants available for this product</p>';
        console.log('No variants to display');
        return;
    }

    // Check if we have hierarchical variants
    const hasHierarchicalVariants = variants.some(variant => variant.variant_value && variant.variant_value.includes(' - '));
    console.log('Has hierarchical variants:', hasHierarchicalVariants);

    if (hasHierarchicalVariants) {
        renderHierarchicalModalVariants(variants, container);
    } else {
        renderSimpleModalVariants(variants, container);
    }
}

function renderHierarchicalModalVariants(variants, container) {
    const groupedVariants = {};

    variants.forEach(variant => {
        const parts = variant.variant_value.split(' - ');
        const mainVariant = parts[0];
        const quantity = parts[1] || variant.variant_value;

        if (!groupedVariants[mainVariant]) {
            groupedVariants[mainVariant] = [];
        }

        groupedVariants[mainVariant].push({
            id: variant.id,
            quantity: quantity,
            price: variant.price,
            stock: variant.stock_quantity,
            variant_type: variant.variant_type
        });
    });

    let html = '';
    Object.keys(groupedVariants).forEach(mainVariant => {
        html += `
            <div class="hierarchical-variant-group">
                <h4>${mainVariant}</h4>
                <div class="variant-options-row">
        `;

        groupedVariants[mainVariant].forEach(quantity => {
            const isOutOfStock = quantity.stock <= 0;
            html += `
                <div class="variant-option-modal ${isOutOfStock ? 'out-of-stock' : ''}"
                     onclick="selectModalVariant(${quantity.id}, '${mainVariant} - ${quantity.quantity}', ${quantity.price}, ${quantity.stock})"
                     data-variant-id="${quantity.id}">
                    <p style="font-weight: 600; color: var(--skc-black); margin: 0 0 4px 0; font-size: 14px;">${quantity.quantity}</p>
                    <p style="color: var(--skc-orange); font-weight: 700; margin: 0 0 4px 0; font-size: 14px;">₹${parseFloat(quantity.price).toFixed(2)}</p>
                    ${isOutOfStock ?
                        '<p style="font-size: 11px; color: #f44336; margin: 0;">Out of stock</p>' :
                        `<p style="font-size: 11px; color: #4caf50; margin: 0;">${quantity.stock} in stock</p>`
                    }
                </div>
            `;
        });

        html += `
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

function renderSimpleModalVariants(variants, container) {
    const variantsByType = {};

    variants.forEach(variant => {
        if (!variantsByType[variant.variant_type]) {
            variantsByType[variant.variant_type] = [];
        }
        variantsByType[variant.variant_type].push(variant);
    });

    let html = '';
    Object.keys(variantsByType).forEach(type => {
        html += `
            <div class="variant-group-modal">
                <h4>${type}:</h4>
                <div class="variant-options-row">
        `;

        variantsByType[type].forEach(variant => {
            const isOutOfStock = variant.stock_quantity <= 0;
            html += `
                <div class="variant-option-modal ${isOutOfStock ? 'out-of-stock' : ''}"
                     onclick="selectModalVariant(${variant.id}, '${variant.variant_value}', ${variant.price}, ${variant.stock_quantity})"
                     data-variant-id="${variant.id}">
                    <p style="font-weight: 600; color: var(--skc-black); margin: 0 0 5px 0;">${variant.variant_value}</p>
                    <p style="color: var(--skc-orange); font-weight: 700; margin: 0 0 5px 0;">₹${parseFloat(variant.price).toFixed(2)}</p>
                    ${isOutOfStock ?
                        '<p style="font-size: 12px; color: #f44336; margin: 0;">Out of stock</p>' :
                        `<p style="font-size: 12px; color: #4caf50; margin: 0;">${variant.stock_quantity} in stock</p>`
                    }
                </div>
            `;
        });

        html += `
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

function selectModalVariant(variantId, variantName, price, stock) {
    if (stock <= 0) return; // Can't select out of stock variants

    selectedModalVariant = {
        id: variantId,
        name: variantName,
        price: price,
        stock: stock
    };

    // Update visual selection
    document.querySelectorAll('.variant-option-modal').forEach(el => {
        el.classList.remove('selected');
    });

    document.querySelector(`[data-variant-id="${variantId}"]`).classList.add('selected');

    // Update price display
    document.getElementById('modalProductPrice').textContent = `₹${parseFloat(price).toFixed(2)}`;

    // Update add to cart button
    const addToCartBtn = document.getElementById('modalAddToCartBtn');
    addToCartBtn.style.opacity = '1';
    addToCartBtn.style.pointerEvents = 'auto';
}

function incrementModalQty() {
    const input = document.getElementById('modalQuantity');
    const current = parseInt(input.value);
    const maxStock = selectedModalVariant ? selectedModalVariant.stock : 10;
    if (current < Math.min(10, maxStock)) {
        input.value = current + 1;
    }
}

function decrementModalQty() {
    const input = document.getElementById('modalQuantity');
    const current = parseInt(input.value);
    if (current > 1) {
        input.value = current - 1;
    }
}

function addToCartFromModal() {
    if (!selectedModalVariant) {
        alert('Please select a variant');
        return;
    }

    const quantity = document.getElementById('modalQuantity').value;

    // Here you would make an AJAX call to add to cart
    console.log('Adding to cart:', {
        productId: currentModalProduct,
        variantId: selectedModalVariant.id,
        variantName: selectedModalVariant.name,
        price: selectedModalVariant.price,
        quantity: quantity
    });

    // Show success message
    try {
        if (typeof showToast === 'function') {
            showToast(`${selectedModalVariant.name} added to cart!`, 'success');
        } else {
            alert(`${selectedModalVariant.name} added to cart!`);
        }
    } catch (e) {
        console.log('Toast function not available, using alert');
        alert(`${selectedModalVariant.name} added to cart!`);
    }

    // Close modal
    closeVariantModal();
}

// Close modal when clicking outside
document.addEventListener('click', function(e) {
    const modal = document.getElementById('variantModal');
    const modalContent = document.querySelector('.variant-modal-content');

    if (modal && modal.classList.contains('show') && e.target === modal && !modalContent.contains(e.target)) {
        closeVariantModal();
    }
});

// Escape key to close modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('variantModal');
        if (modal && modal.classList.contains('show')) {
            closeVariantModal();
        }
    }
});
</script>