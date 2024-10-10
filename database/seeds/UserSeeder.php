<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'root',
                'email' => 'sidyrich@gmail.com',
                'msisdn' => '263774935699',
                'last_name' => 'system',
                'password' => Hash::make('password'),
                'status' => true,
                'access_level' => 'admin',
                'created_at' => '2022-09-07 14:56:32',
            ],
//            [
//                'name' => 'ordinary',
//                'email' => 'ordinary@system',
//                'last_name' => 'system',
//                'password' => Hash::make('password'),
//                'status' => true,
//                'access_level' => 'ordinary',
//                'created_at' => '2022-09-07 14:56:32',
//            ],
        ];
        User::insert($users);

    }
}
