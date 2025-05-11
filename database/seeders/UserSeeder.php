<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        User::create([
            'name' => 'Admin User', 
            'nip' => '1111111111', 
            'nip_verified_at' => now(),
            'email' => 'admin@example.com', 
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

      
        foreach (range(1, 10) as $i) {
            User::create([
                'name' => $faker->name,
                'nip' => $faker->unique()->numerify('##########'),
                'nip_verified_at' => now(),
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('12345'), 
                // 'role' => 'nakes',
            ]);
        }
    }
}
