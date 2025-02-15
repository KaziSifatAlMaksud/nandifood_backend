<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditTerm extends Model
{
    use HasFactory;


    protected $table = 'credit_terms';


    protected $primaryKey = 'id';

    public $timestamps = false; 

    protected $fillable = [
        'credit_terms',
        'credit_type',
        'credit_limit',
        'credit_status',
        'cus_sup_id',
        'type',
    ];

}
