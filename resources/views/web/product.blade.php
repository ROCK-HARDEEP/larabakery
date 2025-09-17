@extends('web.layouts.app')

@section('content')
<section class="skc-section" style="padding-top: 20px; background: #f8f8f8; min-height: 100vh;">
    <!-- Breadcrumb -->
    <div style="background: white; border-bottom: 1px solid var(--skc-border); margin-bottom: 30px;">
        <div class="skc-container" style="padding: 15px 20px;">
            <nav style="display: flex; align-items: center; gap: 10px; font-size: 14px;">
                <a href="{{ route('home') }}" style="color: var(--skc-medium-gray); text-decoration: none; transition: color 0.2s;">Home</a>
                <span style="color: #ccc;">/</span>
                <span style="color: var(--skc-medium-gray);">{{ $product->category->name }}</span>
                <span style="color: #ccc;">/</span>
                <span style="color: var(--skc-black); font-weight: 600;">{{ $product->name }}</span>
            </nav>
        </div>
    </div>

    <!-- Main Product Section -->
    <div class="skc-container">
        <div class="pdp-sticky-boundary" style="background: white; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); overflow: visible;">
            <div class="pdp-two-col" style="display: grid; grid-template-columns: 1fr 1fr; gap: 0;">

                <!-- Product Images / Buy Box (Sticky on desktop) -->
                <div class="buy-box-sticky" style="background: #fafafa; padding: 40px; height: fit-content;">
                    <div class="buy-box-inner">
                    @php
                        $images = $product->images_path ?? [];
                        $hasImages = !empty($images) && is_array($images);
                    @endphp

                    <div style="aspect-ratio: 1/1; background: white; border-radius: 8px; overflow: hidden; margin-bottom: 20px; max-width: 480px; margin-left: auto; margin-right: auto;">
                        @if($hasImages)
                            <img id="mainImage" src="{{ asset('storage/' . $images[0]) }}"
                                 alt="{{ $product->name }}"
                                 style="width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #f5f5f5;">
                                <span style="color: #999; font-size: 18px;">No Image Available</span>
                            </div>
                        @endif
                    </div>

                    @if($hasImages && count($images) > 1)
                        <div style="display: flex; gap: 10px; padding: 5px 0; margin-bottom: 25px; justify-content: center; flex-wrap: wrap;">
                            @foreach($images as $index => $image)
                                <button onclick="changeImage('{{ asset('storage/' . $image) }}')"
                                        style="flex-shrink: 0; width: 48px; height: 48px; border: 2px solid #e0e0e0; border-radius: 8px; overflow: hidden; cursor: pointer; transition: all 0.3s; background: white;">
                                    <img src="{{ asset('storage/' . $image) }}"
                                         alt="Thumbnail {{ $index + 1 }}"
                                         style="width: 100%; height: 100%; object-fit: cover;">
                                </button>
                            @endforeach
                        </div>
                    @endif

                    <!-- Quantity and Add to Cart moved here -->
                    <div style="border-top: 2px solid #e0e0e0; padding-top: 25px;">
                        <div style="display: flex; gap: 15px; align-items: stretch; margin-bottom: 20px;">
                            <!-- Quantity -->
                            <div style="flex: 0 0 auto;">
                                <label style="display: block; font-size: 14px; font-weight: 600; color: var(--skc-black); margin-bottom: 8px; text-transform: uppercase; letter-spacing: 0.5px;">Quantity</label>
                                <div style="display: flex; align-items: center; border: 2px solid #e0e0e0; border-radius: 8px; overflow: hidden; height: 50px;">
                                    <button onclick="decrementQty()" style="padding: 0 16px; height: 100%; background: white; border: none; cursor: pointer; transition: background 0.2s;">
                                        <i class="fas fa-minus" style="color: var(--skc-medium-gray);"></i>
                                    </button>
                                    <input type="number" id="quantity" value="1" min="1" max="10"
                                           style="width: 60px; height: 100%; text-align: center; border: none; border-left: 2px solid #e0e0e0; border-right: 2px solid #e0e0e0; font-weight: 600; font-size: 16px;">
                                    <button onclick="incrementQty()" style="padding: 0 16px; height: 100%; background: white; border: none; cursor: pointer; transition: background 0.2s;">
                                        <i class="fas fa-plus" style="color: var(--skc-medium-gray);"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Add to Cart -->
                            <button onclick="addToCart()"
                                    style="flex: 1; margin-top: 28px; height: 50px; background: var(--skc-black); color: white; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px;">
                                <i class="fas fa-shopping-cart"></i>
                                Add to Cart
                            </button>
                        </div>

                        <!-- Additional Actions -->
                        <div style="display: flex; gap: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
                            <button onclick="toggleWishlist({{ $product->id }})" id="wishlist-btn-{{ $product->id }}" style="display: flex; align-items: center; gap: 8px; background: none; border: none; color: var(--skc-medium-gray); cursor: pointer; font-size: 15px; transition: color 0.2s;">
                                <i class="far fa-heart" style="font-size: 18px;"></i>
                                <span>Add to Wishlist</span>
                            </button>
                            <button onclick="shareProduct()" style="display: flex; align-items: center; gap: 8px; background: none; border: none; color: var(--skc-medium-gray); cursor: pointer; font-size: 15px; transition: color 0.2s;">
                                <i class="fas fa-share-alt" style="font-size: 18px;"></i>
                                <span>Share</span>
                            </button>
                        </div>
                    </div>
                    </div>
                </div>

                <!-- Product Details -->
                <div class="pdp-right" style="padding: 40px;">
                <!-- Title and Rating -->
                <div style="margin-bottom: 25px;">
                    <h1 style="font-size: 32px; font-weight: 700; color: var(--skc-black); margin: 0 0 15px 0;">{{ $product->name }}</h1>
                    <div style="display: flex; align-items: center; gap: 20px;">
                        @if($product->display_rating > 0)
                            <div style="display: flex; align-items: center;">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $product->display_rating)
                                        <i class="fas fa-star" style="color: #ffb800; font-size: 14px; margin-right: 2px;"></i>
                                    @elseif($i - $product->display_rating < 1)
                                        <i class="fas fa-star-half-alt" style="color: #ffb800; font-size: 14px; margin-right: 2px;"></i>
                                    @else
                                        <i class="far fa-star" style="color: #e0e0e0; font-size: 14px; margin-right: 2px;"></i>
                                    @endif
                                @endfor
                                <span style="margin-left: 10px; color: var(--skc-medium-gray); font-size: 14px;">({{ $product->reviews_count }} reviews)</span>
                            </div>
                        @else
                            <div style="color: #999; font-size: 14px; font-style: italic;">
                                No reviews yet - Be the first to review!
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Price Range from Variants -->
                <div style="padding: 20px; background: linear-gradient(90deg, rgba(246,157,28,0.1) 0%, rgba(246,157,28,0.05) 100%); border-left: 4px solid var(--skc-orange); margin-bottom: 25px; border-radius: 0 8px 8px 0;">
                    <span style="font-size: 36px; font-weight: 700; color: var(--skc-orange);">{{ $product->display_price }}</span>
                    @if($product->has_variable_pricing)
                        <p style="font-size: 14px; color: var(--skc-medium-gray); margin: 8px 0 0 0;">Price varies by variant</p>
                    @endif
                </div>

                <!-- Taste -->
                @if($product->taste_display)
                    <div style="margin-bottom: 30px;">
                        <h3 style="font-size: 16px; font-weight: 600; color: var(--skc-black); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Taste</h3>
                        <p style="line-height: 1.8; font-size: 15px;">
                            @if($product->taste_type && $product->taste_value)
                                <span style="color: var(--skc-black); font-weight: 600;">{{ ucfirst($product->taste_type) }}:</span>
                                <span style="color: var(--skc-orange); font-weight: 500;">{{ $product->taste_value }}</span>
                            @else
                                <span style="color: var(--skc-medium-gray);">{{ $product->taste_display }}</span>
                            @endif
                        </p>
                    </div>
                @endif

                <!-- Description -->
                @if($product->description)
                    <div style="margin-bottom: 30px;">
                        <h3 style="font-size: 16px; font-weight: 600; color: var(--skc-black); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px;">Description</h3>
                        <p style="color: var(--skc-medium-gray); line-height: 1.8; font-size: 15px;">{{ $product->description }}</p>
                    </div>
                @endif

                <!-- Variants -->
                @if($product->variants && $product->variants->count() > 0)
                    <div style="margin-bottom: 30px;">
                        <h3 style="font-size: 16px; font-weight: 600; color: var(--skc-black); margin-bottom: 15px; text-transform: uppercase; letter-spacing: 0.5px;">Choose Option:</h3>
                        @php
                            // Check if we have hierarchical variants (with " - " separator)
                            $hasHierarchicalVariants = $product->variants->contains(function($variant) {
                                return strpos($variant->variant_value, ' - ') !== false;
                            });

                            if ($hasHierarchicalVariants) {
                                // Group hierarchical variants by main variant (e.g., Chocolate, Vanilla)
                                $groupedVariants = collect();
                                foreach ($product->variants as $variant) {
                                    $parts = explode(' - ', $variant->variant_value, 2);
                                    $mainVariant = $parts[0];
                                    $quantity = $parts[1] ?? $variant->variant_value;

                                    if (!$groupedVariants->has($mainVariant)) {
                                        $groupedVariants[$mainVariant] = collect();
                                    }

                                    $groupedVariants[$mainVariant]->push([
                                        'id' => $variant->id,
                                        'quantity' => $quantity,
                                        'price' => $variant->price,
                                        'stock' => $variant->stock_quantity,
                                        'variant_type' => $variant->variant_type
                                    ]);
                                }
                            } else {
                                // Simple variants (just quantities/sizes)
                                $variantsByType = $product->variants->groupBy('variant_type');
                            }
                        @endphp

                        @if($hasHierarchicalVariants)
                            <!-- Hierarchical Variants (Flavor + Quantities) -->
                            <div style="margin-bottom: 25px;">
                                <p style="font-size: 14px; color: var(--skc-medium-gray); margin-bottom: 12px;">{{ $product->variants->first()->variant_type }}:</p>
                                <div style="display: flex; flex-direction: column; gap: 15px;">
                                    @foreach($groupedVariants as $mainVariant => $quantities)
                                        <div style="border: 2px solid #e0e0e0; border-radius: 12px; padding: 15px; background: white;">
                                            <h4 style="font-size: 16px; font-weight: 600; color: var(--skc-black); margin: 0 0 12px 0;">{{ $mainVariant }}</h4>
                                            <div class="variant-scroller">
                                                <button class="variant-arrow prev" type="button" aria-label="Scroll options left">&#8249;</button>
                                                <div class="variant-list" style="display: flex; flex-wrap: wrap; gap: 10px;">
                                                @foreach($quantities as $quantityData)
                                                    <label style="cursor: pointer; flex: 0 0 auto;">
                                                        <input type="radio" name="variant" value="{{ $quantityData['id'] }}"
                                                               data-price="{{ $quantityData['price'] }}"
                                                               class="variant-radio" style="display: none;">
                                                        <div class="variant-option" style="padding: 12px 16px; border: 1px solid #e0e0e0; border-radius: 8px; text-align: center; transition: all 0.3s; background: #fafafa; min-width: 120px;">
                                                            <p style="font-weight: 600; color: var(--skc-black); margin: 0 0 4px 0; font-size: 14px;">{{ $quantityData['quantity'] }}</p>
                                                            <p style="color: var(--skc-orange); font-weight: 700; margin: 0 0 4px 0; font-size: 14px;">₹{{ number_format($quantityData['price'], 2) }}</p>
                                                            @if($quantityData['stock'] > 0)
                                                                <p style="font-size: 11px; color: #4caf50; margin: 0;">{{ $quantityData['stock'] }} in stock</p>
                                                            @else
                                                                <p style="font-size: 11px; color: #f44336; margin: 0;">Out of stock</p>
                                                            @endif
                                                        </div>
                                                    </label>
                                                @endforeach
                                                </div>
                                                <button class="variant-arrow next" type="button" aria-label="Scroll options right">&#8250;</button>
                                                <div class="variant-fade left" aria-hidden="true"></div>
                                                <div class="variant-fade right" aria-hidden="true"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <!-- Simple Variants (Quantities/Sizes only) -->
                            @foreach($variantsByType as $type => $variants)
                                <div style="margin-bottom: 20px;">
                                    <p style="font-size: 14px; color: var(--skc-medium-gray); margin-bottom: 12px;">{{ $type ?? 'Options' }}:</p>
                                    <div class="variant-scroller">
                                        <button class="variant-arrow prev" type="button" aria-label="Scroll options left">&#8249;</button>
                                        <div class="variant-list" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px;">
                                        @foreach($variants as $variant)
                                            <label style="cursor: pointer;">
                                                <input type="radio" name="variant" value="{{ $variant->id }}"
                                                       data-price="{{ $variant->price }}"
                                                       class="variant-radio" style="display: none;">
                                                <div class="variant-option" style="padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; text-align: center; transition: all 0.3s; background: white;">
                                                    <p style="font-weight: 600; color: var(--skc-black); margin: 0 0 5px 0;">{{ $variant->variant_value }}</p>
                                                    <p style="color: var(--skc-orange); font-weight: 700; margin: 0 0 5px 0;">₹{{ number_format($variant->price, 2) }}</p>
                                                    @if($variant->stock_quantity > 0)
                                                        <p style="font-size: 12px; color: #4caf50; margin: 0;">{{ $variant->stock_quantity }} in stock</p>
                                                    @else
                                                        <p style="font-size: 12px; color: #f44336; margin: 0;">Out of stock</p>
                                                    @endif
                                                </div>
                                            </label>
                                        @endforeach
                                        </div>
                                        <button class="variant-arrow next" type="button" aria-label="Scroll options right">&#8250;</button>
                                        <div class="variant-fade left" aria-hidden="true"></div>
                                        <div class="variant-fade right" aria-hidden="true"></div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endif
                <!-- Sentinel used for fallback boundary detection -->
                <div class="pdp-boundary-end" aria-hidden="true" style="position: relative; width: 100%; height: 1px;"></div>
            </div>
        </div>
    </div>

    <!-- Product Information Tabs Section -->
    <div class="skc-container" style="margin-top: 30px;">

        <!-- Product Information Tabs -->
        <div style="background: white; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-top: 30px; overflow: hidden;">
            <div style="border-bottom: 1px solid #e0e0e0;">
                <nav style="display: flex;">
                    <button onclick="showTab('details')" class="tab-btn active" 
                            style="flex: 1; padding: 18px; background: white; border: none; font-size: 16px; font-weight: 600; color: var(--skc-orange); cursor: pointer; transition: all 0.3s; border-bottom: 3px solid var(--skc-orange);">
                        Details
                    </button>
                    <button onclick="showTab('ingredients')" class="tab-btn" 
                            style="flex: 1; padding: 18px; background: white; border: none; font-size: 16px; font-weight: 600; color: var(--skc-medium-gray); cursor: pointer; transition: all 0.3s; border-bottom: 3px solid transparent;">
                        Ingredients
                    </button>
                    <button onclick="showTab('nutrition')" class="tab-btn" 
                            style="flex: 1; padding: 18px; background: white; border: none; font-size: 16px; font-weight: 600; color: var(--skc-medium-gray); cursor: pointer; transition: all 0.3s; border-bottom: 3px solid transparent;">
                        Nutrition
                    </button>
                    <button onclick="showTab('storage')" class="tab-btn" 
                            style="flex: 1; padding: 18px; background: white; border: none; font-size: 16px; font-weight: 600; color: var(--skc-medium-gray); cursor: pointer; transition: all 0.3s; border-bottom: 3px solid transparent;">
                        Storage
                    </button>
                </nav>
            </div>
            
            <div style="padding: 35px;">
                <!-- Details Tab -->
                <div id="details-tab" class="tab-content">
                    <h3 style="font-size: 20px; font-weight: 700; color: var(--skc-black); margin-bottom: 15px;">Product Details</h3>
                    @if($product->full_description || $product->description)
                        <p style="color: var(--skc-medium-gray); line-height: 1.8; font-size: 15px; margin-bottom: 20px;">{{ $product->full_description ?? $product->description }}</p>
                    @else
                        <p style="color: var(--skc-medium-gray); line-height: 1.8; font-size: 15px; margin-bottom: 20px;">This premium product is carefully crafted to deliver exceptional quality and taste. Each batch is made with attention to detail, ensuring consistent excellence in every purchase.</p>
                    @endif

                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-top: 30px;">
                        @if($product->hsn_code)
                            <div style="padding: 15px; background: #f8f9fa; border-radius: 8px;">
                                <p style="margin: 0; color: var(--skc-medium-gray); font-size: 13px;">HSN Code</p>
                                <p style="margin: 5px 0 0 0; color: var(--skc-black); font-weight: 600; font-size: 15px;">{{ $product->hsn_code }}</p>
                            </div>
                        @endif

                        @if($product->shelf_life)
                            <div style="padding: 15px; background: #e3f2fd; border-radius: 8px;">
                                <p style="margin: 0; color: var(--skc-medium-gray); font-size: 13px;">Shelf Life</p>
                                <p style="margin: 5px 0 0 0; color: var(--skc-black); font-weight: 600; font-size: 15px;">{{ $product->shelf_life }} days</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Ingredients Tab -->
                <div id="ingredients-tab" class="tab-content" style="display: none;">
                    <h3 style="font-size: 20px; font-weight: 700; color: var(--skc-black); margin-bottom: 15px;">Ingredients</h3>
                    @if($product->ingredients)
                        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <p style="color: var(--skc-medium-gray); line-height: 1.8; font-size: 15px; margin: 0;">{{ $product->ingredients }}</p>
                        </div>
                    @else
                        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                            <p style="color: var(--skc-medium-gray); line-height: 1.8; font-size: 15px; margin: 0;">Premium wheat flour, fresh butter, organic eggs, pure vanilla extract, refined sugar, baking powder, salt, and natural flavoring.</p>
                        </div>
                    @endif

                    @if($product->allergen_info)
                        <div style="margin-top: 20px; padding: 20px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 0 8px 8px 0;">
                            <h4 style="font-size: 16px; font-weight: 600; color: #856404; margin: 0 0 10px 0;">⚠️ Allergen Information</h4>
                            <p style="color: #856404; margin: 0; font-size: 14px; line-height: 1.6;">{{ $product->allergen_info }}</p>
                        </div>
                    @else
                        <div style="margin-top: 20px; padding: 20px; background: #fff3cd; border-left: 4px solid #ffc107; border-radius: 0 8px 8px 0;">
                            <h4 style="font-size: 16px; font-weight: 600; color: #856404; margin: 0 0 10px 0;">⚠️ Allergen Information</h4>
                            <p style="color: #856404; margin: 0; font-size: 14px; line-height: 1.6;">Contains: Wheat (Gluten), Eggs, Milk products. May contain traces of nuts.</p>
                        </div>
                    @endif
                </div>
                
                <!-- Nutrition Tab -->
                <div id="nutrition-tab" class="tab-content" style="display: none;">
                    <h3 style="font-size: 20px; font-weight: 700; color: var(--skc-black); margin-bottom: 20px;">Nutritional Information</h3>

                    <div style="background: #f8f9fa; border-radius: 8px; padding: 20px;">
                        @if($product->nutritional_info && is_array($product->nutritional_info) && count($product->nutritional_info) > 0)
                            <p style="font-size: 14px; color: var(--skc-medium-gray); margin: 0 0 15px 0;">Per 100g serving:</p>
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 2px solid var(--skc-orange);">
                                        <th style="text-align: left; padding: 12px 0; font-weight: 600; color: var(--skc-black);">Nutrient</th>
                                        <th style="text-align: right; padding: 12px 0; font-weight: 600; color: var(--skc-black);">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->nutritional_info as $nutrient => $value)
                                        <tr style="border-bottom: 1px solid #e0e0e0;">
                                            <td style="padding: 12px 0; color: var(--skc-medium-gray); font-size: 15px;">{{ ucfirst(str_replace('_', ' ', $nutrient)) }}</td>
                                            <td style="text-align: right; padding: 12px 0; color: var(--skc-black); font-weight: 500; font-size: 15px;">{{ $value }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p style="font-size: 14px; color: var(--skc-medium-gray); margin: 0 0 15px 0;">Per 100g serving:</p>
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 2px solid var(--skc-orange);">
                                        <th style="text-align: left; padding: 12px 0; font-weight: 600; color: var(--skc-black);">Nutrient</th>
                                        <th style="text-align: right; padding: 12px 0; font-weight: 600; color: var(--skc-black);">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr style="border-bottom: 1px solid #e0e0e0;">
                                        <td style="padding: 12px 0; color: var(--skc-medium-gray); font-size: 15px;">Energy</td>
                                        <td style="text-align: right; padding: 12px 0; color: var(--skc-black); font-weight: 500; font-size: 15px;">450 kcal</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #e0e0e0;">
                                        <td style="padding: 12px 0; color: var(--skc-medium-gray); font-size: 15px;">Protein</td>
                                        <td style="text-align: right; padding: 12px 0; color: var(--skc-black); font-weight: 500; font-size: 15px;">8g</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #e0e0e0;">
                                        <td style="padding: 12px 0; color: var(--skc-medium-gray); font-size: 15px;">Carbohydrates</td>
                                        <td style="text-align: right; padding: 12px 0; color: var(--skc-black); font-weight: 500; font-size: 15px;">55g</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #e0e0e0;">
                                        <td style="padding: 12px 0; color: var(--skc-medium-gray); font-size: 15px;">Fat</td>
                                        <td style="text-align: right; padding: 12px 0; color: var(--skc-black); font-weight: 500; font-size: 15px;">22g</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #e0e0e0;">
                                        <td style="padding: 12px 0; color: var(--skc-medium-gray); font-size: 15px;">Fiber</td>
                                        <td style="text-align: right; padding: 12px 0; color: var(--skc-black); font-weight: 500; font-size: 15px;">2g</td>
                                    </tr>
                                    <tr style="border-bottom: 1px solid #e0e0e0;">
                                        <td style="padding: 12px 0; color: var(--skc-medium-gray); font-size: 15px;">Sugar</td>
                                        <td style="text-align: right; padding: 12px 0; color: var(--skc-black); font-weight: 500; font-size: 15px;">25g</td>
                                    </tr>
                                </tbody>
                            </table>
                        @endif

                        <p style="font-size: 12px; color: var(--skc-medium-gray); margin: 20px 0 0 0; font-style: italic;">* Daily values are based on a 2000 calorie diet</p>
                    </div>
                </div>
                
                <!-- Storage Tab -->
                <div id="storage-tab" class="tab-content" style="display: none;">
                    <h3 style="font-size: 20px; font-weight: 700; color: var(--skc-black); margin-bottom: 15px;">Storage Instructions</h3>
                    @if($product->storage_instructions)
                        <p style="color: var(--skc-medium-gray); line-height: 1.8; font-size: 15px; margin-bottom: 20px;">{{ $product->storage_instructions }}</p>
                    @else
                        <ul style="color: var(--skc-medium-gray); line-height: 2; font-size: 15px; padding-left: 25px;">
                            <li style="margin-bottom: 10px;">Store in a cool, dry place</li>
                            <li style="margin-bottom: 10px;">Keep away from direct sunlight</li>
                            <li style="margin-bottom: 10px;">Consume within 2-3 days for best freshness</li>
                            <li style="margin-bottom: 10px;">Can be frozen for up to 1 month</li>
                        </ul>
                    @endif

                    @if($product->shelf_life)
                        <div style="margin-top: 25px; padding: 20px; background: #e8f5e9; border-left: 4px solid #4caf50; border-radius: 0 8px 8px 0;">
                            <h4 style="font-size: 16px; font-weight: 600; color: #2e7d32; margin: 0 0 8px 0;">Shelf Life</h4>
                            <p style="color: #2e7d32; margin: 0; font-size: 14px;">{{ $product->shelf_life }} days from manufacturing date</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Related Products - You Might Also Like -->
        @if(isset($related) && $related->count() > 0)
            <div style="margin: 60px 0;">
                <div style="text-align: center; margin-bottom: 40px;">
                    <h2 style="font-size: 32px; font-weight: 700; color: var(--skc-black); margin: 0 0 10px 0;">You Might Also Like</h2>
                    <p style="color: var(--skc-medium-gray); font-size: 16px;">Discover more delicious treats</p>
                </div>
                <div class="related-products-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 25px; margin: 0 auto;">
                    @foreach($related as $relatedProduct)
                        <div class="related-product-card" style="background: white; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); transition: all 0.3s ease; border: 2px solid transparent;">
                            <a href="{{ route('product.show', $relatedProduct->slug) }}" style="text-decoration: none; color: inherit;">
                                <!-- Product Image -->
                                <div style="height: 240px; overflow: hidden; background: #f8f9fa; position: relative;">
                                    @if($relatedProduct->first_image)
                                        <img src="{{ asset('storage/' . $relatedProduct->first_image) }}"
                                             alt="{{ $relatedProduct->name }}"
                                             style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s;"
                                             class="related-product-img">
                                    @else
                                        <div style="width: 100%; height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #999;">
                                            <i class="fas fa-cake-candles" style="font-size: 40px; opacity: 0.5; margin-bottom: 8px;"></i>
                                            <span>No Image</span>
                                        </div>
                                    @endif

                                    <!-- Detail View Overlay -->
                                    <div class="related-product-overlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.7); display: flex; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.3s ease;">
                                        <button type="button" style="background: white; color: var(--skc-black); padding: 10px 20px; border: none; border-radius: 25px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;"
                                                onclick="event.stopPropagation(); window.location.href='{{ route('product.show', $relatedProduct->slug) }}'">
                                            <i class="fas fa-eye"></i> Detail View
                                        </button>
                                    </div>
                                </div>

                                <!-- Product Info -->
                                <div style="padding: 20px;">
                                    <!-- Product Name -->
                                    <h3 style="font-size: 16px; font-weight: 600; color: var(--skc-black); margin: 0 0 8px 0; line-height: 1.3; transition: color 0.3s ease;" class="related-product-name">
                                        {{ $relatedProduct->name }}
                                    </h3>

                                    <!-- Product Description -->
                                    <p style="font-size: 13px; color: var(--skc-medium-gray); line-height: 1.5; margin: 0 0 12px 0;">
                                        {{ Str::limit($relatedProduct->description ?? $relatedProduct->short_description ?? '', 60) }}
                                    </p>

                                    <!-- Product Rating -->
                                    <div style="display: flex; align-items: center; gap: 3px; margin-bottom: 15px;">
                                        @if(isset($relatedProduct->display_rating) && $relatedProduct->display_rating > 0)
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $relatedProduct->display_rating)
                                                    <i class="fas fa-star" style="font-size: 12px; color: #ffc107;"></i>
                                                @elseif($i - $relatedProduct->display_rating < 1)
                                                    <i class="fas fa-star-half-alt" style="font-size: 12px; color: #ffc107;"></i>
                                                @else
                                                    <i class="far fa-star" style="font-size: 12px; color: #ddd;"></i>
                                                @endif
                                            @endfor
                                            <span style="font-size: 12px; color: var(--skc-medium-gray); margin-left: 5px;">
                                                ({{ $relatedProduct->reviews_count ?? 0 }})
                                            </span>
                                        @else
                                            <div style="height: 20px; display: flex; align-items: center; color: #999; font-size: 12px;">
                                                No reviews yet
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Price and Add to Cart -->
                                    <div style="display: flex; flex-direction: column; gap: 12px;">
                                        <!-- Price -->
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            @if($relatedProduct->has_variable_pricing ?? false)
                                                <span style="font-size: 14px; color: #666;">From</span>
                                            @endif
                                            <span style="font-size: 20px; font-weight: 700; color: var(--skc-orange);">
                                                ₹{{ number_format($relatedProduct->min_price ?? $relatedProduct->base_price ?? $relatedProduct->price, 0) }}
                                            </span>
                                            @if($relatedProduct->has_variable_pricing ?? false)
                                                <span style="font-size: 14px; color: #666;">onwards</span>
                                            @endif
                                        </div>

                                        <!-- Add to Cart Button -->
                                        <button onclick="quickAddToCart({{ $relatedProduct->id }})"
                                                style="width: 100%; padding: 12px 20px; background: var(--skc-orange); color: white; border: none; border-radius: 25px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 8px;"
                                                class="related-add-cart-btn">
                                            <i class="fas fa-shopping-cart"></i>
                                            <span>Add to Cart</span>
                                        </button>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>

<style>
/* Related Products Grid - Auto-sizing based on item count */
.related-products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 25px;
    margin: 0 auto !important;
    max-width: 1400px;
}

/* Responsive grid for consistent layout */
@media (min-width: 1400px) {
    .related-products-grid {
        grid-template-columns: repeat(5, 1fr);
        max-width: 1400px;
    }
}

@media (max-width: 1399px) and (min-width: 1200px) {
    .related-products-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

@media (max-width: 1199px) and (min-width: 900px) {
    .related-products-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 899px) and (min-width: 600px) {
    .related-products-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 599px) {
    .related-products-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
}

/* Auto-alignment logic for different numbers of items using :has() */
/* 1 item - center it */
.related-products-grid:has(.related-product-card:nth-child(1):nth-last-child(1)) {
    justify-items: center;
    max-width: 280px;
}

/* 2 items - center them */
.related-products-grid:has(.related-product-card:nth-child(2):nth-last-child(1)) {
    max-width: 600px;
    grid-template-columns: repeat(2, 1fr);
}

/* 3 items - center them */
.related-products-grid:has(.related-product-card:nth-child(3):nth-last-child(1)) {
    max-width: 900px;
    grid-template-columns: repeat(3, 1fr);
}

/* 4 items - center them */
.related-products-grid:has(.related-product-card:nth-child(4):nth-last-child(1)) {
    max-width: 1200px;
    grid-template-columns: repeat(4, 1fr);
}

/* 5 items - full width */
.related-products-grid:has(.related-product-card:nth-child(5):nth-last-child(1)) {
    max-width: 1400px;
    grid-template-columns: repeat(5, 1fr);
}

/* 6 items in 2 rows of 3 */
.related-products-grid:has(.related-product-card:nth-child(6):nth-last-child(1)) {
    max-width: 900px;
    grid-template-columns: repeat(3, 1fr);
}

/* 7 items - 4 on top, 3 on bottom */
.related-products-grid:has(.related-product-card:nth-child(7):nth-last-child(1)) {
    max-width: 1200px;
    grid-template-columns: repeat(4, 1fr);
}

/* 8 items in 2 rows of 4 */
.related-products-grid:has(.related-product-card:nth-child(8):nth-last-child(1)) {
    max-width: 1200px;
    grid-template-columns: repeat(4, 1fr);
}

/* 9 items in 2 rows - 5 on top, 4 on bottom */
.related-products-grid:has(.related-product-card:nth-child(9):nth-last-child(1)) {
    max-width: 1400px;
    grid-template-columns: repeat(5, 1fr);
}

/* 10 items in 2 rows of 5 */
.related-products-grid:has(.related-product-card:nth-child(10):nth-last-child(1)) {
    max-width: 1400px;
    grid-template-columns: repeat(5, 1fr);
}

/* Related Products Card Hover Effects */
.related-product-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.15) !important;
    border-color: var(--skc-orange) !important;
}

.related-product-card:hover .related-product-img {
    transform: scale(1.1);
}

.related-product-card:hover .related-product-overlay {
    opacity: 1 !important;
}

.related-product-card .related-product-name:hover {
    color: var(--skc-orange);
}

.related-add-cart-btn:hover {
    background: var(--skc-black) !important;
    transform: scale(1.02);
}

/* Mobile responsive adjustments */
@media (max-width: 768px) {
    /* Override auto-alignment on mobile for better display */
    .related-products-grid:has(.related-product-card:nth-child(3):nth-last-child(1)) {
        grid-template-columns: 1fr;
        max-width: 100%;
    }

    .related-products-grid:has(.related-product-card:nth-child(4):nth-last-child(1)) {
        grid-template-columns: repeat(2, 1fr);
        max-width: 100%;
    }

    .related-product-card div[style*="height: 240px"] {
        height: 200px !important;
    }

    .related-product-card div[style*="padding: 20px"] {
        padding: 15px !important;
    }
}

@media (max-width: 480px) {
    /* Single column on very small screens */
    .related-products-grid:has(.related-product-card:nth-child(2):nth-last-child(1)) {
        grid-template-columns: 1fr;
        max-width: 100%;
    }

    .related-products-grid:has(.related-product-card:nth-child(4):nth-last-child(1)) {
        grid-template-columns: 1fr;
        max-width: 100%;
    }
}

/* Sticky Buy Box: container-bound, responsive, and accessible */
:root { --sticky-offset: 24px; }

.pdp-sticky-boundary { position: relative; overflow: visible; }

@media (min-width: 1024px) {
    .buy-box-sticky { position: sticky; top: var(--sticky-offset); align-self: start; }
    .buy-box-inner { max-height: calc(100vh - var(--sticky-offset)); overflow: auto; overscroll-behavior: contain; -webkit-overflow-scrolling: touch; }
}

@media (max-width: 1023.98px) {
    .buy-box-sticky { position: static; }
    .buy-box-inner { max-height: none; overflow: visible; }
}

/* Respect reduced motion */
@media (prefers-reduced-motion: reduce) {
    .buy-box-inner { scroll-behavior: auto; }
}

/* Fallback states for legacy browsers without sticky */
.no-sticky .buy-box-sticky { position: static; }
.fallback-fixed .buy-box-sticky { position: fixed; top: var(--sticky-offset); width: var(--buy-box-width, auto); }
.fallback-at-bottom .buy-box-sticky { position: absolute; top: auto; bottom: 0; }

/* Ensure ancestors don't break sticky in Safari (visible overflow) */
.pdp-two-col { overflow: visible; }

/* Variant scroller: switches to horizontal scroll when items overflow or count >= 5 */
.variant-scroller { position: relative; }
.variant-scroller .variant-list { scroll-behavior: smooth; }
@media (min-width: 768px) {
    .variant-scroller.is-scrollable .variant-list {
        display: flex !important;
        flex-wrap: nowrap !important;
        gap: 10px !important;
        overflow-x: auto;
        padding-bottom: 6px;
        -webkit-overflow-scrolling: touch;
    }
}
.variant-scroller .variant-arrow { display: none; position: absolute; top: 50%; transform: translateY(-50%); z-index: 2; background: white; border: 1px solid #e0e0e0; width: 32px; height: 32px; border-radius: 16px; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
.variant-scroller .variant-arrow.prev { left: -6px; }
.variant-scroller .variant-arrow.next { right: -6px; }
.variant-scroller.is-scrollable .variant-arrow { display: inline-flex; align-items: center; justify-content: center; }
.variant-scroller .variant-fade { display: none; position: absolute; top: 0; bottom: 0; width: 32px; pointer-events: none; }
.variant-scroller .variant-fade.left { left: 0; background: linear-gradient(to right, rgba(255,255,255,1), rgba(255,255,255,0)); }
.variant-scroller .variant-fade.right { right: 0; background: linear-gradient(to left, rgba(255,255,255,1), rgba(255,255,255,0)); }
.variant-scroller.is-scrollable .variant-fade { display: block; }
</style>

<script>
// Image gallery
function changeImage(src) {
    document.getElementById('mainImage').src = src;
}

// Quantity controls
function incrementQty() {
    const input = document.getElementById('quantity');
    const current = parseInt(input.value);
    if (current < 10) input.value = current + 1;
}

function decrementQty() {
    const input = document.getElementById('quantity');
    const current = parseInt(input.value);
    if (current > 1) input.value = current - 1;
}

// Tab switching
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.style.display = 'none';
    });
    
    // Remove active state from all buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.style.color = 'var(--skc-medium-gray)';
        btn.style.borderBottomColor = 'transparent';
    });
    
    // Show selected tab
    document.getElementById(tabName + '-tab').style.display = 'block';
    
    // Add active state to clicked button
    event.target.style.color = 'var(--skc-orange)';
    event.target.style.borderBottomColor = 'var(--skc-orange)';
}

// Variant selection
document.addEventListener('DOMContentLoaded', function() {
    const variantRadios = document.querySelectorAll('.variant-radio');
    variantRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Remove active state from all options
            document.querySelectorAll('.variant-option').forEach(opt => {
                opt.style.borderColor = '#e0e0e0';
                opt.style.background = 'white';
            });
            
            // Add active state to selected option
            if (this.checked) {
                const option = this.nextElementSibling;
                option.style.borderColor = 'var(--skc-orange)';
                option.style.background = 'rgba(246, 157, 28, 0.05)';
            }
        });
    });
});

// Add to cart
function addToCart() {
    const quantity = document.getElementById('quantity').value;
    const productId = {{ $product->id }};
    
    // Check if variant is selected (if variants exist)
    const hasVariants = {{ $product->variants && $product->variants->count() > 0 ? 'true' : 'false' }};
    let selectedVariant = null;
    
    if (hasVariants) {
        const selectedRadio = document.querySelector('.variant-radio:checked');
        if (!selectedRadio) {
            alert('Please select a variant');
            return;
        }
        selectedVariant = selectedRadio.value;
    }
    
    // Here you would normally make an AJAX request to add to cart
    alert('Added to cart! Product ID: ' + productId + ', Quantity: ' + quantity + (selectedVariant ? ', Variant: ' + selectedVariant : ''));
}

// Hover effects
document.querySelectorAll('button').forEach(btn => {
    if (btn.style.background === 'var(--skc-black)' || btn.style.background === 'rgb(0, 0, 0)') {
        btn.addEventListener('mouseenter', function() {
            this.style.background = 'var(--skc-orange)';
        });
        btn.addEventListener('mouseleave', function() {
            this.style.background = 'var(--skc-black)';
        });
    }
});

// Thumbnail hover effect
document.querySelectorAll('[onclick^="changeImage"]').forEach(thumb => {
    thumb.addEventListener('mouseenter', function() {
        this.style.borderColor = 'var(--skc-orange)';
        this.style.transform = 'scale(1.05)';
    });
    thumb.addEventListener('mouseleave', function() {
        this.style.borderColor = '#e0e0e0';
        this.style.transform = 'scale(1)';
    });
});

// Quick Add to Cart for related products
function quickAddToCart(productId) {
    fetch('/cart/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success notification
            showNotification('Product added to cart successfully!', 'success');
            // Update cart count if available
            if (data.cart_count) {
                updateCartCount(data.cart_count);
            }
        } else {
            showNotification(data.message || 'Failed to add product to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred. Please try again.', 'error');
    });
}

// ===== Sticky Buy Box Utility =====
(function() {
    const MIN_DESKTOP_WIDTH = 1024;
    const root = document.documentElement;
    const boundary = document.querySelector('.pdp-sticky-boundary');
    const buyBox = document.querySelector('.buy-box-sticky');
    const rightCol = document.querySelector('.pdp-right');
    if (!boundary || !buyBox) return;

    function getHeaderHeight() {
        const candidates = [
            document.querySelector('[data-sticky-header]'),
            document.querySelector('header[role="banner"]'),
            document.querySelector('header.skc-header'),
            document.querySelector('header.site-header'),
            document.querySelector('header')
        ].filter(Boolean);
        let h = 0;
        for (const el of candidates) {
            const styles = window.getComputedStyle(el);
            if (['fixed', 'sticky'].includes(styles.position) || el.hasAttribute('data-sticky-header')) {
                h = Math.max(h, el.getBoundingClientRect().height);
            }
        }
        // Add a small breathing space (8px) and clamp to sensible range
        return Math.min(Math.max(h + 8, 16), 96);
    }

    function setOffsetVar() {
        const offset = getHeaderHeight();
        root.style.setProperty('--sticky-offset', offset + 'px');
    }

    function isDesktop() { return window.innerWidth >= MIN_DESKTOP_WIDTH; }

    function supportsSticky() {
        return CSS && (CSS.supports('position', 'sticky') || CSS.supports('position', '-webkit-sticky'));
    }

    // Throttle helper
    function throttle(fn, wait) {
        let last = 0, timer = null;
        return function() {
            const now = Date.now();
            const remaining = wait - (now - last);
            const context = this, args = arguments;
            if (remaining <= 0) {
                clearTimeout(timer); timer = null; last = now; fn.apply(context, args);
            } else if (!timer) {
                timer = setTimeout(function() { last = Date.now(); timer = null; fn.apply(context, args); }, remaining);
            }
        };
    }

    function updateFixedWidthVar() {
        const rect = buyBox.getBoundingClientRect();
        root.style.setProperty('--buy-box-width', rect.width + 'px');
    }

    // Fallback using IntersectionObserver for legacy browsers
    let ioTop = null, ioBottom = null;
    function setupFallback() {
        if (supportsSticky()) return; // native sticky present
        document.body.classList.add('no-sticky');
        const sentinelTop = document.createElement('div');
        sentinelTop.style.cssText = 'position:absolute; top:0; left:0; right:0; height:1px;';
        const sentinelBottom = document.querySelector('.pdp-boundary-end') || document.createElement('div');
        if (!sentinelBottom.parentElement) {
            sentinelBottom.className = 'pdp-boundary-end';
            boundary.appendChild(sentinelBottom);
        }
        boundary.appendChild(sentinelTop);

        updateFixedWidthVar();

        const options = { root: null, threshold: 0 };
        ioTop = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!isDesktop()) return;
                if (entry.isIntersecting) {
                    // Top reached — use fixed
                    document.body.classList.add('fallback-fixed');
                    document.body.classList.remove('fallback-at-bottom');
                }
            });
        }, options);
        ioTop.observe(sentinelTop);

        ioBottom = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (!isDesktop()) return;
                if (entry.isIntersecting) {
                    // Bottom reached — pin to bottom of boundary
                    document.body.classList.add('fallback-at-bottom');
                    document.body.classList.remove('fallback-fixed');
                } else {
                    document.body.classList.remove('fallback-at-bottom');
                }
            });
        }, options);
        ioBottom.observe(sentinelBottom);
    }

    function teardownFallback() {
        if (ioTop) { ioTop.disconnect(); ioTop = null; }
        if (ioBottom) { ioBottom.disconnect(); ioBottom = null; }
        document.body.classList.remove('fallback-fixed', 'fallback-at-bottom', 'no-sticky');
    }

    function applyResponsive() {
        if (isDesktop()) {
            setOffsetVar();
            if (!supportsSticky()) { setupFallback(); }
            else { teardownFallback(); }
        } else {
            teardownFallback();
        }
    }

    // Init
    setOffsetVar();
    applyResponsive();

    // Resize handling (throttled)
    const onResize = throttle(function() {
        setOffsetVar();
        updateFixedWidthVar();
        applyResponsive();
    }, 150);
    window.addEventListener('resize', onResize, { passive: true });
    window.addEventListener('orientationchange', onResize, { passive: true });
})();

// ===== Variant scroller initializer =====
(function() {
    const MIN_SCROLL_COUNT = 5;
    const scrollers = document.querySelectorAll('.variant-scroller');
    if (!scrollers.length) return;

    function shouldScroll(container) {
        const list = container.querySelector('.variant-list');
        if (!list) return false;
        const items = list.querySelectorAll('label');
        if (items.length >= MIN_SCROLL_COUNT) return true;
        return list.scrollWidth > list.clientWidth + 2; // overflow check
    }

    function updateScroller(container) {
        const list = container.querySelector('.variant-list');
        const prev = container.querySelector('.variant-arrow.prev');
        const next = container.querySelector('.variant-arrow.next');
        const leftFade = container.querySelector('.variant-fade.left');
        const rightFade = container.querySelector('.variant-fade.right');
        const enable = window.innerWidth >= 768 && shouldScroll(container);
        container.classList.toggle('is-scrollable', enable);
        if (!enable) return;

        function syncFades() {
            const atStart = list.scrollLeft <= 1;
            const atEnd = list.scrollLeft + list.clientWidth >= list.scrollWidth - 1;
            if (leftFade) leftFade.style.opacity = atStart ? '0' : '1';
            if (rightFade) rightFade.style.opacity = atEnd ? '0' : '1';
        }

        prev?.addEventListener('click', () => {
            list.scrollBy({ left: -Math.max(240, list.clientWidth * 0.6), behavior: 'smooth' });
        });
        next?.addEventListener('click', () => {
            list.scrollBy({ left: Math.max(240, list.clientWidth * 0.6), behavior: 'smooth' });
        });
        list.addEventListener('scroll', syncFades, { passive: true });
        syncFades();
    }

    function initAll() { scrollers.forEach(updateScroller); }

    // Throttle resize
    let rAF = null;
    window.addEventListener('resize', function() {
        if (rAF) return; rAF = requestAnimationFrame(function() { rAF = null; initAll(); });
    }, { passive: true });

    initAll();
})();

// Wishlist functionality
function toggleWishlist(productId) {
    fetch('/wishlist/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const btn = document.getElementById(`wishlist-btn-${productId}`);
            const icon = btn.querySelector('i');
            const text = btn.querySelector('span');
            
            if (data.message.includes('added')) {
                icon.className = 'fas fa-heart';
                text.textContent = 'Remove from Wishlist';
                btn.style.color = '#e74c3c';
                showToast('Added to wishlist!', 'success');
            } else {
                icon.className = 'far fa-heart';
                text.textContent = 'Add to Wishlist';
                btn.style.color = 'var(--skc-medium-gray)';
                showToast('Removed from wishlist!', 'success');
            }
            
            // Update wishlist count in header
            if (data.wishlist_count !== undefined) {
                updateWishlistCount(data.wishlist_count);
            }
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('An error occurred. Please try again.', 'error');
    });
}

// Notification helper function
function showNotification(message, type) {
    // Use existing showToast if available, otherwise create simple notification
    if (typeof showToast === 'function') {
        showToast(message, type);
    } else {
        // Fallback notification
        const notification = document.createElement('div');
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#28a745' : '#dc3545'};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            z-index: 10000;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease;
        `;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
}

// Update cart count helper
function updateCartCount(count) {
    const cartCountElements = document.querySelectorAll('.cart-count, .cart-badge, #cart-count');
    cartCountElements.forEach(element => {
        element.textContent = count;
    });
}

// Share functionality
function shareProduct() {
    const productName = '{{ $product->name }}';
    const productUrl = window.location.href;
    
    if (navigator.share) {
        // Use native share API if available
        navigator.share({
            title: productName,
            text: `Check out this amazing product: ${productName}`,
            url: productUrl
        }).then(() => {
            console.log('Product shared successfully');
        }).catch((error) => {
            console.log('Error sharing:', error);
            fallbackShare(productName, productUrl);
        });
    } else {
        fallbackShare(productName, productUrl);
    }
}

function fallbackShare(productName, productUrl) {
    // Create a modal for sharing options
    const modal = document.createElement('div');
    modal.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    `;
    
    const modalContent = document.createElement('div');
    modalContent.style.cssText = `
        background: white;
        padding: 30px;
        border-radius: 12px;
        max-width: 400px;
        width: 90%;
        text-align: center;
    `;
    
    modalContent.innerHTML = `
        <h3 style="margin: 0 0 20px 0; font-size: 24px; color: var(--skc-black);">Share Product</h3>
        <p style="margin: 0 0 30px 0; color: var(--skc-medium-gray);">Share "${productName}" with your friends</p>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(80px, 1fr)); gap: 15px; margin-bottom: 30px;">
            <a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(productUrl)}" target="_blank" style="display: flex; flex-direction: column; align-items: center; gap: 8px; text-decoration: none; color: #3b5998; padding: 15px; border-radius: 8px; transition: background 0.2s;">
                <i class="fab fa-facebook-f" style="font-size: 24px;"></i>
                <span style="font-size: 12px; font-weight: 600;">Facebook</span>
            </a>
            
            <a href="https://twitter.com/intent/tweet?text=Check out this amazing product: ${encodeURIComponent(productName)}&url=${encodeURIComponent(productUrl)}" target="_blank" style="display: flex; flex-direction: column; align-items: center; gap: 8px; text-decoration: none; color: #1da1f2; padding: 15px; border-radius: 8px; transition: background 0.2s;">
                <i class="fab fa-twitter" style="font-size: 24px;"></i>
                <span style="font-size: 12px; font-weight: 600;">Twitter</span>
            </a>
            
            <a href="https://wa.me/?text=Check out this amazing product: ${encodeURIComponent(productName)} ${encodeURIComponent(productUrl)}" target="_blank" style="display: flex; flex-direction: column; align-items: center; gap: 8px; text-decoration: none; color: #25d366; padding: 15px; border-radius: 8px; transition: background 0.2s;">
                <i class="fab fa-whatsapp" style="font-size: 24px;"></i>
                <span style="font-size: 12px; font-weight: 600;">WhatsApp</span>
            </a>
            
            <button onclick="copyToClipboard('${productUrl}')" style="display: flex; flex-direction: column; align-items: center; gap: 8px; background: none; border: none; color: var(--skc-orange); padding: 15px; border-radius: 8px; cursor: pointer; transition: background 0.2s;">
                <i class="fas fa-copy" style="font-size: 24px;"></i>
                <span style="font-size: 12px; font-weight: 600;">Copy Link</span>
            </button>
        </div>
        
        <button onclick="this.closest('.share-modal').remove()" style="background: var(--skc-black); color: white; border: none; padding: 12px 30px; border-radius: 8px; cursor: pointer; font-weight: 600;">
            Close
        </button>
    `;
    
    modal.className = 'share-modal';
    modal.appendChild(modalContent);
    document.body.appendChild(modal);
    
    // Close modal when clicking outside
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.remove();
        }
    });
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Link copied to clipboard!', 'success');
        document.querySelector('.share-modal').remove();
    }).catch(err => {
        console.error('Could not copy text: ', err);
        showToast('Failed to copy link', 'error');
    });
}

// Update wishlist count in header
function updateWishlistCount(count) {
    const wishlistCountElement = document.querySelector('.wishlist-count');
    if (wishlistCountElement) {
        wishlistCountElement.textContent = count;
        wishlistCountElement.style.display = count > 0 ? 'block' : 'none';
    }
}

// Check if product is in wishlist on page load
document.addEventListener('DOMContentLoaded', function() {
    fetch('/wishlist/count')
        .then(response => response.json())
        .then(data => {
            updateWishlistCount(data.count);
        });
});
</script>
</section>

<!-- Product FAQ Section -->
@if($product->faqs && $product->faqs->count() > 0)
    @include('components.product-faq', ['faqs' => $product->faqs->where('is_active', true)])
@endif

@endsection