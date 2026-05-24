@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Overview of your website content')

@section('content')

<div class="stats-grid">

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-briefcase"></i>
        </div>
        <div>
            <h3>{{ $achievementsCount }}</h3>
            <p>Total Achievements</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fas fa-eye"></i>
        </div>
        <div>
            <h3>{{ $publishedAchievementsCount }}</h3>
            <p>Published Achievements</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-clock"></i>
        </div>
        <div>
            <h3>{{ $waitingMessagesCount }}</h3>
            <p>Waiting Messages</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success-icon">
            <i class="fas fa-check"></i>
        </div>
        <div>
            <h3>{{ $seenMessagesCount }}</h3>
            <p>Seen Messages</p>
        </div>
    </div>

</div>

<div class="admin-tabs">
    <a href="{{ route('admin.realisations.create') }}" class="tab-card">
        <i class="fas fa-plus"></i>
        <h3>Create Achievement</h3>
        <p>Add a new project to your website.</p>
    </a>

    <a href="{{ route('admin.realisations.index') }}" class="tab-card">
        <i class="fas fa-pen"></i>
        <h3>Edit Achievement</h3>
        <p>Update, publish, or delete existing projects.</p>
    </a>

    <a href="{{ route('admin.messages.waiting') }}" class="tab-card">
        <i class="fas fa-envelope"></i>
        <h3>Waiting Messages</h3>
        <p>Check new contact messages.</p>
    </a>

    <a href="{{ route('admin.messages.seen') }}" class="tab-card">
        <i class="fas fa-check-circle"></i>
        <h3>Seen Messages</h3>
        <p>Manage already reviewed messages.</p>
    </a>
</div>

@endsection

