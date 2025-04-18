<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $fillable = [
        'med_record_id',
        'patient_id',
        'name',
        'examination_date',
        'examination_type',
        'unit',
        'status',
        'gender',
        'age',
        'birth_date',
        'birth_place',
        'address',
    ];
}
