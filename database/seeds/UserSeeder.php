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
                'email' => 'root@system',
                'last_name' => 'system',
                'password' => Hash::make('password'),
                'status' => true,
                'created_at' => '2022-09-07 14:56:32',
            ],
        ];
        User::insert($users);

    }
}
