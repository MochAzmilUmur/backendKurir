<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courier extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'nama', 'email', 'telepon', 'level',
        'jenis_kendaraan', 'plat_nomor', 'aktif',
    ];

    protected function casts(): array
    {
        return [
            'level' => 'integer',
            'aktif' => 'boolean',
        ];
    }

    public function scopeSearch(Builder $ambilData, ?string $cari): Builder
    {
        if (empty($cari)) {
            return $ambilData;
        }

        
        $kataKunci = array_filter(preg_split('/\s+/', trim($cari)));

        foreach ($kataKunci as $kata) {
            $ambilData->where('nama', 'LIKE', "%{$kata}%");
        }

        return $ambilData;
    }

    public function scopeFilterLevel(Builder $ambilData, ?string $levelDicari): Builder
    {
        if (empty($levelDicari)) {
            return $ambilData;
        }

        $daftarLevel = array_values(array_filter(
            array_map('intval', explode(',', $levelDicari)),
            fn ($level) => $level >= 1 && $level <= 5
        ));

        return empty($daftarLevel) ? $ambilData->whereRaw('1 = 0') : $ambilData->whereIn('level', $daftarLevel);
    }

    public function scopeApplySort(Builder $ambilData, ?string $kolomUrut, ?string $arahUrut): Builder
    {
        $kolomDiizinkan = ['nama', 'created_at', 'updated_at', 'email', 'telepon', 'level'];
        $kolomUrut = in_array($kolomUrut, $kolomDiizinkan, true) ? $kolomUrut : 'nama';
        $arahAwal = $kolomUrut === 'nama' ? 'asc' : 'desc';
        $arahUrut = in_array(strtolower((string) $arahUrut), ['asc', 'desc']) ? strtolower($arahUrut) : $arahAwal;

        return $ambilData->orderBy($kolomUrut, $arahUrut);
    }
}
