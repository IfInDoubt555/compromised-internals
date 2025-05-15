<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\ContactMessage; 
use App\Mail\ContactMessageMail; 
use App\Mail\ContactConfirmation; 
use App\Http\Requests\ContactMessageRequest;

class ContactController extends Controller
{
    public function show()
    {
        return view('footer.contact');
    }

    public function submit(ContactMessageRequest $request)
    {
        $validated = $request->validated();

        $reference = strtoupper(Str::random(8));
        $validated['reference'] = $reference;

        ContactMessage::create($validated);

        Mail::to('inbox@compromised-internals.test')->send(new ContactMessageMail($validated));
        Mail::to($validated['email'])->send(new ContactConfirmation($validated));

        return redirect()->route('contact')->with('success', "Message sent successfully! Reference: #{$reference}");
    }
}
