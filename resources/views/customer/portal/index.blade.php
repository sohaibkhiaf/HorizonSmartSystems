@extends('layouts.customer')

@section('title', __('customer_portal.title'))

@section('style')
    <link rel="stylesheet" href="{{ asset('css/customer/portal.css') }}">
@endsection

@section('content')

<section class="portal-section {{ app()->getLocale() === 'ar' ? 'is-rtl' : 'is-ltr' }}">

    <div class="portal-header">
        <div>
            <h1>
                {{ __('customer_portal.hello', ['name' => $user->name]) }}
            </h1>
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

    <div class="demos-slider-card">

        @if($demos->count())
            <div class="demo-slider-shell">
                <button type="button" class="demo-nav demo-prev" aria-label="{{ __('customer_portal.previous') }}">
                    <i class="{{ app()->getLocale() === 'ar' ? 'fa-solid fa-angle-right' : 'fa-solid fa-angle-left' }}"></i>
                </button>

                <div class="swiper demos-swiper" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                    <div class="swiper-wrapper">
                        @foreach($demos as $demo)
                            @php
                                $software = $demo->publicSoftware;
                                $locale = app()->getLocale();

                                $softwareName = match ($locale) {
                                    'ar' => $software->ar_name ?? $software->fr_name ?? null,
                                    'en' => $software->en_name ?? $software->fr_name ?? null,
                                    default => $software->fr_name ?? null,
                                };
                            @endphp

                            <div class="swiper-slide">
                                @if(!empty($demo->demo_link))
                                    <a href="{{ $demo->demo_link . '&user='. auth()->user()->username }}" target="_blank" class="portal-demo-card">
                                @else
                                    <div class="portal-demo-card">
                                @endif

                                    <div class="portal-demo-image">
                                        @if($demo->image)
                                            <img
                                                src="{{ asset('uploads/' . $demo->image) }}"
                                                alt="{{ $demo->name }}"
                                                loading="lazy">
                                        @else
                                            <div class="portal-demo-placeholder">
                                                <i class="fa-solid fa-display"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="portal-demo-content">

                                        <h3>{{ $demo->name }}</h3>

                                        @if(!empty($demo->demo_link))
                                            <span class="portal-demo-link">
                                                {{ __('customer_portal.demo_show') }}
                                                <i class="{{ app()->getLocale() == 'ar' ? 'fa-solid fa-arrow-left' : 'fa-solid fa-arrow-right' }}"></i>
                                            </span>
                                        @else
                                            <span class="portal-demo-link muted">
                                                {{ __('customer_portal.demo_not_available') }}
                                            </span>
                                        @endif
                                    </div>

                                @if(!empty($demo->demo_link))
                                    </a>
                                @else
                                    </div>
                                @endif
                            </div>
                        @endforeach

                    </div>
                </div>

                <button type="button" class="demo-nav demo-next" aria-label="{{ __('customer_portal.next') }}">
                    <i class="{{ app()->getLocale() === 'ar' ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right' }}"></i>
                </button>
            </div>
        @else
            <p class="empty-text">
                {{ __('customer_portal.no_offers') }}
            </p>
        @endif
    </div>

    <div class="main-grid">
        <div class="right-column">
            <div class="portal-card chat-card">

                <form method="POST"
                      action="{{ route('customer.portal_chat.send') }}"
                      class="chat-form"
                      enctype="multipart/form-data">
                    @csrf

                    <div class="message-composer" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                        <textarea
                            name="message"
                            class="message-composer-textarea"
                            placeholder="{{ __('customer_portal.write_message') }}"
                        >{{ old('message') }}</textarea>

                        <div class="composer-side-tools">
                            <div class="file-picker composer-file-picker" data-file-picker>
                                <input
                                    type="file"
                                    name="image"
                                    id="customerMessageImage"
                                    accept="image/*"
                                    class="file-picker-input">

                                <label
                                    for="customerMessageImage"
                                    class="file-picker-btn composer-tool-btn"
                                    title="{{ __('customer_portal.attach_image') }}"
                                    aria-label="{{ __('customer_portal.attach_image') }}">
                                    <i class="fa-solid fa-image"></i>
                                    <span class="composer-tool-text">{{ __('customer_portal.attach_image') }}</span>
                                </label>

                                <span class="file-picker-name" data-default="{{ __('customer_portal.no_image_selected') }}">
                                    {{ __('customer_portal.no_image_selected') }}
                                </span>
                            </div>

                            <div class="voice-message-group">
                                <input
                                    type="file"
                                    name="voice_message"
                                    id="customerVoiceMessage"
                                    accept="audio/*,.webm,.ogg,.mp3,.wav,.m4a,.aac,.mp4"
                                    class="voice-file-input">

                                <div
                                    class="voice-recorder"
                                    data-target="customerVoiceMessage"
                                    data-empty-label="{{ __('customer_portal.no_voice_selected') }}"
                                    data-ready-label="{{ __('customer_portal.voice_ready') }}"
                                    data-recording-label="{{ __('customer_portal.recording_in_progress') }}"
                                    data-not-supported-label="{{ __('customer_portal.voice_not_supported') }}"
                                    data-microphone-error-label="{{ __('customer_portal.microphone_error') }}">

                                    <div class="voice-actions">
                                        <button
                                            type="button"
                                            class="voice-record-btn"
                                            data-action="record"
                                            title="{{ __('customer_portal.record_voice') }}"
                                            aria-label="{{ __('customer_portal.record_voice') }}">
                                            <i class="fa-solid fa-microphone"></i>
                                            <span class="composer-tool-text">{{ __('customer_portal.record_voice') }}</span>
                                        </button>

                                        <button
                                            type="button"
                                            class="voice-stop-btn"
                                            data-action="stop"
                                            title="{{ __('customer_portal.stop_recording') }}"
                                            aria-label="{{ __('customer_portal.stop_recording') }}"
                                            disabled>
                                            <i class="fa-solid fa-stop"></i>
                                            <span class="composer-tool-text">{{ __('customer_portal.stop_recording') }}</span>
                                        </button>

                                        <button
                                            type="button"
                                            class="voice-clear-btn"
                                            data-action="clear"
                                            title="{{ __('customer_portal.clear_voice') }}"
                                            aria-label="{{ __('customer_portal.clear_voice') }}"
                                            disabled>
                                            <i class="fa-solid fa-trash"></i>
                                            <span class="composer-tool-text">{{ __('customer_portal.clear_voice') }}</span>
                                        </button>
                                    </div>

                                    <span class="voice-status">
                                        {{ __('customer_portal.no_voice_selected') }}
                                    </span>

                                    <div class="voice-preview-wrap">
                                        <audio controls class="voice-preview"></audio>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn-main">
                        <i class="fa-solid fa-paper-plane"></i>
                        {{ __('customer_portal.send') }}
                    </button>
                </form>

                <div class="chat-box" id="chatBox">

                    <div id="refreshNotice" class="chat-refresh">
                        <i class="fa-solid fa-arrow-rotate-right"></i>
                        {{ __('customer_portal.refresh_messages') }}
                    </div>

                    @php
                        $linkifyMessage = function ($text) {
                            $text = (string) $text;
                            $pattern = '~(?<!@)\b((?:https?://|www\.)[^\s<]+|(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,}(?:[/?#][^\s<]*)?)~iu';
                            $result = '';
                            $offset = 0;

                            if (preg_match_all($pattern, $text, $matches, PREG_OFFSET_CAPTURE)) {
                                foreach ($matches[0] as $match) {
                                    [$rawUrl, $position] = $match;

                                    $result .= e(substr($text, $offset, $position - $offset));

                                    $url = $rawUrl;
                                    $trailing = '';

                                    while ($url !== '' && in_array(substr($url, -1), ['.', ',', ';', ':', '!', '?', ')'], true)) {
                                        $trailing = substr($url, -1) . $trailing;
                                        $url = substr($url, 0, -1);
                                    }

                                    $href = preg_match('~^https?://~i', $url) ? $url : 'https://' . $url;

                                    $result .= '<a href="' . e($href) . '" target="_blank" rel="noopener noreferrer" class="message-inline-link">' . e($url) . '</a>';
                                    $result .= e($trailing);

                                    $offset = $position + strlen($rawUrl);
                                }
                            }

                            return $result . e(substr($text, $offset));
                        };
                    @endphp

                    @forelse($messages as $message)
                        <div class="chat-message {{ $message->sender_type === 'customer' ? 'customer' : 'admin' }}">
                            <div class="message-content">
                                @if($message->message)
                                    <p>{!! $linkifyMessage($message->message) !!}</p>
                                @endif

                                @if($message->link)
                                    <a href="{{ $message->link }}" target="_blank" rel="noopener noreferrer" class="message-link">
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

                </div>
            
                @if ($messages->hasPages())
                    <div class="navigator-container">
                        @if ($messages->currentPage() > 1)
                            <a href="{{ $messages->previousPageUrl() }}">
                                <i class="{{ app()->getLocale() === 'ar' ? 'fa-solid fa-angle-right' : 'fa-solid fa-angle-left' }}"></i>
                            </a>
                        @else
                            <a href="{{ $messages->url($messages->currentPage()) }}" class="disabled">
                                <i class="{{ app()->getLocale() === 'ar' ? 'fa-solid fa-angle-right' : 'fa-solid fa-angle-left' }}"></i>
                            </a>
                        @endif

                        @for ($i = 1; $i <= $messages->lastPage(); $i++)
                            <a href="{{ $messages->url($i) }}"
                                class="{{ $messages->currentPage() == $i ? 'active' : '' }}">
                                {{ $i }}
                            </a>
                        @endfor

                        @if ($messages->currentPage() < $messages->lastPage())
                            <a href="{{ $messages->nextPageUrl() }}">
                                <i class="{{ app()->getLocale() === 'ar' ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right' }}"></i>
                            </a>
                        @else
                            <a href="{{ $messages->url($messages->currentPage()) }}" class="disabled">
                                <i class="{{ app()->getLocale() === 'ar' ? 'fa-solid fa-angle-left' : 'fa-solid fa-angle-right' }}"></i>
                            </a>
                        @endif
                    </div>
                @endif
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

    initDemosSwiper();

    setupFilePickers();
    setupVoiceRecorders();

    setTimeout(() => {
        if (refreshNotice) {
            refreshNotice.style.display = 'block';
        }
    }, 5000);

    if (refreshNotice) {
        refreshNotice.addEventListener('click', () => {
            location.reload();
        });
    }
});

function initDemosSwiper() {
    const swiperElement = document.querySelector('.demos-swiper');

    if (!swiperElement || typeof Swiper === 'undefined') {
        return;
    }

    const slidesCount = swiperElement.querySelectorAll('.swiper-slide').length;

    new Swiper(swiperElement, {
        slidesPerView: 1.05,
        spaceBetween: 14,
        speed: 650,
        rewind: true,
        autoplay: slidesCount > 1 ? {
            delay: 1500,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        } : false,
        navigation: {
            nextEl: '.demo-next',
            prevEl: '.demo-prev',
        },
        breakpoints: {
            560: {
                slidesPerView: 3,
                spaceBetween: 14,
            },
            900: {
                slidesPerView: 4,
                spaceBetween: 16,
            },
            1100: {
                slidesPerView: 5,
                spaceBetween: 18,
            },
            1300: {
                slidesPerView: 6,
                spaceBetween: 18,
            },
        },
    });
}

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

    if (!file) {
        name.textContent = name.dataset.default || '';
        name.removeAttribute('title');
        picker.classList.remove('has-file');
        return;
    }

    name.textContent = file.name;
    name.title = file.name;
    picker.classList.add('has-file');
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

        /*
            Important:
            For best layout protection, Blade should be:

            <div class="voice-preview-wrap">
                <audio controls class="voice-preview"></audio>
            </div>

            This JS also works if only .voice-preview exists,
            but the wrapper is safer for phone width.
        */
        const previewWrap = wrapper.querySelector('.voice-preview-wrap');
        const preview = wrapper.querySelector('.voice-preview');

        if (!recordBtn || !stopBtn || !clearBtn || !status) return;

        const labels = {
            empty: wrapper.dataset.emptyLabel || 'No voice message',
            ready: wrapper.dataset.readyLabel || 'Voice message ready',
            recording: wrapper.dataset.recordingLabel || 'Recording...',
            notSupported: wrapper.dataset.notSupportedLabel || 'Recording is not supported in this browser.',
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

        function hidePreview() {
            if (preview) {
                preview.pause();
                preview.removeAttribute('src');
                preview.load();
                preview.classList.remove('is-visible');
            }

            if (previewWrap) {
                previewWrap.classList.remove('is-visible');
            }
        }

        function showPreview(file) {
            if (!preview || !file) return;

            previewUrl = URL.createObjectURL(file);

            preview.pause();
            preview.src = previewUrl;
            preview.preload = 'metadata';
            preview.load();

            if (previewWrap) {
                previewWrap.classList.add('is-visible');
            }

            preview.classList.add('is-visible');
        }

        function setPreviewFromFile(file, ready = false) {
            resetPreviewUrl();

            if (!file) {
                hidePreview();
                wrapper.classList.remove('has-voice', 'is-recording', 'is-unavailable');

                status.textContent = labels.empty;
                clearBtn.disabled = true;
                updateFilePickerName(fileInput, null);

                return;
            }

            showPreview(file);
            wrapper.classList.add('has-voice');
            wrapper.classList.remove('is-recording', 'is-unavailable');

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
            wrapper.classList.add('is-unavailable');
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
                    wrapper.classList.remove('is-recording');
                    stopStream();

                    recordBtn.disabled = false;
                    stopBtn.disabled = true;
                });

                mediaRecorder.start();

                wrapper.classList.add('is-recording');
                wrapper.classList.remove('has-voice', 'is-unavailable');
                recordBtn.disabled = true;
                stopBtn.disabled = false;
                clearBtn.disabled = true;
                status.textContent = labels.recording;

            } catch (error) {
                stopStream();

                wrapper.classList.remove('is-recording', 'has-voice');
                wrapper.classList.add('is-unavailable');
                recordBtn.disabled = false;
                stopBtn.disabled = true;
                clearBtn.disabled = false;
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
