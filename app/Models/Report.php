<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\UserResource;
use App\Models\BrickWeight;
use App\Models\SubReport;



class Report extends Model
{
    protected $fillable = ['user_id','zone', 'weights', 'average_weight', 'datetime', 'brick_type','username',  'shift',];
    protected $hidden = ['user_id', 'updated_at'];

    protected $casts = [
        'weights' => 'array',
        'datetime' => 'datetime',
        'average_weight' => 'float',
        'shift' => 'string',
        'brick_type' => 'string',
        'zone' => 'string',
        'username' => 'string',

    ];
    public function user()
    {
        return $this->belongsTo(User::class);  // Assumes a 'user_id' column exists in 'reports' table
    }

    public function brickWeights()
{
    return $this->hasMany(BrickWeight::class);
}
public function subreports()
{
    return $this->hasMany(Subreport::class, 'report_id');
}


}
