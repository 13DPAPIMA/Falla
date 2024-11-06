<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function update(Request $request) // TODO: add email verification
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
}
