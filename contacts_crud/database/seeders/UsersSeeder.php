<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contacts = [
            ['name' => 'Bernat', 'email' => 'bernat@email.com',  'password' => '12345678', 'role' => 'admin'],
            ['name' => 'Margalida', 'email' => 'margalida@email.com',  'password' => '12345678', 'role' => 'user'],
            ['name' => 'Miquel', 'email' => 'miquel@email.com',  'password' => '12345678', 'role' => 'visitor'],
        ];

        DB::table('users')->insert($contacts);
    }
}
