<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PickingTicketStatusList extends Model
{
    protected $table = 'picking_ticket_status_list';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'status_name',
        'description',
    ];

}
