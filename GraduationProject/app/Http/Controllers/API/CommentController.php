<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function store(Request $request,$id)
    {
        $video = Video::where('id',$id)
        ->first('id');
        $validation = $request->validate([
            'comment' => ['required','string'],
        ]);
        if($validation)
        {
            Comment::create([
                'user_id' => Auth::user()->id,
                'video_id' => $video->id,
                'comment' => $request->comment
            ]);
        }
        return response()
        ->json('Comment Added Successfully');
    }

    public function update(Request $request,$id,$comment_id)
    {
        $comment = Comment::where('id',$comment_id)
        ->where('user_id',Auth::user()->id)
        ->where('video_id',$id)
        ->first();
        $validation = $request->validate([
            'comment' => ['required','string']
        ]);
        if($validation)
        {
            $comment->update([
                'comment' => $request->comment
            ]);
        }
        return response()
        ->json('Comment Updated Successfully');
    }

    public function delete($id,$comment_id)
    {
        $comment = Comment::where('id',$comment_id)
        ->where('user_id',Auth::user()->id)
        ->where('video_id',$id)
        ->first();
        if(!$comment)
        {
            return response()
            ->json('Not Found');
        }
        $comment->delete();
        return response()
        ->json('Comment Deleted Successfully');
    }
}
