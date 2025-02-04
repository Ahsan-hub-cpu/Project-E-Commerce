<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Sizes;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $productsQuery = Product::query();
        
        // Handle additional filters
        if ($request->query('filter') === 'featured') {
            $productsQuery->where('featured', true);
        } elseif ($request->query('filter') === 'new-arrivals') {
            $productsQuery->where('created_at', '>=', now()->subDays(30));
        } elseif ($request->query('filter') === 'hot-deals') {
            $productsQuery->whereNotNull('sale_price');
        }

        if ($request->has('subcategory')) {
            $productsQuery->whereHas('subCategory', function ($query) use ($request) {
                $query->where('id', $request->subcategory);
            });
        }

        if ($request->has('size')) {
            $productsQuery->whereHas('sizes', function($q) use ($request) {
                $q->where('sizes.id', $request->size);
            });
        }

        // Price filtering using price_from and price_to (from modal)
        if ($request->has('price_from') || $request->has('price_to')) {
            $priceFrom = $request->get('price_from', 0);
            $priceTo = $request->get('price_to', null);

            // If price_to is not provided, default it to the dynamic highest price
            if (!$priceTo) {
                $maxRegular = Product::max('regular_price');
                $maxSale = Product::whereNotNull('sale_price')->max('sale_price');
                $priceTo = max($maxRegular, $maxSale);
            }

            // Use IFNULL to compare sale_price (if exists) or regular_price otherwise
            $productsQuery->whereRaw("IFNULL(sale_price, regular_price) BETWEEN ? AND ?", [$priceFrom, $priceTo]);
        } elseif ($request->has('price')) {
            // Fallback if using the old price filter format (e.g., "0-50")
            $priceRange = explode('-', $request->price);
            $productsQuery->whereBetween('regular_price', [$priceRange[0], $priceRange[1]]);
        }

        // Sorting
        if ($request->has('sort')) {
            switch ($request->sort) {
                case 'newest':
                    $productsQuery->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $productsQuery->orderBy('created_at', 'asc');
                    break;
                case 'a-z':
                    $productsQuery->orderBy('name', 'asc');
                    break;
                case 'z-a':
                    $productsQuery->orderBy('name', 'desc');
                    break;
                case 'price-low-high':
                    $productsQuery->orderBy('regular_price', 'asc');
                    break;
                case 'price-high-low':
                    $productsQuery->orderBy('regular_price', 'desc');
                    break;
                default:
                    $productsQuery->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $productsQuery->orderBy('created_at', 'desc');
        }

        // Get categories and sizes for filters
        $categories = Category::with('subCategories')->get();
        $sizes = Sizes::all();

        // Compute the dynamic highest price among all products:
        $maxRegular = Product::max('regular_price');
        $maxSale = Product::whereNotNull('sale_price')->max('sale_price');
        $maxPrice = max($maxRegular, $maxSale);

        // If any filters are applied, get all matching products; otherwise, paginate
        if (
            $request->has('size') ||
            $request->has('price') ||
            $request->has('price_from') ||
            $request->has('price_to') ||
            $request->has('sort') ||
            $request->has('subcategory')
        ) {
            $products = $productsQuery->get();
        } else {
            $products = $productsQuery->paginate(12);
        }
        return view('shop', compact('products', 'categories', 'sizes', 'maxPrice'));
    }

    public function product_details($product_slug)
    {
        $product = Product::where("slug", $product_slug)->first();
        $rproducts = Product::where("slug", "<>", $product_slug)->take(8)->get();
        return view('details', compact("product", "rproducts"));
    }
}
