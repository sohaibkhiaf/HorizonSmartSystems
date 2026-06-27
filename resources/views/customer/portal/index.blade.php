@extends('layouts.customer')

@section('title', __('customer_portal.title'))

@section('style')
    <link rel="stylesheet" href="{{ asset('css/customer/portal.css') }}">
@endsection

@section('content')

<section class="portal-section {{ app()->getLocale() === 'ar' ? 'is-rtl' : 'is-ltr' }}">

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
            <div class="portal-card offers-card">
                <h2>{{ __('customer_portal.my_offers') }}</h2>

                <div class="demo-list">
                    @forelse($demos as $demo)

                        @if(!empty($demo->demo_link))
                            <a href="{{ $demo->demo_link }}" target="_blank"
                            class="demo-card demo-card-clickable">
                        @else
                            <div class="demo-card">
                        @endif

                            <div class="demo-main">
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
                                </div>
                            </div>

                        @if(!empty($demo->demo_link))
                                <span class="demo-link">
                                    {{ __('customer_portal.demo_show') }}
                                    <i class="{{ app()->getLocale() == 'ar' ? 'fa-solid fa-arrow-left' : 'fa-solid fa-arrow-right' }}"></i>
                                </span>
                            </a>
                        @else
                            </div>
                        @endif

                    @empty
                        <p class="empty-text">
                            {{ __('customer_portal.no_offers') }}
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

                                @if($message->voice_message)
                                    <audio controls preload="metadata" class="message-audio">
                                        <source src="{{ asset('uploads/' . $message->voice_message) }}">
                                        {{ __('customer_portal.audio_not_supported') }}
                                    </audio>
                                @endif

                                <span>
                                    {{ $message->sender_display_name }}
                                    →
                                    {{ $message->receiver_display_name }}
                                    ·
                                    {{ $message->created_at->format('d/m/Y H:i') }}
                                </span>
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
                        class="chat-link-input"
                        placeholder="{{ __('customer_portal.optional_link') }}"
                        value="{{ old('link') }}">

                    <div class="chat-attachments">
                        <div class="file-picker" data-file-picker>
                            <input
                                type="file"
                                name="image"
                                id="customerMessageImage"
                                accept="image/*"
                                class="file-picker-input">

                            <label for="customerMessageImage" class="file-picker-btn">
                                <i class="fa-solid fa-image"></i>
                                {{ __('customer_portal.attach_image') }}
                            </label>

                            <span class="file-picker-name" data-default="{{ __('customer_portal.no_image_selected') }}">
                                {{ __('customer_portal.no_image_selected') }}
                            </span>
                        </div>

                        <div class="file-picker" data-file-picker>
                            <input
                                type="file"
                                name="voice_message"
                                id="customerVoiceMessage"
                                accept="audio/*,.webm,.ogg,.mp3,.wav,.m4a,.aac,.mp4"
                                class="file-picker-input">

                            <label for="customerVoiceMessage" class="file-picker-btn">
                                <i class="fa-solid fa-file-audio"></i>
                                {{ __('customer_portal.attach_voice_file') }}
                            </label>

                            <span class="file-picker-name" data-default="{{ __('customer_portal.no_voice_selected') }}">
                                {{ __('customer_portal.no_voice_selected') }}
                            </span>
                        </div>
                    </div>

                    <div class="voice-message-group">
                        <div
                            class="voice-recorder"
                            data-target="customerVoiceMessage"
                            data-empty-label="{{ __('customer_portal.no_voice_selected') }}"
                            data-ready-label="{{ __('customer_portal.voice_ready') }}"
                            data-recording-label="{{ __('customer_portal.recording_in_progress') }}"
                            data-not-supported-label="{{ __('customer_portal.voice_not_supported') }}"
                            data-microphone-error-label="{{ __('customer_portal.microphone_error') }}">

                            <div class="voice-actions">
                                <button type="button" class="voice-record-btn" data-action="record">
                                    <i class="fa-solid fa-microphone"></i>
                                    <span>{{ __('customer_portal.record_voice') }}</span>
                                </button>

                                <button type="button" class="voice-stop-btn" data-action="stop" disabled>
                                    <i class="fa-solid fa-stop"></i>
                                    <span>{{ __('customer_portal.stop_recording') }}</span>
                                </button>

                                <button type="button" class="voice-clear-btn" data-action="clear" disabled>
                                    <i class="fa-solid fa-trash"></i>
                                    <span>{{ __('customer_portal.clear_voice') }}</span>
                                </button>
                            </div>

                            <span class="voice-status">
                                {{ __('customer_portal.no_voice_selected') }}
                            </span>

                            <audio controls class="voice-preview"></audio>
                        </div>
                    </div>

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
                <label>{{ __('customer_portal.username') }}</label>
                <input type="text" value="{{ $user->username }}" readonly>
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
document.addEventListener('DOMContentLoaded', function () {
    const chatBox = document.getElementById('chatBox');
    const refreshNotice = document.getElementById('refreshNotice');

    if (chatBox) {
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    setupFilePickers();
    setupVoiceRecorders();

    setTimeout(() => {
        if (refreshNotice && chatBox) {
            refreshNotice.style.display = 'block';
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    }, 5000);

    if (refreshNotice) {
        refreshNotice.addEventListener('click', () => {
            location.reload();
        });
    }
});

function setupFilePickers() {
    const fileInputs = document.querySelectorAll('.file-picker-input');

    fileInputs.forEach(input => {
        input.addEventListener('change', function () {
            updateFilePickerName(input, input.files[0] ?? null);
        });
    });
}

function updateFilePickerName(input, file) {
    const picker = input.closest('[data-file-picker]');

    if (!picker) return;

    const name = picker.querySelector('.file-picker-name');

    if (!name) return;

    name.textContent = file ? file.name : (name.dataset.default || '');
}

function setupVoiceRecorders() {
    const recorders = document.querySelectorAll('.voice-recorder');

    recorders.forEach(wrapper => {
        const inputId = wrapper.dataset.target;
        const fileInput = document.getElementById(inputId);

        if (!fileInput) return;

        const recordBtn = wrapper.querySelector('[data-action="record"]');
        const stopBtn = wrapper.querySelector('[data-action="stop"]');
        const clearBtn = wrapper.querySelector('[data-action="clear"]');
        const status = wrapper.querySelector('.voice-status');
        const preview = wrapper.querySelector('.voice-preview');

        const labels = {
            empty: wrapper.dataset.emptyLabel || 'No voice message',
            ready: wrapper.dataset.readyLabel || 'Voice message ready',
            recording: wrapper.dataset.recordingLabel || 'Recording...',
            notSupported: wrapper.dataset.notSupportedLabel || 'Recording is not supported. Upload an audio file instead.',
            microphoneError: wrapper.dataset.microphoneErrorLabel || 'Microphone unavailable or permission denied.',
        };

        let mediaRecorder = null;
        let chunks = [];
        let stream = null;
        let previewUrl = null;

        function getRecordingMimeType() {
            if (!window.MediaRecorder) {
                return '';
            }

            const types = [
                'audio/webm;codecs=opus',
                'audio/webm',
                'audio/ogg;codecs=opus',
                'audio/ogg',
                'audio/mp4'
            ];

            return types.find(type => MediaRecorder.isTypeSupported(type)) || '';
        }

        function getExtension(mimeType) {
            if (mimeType.includes('ogg')) return 'ogg';
            if (mimeType.includes('mp4')) return 'm4a';

            return 'webm';
        }

        function stopStream() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;
            }
        }

        function resetPreviewUrl() {
            if (previewUrl) {
                URL.revokeObjectURL(previewUrl);
                previewUrl = null;
            }
        }

        function setPreviewFromFile(file, ready = false) {
            resetPreviewUrl();

            if (!file) {
                preview.removeAttribute('src');
                preview.style.display = 'none';
                status.textContent = labels.empty;
                clearBtn.disabled = true;
                updateFilePickerName(fileInput, null);

                return;
            }

            previewUrl = URL.createObjectURL(file);
            preview.src = previewUrl;
            preview.style.display = 'block';
            status.textContent = ready ? labels.ready : file.name;
            clearBtn.disabled = false;
            updateFilePickerName(fileInput, file);
        }

        function clearVoice() {
            fileInput.value = '';
            setPreviewFromFile(null);
        }

        fileInput.addEventListener('change', function () {
            setPreviewFromFile(fileInput.files[0] ?? null);
        });

        clearBtn.addEventListener('click', clearVoice);

        if (!navigator.mediaDevices || !window.MediaRecorder) {
            recordBtn.disabled = true;
            status.textContent = labels.notSupported;
            return;
        }

        recordBtn.addEventListener('click', async function () {
            try {
                clearVoice();

                stream = await navigator.mediaDevices.getUserMedia({
                    audio: true
                });

                chunks = [];

                const mimeType = getRecordingMimeType();

                mediaRecorder = mimeType
                    ? new MediaRecorder(stream, { mimeType })
                    : new MediaRecorder(stream);

                mediaRecorder.addEventListener('dataavailable', function (event) {
                    if (event.data && event.data.size > 0) {
                        chunks.push(event.data);
                    }
                });

                mediaRecorder.addEventListener('stop', function () {
                    const finalMimeType = mimeType || 'audio/webm';
                    const extension = getExtension(finalMimeType);

                    const blob = new Blob(chunks, {
                        type: finalMimeType
                    });

                    const file = new File(
                        [blob],
                        'voice-message-' + Date.now() + '.' + extension,
                        { type: finalMimeType }
                    );

                    const transfer = new DataTransfer();
                    transfer.items.add(file);
                    fileInput.files = transfer.files;

                    setPreviewFromFile(file, true);
                    stopStream();

                    recordBtn.disabled = false;
                    stopBtn.disabled = true;
                });

                mediaRecorder.start();

                recordBtn.disabled = true;
                stopBtn.disabled = false;
                clearBtn.disabled = true;
                status.textContent = labels.recording;

            } catch (error) {
                stopStream();

                recordBtn.disabled = false;
                stopBtn.disabled = true;
                status.textContent = labels.microphoneError;
            }
        });

        stopBtn.addEventListener('click', function () {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                mediaRecorder.stop();
            }
        });
    });
}
</script>

@endsection

