<?php

namespace App\Http\Controllers\API;

use App\Models\Video;
use App\Models\Playlist;
use Illuminate\Http\Request;
use App\Models\PlaylistVideo;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PlaylistController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        $playlists = Playlist::with(['videos' => function ($query) {
                $query->select('title',
                'description',
                'url',
                'thumbnail_url')
                ->selectRaw('IFNULL(JSON_LENGTH(view_count), 0) as total_views');
        }])
        ->paginate();
        return response()
            ->json($playlists);
    }

    public function myPlaylist()
    {
        $playlists = Playlist::with(['videos' => function ($query) {
            $query->select('title',
            'description',
            'url',
            'thumbnail_url')
            ->where('user_id', '=', Auth::user())
            ->selectRaw('IFNULL(JSON_LENGTH(view_count), 0) as total_views');
    }])
    ->paginate();
    return response()
    ->json($playlists);
    }

    public function show($id)
    {
        $playlist= Playlist::where('id', $id)
            ->with(['videos' => function ($query) {
            $query->select('title',
            'description',
            'url',
            'thumbnail_url')
            ->selectRaw('IFNULL(JSON_LENGTH(view_count), 0) as total_views');
    }])
    ->first();
        if ($playlist) {
            return response()
                ->json($playlist);
        }
        return response()
            ->json('Playlist Not Found');
    }

    public function store(Request $request)
    {
        $validation = $request->validate([
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
        ]);
        if ($validation) {
            Playlist::create([
                'title' => $request->title,
                'description' => $request->description,
                'user_id' => Auth::user()->id
            ]);
        }
        return response()
            ->json('Playlist Created Successfully');
    }

    public function update($id, Request $request)
    {
        $playlist = Playlist::where('user_id', Auth::user()->id)
            ->where('id', $id)
            ->first();
        if ($playlist) {
            $validation = $request->validate([
                'title' => ['required', 'string'],
                'description' => ['required', 'string'],
            ]);
            if ($validation) {
                $playlist->update([
                    'title' => $request->title,
                    'description' => $request->description,
                ]);
            }
            return response()
                ->json('Playlist Updated Successfully');
        }
        return response()
            ->json('Playlist Not Found');
    }

    public function delete($id)
    {
        $playlist = Playlist::where('user_id', Auth::user()->id)
            ->where('id', $id)
            ->first();
        if ($playlist) {
            $playlist->delete();
            return response()
                ->json('Playlist Deleted Successfully');
        }
        return response()
            ->json('Playlist Not Found');
    }

    public function addVideoToPlaylist($id, Request $request)
    {
        $playlist = Playlist::where('user_id', Auth::user()->id)
            ->where('id', $id)
            ->first();
        $validation = $request->validate([
            'video_id' => ['required', 'exists:videos,id'],
        ]);
        if ($playlist) {
            if ($validation) {
                PlaylistVideo::create([
                    'playlist_id' => $playlist->id,
                    'video_id' => $request->video_id
                ]);
                return response()
                    ->json('Video Added To ' . $playlist->title . ' Successfully');
            }
        }
        return response()
            ->json('Playlist Not Found');
    }

    public function removeVideoFromPlaylist($playlist_id, $video_id)
    {
        $playlist = Playlist::where('id', $playlist_id)
            ->where('user_id', Auth::user()->id)
            ->first();

        $video = Video::where('id', $video_id)
            ->where('user_id', Auth::user()->id)
            ->first();
        $playlistVideo = PlaylistVideo::where('playlist_id', $playlist_id)
            ->where('video_id', $video_id)
            ->first();
        if (isset($video) && isset($playlist)) {
            $playlistVideo->delete();
            return response()
                ->json("$video->title Deleted From " . $playlist->title . ' Successfully');
        }
        return response()
            ->json('Playlist or Video Not Found');
    }
}
