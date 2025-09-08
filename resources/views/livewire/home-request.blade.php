@section('title')
    <title>📮 Contact Us – Partnership·Feedback·Manual Testing Request | Web-PSQC</title>
    <meta name="description"
        content="Web-PSQC Contact: Partnership proposals, service feedback, manual testing requests, and more. Our team will respond promptly. PDF, image, and ZIP attachments supported.">
    <meta name="keywords"
        content="Web-PSQC contact, customer service, partnership proposal, service feedback, manual testing request, website testing inquiry">
    <meta name="author" content="Web-PSQC">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="{{ url()->current() }}" />

    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="Web-PSQC" />
    <meta property="og:title" content="Contact Us – Partnership·Feedback·Manual Testing Request | Web-PSQC" />
    <meta property="og:description"
        content="We welcome partnership proposals, feedback, and manual testing requests. Text, image, PDF, and ZIP attachments supported with prompt responses." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="Web-PSQC Contact Us" />
    @endif

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="Contact Us – Partnership·Feedback·Manual Testing Request | Web-PSQC" />
    <meta name="twitter:description"
        content="We welcome all inquiries related to Web-PSQC. Our team will respond promptly." />
    @if ($setting && $setting->og_image)
        <meta name="twitter:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
    @endif

    {{-- JSON-LD: Organization --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => 'Web-PSQC',
    'url'  => url('/'),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    {{-- JSON-LD: ContactPage (+ ContactAction) --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'ContactPage',
    'name' => 'Contact Us',
    'url'  => url()->current(),
    'description' => 'Page for submitting inquiries related to Web-PSQC including partnership proposals, service feedback, and manual testing requests.',
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'Web-PSQC',
        'url'  => url('/'),
    ],
    'potentialAction' => [
        '@type' => 'ContactAction',
        'target' => url()->current(),
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>
@endsection

@section('css')
@endsection

<div>
    <section class="py-5">
        <div class="container">
            @include('inc.component.message')
            <h2 class="h1 text-center mb-4">Contact Us</h2>

            <div class="text-center mb-4">
                <p class="lead fw-semibold">"Feel free to share your questions or suggestions with us."</p>
                <p class="text-muted">
                    We welcome all inquiries related to Web-PSQC including partnership proposals, service feedback, and
                    manual testing requests.<br>
                    Text, images, and attachments are all accepted. Our team will review and respond promptly.
                </p>
            </div>

            <form wire:submit.prevent="submit" class="card mx-auto p-4 shadow-sm" style="max-width: 720px;"
                enctype="multipart/form-data">
                {{-- Name --}}
                <div class="mb-4">
                    <label for="name" class="form-label">Name *</label>
                    <input type="text" id="name" wire:model.defer="name" class="form-control" required>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-4">
                    <label for="email" class="form-label">Email Address *</label>
                    <input type="email" id="email" wire:model.defer="email" class="form-control" required>
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="mb-4">
                    <label for="description" class="form-label">Inquiry Details *</label>
                    <textarea id="description" wire:model.defer="description" class="form-control" rows="5" required></textarea>
                    @error('description')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- File Upload --}}
                <div class="mb-4" x-data="{ progress: 0 }" x-on:livewire-upload-start="progress = 0"
                    x-on:livewire-upload-progress="progress = $event.detail.progress"
                    x-on:livewire-upload-finish="progress = 100"
                    x-on:livewire-upload-error="progress = 0; alert('File upload failed. Please try again.')">

                    <label for="file" class="form-label">
                        attachments (Optional) - PDF, JPG, PNG, ZIP / Max 10MB
                    </label>

                    <input type="file" id="file" wire:model="file" class="form-control"
                        accept=".pdf,.jpg,.jpeg,.png,.zip" />

                    {{-- Upload Progress Display --}}
                    <div class="progress mt-2" x-show="progress > 0 && progress < 100">
                        <div class="progress-bar" role="progressbar" :style="'width: ' + progress + '%'"
                            aria-valuemin="0" aria-valuemax="100">
                            <span x-text="progress + '%'"></span>
                        </div>
                    </div>

                    @error('file')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- Submit Button --}}
                <div class="text-center">
                    <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled"
                        wire:target="file,submit">
                        Submit Inquiry
                    </button>
                </div>
            </form>

            {{-- Success Message --}}
            @if (session()->has('success'))
                <div class="mx-auto mt-4" style="max-width: 720px;">
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                </div>
            @endif
        </div>
    </section>
</div>

@section('js')
@endsection
