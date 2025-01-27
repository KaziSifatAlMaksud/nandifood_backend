<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceExcelFile extends Model
{
    use HasFactory;
    protected $table = 'price_excel_file';
     protected $primaryKey = 'id';
    protected $fillable = [
        'price_list_id',
        'price_list_name',
        'eff_date',
        'exp_date',
        'status',
        'last_update',
        'updated_by',
        'action',
        'file',
    ];
        public $timestamps = false;
}
