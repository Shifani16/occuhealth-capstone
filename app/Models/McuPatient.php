<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class McuPatient extends Model
{
    protected $fillable = [
        'patient_id',
        'name',
        'examination_date',
        'examination_type',
        'status',
        'saran',
    ];

    protected $casts = [
        'examination_date' => 'date',
    ];

    public function mcuResults()
    {
         return $this->hasMany(McuResult::class, 'patient_id');
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}