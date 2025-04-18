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
        'status'
    ];

    public function mcuResult()
    {
        return $this->hasMany(McuResult::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
