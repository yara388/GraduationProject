<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use App\Models\Video;
use App\Models\Subscriber;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $videos = Video::with('user')
        ->where('privacy', '!=' ,'private')
        ->get();
        $videos_view = $videos->map(function($video){
            $count = count(json_decode($video->view_count));
            return [
                'title' => $video->title,
                'description' => $video->description,
                'url' => $video->url,
                'thumbnail_url' => $video->thumbnail_url,
                'view_count' => $count,
                'created_at' => date('d/m/Y',strtotime($video->created_at))
            ];
        })
        ->all();
        $subscribe = Subscriber::where('subscriber_id','=',Auth::user()->id)
        ->get();
        if(count($subscribe) > 0)
        {
            $users = User::get();
            foreach($subscribe as $subscribed)
            {
                foreach($users as $user)
                {
                    if($user->id == $subscribed->user_id)
                    {
                        $subscribes [] = ['name' => $user->name , 'image' => $user->image];
                    }
                }
            }
            return response()
            ->json(['videos'=>$videos_view,'subscribes' => $subscribes]);
        }
        return response()
        ->json(['videos'=>$videos_view,'subscribe' => $subscribe]);
    }
}
