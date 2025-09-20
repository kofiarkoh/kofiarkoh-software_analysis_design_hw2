<?php

namespace App\View\Components;

use App\Models\Vendor\Category;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CategoryView extends Component
{

    public string $viewType;


    /**
     * Create a new component instance.
     */
    public function __construct($viewType = 'desktop')
    {
        $this->viewType = $viewType;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {

        $payload = [
            'categories' =>  Category::take(9)->get(),
        ];

        if ($this->viewType === 'mobile') {
            return view('components.category-view-mobile', $payload);
        }
        return view('components.category-view', $payload );
    }
}
