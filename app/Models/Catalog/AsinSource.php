<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsinSource extends Model
{
    use HasFactory;

    protected $connection = 'catalog';
    protected $fillable = [
        'asin',
        'user_id',
        'source',
    ];

    public function mws_region()
    {
        return $this->hasOne(Mws_region::class, 'region_code', 'destination_1');
    }

    public function aws_credential()
    {
        return $this->belongsTo(Aws_credential::class,);
    }

    public function aws()
    {

        return $this->hasOneThrough(
            Aws_credential::class,
            Mws_region::class,
            'region_code', // Foreign key on orders table...
            'mws_region_id', // Foreign key on products table...
            'source', // Local key on suppliers table...
            // 'id' // Local key on products table...
        );
    }
}