@extends('admin.layouts.app')

@section('title', 'Edit Achievement')
@section('page_title', 'Edit Achievement')
@section('page_subtitle', 'Update achievement information')

@section('content')

<div class="content-card">
    <form method="POST" action="{{ route('admin.achievements.update', $achievement) }}" 
        enctype="multipart/form-data">
        
        @csrf
        @method('PUT')

        @include('admin.achievements.form', [
            'achievement' => $achievement,
            'buttonText' => 'Update Achievement'
        ])
    </form>
</div>

@endsection
