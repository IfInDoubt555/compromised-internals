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
use Illuminate\Contracts\View\View;

class ContactController extends Controller
{
    /**
     * Show the contact form.
     */
    public function show(): View
    {
        return view(
            /** @var view-string $view */
            $view = 'footer.contact'
        );
    }

    /**
     * Handle the contact form submission.
     */
    public function submit(ContactMessageRequest $request): RedirectResponse
    {
        // The FormRequest has already validated everything (including reCAPTCHA).
        $data = $request->validated();

        // Build the payload—don't include recaptcha_token.
        /** @var array{name:string,email:string,message:string,reference:string} $payload */
        $payload = [
            'name'      => $data['name'],
            'email'     => $data['email'],
            'message'   => $data['message'],
            'reference' => strtoupper(Str::random(8)),
        ];

        // Persist to database.
        ContactMessage::create($payload);

        // Queue the notification emails.
        // Ensure QUEUE_CONNECTION is set and a worker is running.
        try {
            // Notify your inbox
            Mail::to(config('mail.from.address'))
                ->queue(new ContactMessageMail($payload));

            // Send confirmation to the user
            Mail::to($payload['email'])
                ->queue(new ContactConfirmation($payload));
        } catch (\Throwable $e) {
            Log::error(
                'Contact mail failed',
                ['ref' => $payload['reference'], 'error' => $e->getMessage()]
            );

            return back()
                ->withErrors('Sorry, we were unable to send your message. Please try again later.')
                ->withInput();
        }

        // Redirect back with success message
        return redirect()
            ->route('contact') // adjust if your route name differs
            ->with('success', "Message sent successfully! Your reference is #{$payload['reference']}.");
    }
}