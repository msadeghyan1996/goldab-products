<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $stats = [
            'products' => Product::count(),
            'categories' => Category::count(),
            'admins' => Admin::count(),
            'available' => Product::where('availability', Product::AVAILABLE)->count(),
            'unavailable' => Product::where('availability', Product::UNAVAILABLE)->count(),
        ];
        $latestProducts = Product::with('category')->latest()->limit(8)->get();

        return view('dashboard', compact('stats', 'latestProducts'));
    }
}
