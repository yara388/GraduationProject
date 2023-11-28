<?php

namespace App\Models;

use App\Models\Reply;
use App\Models\Dislike;
use App\Models\Playlist;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Video extends Model
{
    use HasFactory;
    protected $fillable = ['title','description','user_id','url','thumbnail_url','view_count','privacy'];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'privacy',
        'view_count'
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class,'playlist_video');
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    public function dislikes()
    {
        return $this->hasMany(Dislike::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }
}
