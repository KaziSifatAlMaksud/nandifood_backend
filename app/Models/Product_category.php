<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Product_category extends Model
{
     use HasFactory;
     protected $table = 'product_category';
     protected $primaryKey = 'id';
         protected $fillable = [
        'category_name',
        'status',
        'level',
    ];
}
