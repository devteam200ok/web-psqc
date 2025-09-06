<div class="page-body px-xl-3">
    <div class="container-xl">
        <div class="row">
            @include('inc.component.message')
            <div class="col-xl-12 mb-2">
                <div class="card">
                    <div class="card-body" style="background-color: #e3e3e352;">
                        <div class="row">
                            @php
                                $files = [
                                    'logo_color_square_svg' => [
                                        'label' => 'Squared Color Logo (.svg)',
                                        'preview' => 'branding/logo_color_square.svg',
                                    ],
                                    'logo_color_svg' => [
                                        'label' => 'Color Logo (.svg)',
                                        'preview' => 'branding/logo_color.svg',
                                    ],
                                    'logo_white_square_svg' => [
                                        'label' => 'Squared White Logo (.svg)',
                                        'preview' => 'branding/logo_white_square.svg',
                                    ],
                                    'logo_white_svg' => [
                                        'label' => 'White Logo (.svg)',
                                        'preview' => 'branding/logo_white.svg',
                                    ],
                                    'logo_color_square_png' => [
                                        'label' => 'Squared Color Logo (PNG)',
                                        'preview' => 'branding/logo_color_512.png',
                                    ],
                                    'logo_color_png' => [
                                        'label' => 'Color Logo (PNG)',
                                        'preview' => 'branding/logo_color.png',
                                    ],
                                    'logo_white_square_png' => [
                                        'label' => 'Squared White Logo (PNG)',
                                        'preview' => 'branding/logo_white_512.png',
                                    ],
                                    'logo_white_png' => [
                                        'label' => 'White Logo (PNG)',
                                        'preview' => 'branding/logo_white.png',
                                    ],
                                ];
                            @endphp

                            @foreach ($files as $field => $info)
                                <div class="col-xl-3 mb-3">
                                    <label class="form-label" for="{{ $field }}">{{ $info['label'] }}</label>
                                    <input type="file" id="{{ $field }}" class="form-control"
                                        wire:model="{{ $field }}"
                                        accept="{{ Str::endsWith($field, 'svg') ? '.svg' : '.png' }}">
                                    @error($field)
                                        <div class="text-danger fs-xs mt-1">{{ $message }}</div>
                                    @enderror

                                    @if (Storage::disk('public')->exists($info['preview']))
                                        <div class="mt-3">
                                            <img src="{{ asset('storage/' . $info['preview']) }}" style="max-width:200px">
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="text-end mt-4">
                            <button wire:click="update" wire:loading.attr="disabled" class="btn btn-primary">
                                업데이트
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>