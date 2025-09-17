<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)
            ->withCount(['products as products_count' => function($q){ 
                $q->where('is_active', true); 
            }])
            ->firstOrFail();
            
        $query = Product::where('category_id', $category->id)
            ->where('is_active', true)
            ->withSum('variants as total_stock', 'stock');

        // Price filter
        $min = request('min');
        $max = request('max');
        if ($min !== null) $query->byPriceRange($min, null);
        if ($max !== null) $query->byPriceRange(null, $max);

        // Sort options
        $sort = request('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $query->leftJoin('product_variants as pv1', 'products.id', '=', 'pv1.product_id')
                      ->selectRaw('products.*, MIN(pv1.price) as min_variant_price')
                      ->groupBy('products.id')
                      ->orderBy('min_variant_price', 'asc');
                break;
            case 'price_desc':
                $query->leftJoin('product_variants as pv2', 'products.id', '=', 'pv2.product_id')
                      ->selectRaw('products.*, MIN(pv2.price) as min_variant_price')
                      ->groupBy('products.id')
                      ->orderBy('min_variant_price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();
        return view('web.category', compact('category', 'products'));
    }



    public function products(Request $request)
    {
        $query = Product::where('is_active', true)
            ->with('category')
            ->withSum('variants as total_stock', 'stock');

        // Search query
        if ($request->filled('q')) {
            $searchTerm = $request->input('q');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhere('description', 'LIKE', '%' . $searchTerm . '%')
                  ->orWhereHas('category', function($cq) use ($searchTerm) {
                      $cq->where('name', 'LIKE', '%' . $searchTerm . '%');
                  });
            });
        }

        // Category filter (by id or slug)
        if ($request->filled('category')) {
            $category = $request->input('category');
            $query->when(is_numeric($category), function($q) use ($category){
                $q->where('category_id', (int) $category);
            }, function($q) use ($category){
                $q->whereHas('category', function($cq) use ($category){
                    $cq->where('slug', $category);
                });
            });
        }

        // Price range filter using variants
        if ($request->filled('min')) {
            $query->whereHas('variants', function($q) use ($request) {
                $q->where('price', '>=', (float) $request->input('min'))
                  ->where('is_active', true);
            });
        }
        if ($request->filled('max')) {
            $query->whereHas('variants', function($q) use ($request) {
                $q->where('price', '<=', (float) $request->input('max'))
                  ->where('is_active', true);
            });
        }

        // Stock filter
        if ($request->filled('in_stock') && $request->input('in_stock')) {
            $query->whereHas('variants', function($q) {
                $q->where('stock_quantity', '>', 0)
                  ->where('is_active', true);
            });
        }

        // Sort
        $sort = $request->input('sort');
        switch ($sort) {
            case 'price_asc':
                $query->orderBy(
                    \App\Models\ProductVariant::select('price')
                        ->whereColumn('product_variants.product_id', 'products.id')
                        ->where('is_active', true)
                        ->orderBy('price', 'asc')
                        ->limit(1),
                    'asc'
                );
                break;
            case 'price_desc':
                $query->orderBy(
                    \App\Models\ProductVariant::select('price')
                        ->whereColumn('product_variants.product_id', 'products.id')
                        ->where('is_active', true)
                        ->orderBy('price', 'desc')
                        ->limit(1),
                    'desc'
                );
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'rating_desc':
                $query->orderBy('rating', 'desc');
                break;
            case 'rating_asc':
                $query->orderBy('rating', 'asc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get(['id','name','slug']);

        // Count active filters for mobile display
        $activeFiltersCount = 0;
        if ($request->filled('category')) $activeFiltersCount++;
        if ($request->filled('min') || $request->filled('max')) $activeFiltersCount++;
        if ($request->filled('sort') && $sort != '') $activeFiltersCount++;

        // Check if this is an AJAX request for infinite scroll
        if ($request->ajax()) {
            $html = '';
            foreach ($products as $product) {
                $html .= view('web.partials.product-card', compact('product'))->render();
            }

            return response()->json([
                'html' => $html,
                'hasMore' => $products->hasMorePages(),
                'nextPage' => $products->currentPage() + 1,
            ]);
        }

        return view('web.products', [
            'products' => $products,
            'categories' => $categories,
            'activeCategory' => $request->input('category'),
            'min' => $request->input('min'),
            'max' => $request->input('max'),
            'sort' => $sort,
            'inStock' => $request->input('in_stock', false),
            'activeFiltersCount' => $activeFiltersCount,
            'searchQuery' => $request->input('q'),
        ]);
    }

    // AJAX search for autocomplete
    public function ajaxSearch(Request $request)
    {
        $q = $request->get('q', '');
        
        if (strlen($q) < 2) {
            return response()->json(['products' => []]);
        }

        $products = Product::where('is_active', true)
            ->fuzzySearch($q)
            ->with('category')
            ->select('id', 'name', 'slug', 'images_path')
            ->take(8)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'price' => $product->min_price,
                    'image' => $product->first_image ? asset('storage/' . $product->first_image) : null,
                    'category' => $product->category->name ?? '',
                    'url' => route('product.show', $product->slug)
                ];
            });

        return response()->json(['products' => $products]);
    }

    // Mobile search page
    public function mobileSearch(Request $request)
    {
        $categories = Category::orderBy('name')->get(['id','name','slug','image']);
        $recentSearches = session('recent_searches', []);
        $popularSearches = [
            'chocolate cake',
            'bread',
            'cookies',
            'pastries',
            'cupcakes',
            'muffins',
            'croissants',
            'donuts'
        ];

        return view('web.search.mobile', [
            'categories' => $categories,
            'recentSearches' => $recentSearches,
            'popularSearches' => $popularSearches,
            'searchQuery' => $request->input('q', ''),
        ]);
    }

    public function allCategories()
    {
        $categories = Category::withCount(['products as products_count' => function($query) {
            $query->where('is_active', true);
        }])
        ->orderBy('name')
        ->get();

        return view('web.categories', compact('categories'));
    }

    public function categoryProducts(Category $category, $sort = null, $min = null, $max = null)
    {
        $request = request();

        // Build the query for products in this category
        $query = Product::where('is_active', true)
            ->where('category_id', $category->id)
            ->with('category')
            ->withSum('variants as total_stock', 'stock');

        // Apply price filter from URL parameters
        if ($min !== null) {
            $query->byPriceRange((float) $min, null);
            $request->merge(['min' => $min]);
        }
        if ($max !== null) {
            $query->byPriceRange(null, (float) $max);
            $request->merge(['max' => $max]);
        }

        // Apply additional filters from query parameters
        if ($request->filled('min') && !$min) {
            $query->byPriceRange((float) $request->input('min'), null);
        }
        if ($request->filled('max') && !$max) {
            $query->byPriceRange(null, (float) $request->input('max'));
        }

        // Stock filter
        if ($request->filled('in_stock') && $request->input('in_stock')) {
            $query->whereHas('variants', function($q) {
                $q->where('stock_quantity', '>', 0)
                  ->where('is_active', true);
            });
        }

        // Apply sorting from URL parameter or query string
        $sortOption = $sort ?? $request->input('sort', 'newest');
        switch ($sortOption) {
            case 'price_asc':
                $query->leftJoin('product_variants as pv3', 'products.id', '=', 'pv3.product_id')
                      ->selectRaw('products.*, MIN(pv3.price) as min_variant_price')
                      ->groupBy('products.id')
                      ->orderBy('min_variant_price', 'asc');
                break;
            case 'price_desc':
                $query->leftJoin('product_variants as pv4', 'products.id', '=', 'pv4.product_id')
                      ->selectRaw('products.*, MIN(pv4.price) as min_variant_price')
                      ->groupBy('products.id')
                      ->orderBy('min_variant_price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'rating_desc':
                $query->orderBy('rating', 'desc');
                break;
            case 'rating_asc':
                $query->orderBy('rating', 'asc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get(['id','name','slug']);

        // Count active filters for mobile display
        $activeFiltersCount = 1; // Category is always active
        if ($min || $max || $request->filled('min') || $request->filled('max')) $activeFiltersCount++;
        if ($sortOption && $sortOption != 'newest') $activeFiltersCount++;

        // Check if this is an AJAX request for infinite scroll
        if ($request->ajax()) {
            $html = '';
            foreach ($products as $product) {
                $html .= view('web.partials.product-card', compact('product'))->render();
            }

            return response()->json([
                'html' => $html,
                'hasMore' => $products->hasMorePages(),
                'nextPage' => $products->currentPage() + 1,
            ]);
        }

        // Merge route parameters into request for view
        $request->merge([
            'category' => $category->slug,
            'sort' => $sortOption,
            'min' => $min ?? $request->input('min'),
            'max' => $max ?? $request->input('max'),
        ]);

        return view('web.products', [
            'products' => $products,
            'categories' => $categories,
            'activeCategory' => $category->slug,
            'categoryName' => $category->name,
            'min' => $min ?? $request->input('min'),
            'max' => $max ?? $request->input('max'),
            'sort' => $sortOption,
            'inStock' => $request->input('in_stock', false),
            'activeFiltersCount' => $activeFiltersCount,
            'searchQuery' => '',
        ]);
    }

    public function searchProducts($query, Category $category = null)
    {
        $request = request();

        // Build the search query
        $productQuery = Product::where('is_active', true)
            ->with('category')
            ->withSum('variants as total_stock', 'stock');

        // Apply search filters
        $productQuery->where(function($q) use ($query) {
            $q->where('name', 'LIKE', '%' . $query . '%')
              ->orWhere('description', 'LIKE', '%' . $query . '%')
              ->orWhereHas('category', function($cq) use ($query) {
                  $cq->where('name', 'LIKE', '%' . $query . '%');
              });
        });

        // Apply category filter if provided
        if ($category) {
            $productQuery->where('category_id', $category->id);
        }

        // Apply additional filters from query parameters
        if ($request->filled('min')) {
            $productQuery->byPriceRange((float) $request->input('min'), null);
        }
        if ($request->filled('max')) {
            $productQuery->byPriceRange(null, (float) $request->input('max'));
        }
        if ($request->filled('in_stock') && $request->input('in_stock')) {
            $productQuery->whereHas('variants', function($q) {
                $q->where('stock_quantity', '>', 0)
                  ->where('is_active', true);
            });
        }

        // Sort
        $sort = $request->input('sort', 'newest');
        switch ($sort) {
            case 'price_asc':
                $productQuery->leftJoin('product_variants as pv5', 'products.id', '=', 'pv5.product_id')
                              ->selectRaw('products.*, MIN(pv5.price) as min_variant_price')
                              ->groupBy('products.id')
                              ->orderBy('min_variant_price', 'asc');
                break;
            case 'price_desc':
                $productQuery->leftJoin('product_variants as pv6', 'products.id', '=', 'pv6.product_id')
                              ->selectRaw('products.*, MIN(pv6.price) as min_variant_price')
                              ->groupBy('products.id')
                              ->orderBy('min_variant_price', 'desc');
                break;
            case 'name_asc':
                $productQuery->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $productQuery->orderBy('name', 'desc');
                break;
            case 'rating_desc':
                $productQuery->orderBy('rating', 'desc');
                break;
            case 'rating_asc':
                $productQuery->orderBy('rating', 'asc');
                break;
            case 'newest':
                $productQuery->orderBy('created_at', 'desc');
                break;
            default:
                $productQuery->latest();
                break;
        }

        $products = $productQuery->paginate(12)->withQueryString();
        $categories = Category::orderBy('name')->get(['id','name','slug']);

        // Count active filters
        $activeFiltersCount = 1; // Search is always active
        if ($category) $activeFiltersCount++;
        if ($request->filled('min') || $request->filled('max')) $activeFiltersCount++;
        if ($sort && $sort != 'newest') $activeFiltersCount++;

        // Check if this is an AJAX request for infinite scroll
        if ($request->ajax()) {
            $html = '';
            foreach ($products as $product) {
                $html .= view('web.partials.product-card', compact('product'))->render();
            }

            return response()->json([
                'html' => $html,
                'hasMore' => $products->hasMorePages(),
                'nextPage' => $products->currentPage() + 1,
            ]);
        }

        return view('web.products', [
            'products' => $products,
            'categories' => $categories,
            'activeCategory' => $category?->slug,
            'categoryName' => $category?->name,
            'min' => $request->input('min'),
            'max' => $request->input('max'),
            'sort' => $sort,
            'inStock' => $request->input('in_stock', false),
            'activeFiltersCount' => $activeFiltersCount,
            'searchQuery' => $query,
        ]);
    }
}