<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCategory extends Model
{
    use HasFactory;

    protected $table = 'customer_category';

    protected $primaryKey = 'id'; 

    protected $fillable = [
        'category_name',
        'status',
    ];

    public $timestamps = false; 
}
