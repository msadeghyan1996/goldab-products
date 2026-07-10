@extends('layouts.app')

@section('title', 'دسته جدید')
@section('page-heading', 'ایجاد دسته')

@section('content')
<div class="mb-4"><h1 class="h4">ایجاد دسته‌بندی</h1></div>
<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('categories.store') }}" enctype="multipart/form-data">
            @csrf
            @include('categories._form')
        </form>
    </div>
</div>
@endsection
