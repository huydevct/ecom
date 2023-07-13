<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($k = 1; $k <= 5; $k++) {
            $a = 10000;
            for ($j = 1; $j <= 100; $j++) {
                $users = [];
                for ($i = $a * $j; $i <= $a * $j + 9000; $i++) {
                    $user = [
                        'first_name' => $i . "a",
                        'last_name' => $i . "b",
                        'address' => $i . "c",
                        'phone_number' => $i . "d",
                        'email' => $i . $k."@gmail.com",
                        'password' => $i,
                        'created_at' => now(),
                    ];
                    array_push($users, $user);
                }
                User::insert($users);
            }
        }
    }
}
