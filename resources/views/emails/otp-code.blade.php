<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SK Namayan Digital Registry - OTP Verification</title>
</head>
<body style="margin: 0; padding: 0; background-color: #0f172a; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased;">
    <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="background-color: #0f172a; padding: 40px 10px;">
        <tr>
            <td align="center">
                <!-- Main Container Card -->
                <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0" style="max-width: 560px; background-color: #1e293b; border: 1px solid #334155; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5);">
                    
                    <!-- Header Branding Banner -->
                    <tr>
                        <td style="padding: 32px 40px; background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%); text-align: center; border-bottom: 1px solid #3b82f6;">
                            <table role="presentation" width="100%" border="0" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td align="center">
                                        <div style="display: inline-block; padding: 10px 20px; background-color: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 50px; margin-bottom: 12px;">
                                            <span style="color: #60a5fa; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px;">Barangay Namayan Youth Portal</span>
                                        </div>
                                        <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 900; letter-spacing: -0.5px; text-transform: uppercase;">SK Namayan Digital Registry</h1>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Body Content -->
                    <tr>
                        <td style="padding: 40px; text-align: center;">
                            <h2 style="margin: 0 0 12px 0; color: #f8fafc; font-size: 20px; font-weight: 800;">Verify Your Email Address</h2>
                            <p style="margin: 0 0 28px 0; color: #94a3b8; font-size: 14px; line-height: 1.6;">
                                Mabuhay, <strong style="color: #cbd5e1;">{{ $firstName }}</strong>! Thank you for registering with the SK Namayan Digital Registry. Please use the 6-digit verification code below to activate your account.
                            </p>

                            <!-- OTP Box -->
                            <div style="background-color: #0f172a; border: 2px dashed #3b82f6; border-radius: 16px; padding: 24px 16px; margin-bottom: 28px;">
                                <span style="display: block; color: #64748b; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 8px;">One-Time Password Code</span>
                                <span style="color: #60a5fa; font-family: 'Courier New', Courier, monospace; font-size: 38px; font-weight: 900; letter-spacing: 12px; line-height: 1;">{{ $otp }}</span>
                            </div>

                            <p style="margin: 0 0 8px 0; color: #f59e0b; font-size: 12px; font-weight: 700;">
                                ⏳ This verification code will expire in 10 minutes.
                            </p>
                            <p style="margin: 0; color: #64748b; font-size: 12px; line-height: 1.5;">
                                If you did not initiate this registration request, please disregard this email.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 24px 40px; background-color: #0f172a; border-top: 1px solid #1e293b; text-align: center;">
                            <p style="margin: 0 0 6px 0; color: #64748b; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
                                Sangguniang Kabataan Barangay Namayan
                            </p>
                            <p style="margin: 0; color: #475569; font-size: 11px;">
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
