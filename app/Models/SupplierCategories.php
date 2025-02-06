<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class SupplierCategories extends Model
{
   use HasFactory;

    protected $table = 'supplier_categories'; 
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $fillable = [
        'category_name',
        'status',
    ];
}
