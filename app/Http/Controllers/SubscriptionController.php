<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name'  => 'nullable|string|max:100',
            'plan'  => 'required|in:free,basic,premium',
        ]);

        $existing = Subscription::where('email', $request->email)->first();

        if ($existing) {
            if ($existing->status === 'active') {
                return back()->with('subscription_error', 'This email is already subscribed!');
            }
            // Re-activate cancelled subscription
            $existing->update([
                'status'        => 'active',
                'plan'          => $request->plan,
                'name'          => $request->name ?? $existing->name,
                'cancelled_at'  => null,
                'subscribed_at' => now(),
            ]);
            return back()->with('subscription_success', 'Welcome back! Your subscription has been reactivated.');
        }

        Subscription::create([
            'email'         => $request->email,
            'name'          => $request->name,
            'plan'          => $request->plan,
            'status'        => 'active',
            'token'         => Subscription::generateToken(),
            'subscribed_at' => now(),
        ]);

        return back()->with('subscription_success', 'You\'ve successfully subscribed to BookHive!');
    }

    public function unsubscribe(string $token)
    {
        $subscription = Subscription::where('token', $token)->firstOrFail();

        $subscription->update([
            'status'       => 'cancelled',
            'cancelled_at' => now(),
        ]);

        return view('subscriptions.unsubscribed');
    }

    public function plans()
    {
        return view('subscriptions.plans');
    }
}
