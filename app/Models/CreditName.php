<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditName extends Model
{
  use HasFactory;

    protected $table = 'credit_name';
    protected $primaryKey = 'id';
    public $timestamps = false;

   protected $fillable = ['credit_term_name'];
}
