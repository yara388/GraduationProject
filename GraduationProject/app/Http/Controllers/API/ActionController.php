<?php

namespace App\Http\Controllers\API;

use App\Models\Like;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Dislike;
use App\Models\Video;
use Illuminate\Support\Facades\Auth;

class ActionController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function like($id)
    {
        $video = Video::where('id', $id)
        ->first();
        $like = Like::where('video_id', $id)
        ->where('user_id', Auth::user()->id)
        ->first();
        $dislike = Dislike::where('video_id', $id)
        ->where('user_id', Auth::user()->id)
        ->first();
        if($video == null)
        {
            return response()
            ->json('Not Found');
        }
        else
        {
            if($like == null && $dislike == null)
            {
                Like::create([
                    'user_id' => Auth::user()->id,
                    'video_id' => $id
                ]);
                return response()
                ->json('You Liked This Video');
            }
            elseif($like == null && isset($dislike))
            {
                Like::create([
                    'user_id' => Auth::user()->id,
                    'video_id' => $id
                ]);
                $dislike->delete();
                return response()
                ->json('You Liked This Video');
            }elseif(isset($like) && $dislike == null)
            {
                $like->delete();
                return response()
                ->json('Like Removed');
            }
        }
    }

    public function dislike($id)
    {
        $video = Video::where('id', $id)
        ->first();
        $like = Like::where('video_id', $id)
        ->where('user_id', Auth::user()->id)
        ->first();
        $dislike = Dislike::where('video_id', $id)
        ->where('user_id', Auth::user()->id)
        ->first();
        if($video == null)
        {
            return response()
            ->json('Not Found');
        }
        else
        {
            if($like == null && $dislike == null)
            {
                Dislike::create([
                    'user_id' => Auth::user()->id,
                    'video_id' => $id
                ]);
                return response()
                ->json('You Disliked This Video');
            }
            elseif($dislike == null && isset($like))
            {
                Dislike::create([
                    'user_id' => Auth::user()->id,
                    'video_id' => $id
                ]);
                $like->delete();
                return response()
                ->json('You DisLiked This Video');
            }elseif(isset($dislike) && $like == null)
            {
                $dislike->delete();
                return response()
                ->json('Dislike Removed');
            }
        }
    }
}
