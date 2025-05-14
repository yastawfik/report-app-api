<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Http\Resources\UserResource;
use App\Models\BrickWeight;
use App\Models\SubReport;



class Report extends Model
{
    protected $fillable = ['user_id','zone', 'weights', 'average_weight', 'datetime', 'brick_type','username',  'shift'];
    protected $hidden = ['user_id', 'updated_at'];

    protected $casts = [
        'weights' => 'array',
        'datetime' => 'datetime',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);  // Assumes a 'user_id' column exists in 'reports' table
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'created_at' => $this->created_at,
            'user' => $this->user ? new UserResource($this->user) : null,
            // Check if user is loaded
        ];
    }
    public function brickWeights()
{
    return $this->hasMany(BrickWeight::class);
}
public function subreports()
{
    return $this->hasMany(SubReport::class); // assuming you have a SubReport model
}


}
