<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class AdminSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'trakkiezstore@gmail.com'],
            [
                'name'              => 'Admin',
                'password'          => Hash::make('ahsan@3435'),
                'utype'             => 'ADM', 
                'email_verified_at' => now(),
                'remember_token'    => Str::random(10),
            ]
        );
    }
}
