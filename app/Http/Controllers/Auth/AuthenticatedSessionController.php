<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // 🔐 reCAPTCHA v3 validation
        $recaptchaToken = $request->input('recaptcha_token');

        if (!$recaptchaToken) {
            return back()->withErrors([
                'recaptcha' => 'Missing reCAPTCHA token. Please try again.',
            ])->withInput();
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => config('services.recaptcha.secret_key'),
                'response' => $recaptchaToken,
                'remoteip' => $request->ip(),
            ]);

            if ($response->failed()) {
                return back()->withErrors([
                    'recaptcha' => 'Unable to validate reCAPTCHA at this time. Please try again later.',
                ])->withInput();
            }

            $result = $response->json();

            if (!($result['success'] ?? false) || ($result['score'] ?? 0) < 0.5) {
                return back()->withErrors([
                    'recaptcha' => 'reCAPTCHA verification failed. Please try again.',
                ])->withInput();
            }
        } catch (\Throwable $e) {
            // Log the error if needed: Log::error('reCAPTCHA failed', ['error' => $e->getMessage()]);
            return back()->withErrors([
                'recaptcha' => 'Server error during reCAPTCHA validation. Please try again later.',
            ])->withInput();
        }

        // ✅ Authenticate user
        $request->authenticate();

        // ⛔ Ban check before session regeneration
        if (Auth::user()?->banned_at) {
            Auth::guard('web')->logout();

            return back()->withErrors([
                'email' => 'Your account has been banned.',
            ]);
        }

        // 🔄 Regenerate session safely
        $request->session()->regenerate();

        // ✅ Redirect to intended page
        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}