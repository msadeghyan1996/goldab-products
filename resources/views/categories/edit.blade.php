@extends('layouts.app')

@section('title', 'ویرایش دسته')
@section('page-heading', 'ویرایش دسته')

@section('content')
<div class="mb-4"><h1 class="h4">ویرایش {{ $category->name }}</h1></div>
<div class="card">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('categories.update', $category) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include('categories._form')
        </form>
    </div>
</div>
@endsection
