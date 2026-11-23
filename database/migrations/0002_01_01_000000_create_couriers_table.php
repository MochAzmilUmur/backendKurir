<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('couriers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama', 150)->index();
            $table->string('email', 150)->unique();
            $table->string('telepon', 20)->unique();
            $table->unsignedTinyInteger('level')->default(1)->index();
            $table->string('jenis_kendaraan', 50)->nullable();
            $table->string('plat_nomor', 15)->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('couriers');
    }
};
