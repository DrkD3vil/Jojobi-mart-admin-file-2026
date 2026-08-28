<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|max:255',
        ]);

        NewsletterSubscriber::firstOrCreate(
            ['email' => $data['email']],
            ['subscribed_at' => now()]
        );

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => "You're subscribed!"]);
        }

        return back()->with('success', "You're subscribed!");
    }
}
