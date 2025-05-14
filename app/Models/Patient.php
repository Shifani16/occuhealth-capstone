<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'med_record_id',
        'patient_id',
        'name',
        // 'examination_date',
        // 'examination_type',
        // 'unit',
        'status',
        'gender',
        'age',
        'birth_date',
        'jabatan',
        'ketenagaan',
    ];

    protected $casts = [
        'age' => 'integer',
        'birth_date' => 'date',
    ];

    public function mcuPatients()
    {
        return $this->hasMany(McuPatient::class);
    }

    public function mcuResults()
    {
        return $this->hasMany(McuResult::class);
    }
}
