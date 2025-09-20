<?php

namespace App\Models\Admin;

use App\Models\Shop;
use Chiiya\FilamentAccessControl\Models\FilamentUser;
use Database\Factories\UserFactory;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Models\Contracts\HasTenants;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

class Admin extends FilamentUser
{


    protected $table = 'filament_users';


    public function canAccessPanel(Panel $panel): bool
    {

        return true;
        if ($panel->getId() === 'admin') {
            $user = auth('filament')->user();
            return $user && $user->hasRole('super-admin');

        }

        return false;
    }

}
