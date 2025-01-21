<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product_sub_category2 extends Model
{
      use HasFactory;
     protected $table = 'product_sub_category2';
     protected $primaryKey = 'id';
         protected $fillable = [
        'category_name',
        'category_id',
        'sub_category1',
        'status',
        'level',
    ];
}
