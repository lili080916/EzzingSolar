<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userData = [
            'name' => 'Liliana',
            'surname' => 'Juez',
            'email' => 'lili080916@gmail.com',
            'password' => Hash::make('123123'),
            'birthday' => '1988-02-24'
        ];

        User::create($userData);

        \App\Models\User::factory()->count(5)->create();

    }
}
