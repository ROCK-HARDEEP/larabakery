<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\CustomSection;
use Illuminate\Support\Facades\View;

class CustomSectionServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function ($view) {
            $customSections = CustomSection::where('is_active', true)
                ->orderBy('position')
                ->get()
                ->groupBy('page');
            
            $view->with('customSections', $customSections);
        });
    }

    public function register()
    {
        //
    }
}