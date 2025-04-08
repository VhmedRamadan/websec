<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoughtProduct extends Model{
    
    protected $fillable = [
        'uid',
        'pid',
        'created_at',
        'updated_at',
    ];
}