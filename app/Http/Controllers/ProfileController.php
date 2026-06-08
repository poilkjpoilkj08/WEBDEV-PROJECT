<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $userRoles = $user->roles->pluck('role')->toArray();
        return view('profile.show', compact('user', 'userRoles'));
    }

    public function edit()
    {
        $user = Auth::user();
        $userRoles = $user->roles->pluck('role')->toArray();
        return view('profile.edit', compact('user', 'userRoles'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'saved_latitude' => 'nullable|numeric|between:-90,90',
            'saved_longitude' => 'nullable|numeric|between:-180,180',
            'saved_street' => 'nullable|string|max:255',
            'saved_postal_code' => 'nullable|string|max:20',
            'saved_province' => 'nullable|string|max:100',
            'saved_city' => 'nullable|string|max:100',
            'saved_district' => 'nullable|string|max:100',
        ]);

        $user->update($validated);

        return response()->json(['success' => true, 'message' => 'Profile updated']);
    }
}