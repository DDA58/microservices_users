<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse as Response;

class UserController extends Controller
{
    public function __construct() {
        $this->middleware('auth:api');
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

        return response()->json(['message' => 'Update success', 'token' => auth()->tokenById($user->id)]);
    }
}
