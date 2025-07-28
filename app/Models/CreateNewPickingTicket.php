<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreateNewPickingTicket extends Model
{
    protected $table = 'create_new_picking_ticket';

    protected $fillable = [
        'sales_order',
        'warehouse',
        'picking_ticket_no',
        'picking_ticket_date',
        'sales_rep',
        'customer',
        'ship_to_address',
        'address1',
        'address2',
        'city',
        'state',
        'country',
        'zip_code',
        'order_priority',
        'planned_shipping_out_date',
        'scheduled_picking_start_date',
        'scheduled_picking_end_date',
        'actual_picking_start_date',
        'actual_picking_end_date',
        'assigned',
        'partial_shipment',
        'last_updated',
        'last_updated_by',
        'status',
        'performance',
        'sales_order_status',
        'picking_method',
        'picking_operation',
    ];

    public $timestamps = false;
}
