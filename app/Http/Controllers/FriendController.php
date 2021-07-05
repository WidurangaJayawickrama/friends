<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\InviteEvent;
use RealRashid\SweetAlert\Facades\Alert;

class FriendController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $friendList = $this->getAcceptedFriendList($request);
        $friendRequestList = $this->getFriendRequestList();
        return view('dashboard')->with([
            'friendList' => $friendList,
            'friendRequestList' => $friendRequestList
        ]);
    }


    public function sendInvitation(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $email = $request->email;
        $user = Auth::user();
        $recipient = User::where('email', $email)->first();
        if (!empty($recipient)) {
            if (!$user->hasSentFriendRequestTo($recipient)) {
                $user->befriend($recipient);
            }
        }
        event(new InviteEvent($email));
        Alert::toast('Friend request send', 'success');

        return redirect()->route('dashboard');
    }


    public function getAcceptedFriendList($request)
    {
        $search = '';
        if ($request->has('query')) {
            $search = $request->get('query');
        }

        $user = Auth::user();
        $friends = $user->friends()->where('status', 'accepted')->get();
        if (count($friends) > 0) {
            $ids = $friends->pluck('sender_id')->merge($friends->pluck('recipient_id'))->unique();
        } else {
            $ids = [];
        }

        return User::whereIn('id', $ids)->whereNotIn('id', [$user->id])
            ->when($search, function ($query, $search) {
                return $query->where('first_name', 'like', '%' . $search . '%');
            })->paginate();
    }


    public function getFriendRequestList()
    {
        $user = Auth::user();
        $list = $user->getFriendRequests();
        $sender = collect($list)->pluck('sender_id');
        if (count($sender) > 0) {
            $result = User::whereIN('id', $sender)->get();
        } else {
            $result = [];
        }

        return $result;
    }


    public function acceptRequest(Request $request, int $id)
    {
        $user = Auth::user();
        $sender = User::findOrFail($id);

        if ($request->ajax()) {
            $user->acceptFriendRequest($sender);
        } else {
            if (!$request->hasValidSignature()) {
                abort(401);
            }
            try {
                $user->acceptFriendRequest($sender);
                Alert::toast('Friend request accepted', 'success');
            } catch (\Exception $exception) {
                Alert::toast('Sorry, Friend request cannot accepted', 'error');
            }

            return redirect()->route('dashboard');
        }
    }


    public function denyRequest(int $id)
    {
        try {
            $user = Auth::user();
            $sender = User::findOrFail($id);
            $user->denyFriendRequest($sender);
            $response = response()->json(200);
        } catch (\Exception $exception) {
            $response = response()->json($exception->getCode());
        }

        return $response;
    }

    public function removeFriend(int $id)
    {
        try {
            $user = Auth::user();
            $friend = User::findOrFail($id);
            $user->unfriend($friend);

            $response = response()->json(200);
        } catch (\Exception $exception) {
            $response = response()->json($exception->getCode());
        }

        return $response;
    }
}
