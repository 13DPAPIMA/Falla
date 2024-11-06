<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
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
}
