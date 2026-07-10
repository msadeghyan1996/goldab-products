<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'پنل مدیریت') | {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    <link href="{{ asset('css/brand-font.css') }}" rel="stylesheet">
</head>
<body>
<aside class="sidebar">
    <div class="sidebar-brand"><i class="bi bi-grid-1x2-fill ms-2"></i> مدیریت محصولات</div>
    <nav class="nav flex-column py-3">
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2 ms-2"></i>داشبورد</a>
        <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}"><i class="bi bi-box-seam ms-2"></i>محصولات</a>
        <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}"><i class="bi bi-diagram-3 ms-2"></i>دسته‌بندی‌ها</a>
        <a class="nav-link {{ request()->routeIs('admins.*') ? 'active' : '' }}" href="{{ route('admins.index') }}"><i class="bi bi-people ms-2"></i>مدیران</a>
    </nav>
</aside>
<main class="main">
    <header class="topbar d-flex align-items-center justify-content-between px-3 px-lg-4">
        <button type="button" class="btn btn-light d-lg-none" data-sidebar-toggle><i class="bi bi-list"></i></button>
        <div class="d-none d-lg-block text-muted">@yield('page-heading', 'پنل مدیریت')</div>
        <div class="d-flex align-items-center gap-3 me-auto">
            <span class="small"><i class="bi bi-person-circle ms-1"></i>{{ auth()->user()->name }}</span>
            <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-sm btn-outline-secondary" type="submit"><i class="bi bi-box-arrow-left ms-1"></i>خروج</button></form>
        </div>
    </header>
    <div class="content container-fluid">
        @if ($errors->has('admin') || $errors->has('category'))
            <div class="alert alert-danger">{{ $errors->first('admin') ?: $errors->first('category') }}</div>
        @endif
        @yield('content')
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('js/admin.js') }}"></script>
@if(session('success'))
<script>document.addEventListener('DOMContentLoaded', () => Swal.fire({toast:true,position:'top-start',icon:'success',title:@json(session('success')),showConfirmButton:false,timer:3500,timerProgressBar:true}));</script>
@endif
@stack('scripts')
</body>
</html>
