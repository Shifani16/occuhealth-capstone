<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class McuResult extends Model
{
    protected $fillable = [
        'mcu_patient_id', 
        'category', 
        'result', 
        'result_date'
    ];

    protected $casts = [
        'result_date' => 'date',
    ];

    public function mcuPatient()
    {
        return $this->belongsTo(McuPatient::class);
    }

    
}
