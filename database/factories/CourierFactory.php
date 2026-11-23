<?php

namespace Database\Factories;

use App\Models\Courier;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourierFactory extends Factory
{
    protected $model = Courier::class;

    public function definition(): array
    {
        return [
            'nama' => $this->faker->unique()->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'telepon' => $this->faker->unique()->numerify('0812###-####'),
            'level' => $this->faker->numberBetween(1, 5),
            'jenis_kendaraan' => $this->faker->randomElement(['motor', 'mobil', 'van', 'truk']),
            'plat_nomor' => $this->faker->optional()->numerify('B-###'),
            'aktif' => $this->faker->boolean(90),
        ];
    }

    public function tidakAktif(): static
    {
        return $this->state(fn (array $atribut) => [
            'aktif' => false,
        ]);
    }
}
