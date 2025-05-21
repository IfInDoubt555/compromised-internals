<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactMessageRequest;
use App\Mail\ContactMessageMail;
use App\Mail\ContactConfirmation;
use App\Models\ContactMessage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;

class ContactController extends Controller
{
    /**
     * Show the contact form.
     */
    public function show()
    {
        return view('footer.contact');
    }

    /**
     * Handle the contact form submission.
     */
    public function submit(ContactMessageRequest $request): RedirectResponse
    {
        // The FormRequest has already validated everything (including reCAPTCHA).
        $data = $request->validated();

        // Build the payload—don't include recaptcha_token.
        $payload = [
            'name'      => $data['name'],
            'email'     => $data['email'],
            'message'   => $data['message'],
            'reference' => strtoupper(Str::random(8)),
        ];

        // Persist to database.
        ContactMessage::create($payload);

        // Queue the notification emails.
        // If you haven't already, ensure QUEUE_CONNECTION is set and `queue:work` is running.
        try {
            // Notify your inbox
            Mail::to(config('mail.from.address'))
                ->queue(new ContactMessageMail($payload));

            // Send confirmation to the user
            Mail::to($payload['email'])
                ->queue(new ContactConfirmation($payload));
        } catch (\Exception $e) {
            // Log the failure for later debugging
            Log::error("Contact mail failed [ref={$payload['reference']}]: {$e->getMessage()}");

            return back()
                ->withErrors('Sorry, we were unable to send your message. Please try again later.')
                ->withInput();
        }

        // Redirect back with success message
        return redirect()
            ->route('contact')
            ->with('success', "Message sent successfully! Your reference is #{$payload['reference']}.");
    }
}