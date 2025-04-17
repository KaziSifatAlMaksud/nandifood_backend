<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PRD extends Model
{
    protected $table = 'prds'; // Change if your table name is different

    protected $primaryKey = 'id';

    protected $fillable = [
        'warehouse',
        'country',
        'city',
        'state',
        'prd_date',
        'prd_no',
        'pro_start_date',
        'pro_end_date',
        'pro_supervisor',
        'last_updated',
        'last_updated_by',
        'status',
        'is_approve',
        'notes'
    ];

    public $timestamps = false; 


}
