<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceStatus extends Model
{
    protected $table = 'performance_status';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'status_name',
        'description',
    ];
}
