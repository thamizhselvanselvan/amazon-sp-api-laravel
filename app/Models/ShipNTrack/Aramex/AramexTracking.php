<?php

namespace App\Models\ShipNTrack\Aramex;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AramexTracking extends Model
{
    use HasFactory;
    protected $connection = 'shipntracking';
    protected $fillable = [
        'awbno',
        'update_code',
        'update_description',
        'update_date_time',
        'update_location',
        'comment',
        'gross_weight',
        'chargeable_weight',
        'weight_unit',
        'problem_code',
    ];
}
