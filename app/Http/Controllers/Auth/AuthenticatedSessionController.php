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
        $recaptchaToken = $request->input('recaptcha_token');

        if (!$recaptchaToken) {
            return back()->withErrors([
                'recaptcha' => 'Missing reCAPTCHA token. Please refresh and try again.',
            ])->withInput();
        }

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => config('services.recaptcha.secret_key'),
                'response' => $recaptchaToken,
                'remoteip' => $request->ip(),
            ]);

            if ($response->failed()) {
                Log::error('reCAPTCHA HTTP failure', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                return back()->withErrors([
                    'recaptcha' => 'reCAPTCHA server error. Please try again later.',
                ])->withInput();
            }

            $result = $response->json();

            if (!($result['success'] ?? false)) {
                Log::warning('reCAPTCHA failed', ['response' => $result]);

                return back()->withErrors([
                    'recaptcha' => 'reCAPTCHA verification failed. Error: ' . implode(', ', $result['error-codes'] ?? ['unknown']),
                ])->withInput();
            }

            if (($result['score'] ?? 0) < 0.5) {
                return back()->withErrors([
                    'recaptcha' => 'Suspicious activity detected. Please try again.',
                ])->withInput();
            }
        } catch (\Throwable $e) {
            Log::error('reCAPTCHA exception', ['message' => $e->getMessage()]);
            return back()->withErrors([
                'recaptcha' => 'A server error occurred during login. Try again later.',
            ])->withInput();
        }

        // ✅ User authentication
        $request->authenticate();

        // ⛔ Banned user check
        if (Auth::user()?->banned_at) {
            Auth::guard('web')->logout();

            return back()->withErrors([
                'email' => 'Your account has been banned.',
            ]);
        }

        // 🔄 Regenerate session
        $request->session()->regenerate();

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