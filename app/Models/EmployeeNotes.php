<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeNotes extends Model
{
     use HasFactory;
    protected $table = 'employee_notes';
    protected $fillable = [
        'id',
        'employee_id',
        'file_description',
        'note_date',
        'uploaded_by',
        'file_path',
        'created_at',
        'updated_at',
    ];

    // public function employee()
    // {
    //     return $this->belongsTo(Employee::class);
    // }
}
