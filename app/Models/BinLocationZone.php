<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BinLocationZone extends Model
{
    use HasFactory;

    protected $table = 'bin_location_zones';
    protected $primaryKey = 'id'; // This is optional if you are using the default 'id'
    protected $fillable = [
        'zone_code',
        'zone_description',
    ];
    public $timestamps = true;

}
