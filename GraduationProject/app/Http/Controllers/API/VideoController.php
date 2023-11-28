<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VideoController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }


    public function myVideos()
    {
        $videos = Video::where('user_id', Auth::user()->id)
        ->get();
        if(count($videos) > 0)
        {
            foreach($videos as $video)
            {
                $views[] = count(json_decode($video->view_count));
            }
            return response()
            ->json(['videos'=>$videos,'views'=>$views],200);
        }
        return response()
        ->json("No Videos");
    }

    public function store(Request $request)
    {
        $validation = $request->validate([
            'title' => ['required','string'],
            'description' => ['required','string'],
            'user_id' => ['exists:users,id'],
            'url' => ['required','mimes:mp4,vlc'],
        ]);

        $file_url = $validation['url'];
        $video_ext = $file_url->getClientOriginalExtension();
        $video_name = time() . rand(1000,10000000) . '.' . $video_ext;
        $video_destination = $file_url->move(public_path('videos/'. Auth::user()->name . '_' . Auth::user()->id  . '/'),$video_name);

        if($request->has('thumbnail_url'))
        {
            $thumbnail_validation = $request->validate([
                'thumbnail_url' => ['mimes:jpg,jpeg,jfif,png']
            ]);
            $file_thumbnail_url = $thumbnail_validation['thumbnail_url'];
            $thumbnail_ext = $file_thumbnail_url->getClientOriginalExtension();
            $thumbnail_name = time() . rand(100,1000000) . '.' . $thumbnail_ext;
            $thumbnail_destination = $file_thumbnail_url->move(public_path('images/thumbnails/' . Auth::user()->name . '_' . Auth::user()->id  .'/'),$thumbnail_name);
        }
        if($validation)
        {
            $viewers = [];
            $viewers = json_encode($viewers);
            Video::create([
                'title' => $request->title,
                'description' => $request->description,
                'user_id' => Auth::user()->id,
                'url' => $video_destination,
                'thumbnail_url' => $thumbnail_destination ?? null,
                'view_count' => $viewers,
                'privacy' => $request->privacy ?? 'public',
            ]);
        }
        return response()
        ->json('Video Created Successfully',201);
    }

    public function edit(Request $request , $id)
    {
        $video = Video::where('user_id', Auth::user()->id)
        ->where('id', $id)
        ->first();
        if(!$video)
        {
            return response()
            ->json('Not Found',404);
        }
        $validation = $request->validate([
            'title' => ['sometimes','string'],
            'description' => ['sometimes','string'],
        ]);
        if($request->has('url'))
        {
            $validation_url = $request->validate([
                'url' => ['mimes:mp4,vlc']
            ]);
            unlink($video->url);
            $file_url = $validation_url['url'];
            $video_ext = $file_url->getClientOriginalExtension();
            $video_name = time() . rand(1000,10000000) . '.' . $video_ext;
            $video_destination = $file_url->move(public_path('videos/'. Auth::user()->name . '_' . Auth::user()->id . '/'),$video_name);
        }

        if($request->has('thumbnail_url'))
        {
            $thumbnail_validation = $request->validate([
                'thumbnail_url' => ['mimes:jpg,jpeg,jfif,png']
            ]);
            if($video->thumbnail_url !== null)
            {
                unlink($video->thumbnail_url);
            }
            $file_thumbnail_url = $thumbnail_validation['thumbnail_url'];
            $thumbnail_ext = $file_thumbnail_url->getClientOriginalExtension();
            $thumbnail_name = time() . rand(100,1000000) . '.' . $thumbnail_ext;
            $thumbnail_destination = $file_thumbnail_url->move(public_path('images/thumbnails/' . Auth::user()->name .'_'. Auth::user()->id .'/'),$thumbnail_name);
            $video->thumbnail_url = $thumbnail_destination;
        }
        if($validation)
        {
            $video->update([
                'title' => $request->title,
                'description' => $request->description,
                'user_id' => Auth::user()->id,
                'url' => $video_destination,
                'privacy' => $request->privacy ?? 'public',
            ]);
        }
        return response()
        ->json('Video Updated Successfully',201);
    }

    public function show($id)
    {
        $video = Video::where('id', $id)
        ->first();
        if(!$video)
        {
            return response()
            ->json('Not Found',404);
        }
        $viewers = json_decode($video->view_count, true);
        if(!in_array(Auth::user()->id,$viewers))
        {
            $viewers[] = Auth::user()->id;
            $video->update([
                'view_count' => $viewers
            ]);
        }
        $video_views = Video::with('user')
        ->withCount('likes','dislikes')
        ->where('id',$id)
        ->first();
        $comments = Comment::with('replies')
        ->withCount('replies')
        ->where('video_id',$video_views->id)
        ->get();
        $views = count(json_decode($video_views->view_count));
        return response()
        ->json(['video'=>$video_views,'views'=>$views,'comments'=>$comments]);
    }

    public function delete($id)
    {
        $video = Video::where('user_id',Auth::user()->id)
        ->where('id',$id)
        ->first();
        if(!$video)
        {
            return response()
            ->json('Not Found',404);
        }
        unlink($video->url);
        if($video->thumbnail_url != null)
        {
            unlink($video->thumbnail_url);
        }
        $video->delete();
        return response()
        ->json('Video Deleted Successfully',201);
    }
}
