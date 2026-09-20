<?php

namespace App\View\Components;

use App\Models\Category;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class CategoryNav extends Component
{
    public $categories;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $data = Cache::remember('nav_categories', 3600, function () {
            return Category::select('id', 'title', 'slug')
                ->orderBy('title')
                ->get()
                ->map(function ($category) {
                    return [
                        'id' => $category->id,
                        'title' => $category->title,
                        'slug' => $category->slug,
                    ];
                })
                ->all();
        });

        $data = collect($data)
            ->map(fn ($category) => (object) $category);

        $chunkSize = max(1, (int) ceil($data->count() / 3));

        $this->categories = $data->chunk($chunkSize);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.category-nav');
    }
}
