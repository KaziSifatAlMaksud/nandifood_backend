<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrdCrew extends Model
{
    use HasFactory;

    protected $table = 'prd_crew';

    protected $fillable = [
        'prd_id',
        'emp_id'
    ];
    public $timestamps = false; 

}
