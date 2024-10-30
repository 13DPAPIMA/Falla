<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'gender' => 'required|in:Male,Female',
        ]);

        $validatedData['name'] = $validatedData['email'];
        $validatedData['role'] = 'user';

        $user = User::create($validatedData);

        // Dispatch the Registered event to send the verification email
        event(new Registered($user));

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;
            return [
                'user' => $user,
                'token' => $token
            ];
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validatedData = $request->validate([
            'email' => 'min:8|max:50|sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|confirmed',
            'gender' => 'sometimes|in:Male,Female',
        ]);

        $dataToUpdate = array_intersect_key($validatedData, array_flip(['email', 'gender']));

        if (isset($validatedData['password'])) {
            $dataToUpdate['password'] = bcrypt($validatedData['password']);
        }

        $user->update($dataToUpdate);

        return $user;
    }

    public function verifyPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = $request->user();

        if (Hash::check($request->password, $user->password)) {
            return response()->json(['verified' => true]);
        }

        return response()->json(['verified' => false], 401);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Successfully logged out']);
    }
}
