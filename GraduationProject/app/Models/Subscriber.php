<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','subscriber_id'];
    protected $hidden = ['id','user_id','created_at','updated_at'];

    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
