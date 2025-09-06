<div>
    <div class="mb-4">
        <div class="input-group input-group-flat">
            <span class="input-group-text">
                <i class="icon icon-search"></i>
            </span>
            <input wire:model.live.debounce.250ms="SearchIcon" type="text" class="form-control"
                placeholder="Search icons..." />
        </div>
    </div>

    <div class="row row-deck">
        @foreach ($icons as $icon)
            <div class="col-xl-2 col-lg-3 col-md-4 col-6 mb-3">
                <div class="card card-sm">
                    <div class="card-body px-2 text-center">
                        <i class="{{ $icon->icon }}" style="font-size: 28px;"></i>
                        <button wire:click="selectIcon('{{ $icon->icon }}')"
                            class="btn btn-outline-secondoary btn-sm text-truncate mt-3 px-2 w-100">
                            {{ $icon->title }}
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $icons->onEachSide(0)->links() }}
    </div>
</div>
