@extends('layouts.app') @section('title','مدیر جدید') @section('page-heading','ایجاد مدیر')
@section('content')<div class="mb-4"><h1 class="h4">ایجاد مدیر جدید</h1></div><div class="card"><div class="card-body p-4"><form method="POST" action="{{ route('admins.store') }}">@csrf @include('admins._form')</form></div></div>@endsection
