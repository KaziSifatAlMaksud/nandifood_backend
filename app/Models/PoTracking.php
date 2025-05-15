<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoTracking extends Model
{
    use HasFactory;

    protected $table = 'po_tracking';

    protected $fillable = [
        'po_id',
        'date_created',
        'date_submitted',
        'date_approved',
        'created_by',
        'submitted_by',
        'submitted_to',
        'approved_by',
    ];

    public $timestamps = false; // Set to true if you're using created_at and updated_at
}
