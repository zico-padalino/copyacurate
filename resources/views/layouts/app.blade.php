<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $appName }} - @yield('title', 'Dashboard Keuangan')</title>
    @include('partials.styles')
</head>
<body>
<div class="shell">
    @include('partials.sidebar')
    <main class="main">
        <header class="topbar">
            <div class="crumb">@yield('crumb')</div>
            <div class="profile">
                <div>
                    <div>{{ $user->name }}</div>
                    <div class="muted">{{ ucfirst($user->role) }}</div>
                </div>
                <span class="avatar">{{ collect(explode(' ', $user->name))->map(fn ($part) => strtoupper(substr($part, 0, 1)))->take(2)->implode('') }}</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="button ghost small" type="submit">Keluar</button>
                </form>
            </div>
        </header>
        <section class="content">
            @if (session('success'))
                <div class="flash">✓ {{ session('success') }}</div>
            @endif
            @if ($errors->any())
                <div class="flash flash-error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
            @yield('content')
        </section>
    </main>
</div>
@include('partials.rupiah-input')
</body>
</html>
