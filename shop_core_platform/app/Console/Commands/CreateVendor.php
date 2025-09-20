<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vendor;
use Illuminate\Support\Facades\Hash;

class CreateVendor extends Command
{
    protected $signature = 'vendor:create {--first_name=} {--last_name=} {--email=} {--password=}';
    protected $description = 'Create a new vendor user';

    public function handle(): void
    {
        $password = $this->option('password') ?? 'password';

        $vendor = Vendor\Vendor::create([
            'first_name' => $this->option('first_name'),
            'last_name' => $this->option('last_name'),
            'email' => $this->option('email'),
            'password' => Hash::make($password),
        ]);

        $this->info("Vendor {$vendor->email} created successfully.");
    }
}
