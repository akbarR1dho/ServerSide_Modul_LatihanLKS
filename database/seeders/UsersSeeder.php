<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'User1',
            'email' => 'user1@webtech.id',
            'password' => 'password1',
        ]);

        User::create([
            'name' => 'User2',
            'email' => 'user2@webtech.id',
            'password' => 'password2',
        ]);

        User::create([
            'name' => 'User3',
            'email' => 'user3@webtech.id',
            'password' => 'password3',
        ]);
    }
}
