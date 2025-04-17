<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
        'is_approved',
        'dgn_number',
        'Supplier',
        'bol_number',
        'disposal_date',
        'disposal_by',
        'last_updated_by'
    ];
    public $timestamps = false; 
    public function damageDetails()
    {
        return $this->hasMany(DgnDamageDetail::class, 'dgn_id');
    }
    public function attachments()
    {
        return $this->hasMany(DGNAttachment::class, 'dgn_id');
    }



    
}
