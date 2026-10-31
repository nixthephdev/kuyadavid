<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/** One admin account from .env (ADMIN_NAME, ADMIN_EMAIL, ADMIN_PASSWORD). */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = config('site.admin');

        if (blank($admin['email']) || blank($admin['password'])) {
            $this->command?->warn('ADMIN_EMAIL or ADMIN_PASSWORD is empty in .env: no admin account created.');

            return;
        }

        User::updateOrCreate(
            ['email' => $admin['email']],
            ['name' => $admin['name'], 'password' => $admin['password']],
        );

        $this->command?->info("Admin account ready: {$admin['email']}");
    }
}
