<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\POItemDetail;

class PO extends Model
{
    protected $table = 'pos';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'po_no',
        'po_date',
        'po_due_date',
        'supplier',
        'supp_country',
        'supp_state',
        'supp_city',
        'warehouse',
        'war_country',
        'war_state',
        'war_city',
        'priority',
        'currency',
        'amount',
        'po_status',
        'receiving_status',
        'is_approve',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'notes',
    ];
    public function poItemDetails()
    {
        return $this->hasMany(POItemDetail::class, 'po_id'); // Adjust 'po_id' if your foreign key is different
    }

}
