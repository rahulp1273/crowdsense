<!DOCTYPE html>
<html>
<head>
    <style>
        .email-container {
            font-family: 'Inter', sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 40px;
            background-color: #f8faff;
            border-radius: 24px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .otp-box {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .otp-code {
            font-size: 36px;
            font-weight: 900;
            letter-spacing: 10px;
            color: #4f46e5;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #94a3b8;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h2 style="font-weight: 900; tracking: -1px;">CrowdSense</h2>
            <p style="color: #64748b;">Secure Profile Update</p>
        </div>
        <div class="otp-box">
            <p style="font-weight: 600; color: #1e293b;">Your verification code for profile changes:</p>
            <div class="otp-code">{{ $otp }}</div>
            <p style="font-size: 14px; color: #64748b;">This code will expire in 10 minutes.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} CrowdSense. All rights reserved.
        </div>
    </div>
</body>
</html>
