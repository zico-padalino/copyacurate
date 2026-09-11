@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
<div class="login-card">
    <div class="login-brand">
        <div class="login-logo">{{ $appName }}</div>
        <p>{{ $tagline }}</p>
    </div>

    @if ($errors->any())
        <div class="flash flash-error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="login-form">
        @csrf
        <div>
            <label class="muted" for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email', 'budi@kabarbanten.test') }}" required autofocus>
        </div>
        <div>
            <label class="muted" for="password">Kata sandi</label>
            <input id="password" type="password" name="password" required>
        </div>
        <label class="remember">
            <input type="checkbox" name="remember" value="1" @checked(old('remember'))>
            Ingat saya
        </label>
        <button class="button" type="submit" style="width:100%">Masuk</button>
    </form>
    <p class="login-hint muted">Demo: budi@kabarbanten.test / password</p>
</div>
@endsection
