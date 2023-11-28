<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }


    public function register(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = Auth::login($user);
        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'user' => $user,
            'authorisation' => [
                'token' => $token,
                'type' => 'bearer',
            ]
        ]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = User::where('id',Auth::user()->id)
        ->with('videos')
        ->withCount('videos')
        ->with('playlists')
        ->withCount('playlists')
        ->with('subscribers')
        ->withCount('subscribers')
        ->first();
        $subscribed = Subscriber::where('subscriber_id',Auth::user()->id)
        ->pluck('user_id');
        $subscribed_count = count($subscribed);
        return response()->json([ 'user' => $user , 'subscribed' => $subscribed,'subscribed_count' => $subscribed_count]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(Auth::refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }

    public function update(Request $request)
    {
        $user = User::where('id',Auth::user()->id)
        ->first();
        if($request->has('email'))
        {
            $validation_mail = $request->validate([
                'email' => ['email',"unique:users,email,$user->id"],
            ]);
            $user->update([
                $user->email = $validation_mail['email']
            ]);
        }
        if($request->has('password'))
        {
            $validation_mail = $request->validate([
                'password' => ['min:6'],
            ]);
            $user->update([
                $user->password = Hash::make($validation_mail['password'])
            ]);
        }
        if($request->has('bio'))
        {
            $validation_bio = $request->validate([
                'bio' => ['sometimes','string'],
            ]);
            $user->update([
                $user->bio = $validation_bio['bio']
            ]);
        }
        if($request->has('name'))
        {
            $validation_name = $request->validate([
                'name' => ['sometimes','string'],
            ]);
            $user->update([
                $user->name = $validation_name['name']
            ]);
        }
        if($request->has('image'))
        {
            $image_validation = $request->validate([
                'image' => ['mimes:jpg,jpeg,jfif,png']
            ]);
            if($user->image != null)
            {
                unlink($user->image);
            }
            $file_image = $image_validation['image'];
            $image_ext = $file_image->getClientOriginalExtension();
            $image_name = time() . rand(100,1000000) . '.' . $image_ext;
            $image_destination = $file_image->move(public_path('images/' . Auth::user()->name . '_' . Auth::user()->id  .'/'),$image_name);
            $user->update([
                $user->image = $image_destination
            ]);
        }
        return response()
        ->json('User Data Updated Successfully');
    }

    public function delete()
    {
        $user = User::where('id',Auth::user()->id)
        ->first();
        if($user->image != null)
        {
            unlink($user->image);
        }
        $user->delete();
        return response()
        ->json('User Data Deleted Successfully');
    }
}
