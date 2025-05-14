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
            $table->string('med_record_id'); 
            $table->string('patient_id');   
            $table->string('name');              
            // $table->date('examination_date');  
            // $table->string('examination_type'); 
            $table->string('unit')->nullable();    
            // $table->enum('status', ['Delivered', 'Process', 'Cancelled']);
            $table->enum('gender', ['Laki-laki', 'Perempuan']);
            $table->integer('age');
            $table->date('birth_date');
            $table->string('jabatan')->nullable();
            $table->string('ketenagaan')->nullable();
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
