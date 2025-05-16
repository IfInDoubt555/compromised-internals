<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>We Received Your Message</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; padding: 24px;">
    <div style="max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">

        <h2 style="color: #1f2937;">Thanks for reaching out, {{ $data['name'] }}!</h2>

        <p style="color: #374151;">We’ve received your message and we’re already looking into it. Please allow 24–72 hours for a response.</p>

        <p style="color: #374151;">Your reference number is: <strong>#{{ $data['reference'] }}</strong></p>

        <hr style="margin: 24px 0; border: none; border-top: 1px solid #e5e7eb;">

        <p style="font-weight: bold; color: #1f2937;">Your message:</p>
        <p style="color: #374151; white-space: pre-line;">{{ $data['message'] }}</p>

        <p style="margin-top: 24px; color: #374151;">We appreciate your patience and interest in Compromised Internals. Talk soon!</p>

        <p style="margin-top: 16px; color: #6b7280;">— The CI Team</p>
    </div>
</body>

</html>