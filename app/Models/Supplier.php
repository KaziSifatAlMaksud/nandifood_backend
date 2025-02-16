<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $table = 'suppliers';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'supplier_no',
        'supplier_legal_name',
        'supplier_trade_name',
        'address1',
        'address2',
        'country',
        'state',
        'city',
        'zip_code',
        'email',
        'phone',
        'mobile',
        'first_name',
        'middle_name',
        'last_name',
        'position',
        'supplier_category',
        'account_manager',
        'category_manager',
        'eff_date',
        'credit_terms',
        'last_updated',
        'last_updated_by',
        'status',
        'img',
        'notes',
        'is_approved',
    ];

    public function notes()
    {
        return $this->hasMany(SupplierNote::class);
    }

}
