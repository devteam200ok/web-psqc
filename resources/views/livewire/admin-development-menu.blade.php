@section('css')
@endsection
<div class="page-body px-xl-3">
    <div class="container-xl">
        @include('inc.component.message')
        <div class="row">
            {{-- 1st Menu --}}
            <div class="col-xl-6 mb-3">
                <h3 class="card-title">1st Menu</h3>
                @if (count($parents) > 0)
                    <div class="card mb-2">
                        <div class="list-group list-group-flush">
                            @foreach ($parents as $menu)
                                <li
                                    class="list-group-item d-flex align-items-center {{ $parent_id == $menu->id ? 'active' : '' }}">
                                    <div class="me-3">
                                        {{ $loop->index + 1 }}
                                    </div>
                                    <div>
                                        @if ($menu->type == 'group')
                                            <h4 wire:click="selectParent({{ $menu->id }})" class="mb-0 align-middle"
                                                style="cursor: pointer">
                                                <i class="me-1 {{ $menu->icon }}" style="font-size:16px"></i>
                                                {{ $menu->title }}
                                                ({{ App\Models\Menu::where('menu_id', $menu->id)->count() }})
                                            </h4>
                                        @else
                                            <h4 class="mb-0 align-middle">
                                                <i class="me-1 {{ $menu->icon }}" style="font-size:16px"></i>
                                                {{ $menu->title }}
                                            </h4>
                                        @endif
                                        <span class="fs-9">
                                            {{ url('/') }}/{{ $menu->role }}/{{ $menu->url }}
                                        </span>
                                    </div>
                                    <span class="fs-9 ms-auto">
                                        @if ($loop->index != 0)
                                            <i wire:click.stop="upParent({{ $menu->id }})" class="ti ti-arrow-up"
                                                style="cursor: pointer">
                                            </i>
                                        @endif
                                        @if ($loop->index != count($parents) - 1)
                                            <i wire:click.stop="downParent({{ $menu->id }})"
                                                class="ti ti-arrow-down ms-2" style="cursor: pointer">
                                            </i>
                                        @endif
                                        <i wire:click.stop="editParent({{ $menu->id }})" class="ti ti-edit ms-2"
                                            style="cursor: pointer">
                                        </i>
                                        <i wire:click.stop="deleteMenu({{ $menu->id }})"
                                            wire:confirm="Are you sure you want to delete this menu?"
                                            class="ti ti-trash ms-2" style="cursor: pointer">
                                        </i>
                                    </span>
                                </li>
                            @endforeach
                        </div>
                    </div>
                @endif
                <div class="my-3">
                    <div class="mt-2 input-group">
                        <span class="input-group-text" style="min-width:100px">
                            {{ config('app.url') }}/
                        </span>
                        <input wire:model="role" type="text" class="form-control" placeholder="role">
                        <span class="input-group-text">/</span>
                        <input wire:model="url" type="text" class="form-control" placeholder="url">
                    </div>

                    <div class="mt-2 input-group">
                        <span class="input-group-text" style="min-width:40px;cursor: pointer;" data-bs-toggle="modal"
                            data-bs-target="#adminDevelopmentIcon">
                            @if ($icon != '')
                                <i class="{{ $icon }}" style="font-size:20px"></i>
                            @else
                                Icon
                            @endif
                        </span>
                        <input wire:model="title" type="text" class="form-control" placeholder="Menu Title">
                        <button wire:click="storeParent" class="btn btn-primary px-3">
                            {{ $menu_id == 0 ? 'Add' : 'Update' }}
                        </button>
                    </div>
                    <div class="mt-2">
                        <label class="form-check">
                            <input wire:model.change="isGroup" class="form-check-input" type="checkbox" />
                            <span class="form-check-label">Group Type</span>
                        </label>
                    </div>
                </div>
            </div>
            @if ($parent_id != 0)
                {{-- 2nd Menu --}}
                <div class="col-xl-6 mb-3">
                    <h3 class="card-title">2nd Menu</h3>
                    @if (count($children) > 0)
                        <div class="card mb-2">
                            <div class="list-group list-group-flush">
                                @foreach ($children as $menu)
                                    <li
                                        class="list-group-item d-flex align-items-center {{ $menu_id == $menu->id ? 'active' : '' }}">
                                        <div class="me-3">
                                            {{ $loop->index + 1 }}
                                        </div>
                                        <div>
                                            @if ($menu->type == 'group')
                                                <h4 wire:click="selectParent({{ $menu->id }})"
                                                    class="mb-0 align-middle text-primary" style="cursor: pointer">
                                                    <i class="me-1 {{ $menu->icon }}" style="font-size:16px"></i>
                                                    {{ $menu->title }}
                                                    ({{ App\Models\Menu::where('menu_id', $menu->id)->count() }})
                                                </h4>
                                            @else
                                                <h4 class="mb-0 align-middle">
                                                    <i class="me-1 {{ $menu->icon }}" style="font-size:16px"></i>
                                                    {{ $menu->title }}
                                                </h4>
                                            @endif
                                            <span class="fs-9">
                                                {{ url('/') }}/{{ $parent->role }}/{{ $parent->url }}/{{ $menu->url }}
                                            </span>
                                        </div>
                                        <span class="fs-9 ms-auto">
                                            @if ($loop->index != 0)
                                                <i wire:click.stop="upChild({{ $menu->id }})"
                                                    class="ti ti-arrow-up" style="cursor: pointer">
                                                </i>
                                            @endif
                                            @if ($loop->index != count($parents) - 1)
                                                <i wire:click.stop="downChild({{ $menu->id }})"
                                                    class="ti ti-arrow-down ms-2" style="cursor: pointer">
                                                </i>
                                            @endif
                                            <i wire:click.stop="editChild({{ $menu->id }})"
                                                class="ti ti-edit ms-2" style="cursor: pointer">
                                            </i>
                                            @if (App\Models\Menu::where('menu_id', $menu->id)->count() == 0)
                                                <i wire:click.stop="deleteMenu({{ $menu->id }})"
                                                    wire:confirm="Are you sure you want to delete this menu?"
                                                    class="ti ti-trash ms-2" style="cursor: pointer">
                                                </i>
                                            @endif
                                        </span>
                                    </li>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div class="my-3">
                        <div class="mt-2 input-group">
                            <span class="input-group-text" style="min-width:240px">
                                {{ config('app.url') }}/{{ $parent->role }}/{{ $parent->url }}/
                            </span>
                            <input wire:model="child_url" type="text" class="form-control" placeholder="url">
                        </div>
                        <div class="mt-2 input-group">
                            <input wire:model="child_title" type="text" class="form-control" placeholder="Menu Title">
                            <button wire:click="storeChild" class="btn btn-primary px-3">
                                {{ $child_id == 0 ? 'Add' : 'Update' }}
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@section('js')
    @include('inc.component.icon')
    <script>
        document.addEventListener('icon_selected', function(event) {
            Livewire.dispatch('setIcon', {
                icon: event.detail.icon
            });

            var modalEl = document.getElementById('adminDevelopmentIcon');
            modalEl.classList.remove('show');
            modalEl.style.display = 'none';
            modalEl.setAttribute('aria-hidden', 'true');

            document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            document.body.classList.remove('modal-open');
        });
    </script>
@endsection
