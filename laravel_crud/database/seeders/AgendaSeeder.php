<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            ['name' => 'Bernat Smith', 'slug' => Str::slug('Bernat Smith', '-'), 'email' => 'bernat@email.com', 'phone' => '123456789', 'address' => 'Calle 123'],
            ['name' => 'Margalida Johnson', 'slug' => Str::slug('Margalida Johnson', '-'), 'email;' => 'mjohnson@email.com', 'phone' => '987654321', 'address' => 'Calle calle 321'],
            ['name' => 'Miquel Jackson', 'slug' => Str::slug('Miquel Jackson', '-'), 'email' => 'mjackson@email.com', 'phone' => '123432123', 'address' => 'calle 123, street'],
        ];

        DB::table('agenda')->insert($agenda);
    }
}
