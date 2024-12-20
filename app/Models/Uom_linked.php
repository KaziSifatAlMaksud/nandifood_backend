<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Uom_linked extends Model
{
        protected $table = 'uom_linked';

     protected $fillable = [
        'uom_id',      
        'conv_form_id',
        'conv_to_id',
        'conv_qty',     
        'status'         
];


}
