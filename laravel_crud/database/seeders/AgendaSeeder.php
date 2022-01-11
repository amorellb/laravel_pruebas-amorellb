<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AgendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $agenda = [
            ['firstname' => 'Bernat', 'lastname' => 'Smith', 'contact_number' => '123456789'],
            ['firstname' => 'Margalida', 'lastname' => 'Johnson', 'contact_number' => '987654321'],
            ['firstname' => 'Miquel', 'lastname' => 'Jackson', 'contact_number' => '123432123'],
        ];

        DB::table('agenda')->insert($agenda);
    }
}
