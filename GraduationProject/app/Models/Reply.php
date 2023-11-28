<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'video_id', 'comment_id','reply'];

    public function user()
    {
        return  $this->belongsTo(User::class);
    }
    
    public function video()
    {
        return  $this->belongsTo(Video::class);
    }

    public function comment()
    {
        return  $this->belongsTo(Comment::class);
    }
}
