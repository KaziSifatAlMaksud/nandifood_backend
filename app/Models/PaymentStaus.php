<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentStatus extends Model
{
    use HasFactory;

    protected $table = 'payment_status';
    protected $primaryKey = 'id'; // specify primary key if not 'id'`

    protected $fillable = [
        'status_name',
        'status_comment',
    ];

    public $timestamps = false; // disable if you're not using created_at & updated_at
}
