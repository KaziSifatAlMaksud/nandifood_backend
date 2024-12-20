<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Uom_linked extends Model
{
       use HasFactory;

     protected $fillable = [
        'uom_id',      
        'conv_form_id',
        'conv_to_id',
        'conv_qty',     
        'status'         
    ];

       public function uom()
    {
        return $this->belongsTo(UOM::class); 
    }


}
