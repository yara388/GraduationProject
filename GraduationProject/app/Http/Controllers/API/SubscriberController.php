<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriberController extends Controller
{
    public function subscribe(Request $request)
    {
        if(Auth::user()->id != $request->user_id)
        {
            $validation = $request->validate([
                'user_id' => ['exists:users,id']
            ]);
            if($validation)
            {
                Subscriber::create([
                    'subscriber_id' => Auth::user()->id,
                    'user_id' => $request->user_id
                ]);
            }
            return response()
            ->json('Subscribed Successfully');
        }
        return response()
        ->json('Not Allowed');
    }

    public function unsubscribe($user_id)
    {
        $subscription = Subscriber::where('subscriber_id','=', Auth::user()->id)
        ->where('user_id','=',$user_id)
        ->first();
        if($subscription == null)
        {
            return response()
            ->json('Not Found');
        }
        $subscription->delete();
        return response()
        ->json('Unsubscribed Successfully');
    }
}
