<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>New Contact Form Message</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f3f4f6; padding: 24px;">
    <div style="max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.05);">

        <h2 style="color: #1f2937;">📨 New Contact Form Submission</h2>

        <table style="width: 100%; border-spacing: 0; margin-top: 16px; font-size: 15px;">
            <tr>
                <td style="font-weight: bold; color: #111827;">Reference:</td>
                <td>#{{ $data['reference'] }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; color: #111827;">Name:</td>
                <td>{{ $data['name'] }}</td>
            </tr>
            <tr>
                <td style="font-weight: bold; color: #111827;">Email:</td>
                <td>{{ $data['email'] }}</td>
            </tr>
        </table>

        <hr style="margin: 24px 0; border: none; border-top: 1px solid #e5e7eb;">

        <p style="font-weight: bold; color: #1f2937;">Message:</p>
        <p style="color: #374151; white-space: pre-line;">{{ $data['message'] }}</p>

        <p style="margin-top: 32px; font-size: 13px; color: #9ca3af;">
            This message was submitted through the Compromised Internals contact form.
        </p>
    </div>
</body>

</html>