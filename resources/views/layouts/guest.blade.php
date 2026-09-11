<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $appName }} - @yield('title', 'Masuk')</title>
    @include('partials.styles')
    <style>
        body{min-height:100vh;display:grid;place-items:center;background:
            radial-gradient(circle at top left, #2f6b4d 0, transparent 42%),
            linear-gradient(160deg, #0f2f22 0%, #123c2a 45%, #1a4d36 100%);padding:24px}
        .login-card{width:100%;max-width:420px;background:#fff;border-radius:14px;padding:34px 32px;box-shadow:0 20px 50px #00000033}
        .login-brand{margin-bottom:26px}
        .login-logo{font:700 30px Georgia,serif;color:#143b2a;margin-bottom:6px}
        .login-brand p{margin:0;color:#748078;font-size:14px}
        .login-form{display:grid;gap:14px}
        .login-form label.muted{display:block;margin-bottom:6px}
        .login-form input[type=email],.login-form input[type=password]{width:100%;border:1px solid #d9e3db;border-radius:8px;padding:12px;font:inherit}
        .remember{display:flex;align-items:center;gap:8px;font-size:13px;color:#5d6b62}
        .login-hint{margin:18px 0 0;text-align:center}
    </style>
</head>
<body>
@yield('content')
</body>
</html>
