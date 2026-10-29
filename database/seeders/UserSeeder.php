<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Demo accounts. Admins and employees use the web dashboard; customers use the API
     * (mobile/web client). Every demo account uses the password "password".
     */
    public function run(): void
    {
        $users = [
            ['Admin User', 'admin@demo.com', 'admin'],
            ['Lina Haddad', 'employee@demo.com', 'employee'],
            ['Omar Khalil', 'omar.employee@demo.com', 'employee'],
            ['Sara Nasser', 'customer@demo.com', 'customer'],
            ['Rami Saleh', 'rami@demo.com', 'customer'],
            ['Maya Darwish', 'maya@demo.com', 'customer'],
            ['Karim Aziz', 'karim@demo.com', 'customer'],
            ['Nour Hamdan', 'nour@demo.com', 'customer'],
            ['Yara Mansour', 'yara@demo.com', 'customer'],
            ['Fadi Ibrahim', 'fadi@demo.com', 'customer'],
            ['Dana Youssef', 'dana@demo.com', 'customer'],
        ];

        foreach ($users as [$name, $email, $role]) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make('password'),
                'is_active' => true,
            ]);
            $user->forceFill(['email_verified_at' => now()])->save();
            $user->assignRole($role);
        }
    }
}
