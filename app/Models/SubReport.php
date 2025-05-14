<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubReport extends Model
{
    use HasFactory;
 protected $table = 'sub_reports';
    // Define the fillable attributes
    protected $fillable = [
        'report_id',   // foreign key to the Report
        'zone',        // zone of the subreport
        'brick_type',  // brick type
        'weights',     // JSON or array of weights
        'average_weight', // average weight
        'datetime',    // datetime of the subreport
        'shift',       // shift of the subreport
        'username',    // username of the person who created the subreport

    ];

    // Define the relationship with the Report
 public function report()
{
    return $this->belongsTo(Report::class);
}
}
