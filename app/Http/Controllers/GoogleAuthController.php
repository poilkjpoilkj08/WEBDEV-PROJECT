<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class GoogleAuthController extends Controller
{
    /**
     * Redirect to Google for authentication
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Handle Google callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Check if user exists by google_id
            $user = User::where('google_id', $googleUser->getId())->first();

            if ($user) {
                // User exists, log them in
                Auth::login($user, true);
                return redirect('/dashboard')->with('success', 'Welcome back! Logged in with Google.');
            } else {
                // Check if email already exists (from previous email/password registration)
                $existingUser = User::where('email', $googleUser->getEmail())->first();

                if ($existingUser) {
                    // Link Google ID to existing account
                    $existingUser->update(['google_id' => $googleUser->getId()]);
                    Auth::login($existingUser, true);
                    return redirect('/dashboard')->with('success', 'Google ID linked to your account!');
                } else {
                    // Create new user with Google data
                    $newUser = User::create([
                        'name' => $googleUser->getName(),
                        'email' => $googleUser->getEmail(),
                        'google_id' => $googleUser->getId(),
                        'password' => bcrypt('google_' . $googleUser->getId()), // Random password for OAuth users
                    ]);

                    Auth::login($newUser, true);
                    return redirect('/dashboard')->with('success', 'Account created and logged in with Google!');
                }
            }
        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Failed to authenticate with Google. Please try again.');
        }
    }
}
