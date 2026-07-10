@extends('layouts.app') @section('title','ویرایش مدیر') @section('page-heading','ویرایش مدیر')
@section('content')<div class="mb-4"><h1 class="h4">ویرایش {{ $admin->name }}</h1></div><div class="card"><div class="card-body p-4"><form method="POST" action="{{ route('admins.update',$admin) }}">@csrf @method('PUT') @include('admins._form')</form></div></div>@endsection
