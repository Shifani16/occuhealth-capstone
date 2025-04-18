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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('med_record_id'); // Nomor Rekam Medis
            $table->string('patient_id');        // Nomor Pasien
            $table->string('name');                  // Nama Pasien
            $table->date('examination_date');        // Tanggal Pemeriksaan
            $table->string('examination_type');      // Jenis Pemeriksaan
            $table->string('unit')->nullable();      // Unit Kerja
            $table->enum('status', ['Delivered', 'Process', 'Cancelled']); // Status Pemeriksaan
            $table->enum('gender', ['Laki-laki', 'Perempuan']);
            $table->integer('age');
            $table->date('birth_date');
            $table->string('birth_place');
            $table->text('address');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
