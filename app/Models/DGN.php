<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DGN extends Model
{
    use HasFactory;

    protected $table = 'dgns';

    protected $fillable = [
        'defult_warehouse',
        'damage_date',
        'address1',
        'address2',
        'regerence_no',
        'country',
        'state',
        'damage_reported_by',
        'city',
        'zip_code',
        'last_update',
        'email',
        'phone',
        'status',
        'office_phone',
        'notes',
        'is_approve',
    ];

    // Optionally, you can define relationships if needed.
    // For example, if you want to define a relationship with DGNAttachment.
    public function attachments()
    {
        return $this->hasMany(DGNAttachment::class, 'dgn_id');
    }
}
