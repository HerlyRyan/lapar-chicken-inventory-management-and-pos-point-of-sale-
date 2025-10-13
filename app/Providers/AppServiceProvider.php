<?php

namespace App\Providers;

use App\Models\RawMaterial;
use App\Models\Supplier;
// Observer files don't exist, so removing these imports
// use App\Observers\RawMaterialObserver;
// use App\Observers\SupplierObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

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
        // Use Bootstrap 5 pagination styling
        Paginator::defaultView('pagination::bootstrap-5');
        Paginator::defaultSimpleView('pagination::simple-bootstrap-5');
        
        // Register view composers
        \Illuminate\Support\Facades\View::composer('layouts.app', \App\Http\View\Composers\BranchComposer::class);
        
        // Observer files don't exist yet, so commenting out
        // Register observers for real-time updates
        // RawMaterial::observe(RawMaterialObserver::class);
        // Supplier::observe(SupplierObserver::class);
    }
}
