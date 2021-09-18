<?php

namespace App\Http\Controllers;

use App\Events\UserProfileUpdated;
use App\Models\User;
use App\Services\EventDispatcher\IEventDispatcher;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse as Response;

class UserController extends Controller
{
    private IEventDispatcher $dispatcher;

    public function __construct(IEventDispatcher $dispatcher) {
        $this->middleware('auth:api');
        $this->dispatcher = $dispatcher;
    }

    public function show(User $user): Response {
        return response()->json($user);
    }

    public function edit(Request $request, User $user): Response {
        $data = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $data['password'] = bcrypt($data['password']);

        $user->update($data);

        $this->dispatcher->dispatch(
            new UserProfileUpdated($user)
        );
        return response()->json(['message' => 'Update success', 'token' => auth()->tokenById($user->id)]);
    }
}
