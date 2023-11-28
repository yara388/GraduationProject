<?php

namespace App\Http\Controllers\API;

use App\Models\Reply;
use App\Models\Video;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class ReplyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function store($comment_id,$video_id,Request $request)
    {
        $comment = Comment::with('video')
        ->where('video_id',$video_id)
        ->where('id',$comment_id)
        ->first();
        $validation = $request->validate([
            'reply' => ['required','string'],
        ]);
        if($validation && $comment)
        {
            Reply::create([
                'user_id' => Auth::user()->id,
                'video_id' => $comment->video_id,
                'comment_id' => $comment->id,
                'reply' => $request->reply
            ]);
            return response()
            ->json('Reply Added Successfully');
        }
        return response()
        ->json('Not Found');
    }

    public function update($comment_id,$video_id,$id,Request $request)
    {
        $reply = Reply::where('user_id',Auth::user()->id)
        ->where('video_id',$video_id)
        ->where('comment_id',$comment_id)
        ->where('id',$id)
        ->first();
        if($reply)
        {
            $validation = $request->validate([
                'reply' => ['required','string']
            ]);
            if($validation)
            {
                $reply->update([
                    'reply' => $request->reply,
                ]);
                return response()
                ->json('Reply updated successfully');
            }
        }
        return response()
        ->json('Not Found');
    }

    public function delete($comment_id,$video_id,$id,Request $request)
    {
        $reply = Reply::where('user_id',Auth::user()->id)
        ->where('video_id',$video_id)
        ->where('comment_id',$comment_id)
        ->where('id',$id)
        ->first();
        if($reply)
        {
                $reply->delete();
                return response()
                ->json('Reply Deleted successfully');
        }
        return response()
        ->json('Not Found');
    }
}
