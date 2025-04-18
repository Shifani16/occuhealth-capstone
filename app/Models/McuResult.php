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

    public function patient()
    {
        return $this->belongsTo(McuPatient::class, 'mcu_patient_id');
    }

    
}
