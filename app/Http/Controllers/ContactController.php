<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\ContactMessage; 
use App\Mail\ContactMessageMail; 
use App\Mail\ContactConfirmation; 
use App\Http\Requests\ContactMessageRequest;
use Illuminate\Support\Facades\Http;

class ContactController extends Controller
{
    public function show()
    {
        return view('footer.contact');
    }

    public function submit(ContactMessageRequest $request)
    {
        // 🔐 reCAPTCHA v3 validation
        $recaptchaResponse = $request->input('recaptcha_token');

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $recaptchaResponse,
            'remoteip' => $request->ip(),
        ]);

        $result = $response->json();

        if (!($result['success'] ?? false) || ($result['score'] ?? 0) < 0.5) {
            return back()->withErrors([
                'recaptcha' => 'Failed reCAPTCHA validation. Please try again or contact us another way.',
            ])->withInput();
        }

        // ✅ Continue if passed
        $validated = $request->validated();

        $reference = strtoupper(Str::random(8));
        $validated['reference'] = $reference;

        ContactMessage::create($validated);

        Mail::to('inbox@compromised-internals.test')->send(new ContactMessageMail($validated));
        Mail::to($validated['email'])->send(new ContactConfirmation($validated));

        return redirect()->route('contact')->with('success', "Message sent successfully! Reference: #{$reference}");
    }
}
