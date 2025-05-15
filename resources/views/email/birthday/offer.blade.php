<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Happy Birthday!</title>
</head>

<body style="font-family: sans-serif; background: #f1f5f9; padding: 2rem; color: #111827;">
    <div style="max-width: 600px; margin: 0 auto; background: white; padding: 2rem; border-radius: 8px;">
        <h1 style="font-size: 1.75rem; margin-bottom: 1rem;">🎉 Happy Birthday, {{ $user->profile->display_name ?? $user->name }}!</h1>
        <p>We’re excited to celebrate with you. Here's a special offer to enjoy:</p>

        <div style="background-color: #e0f2fe; padding: 1rem; margin: 1rem 0; border-left: 4px solid #3b82f6;">
            <p><strong>Use Code:</strong> <code>{{ $offerCode }}</code></p>
            <p><strong>Offer:</strong> 20% off anything in the <a href="{{ url('/shop') }}">Shop</a></p>
            <p><strong>Valid for:</strong> 3 days</p>
        </div>

        <p>
            <a href="{{ url('/shop') }}"
                style="display: inline-block; background-color: #3b82f6; color: white; padding: 0.75rem 1.25rem; border-radius: 6px; text-decoration: none;">
                Claim Your Discount
            </a>
        </p>

        <p style="margin-top: 2rem;">Stay fast,<br><strong>Compromised Internals Team</strong></p>
    </div>
</body>

</html>