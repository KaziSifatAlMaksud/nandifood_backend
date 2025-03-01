<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerNote extends Model
{
    use HasFactory;

    protected $table = 'customer_notes';

    protected $primaryKey = 'id';

        public $timestamps = false; // to disable default timestamp 
    protected $fillable = [
        'customer_id',
        'file_description',
        'note_date',
        'file_path',
        'uploaded_by',
        'type',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
