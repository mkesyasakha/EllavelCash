<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'nama kalian(sano)',
            'email' => 'sano@gmail.com',
            'phone' => '089166281628',
            'password' => '12345678',
        ])->assignRole('customers');
        User::create([
            'name' => 'admin',
            'email' => 'admin@gmail.com',
            'phone' => '089166281625',
            'password' => '12345678',
        ])->assignRole('admin');
    }
}
