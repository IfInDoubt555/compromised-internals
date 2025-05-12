<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use App\Models\ContactMessage; // 👈 Model for DB storage
use App\Mail\ContactMessageMail; // 👈 Mailable to admin
use App\Mail\ContactConfirmation; // 👈 Mailable to user

class ContactController extends Controller
{
    public function show()
    {
        return view('footer.contact');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email',
            'message' => 'required|string|min:10',
        ]);

        // Generate a unique reference ID for tracking
        $reference = strtoupper(Str::random(8));
        $validated['reference'] = $reference;

        // Save to database
        ContactMessage::create($validated);

        // Send email to admin and confirmation to user
        Mail::to('inbox@compromised-internals.test')->send(new ContactMessageMail($validated));
        Mail::to($validated['email'])->send(new ContactConfirmation($validated));

        // Redirect with success
        return redirect()->route('contact')->with('success', "Message sent successfully! Reference: #{$reference}");
    }
}
