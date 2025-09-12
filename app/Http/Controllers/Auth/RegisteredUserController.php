<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        // ---- 0) Honeypot: reject if hidden field is filled (bots love this)
        // Add <input type="text" name="nickname" class="hidden" tabindex="-1" autocomplete="off">
        if (filled($request->input('nickname'))) {
            throw ValidationException::withMessages([
                'nickname' => 'Spam detected.',
            ]);
        }

        // ---- 1) reCAPTCHA server-side verification (defense-in-depth)
        // Accept either v2 "g-recaptcha-response" or v3 "recaptcha_token"
        $captchaToken = $request->input('recaptcha_token') ?? $request->input('g-recaptcha-response');

        if (blank($captchaToken)) {
            throw ValidationException::withMessages([
                'captcha' => 'Captcha token missing.',
            ]);
        }

        $secret = config('services.recaptcha.secret');
        if (blank($secret)) {
            // Fail closed if misconfigured in production
            throw ValidationException::withMessages([
                'captcha' => 'Captcha configuration error.',
            ]);
        }

        $verify = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret'   => $secret,
            'response' => $captchaToken,
            'remoteip' => $request->ip(),
        ])->json();

        // Accept v2 (success only) and v3 (success + score threshold) responses
        $passed = ($verify['success'] ?? false) &&
                  (!array_key_exists('score', $verify) || ($verify['score'] ?? 0) >= (float)config('services.recaptcha.threshold', 0.5));

        if (! $passed) {
            throw ValidationException::withMessages([
                'captcha' => 'Captcha verification failed. Please try again.',
            ]);
        }

        // Optional: if you’re using v3 with an "action", enforce it:
        // if (($verify['action'] ?? null) !== 'register') { ... }

        // ---- 2) Validate the rest via your FormRequest
        $validated = $request->validated();

        // ---- 3) Disposable/blocked email domain filter
        $email = strtolower(trim($validated['email']));
        $domain = Str::after($email, '@');

        $blocked = config('auth.blocked_email_domains', [
            // Common throwaways
            'mailinator.com', '10minutemail.com', 'guerrillamail.com', 'tempmail.com',
            'yopmail.com', 'mail.tm', 'temp-mail.org', 'fakeinbox.com', 'trashmail.com',
        ]);

        if (in_array($domain, $blocked, true)) {
            throw ValidationException::withMessages([
                'email' => 'This email domain is not allowed.',
            ]);
        }

        // ---- 4) Normalize name
        $name = trim($validated['name']);

        // ---- 5) Create user safely
        $user = DB::transaction(function () use ($name, $email, $validated) {
            return User::create([
                'name'     => $name,
                'email'    => $email,
                'password' => Hash::make($validated['password']),
            ]);
        });

        event(new Registered($user));

        // Login then send to verification notice (routes should protect unverified users)
        Auth::login($user);

        return redirect()->route('verification.notice');
    }
}