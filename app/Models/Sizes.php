<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Sizes extends Model
{
    use HasFactory;
    protected $table = 'sizes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'size_name',
        'size_kg',
        'size_lb',
        'status',
    ];
}
