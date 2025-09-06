@section('css')
@endsection

<div class="page-body px-xl-3">
    <div class="container-xl">
        <div class="row">
            @include('inc.component.message')

            <div class="col-xl-6 mb-2">
                <div class="card mb-3">
                    <div class="card-header">SEO 설정</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">SEO Title</label>
                            <input wire:model.defer="seo_title" type="text" class="form-control" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SEO Description</label>
                            <input wire:model.defer="seo_description" type="text" class="form-control" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SEO Keywords</label>
                            <input wire:model.defer="seo_keywords" type="text" class="form-control" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">SEO Author</label>
                            <input wire:model.defer="seo_author" type="text" class="form-control" />
                        </div>
                    </div>
                </div>
            </div>
             <div class="col-xl-6 mb-2">
                <div class="card mb-3">
                    <div class="card-header">Open Graph 설정</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">OG URL</label>
                            <input wire:model.defer="og_url" type="text" class="form-control" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">OG Type</label>
                            <input wire:model.defer="og_type" type="text" class="form-control" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">OG Title</label>
                            <input wire:model.defer="og_title" type="text" class="form-control" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">OG Description</label>
                            <input wire:model.defer="og_description" type="text" class="form-control" />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">OG Image (png)</label>
                            <input wire:model="new_og_image" type="file" class="form-control" accept="image/png" />
                            @if (Storage::disk('public')->exists($og_image))
                                <div class="mt-2">
                                    <img src="{{ asset('storage/' . $og_image) }}" style="max-width:200px;border:1px solid #ddd;" />
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end mb-3">
                    <button wire:click="update" wire:loading.attr="disabled" type="button" class="btn btn-primary">
                        업데이트
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@section('js')
@endsection
