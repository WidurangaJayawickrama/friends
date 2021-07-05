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
    public function index()
    {
        $friendList = $this->getAcceptedFriendList();
        $friendRequestList = $this->getFriendRequestList();
        $userList = $this->users();
        return view('dashboard')->with([
            'friendList' => $friendList,
            'friendRequestList' => $friendRequestList,
            'userList' => $userList
        ]);
    }

    public function users()
    {
        return User::select('id', 'first_name', 'last_name', 'email')->whereNotIn('id', [Auth::user()->id])->get();
    }

    public function getAcceptedFriendList($perPage = 20)
    {
        $user = Auth::user();
        $list = $user->getFriends($perPage, '', ['id', 'first_name', 'last_name', 'email']);
        return $list;
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

    public function sendRequest(int $id)
    {
        try {
            $user = Auth::user();
            $recipient = User::findOrFail($id);
            if (!$user->hasSentFriendRequestTo($recipient)) {
                $user->befriend($recipient);
                event(new InviteEvent($recipient));
                $result = ['status' => 200, 'type' => 'success', 'message' => 'Request has been sent'];
            } else {
                $result = ['status' => 200, 'type' => 'info', 'message' => 'Request has been already sent'];
            }
        } catch (\Exception $exception) {
            $result = [
                'status' => $exception->getCode(),
                'type' => 'error',
                'message' => $exception->getMessage()
            ];
        }

        return $result;
    }

    public function acceptRequest(Request $request, int $id)
    {
        if (!$request->hasValidSignature()) {
            abort(401);
        }
        try {
            $user = Auth::user();
            $sender = User::findOrFail($id);
            $user->acceptFriendRequest($sender);
            Alert::toast('Friend request accepted', 'success');
        } catch (\Exception $exception) {
            Alert::toast('Sorry, Friend request cannot accepted', 'error');
        }

        return redirect()->route('dashboard');
    }

    public function denyRequest(int $id)
    {
        try {
            $user = Auth::user();
            $sender = User::findOrFail($id);
            $user->denyFriendRequest($sender);
            $result = ['status' => 200];
        } catch (\Exception $exception) {
            $result = ['status' => $exception->getCode()];
        }

        return $result;
    }

    public function removeFriend(int $id)
    {
        try {
            $user = Auth::user();
            $friend = User::findOrFail($id);
            $user->unfriend($friend);
            $result = ['status' => 200];
        } catch (\Exception $exception) {
            $result = ['status' => $exception->getCode()];
        }

        return $result;
    }
}
