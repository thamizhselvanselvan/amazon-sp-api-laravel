<?php

namespace App\Models\Inventory;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shelve extends Model
{
    use HasFactory;

    protected $connection = 'in';

    // public function __construct(array $attributes = [])
    // {
    //     parent::__construct($attributes);
    //     $this->getConnection()->setTablePrefix('in_');
    // }

    // public function __destruct()
    // {
    //     $this->getConnection()->setTablePrefix('sp_');
    // }

    protected $fillable = ['rack_id', 'name'];

    public function bins()
    {
        return $this->hasMany(Bin::class);
    }

    public function racks()
    {
        return $this->hasOne(Rack::class, 'id', 'rack_id');
    }
}
