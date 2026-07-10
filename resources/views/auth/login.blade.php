<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ورود به پنل مدیریت</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    <link href="{{ asset('css/brand-font.css') }}" rel="stylesheet">
</head>
<body><div class="login-page"><div class="card login-card p-3 p-md-4">
    <div class="card-body">
        <div class="text-center mb-4"><div class="display-6 text-primary"><i class="bi bi-shield-lock"></i></div><h1 class="h4 mt-2">ورود مدیران</h1><p class="text-muted small">شماره موبایل و رمز عبور خود را وارد کنید</p></div>
        <form method="POST" action="{{ route('login.store') }}">@csrf
            <div class="mb-3"><label class="form-label required" for="mobile">شماره موبایل</label><input dir="ltr" id="mobile" name="mobile" value="{{ old('mobile') }}" class="form-control @error('mobile') is-invalid @enderror" inputmode="numeric" autocomplete="username" autofocus>@error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="mb-3"><label class="form-label required" for="password">رمز عبور</label><input dir="ltr" type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="current-password">@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="form-check mb-4"><input type="hidden" name="remember" value="0"><input class="form-check-input" type="checkbox" name="remember" value="1" id="remember"><label class="form-check-label" for="remember">مرا به خاطر بسپار</label></div>
            <button class="btn btn-primary w-100 py-2" type="submit">ورود به پنل</button>
        </form>
    </div>
</div></div></body></html>
