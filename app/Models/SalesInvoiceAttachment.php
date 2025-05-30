<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesInvoiceAttachment extends Model
{
    protected $table = 'sales_invoice_attachment';

    protected $fillable = [
        'si_id',
        'file_description',
        'note_date',
        'file_path',
        'uploaded_by',
        'created_at',
        'updated_at',
    ];

    public $timestamps = true;
}