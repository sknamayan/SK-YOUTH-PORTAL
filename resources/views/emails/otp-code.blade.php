<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="only light">
    <title>SK Namayan Digital Registry - Email Verification</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #0f172a;">
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #f8fafc; padding: 40px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 540px; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05);">
                    
                    <!-- Header -->
                    <tr>
                        <td style="padding: 28px 36px; background-color: #1e3a8a; text-align: center;">
                            <div style="font-size: 11px; font-weight: 800; color: #93c5fd; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 4px;">BARANGAY NAMAYAN YOUTH PORTAL</div>
                            <div style="font-size: 20px; font-weight: 900; color: #ffffff; text-transform: uppercase; letter-spacing: -0.5px;">SK Namayan Digital Registry</div>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 36px; text-align: center; background-color: #ffffff;">
                            <h2 style="margin: 0 0 12px 0; font-size: 18px; font-weight: 800; color: #0f172a;">Verify Your Account Email</h2>
                            <p style="margin: 0 0 24px 0; font-size: 14px; color: #475569; line-height: 1.6;">
                                Mabuhay, <strong>{{ $firstName }}</strong>! Please use the 6-digit verification code below or click the direct verification button to activate your account.
                            </p>

                            <!-- OTP Text Block -->
                            <div style="background-color: #f1f5f9; border: 2px dashed #94a3b8; border-radius: 12px; padding: 20px; margin-bottom: 24px;">
                                <div style="font-size: 10px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 6px;">ONE-TIME VERIFICATION CODE</div>
                                <div style="font-family: 'Courier New', Courier, monospace; font-size: 36px; font-weight: 900; color: #1e3a8a; letter-spacing: 10px;">{{ $otp }}</div>
                            </div>

                            <!-- Direct Verification Link Button -->
                            @if(isset($verificationUrl))
                                <div style="margin-bottom: 24px;">
                                    <a href="{{ $verificationUrl }}" style="display: inline-block; padding: 14px 32px; background-color: #2563eb; color: #ffffff; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; text-decoration: none; border-radius: 12px;">
                                        Click to Verify Automatically &rarr;
                                    </a>
                                </div>
                            @endif

                            <p style="margin: 0 0 8px 0; font-size: 12px; font-weight: 700; color: #d97706;">
                                This code and link will expire in 10 minutes.
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                                If you did not request this verification, no further action is required.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 20px 36px; background-color: #f8fafc; border-top: 1px solid #f1f5f9; text-align: center;">
                            <p style="margin: 0 0 4px 0; font-size: 11px; font-weight: 800; color: #64748b; text-transform: uppercase; letter-spacing: 1px;">
                                Sangguniang Kabataan Barangay Namayan
                            </p>
                            <p style="margin: 0; font-size: 11px; color: #94a3b8;">
                                City of Mandaluyong, Metro Manila, Philippines
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
