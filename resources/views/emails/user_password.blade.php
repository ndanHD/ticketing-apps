<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if ($type === 'new')
            Welcome Email!
        @else
            Reset Password
        @endif
    </title>
    <style>
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            background-color: #f4f4f7;
            color: #51545E;
            margin: 0;
            padding: 0;
        }

        .email-wrapper {
            width: 100%;
            background-color: #f4f4f7;
            padding: 20px 0;
        }

        .email-content {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .email-header {
            background-color: #3869D4;
            color: #ffffff;
            padding: 20px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }

        .email-body {
            padding: 30px;
            line-height: 1.6;
        }

        .credentials-table {
            width: 100%;
            max-width: 400px;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #F0F4FF;
            border-radius: 6px;
        }

        .credentials-table td {
            padding: 10px 15px;
            font-weight: bold;
        }

        .credentials-table td.label {
            text-align: left;
            width: 50%;
        }


        .button-container {
            text-align: center;
            margin: 30px 0;
        }

        .button {
            background-color: #3869D4;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: bold;
            display: inline-block;
        }

        .footer {
            font-size: 12px;
            color: #A8AAAF;
            text-align: center;
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="email-wrapper">
        <div class="email-content">
            <div class="email-header">
                {{ config('app.name') }}
            </div>
            <div class="email-body">
                <h2>Hello {{ $name }},</h2>
                <p>
                    @if ($type === 'new')
                        Welcome! Your account has been successfully created. You can log in using the credentials below:
                    @else
                        Your password has been reset. Please use the temporary password below to login:
                    @endif
                </p>

                <table class="credentials-table">
                    <tr>
                        <td class="label">Username</td>
                        <td>:</td>
                        <td class="value">{{ $email }}</td>
                    </tr>
                    <tr>
                        <td class="label">Temporary Password:</td>
                        <td>:</td>
                        <td class="value">{{ $password }}</td>
                    </tr>
                </table>

                <div class="button-container">
                    <a href="{{ url('/login') }}" class="button">Log In to Your Account</a>
                </div>

                <p>For security reasons, you will be <strong>prompted to change your password immediately upon first
                        login</strong>. Please make sure to choose a strong and secure password.</p>

                <p>If you did not create this account, please ignore this email.</p>

                <p>Thank you,<br>{{ config('app.name') }}</p>
            </div>
        </div>

        <div class="footer">
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
    </div>
</body>

</html>
