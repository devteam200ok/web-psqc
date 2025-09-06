@section('title')
    <title>📮 문의하기 – 제휴·피드백·수동 검사 요청 | DevTeam Test</title>
    <meta name="description"
        content="DevTeam Test 문의하기: 제휴 제안, 서비스 피드백, 수동 검사 요청 등 무엇이든 남겨주세요. 담당자가 확인 후 신속히 답변드립니다. PDF·이미지·ZIP 첨부 가능.">
    <meta name="keywords" content="DevTeam Test 문의, 고객센터, 제휴 제안, 서비스 피드백, 수동 검사 요청, 웹사이트 테스트 문의">
    <meta name="author" content="DevTeam Co., Ltd.">
    <meta name="robots" content="index,follow">
    <link rel="canonical" href="{{ url()->current() }}" />

    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta property="og:site_name" content="DevTeam Test" />
    <meta property="og:title" content="문의하기 – 제휴·피드백·수동 검사 요청 | DevTeam Test" />
    <meta property="og:description" content="제휴·피드백·수동 검사 요청을 환영합니다. 텍스트·이미지·PDF·ZIP 첨부 가능하며 신속히 답변드립니다." />
    @php $setting = \App\Models\Setting::first(); @endphp
    @if ($setting && $setting->og_image)
        <meta property="og:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
        <meta property="og:image:alt" content="DevTeam Test 문의하기" />
    @endif

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="문의하기 – 제휴·피드백·수동 검사 요청 | DevTeam Test" />
    <meta name="twitter:description" content="DevTeam Test와 관련된 모든 문의를 환영합니다. 담당자가 신속히 답변드립니다." />
    @if ($setting && $setting->og_image)
        <meta name="twitter:image" content="{{ url('/') }}/storage/{{ $setting->og_image }}" />
    @endif

    {{-- JSON-LD: Organization --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => 'DevTeam Co., Ltd.',
    'url'  => url('/'),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    {{-- JSON-LD: ContactPage (+ ContactAction) --}}
    <script type="application/ld+json">
{!! json_encode([
    '@' . 'context' => 'https://schema.org',
    '@type' => 'ContactPage',
    'name' => '문의하기',
    'url'  => url()->current(),
    'description' => '제휴 제안, 서비스 피드백, 수동 검사 요청 등 DevTeam Test 관련 문의를 접수하는 페이지입니다.',
    'isPartOf' => [
        '@type' => 'WebSite',
        'name' => 'DevTeam Test',
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
            <h2 class="h1 text-center mb-4">문의하기</h2>

            <div class="text-center mb-4">
                <p class="lead fw-semibold">"궁금한 점이나 제안 사항을 자유롭게 말씀해 주세요."</p>
                <p class="text-muted">
                    제휴 제안, 서비스 피드백, 수동 검사 요청 등 DevTeam Test와 관련된 모든 문의를 환영합니다.<br>
                    텍스트, 그림, 첨부파일 모두 가능합니다. 담당자가 확인 후 신속히 답변 드리겠습니다.
                </p>
            </div>

            <form wire:submit.prevent="submit" class="card mx-auto p-4 shadow-sm" style="max-width: 720px;"
                enctype="multipart/form-data">
                {{-- 이름 --}}
                <div class="mb-4">
                    <label for="name" class="form-label">이름 *</label>
                    <input type="text" id="name" wire:model.defer="name" class="form-control" required>
                    @error('name')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- 이메일 --}}
                <div class="mb-4">
                    <label for="email" class="form-label">이메일 주소 *</label>
                    <input type="email" id="email" wire:model.defer="email" class="form-control" required>
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- 설명 --}}
                <div class="mb-4">
                    <label for="description" class="form-label">문의 내용 *</label>
                    <textarea id="description" wire:model.defer="description" class="form-control" rows="5" required></textarea>
                    @error('description')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- 파일 업로드 --}}
                <div class="mb-4" x-data="{ progress: 0 }" x-on:livewire-upload-start="progress = 0"
                    x-on:livewire-upload-progress="progress = $event.detail.progress"
                    x-on:livewire-upload-finish="progress = 100" x-on:livewire-upload-error="progress = 0">
                    <label for="file" class="form-label">
                        첨부파일 (선택사항) - PDF, JPG, PNG, ZIP / 최대 10MB
                    </label>
                    <input type="file" id="file" wire:model="file" class="form-control"
                        accept=".pdf,.jpg,.png,.zip" />

                    {{-- 업로드 진행률 표시 --}}
                    <div class="progress mt-2" x-show="progress > 0">
                        <div class="progress-bar" role="progressbar" :style="'width: ' + progress + '%'"
                            aria-valuemin="0" aria-valuemax="100">
                            <span x-text="progress + '%'"></span>
                        </div>
                    </div>

                    @error('file')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                {{-- 제출 버튼 --}}
                <div class="text-center">
                    <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled"
                        wire:target="file,submit">
                        문의 제출하기
                    </button>
                </div>
            </form>

            {{-- 성공 메시지 --}}
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
