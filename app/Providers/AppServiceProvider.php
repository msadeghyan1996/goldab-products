<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Category;
use App\Models\Product;
use App\Policies\AdminPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\ProductPolicy;
use App\Services\GoldPriceService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Admin::class, AdminPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Paginator::useBootstrapFive();

        View::composer('layouts.storefront', function ($view): void {
            $view->with('headerCategories', Category::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name']));
            $view->with('footerCategories', Category::query()
                ->where('is_active', true)
                ->whereHas('products')
                ->orderBy('name')
                ->get(['id', 'name']));
            $view->with('headerGoldRate', app(GoldPriceService::class)->current());
        });
    }
}
