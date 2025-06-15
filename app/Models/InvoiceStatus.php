<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceStatus extends Model
{
    use HasFactory;

    protected $table = 'invoice_status';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = [
        'status_type',
        'status_comment',
    ];
}
