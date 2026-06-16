@extends('layouts.customer')

@section('title', __('customer_portal.title'))

@section('style')
    <link rel="stylesheet" href="{{ asset('css/customer/portal.css') }}">
@endsection

@section('content')

<section class="portal-section">

    <div class="portal-header">
        <div>
            <span class="welcome-label">
                {{ __('customer_portal.welcome_label') }}
            </span>

            <h1>
                {{ __('customer_portal.hello', ['name' => $user->name]) }}
            </h1>

            <p>{{ __('customer_portal.subtitle') }}</p>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button class="logout-btn"
                onclick="return confirm('{{ __('customer_portal.logout_confirm') }}')">
                <i class="fa-solid fa-right-from-bracket"></i>
                {{ __('customer_portal.logout') }}
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert-error">
            {{ $errors->first() }}
        </div>
    @endif

    <div class="main-grid">

        <div class="left-column">
            <div class="portal-card">
                <h2>{{ __('customer_portal.my_demos') }}</h2>

                <div class="demo-list">
                    @forelse($demos as $demo)
                        <div class="demo-card">
                            @if($demo->image)
                                <div class="demo-card-image">
                                    <img src="{{ asset('uploads/' . $demo->image) }}" alt="{{ $demo->name }}">
                                </div>
                            @else
                                <div class="demo-card-image demo-card-placeholder">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif

                            <div class="demo-content">
                                <h3>{{ $demo->name }}</h3>
                                <p>{{ __('customer_portal.demo_available') }}</p>

                                @if($demo->admin_note)
                                    <div class="demo-note">
                                        <strong>{{ __('customer_portal.admin_note') }}</strong>
                                        <p>{{ $demo->admin_note }}</p>
                                    </div>
                                @endif
                            </div>

                            <a href="{{ $demo->demo_link }}" target="_blank" class="demo-link">
                                <i class="fas fa-up-right-from-square"></i>
                                {{ __('customer_portal.open_demo') }}
                            </a>
                        </div>
                    @empty
                        <p class="empty-text">
                            {{ __('customer_portal.no_demo') }}
                        </p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="right-column">
            <div class="portal-card chat-card">
                <h2>{{ __('customer_portal.contact_admin') }}</h2>

                <div class="chat-box" id="chatBox">
                    @forelse($messages as $message)
                        <div class="chat-message {{ $message->sender_type === 'customer' ? 'customer' : 'admin' }}">
                            <div class="message-content">
                                @if($message->message)
                                    <p>{{ $message->message }}</p>
                                @endif

                                @if($message->link)
                                    <a href="{{ $message->link }}" target="_blank" class="message-link">
                                        {{ $message->link }}
                                    </a>
                                @endif

                                @if($message->image)
                                    <a href="{{ asset('uploads/' . $message->image) }}" target="_blank">
                                        <img
                                            src="{{ asset('uploads/' . $message->image) }}"
                                            alt="{{ __('customer_portal.message_image_alt') }}"
                                            class="message-image">
                                    </a>
                                @endif

                                <span>{{ $message->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="empty-text">
                            {{ __('customer_portal.no_messages') }}
                        </p>
                    @endforelse

                    <div id="refreshNotice" class="chat-refresh">
                        <i class="fa-solid fa-arrow-rotate-right"></i>
                        {{ __('customer_portal.refresh_messages') }}
                    </div>
                </div>

                <form method="POST"
                      action="{{ route('customer.portal_chat.send') }}"
                      class="chat-form"
                      enctype="multipart/form-data">
                    @csrf

                    <textarea
                        name="message"
                        placeholder="{{ __('customer_portal.write_message') }}"
                    >{{ old('message') }}</textarea>

                    <input
                        type="url"
                        name="link"
                        placeholder="{{ __('customer_portal.optional_link') }}"
                        value="{{ old('link') }}">

                    <input type="file" name="image" accept="image/*">

                    <button type="submit" class="btn-main">
                        <i class="fa-solid fa-paper-plane"></i>
                        {{ __('customer_portal.send') }}
                    </button>
                </form>
            </div>
        </div>

    </div>

    <div class="portal-card profile-card">
        <h2>{{ __('customer_portal.my_info') }}</h2>

        <form method="POST" action="{{ route('customer.portal_profile.update') }}" class="profile-form">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>{{ __('customer_portal.name') }}</label>
                <input type="text" value="{{ $user->name }}" readonly>
            </div>

            <div class="form-group">
                <label>{{ __('customer_portal.email') }}</label>
                <input type="email" value="{{ $user->email }}" readonly>
            </div>

            <div class="form-group">
                <label>{{ __('customer_portal.phone') }}</label>
                <input
                    type="text"
                    name="phone"
                    value="{{ old('phone', $user->phone) }}"
                    placeholder="{{ __('customer_portal.your_phone') }}">
            </div>

            <div class="form-group">
                <label>{{ __('customer_portal.company') }}</label>
                <input
                    type="text"
                    name="company"
                    value="{{ old('company', $user->company) }}"
                    placeholder="{{ __('customer_portal.your_company') }}">
            </div>

            <div class="form-group">
                <label>{{ __('customer_portal.new_password') }}</label>
                <input
                    type="password"
                    name="password"
                    placeholder="{{ __('customer_portal.new_password_placeholder') }}">
            </div>

            <div class="form-group">
                <label>{{ __('customer_portal.confirm_password') }}</label>
                <input
                    type="password"
                    name="password_confirmation"
                    placeholder="{{ __('customer_portal.confirm_password_placeholder') }}">
            </div>

            <button type="submit" class="btn-main">
                <i class="fa-solid fa-arrow-rotate-right"></i>
                {{ __('customer_portal.update') }}
            </button>
        </form>
    </div>

</section>

<script>
// AUTO SCROLL CHAT TO LAST MESSAGES ////////////////////////
document.addEventListener('DOMContentLoaded', function () {
    const chatBox = document.getElementById('chatBox');

    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }
});

// REFRESH BUTTON ////////////////////////////////////////////

document.addEventListener('DOMContentLoaded', function () {
    const chatBox = document.getElementById('chatBox');
    const refreshNotice = document.getElementById('refreshNotice');

    // Scroll to bottom initially
    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    // Show after 10s
    setTimeout(() => {
        if (refreshNotice) {
            refreshNotice.style.display = 'block';

            // Scroll again so user sees it
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    }, 12000);

    // Click = refresh
    if (refreshNotice) {
        refreshNotice.addEventListener('click', () => {
            location.reload();
        });
    }
});

</script>


@endsection
