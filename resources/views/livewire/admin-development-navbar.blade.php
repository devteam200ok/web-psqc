@section('css')
@endsection
<div class="page-body px-xl-3">
    <div class="container-xl">
        @include('inc.component.message')
        <div class="row">
            <div class="col-xl-4 mb-3">
                <div class="my-3">
                    <div class="mt-2 input-group">
                        <span class="input-group-text" style="min-width:100px">
                            {{ config('app.url') }}/
                        </span>
                        <input wire:model="url" type="text" class="form-control" placeholder="url">
                    </div>

                    <div class="mt-2 input-group">
                        <input wire:model="title" type="text" class="form-control" placeholder="Menu Title">
                        <button wire:click="generate" class="btn btn-primary px-3">
                            create
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@section('js')
@endsection
