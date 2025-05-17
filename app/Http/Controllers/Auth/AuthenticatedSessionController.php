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
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $recaptchaToken = $request->input('recaptcha_token');

        if (!$recaptchaToken) {
            Log::warning('Missing reCAPTCHA token.');
            return back()->withErrors([
                'recaptcha' => 'Missing reCAPTCHA token. Please try again.',
            ])->withInput();
        }

        Log::info('reCAPTCHA token received', ['token' => $recaptchaToken]);

        try {
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => config('services.recaptcha.secret_key'), // ✅ correct use of secret_key
                'response' => $recaptchaToken,
                'remoteip' => $request->ip(),
            ]);

            $result = $response->json();
            Log::info('reCAPTCHA API result', $result);

            if (!($result['success'] ?? false)) {
                return back()->withErrors([
                    'recaptcha' => 'reCAPTCHA failed: ' . implode(', ', $result['error-codes'] ?? ['unknown']),
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
                'recaptcha' => 'Server error validating reCAPTCHA. Try again later.',
            ])->withInput();
        }

        try {
            $request->authenticate();
            Log::info('Login success for ' . $request->input('email'));
        } catch (ValidationException $e) {
            Log::warning('Login failed for ' . $request->input('email'));
            throw $e;
        }

        if (Auth::user()?->banned_at) {
            Log::warning('Banned user attempted login: ' . Auth::user()->email);
            Auth::guard('web')->logout();

            return back()->withErrors([
                'email' => 'Your account has been banned.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}