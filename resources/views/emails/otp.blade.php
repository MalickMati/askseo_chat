<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your OTP Code</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f4f7;
            margin: 0;
            padding: 0;
        }

        .email-wrapper {
            max-width: 600px;
            margin: 30px auto;
            background-color: #ffffff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .logo {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #472C6F;
            margin-bottom: 20px;
        }

        .otp-box {
            background-color: #f0f0ff;
            border: 1px dashed #472C6F;
            font-size: 24px;
            text-align: center;
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
            letter-spacing: 2px;
            font-weight: bold;
            color: #333;
        }

        .footer {
            font-size: 12px;
            color: #888;
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="logo">ASK SEO</div>

        <p>Hi there,</p>

        <p>Your One-Time Password (OTP) is below. Use this to verify your email address:</p>

        <div class="otp-box">{{ $otp }}</div>

        <p>This code is valid for the next 10 minutes.</p>

        <p>If you did not request this code, please ignore this email.</p>

        <p>Thank you,<br><strong>The ASK SEO Team</strong></p>

        <div class="footer">
            © {{ now()->year }} ASK SEO. All rights reserved.
        </div>
    </div>
</body>
</html>