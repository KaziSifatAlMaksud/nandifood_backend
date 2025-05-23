<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductNote extends Model
{
    use HasFactory;
    protected $table = 'product_notes';
    protected $fillable = [
        'product_id',
        'file_description',
        'note_date',
        'uploaded_by',
        'file_path',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
