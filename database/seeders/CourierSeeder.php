<?php

namespace Database\Seeders;

use App\Models\Courier;
use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    public function run(): void
    {
        Courier::factory(25)->create();

        Courier::factory()->create([
            'nama' => 'Budiono Hadi Agung',
            'level' => 2,
        ]);
    }
}
