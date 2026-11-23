<?php

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

it('tetap cepat mengambil 15 dari 10.000 data tanpa select *', function () {
    $baris = [];

    for ($nomor = 1; $nomor <= 10_000; $nomor++) {
        $baris[] = [
            'id' => (string) Str::uuid(),
            'nama' => 'Kurir '.$nomor,
            'email' => "kurir{$nomor}@contoh.test",
            'telepon' => '0812'.str_pad((string) $nomor, 8, '0', STR_PAD_LEFT),
            'level' => ($nomor % 5) + 1,
            'jenis_kendaraan' => 'motor',
            'plat_nomor' => null,
            'aktif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    foreach (array_chunk($baris, 100) as $potongan) {
        DB::table('couriers')->insert($potongan);
    }

    $sqlDicari = [];
    DB::listen(function (QueryExecuted $query) use (&$sqlDicari) {
        $sqlDicari[] = $query->sql;
    });

    $mulai = microtime(true);
    $respons = $this->getJson('/api/couriers?per_page=15');
    $lamanya = microtime(true) - $mulai;

    $respons->assertOk()
        ->assertJsonCount(15, 'data')
        ->assertJsonPath('meta.total', 10000);

    $selectStar = array_filter($sqlDicari, fn (string $sql) => str_contains(strtolower($sql), 'select *'));
    $terbatas = array_filter($sqlDicari, fn (string $sql) => str_contains($sql, 'limit 15'));

    expect($selectStar)->toBeEmpty();
    expect($terbatas)->not->toBeEmpty();
    expect($lamanya)->toBeLessThan(5.0);
});
