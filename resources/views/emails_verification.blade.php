<!-- resources/views/emails/verification.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Email Verification</title>
</head>
<body>
    <h1>Email Verification</h1>
    <p>This is the verification code.</p>
    <p>Your OTP for registration is: {{ $token }}</p>
</body>
</html>