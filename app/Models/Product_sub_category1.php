<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product_sub_category1 extends Model
{
    use HasFactory;
     protected $table = 'product_sub_category1';
     protected $primaryKey = 'id';
         protected $fillable = [
        'category_name',
        'category_id',
        'status',
        'level',
    ];
}
