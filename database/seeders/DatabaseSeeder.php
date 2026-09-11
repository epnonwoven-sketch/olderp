<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\UserTypeMaster::factory()->create([
            'user_type' => 'Supper Admin'
        ]);
        

        \App\Models\User::factory()->create([
            'name' => 'Supper Admin',
            'email' => 'evergreen@gmail.com',
            "password"=> Hash::make(12345),
            'user_type_id' => '1',
        ]);
    }
}
