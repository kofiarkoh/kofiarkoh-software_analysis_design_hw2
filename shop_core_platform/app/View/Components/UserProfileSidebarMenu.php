<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UserProfileSidebarMenu extends Component
{
    public string $active;

    public function __construct(string $active = '')
    {
        $this->active = $active;
    }

    public function render()
    {
        return view('components.user-profile-sidebar-menu');
    }
}
