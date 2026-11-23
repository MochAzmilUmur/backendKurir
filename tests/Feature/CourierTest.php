<?php

use App\Models\Courier;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Courier', function () {
    beforeEach(function () {
        Courier::factory()->count(5)->create(['level' => 1]);
    });

    it('returns standard pagination structure', function () {
        $response = $this->getJson('/api/couriers?per_page=10');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [[
                    'id', 'nama', 'email', 'telepon', 'level',
                    'jenis_kendaraan', 'plat_nomor', 'aktif', 'created_at', 'updated_at',
                ]],
                'links', 'meta',
            ]);
    });

    it('sorts by name asc by default', function () {
        $response = $this->getJson('/api/couriers?per_page=100');

        $response->assertStatus(200);

        $nama = $response->json('data.*.nama');
        $diurutkan = $nama;
        sort($diurutkan);
        expect($nama)->toBe($diurutkan);
    });

    it('sorts by created_at desc when overridden', function () {
        $response = $this->getJson('/api/couriers?per_page=100&sort_by=created_at&sort_dir=desc');

        $response->assertStatus(200);

        $tanggal = $response->json('data.*.created_at');
        $diurutkan = $tanggal;
        rsort($diurutkan);
        expect($tanggal)->toBe($diurutkan);
    });

    it('finds courier by multi-word search', function () {
        Courier::factory()->create(['nama' => 'Budiono Hadi Agung', 'level' => 1]);
        Courier::factory()->create(['nama' => 'Jane Doe', 'level' => 1]);

        $response = $this->getJson('/api/couriers?search=budi+agung&per_page=100');

        $response->assertStatus(200);
        expect($response->json('data'))->toHaveCount(1);
        expect($response->json('data.0.nama'))->toBe('Budiono Hadi Agung');
    });

    it('filters couriers by level', function () {
        Courier::factory()->create(['level' => 2]);
        Courier::factory()->create(['level' => 3]);

        $response = $this->getJson('/api/couriers?level=2,3&per_page=100');

        $response->assertStatus(200);
        $level = collect($response->json('data.*.level'))->unique()->sort()->values()->toArray();
        expect($level)->toEqual([2, 3]);
    });

    it('returns 200 when showing an existing courier', function () {
        $dataKurir = Courier::factory()->create();

        $this->getJson('/api/couriers/'.$dataKurir->id)
            ->assertStatus(200)
            ->assertJsonPath('data.id', $dataKurir->id);
    });

    it('returns 404 when showing a non-existing courier', function () {
        $this->getJson('/api/couriers/'.fake()->uuid())
            ->assertStatus(404)
            ->assertJsonPath('message', 'Record not found.');
    });

    it('stores a new courier', function () {
        $data = [
            'nama' => 'Budiarto Wicaksono',
            'email' => 'budi@example.com',
            'telepon' => '081234567890',
            'level' => 2,
            'jenis_kendaraan' => 'mobil',
            'plat_nomor' => 'B-123',
            'aktif' => true,
        ];

        $this->postJson('/api/couriers', $data)
            ->assertStatus(201)
            ->assertJsonPath('data.nama', 'Budiarto Wicaksono');

        $this->assertDatabaseHas('couriers', ['email' => 'budi@example.com']);
    });

    it('validates store request', function () {
        $this->postJson('/api/couriers', [
            'nama' => 'Ab',
            'email' => 'invalid-email',
            'telepon' => '081234567890',
            'level' => 9,
        ])->assertStatus(422);
    });

    it('prevents duplicate email and phone on store', function () {
        Courier::factory()->create([
            'email' => 'dup@example.com',
            'telepon' => '081234567890',
        ]);

        $this->postJson('/api/couriers', [
            'nama' => 'Copy Courier',
            'email' => 'dup@example.com',
            'telepon' => '081234567890',
            'level' => 1,
        ])->assertStatus(422);
    });

    it('updates an existing courier', function () {
        $dataKurir = Courier::factory()->create(['nama' => 'Old Name', 'level' => 2]);

        $this->putJson('/api/couriers/'.$dataKurir->id, [
            'nama' => 'New Name',
            'email' => 'new@example.com',
            'telepon' => '081234567890',
            'level' => 3,
            'jenis_kendaraan' => 'van',
            'plat_nomor' => 'B-456',
            'aktif' => false,
        ])
            ->assertStatus(200)
            ->assertJsonPath('data.nama', 'New Name')
            ->assertJsonPath('data.level', 3)
            ->assertJsonPath('data.aktif', false);

        $this->assertDatabaseHas('couriers', ['id' => $dataKurir->id, 'nama' => 'New Name', 'level' => 3]);
    });

    it('deletes an existing courier', function () {
        $dataKurir = Courier::factory()->create();

        $this->deleteJson('/api/couriers/'.$dataKurir->id)
            ->assertStatus(200)
            ->assertJsonPath('message', 'Courier deleted successfully.');

        $this->assertDatabaseMissing('couriers', ['id' => $dataKurir->id]);
    });
});
