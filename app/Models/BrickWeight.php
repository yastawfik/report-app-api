<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrickWeight extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'zone',
        'weight',
    ];

    public function report()
    {
        return $this->belongsTo(Report::class);
    }
}
