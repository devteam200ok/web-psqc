@section('css')
@endsection
<div class="page-body px-xl-3">
    <div class="container-xl">
        @include('inc.component.message')
        <div class="row">
            <div class="col-xl-6 mb-2">
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <h6>Sendgrid Email</h6>
                            </div>
                            <div class="col-xl-12 mb-2">
                                <label class="form-label">sendgrid_key</label>
                                <input type="text" wire:model="sendgrid_key" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6 mb-2">
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <h6>Open AI</h6>
                            </div>
                            <div class="col-xl-12 mb-2">
                                <label class="form-label">openai_key</label>
                                <input type="text" wire:model="openai_key" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-12 mb-2">
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <h6>Paypal</h6>
                            </div>
                            <div class="col-xl-12 mb-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" id="paypal_mode_live" type="radio"
                                        name="paypal_mode" value="live" wire:model="paypal_mode" />
                                    <label class="form-check-label" for="paypal_mode_live">Live</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" id="paypal_mode_sandbox" type="radio"
                                        name="paypal_mode" value="sandbox" wire:model="paypal_mode" />
                                    <label class="form-check-label" for="paypal_mode_sandbox">Sandbox</label>
                                </div>
                            </div>
                            <div class="col-xl-6 mb-2">
                                <label class="form-label">paypal_client_id_live</label>
                                <input type="text" wire:model="paypal_client_id_live" class="form-control">
                            </div>
                            <div class="col-xl-6 mb-2">
                                <label class="form-label">paypal_secret_live</label>
                                <input type="text" wire:model="paypal_secret_live" class="form-control">
                            </div>
                            <div class="col-xl-6 mb-2">
                                <label class="form-label">paypal_client_id_sandbox</label>
                                <input type="text" wire:model="paypal_client_id_sandbox" class="form-control">
                            </div>
                            <div class="col-xl-6 mb-2">
                                <label class="form-label">paypal_secret_sandbox</label>
                                <input type="text" wire:model="paypal_secret_sandbox" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-12 mb-2">
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <h6>Toss</h6>
                            </div>
                            <div class="col-xl-12 mb-2">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" id="toss_mode_live" type="radio" name="toss_mode"
                                        value="live" wire:model="toss_mode" />
                                    <label class="form-check-label" for="toss_mode_live">Live</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" id="toss_mode_sandbox" type="radio"
                                        name="toss_mode" value="sandbox" wire:model="toss_mode" />
                                    <label class="form-check-label" for="toss_mode_sandbox">Sandbox</label>
                                </div>
                            </div>
                            <div class="col-xl-6 mb-2">
                                <label class="form-label">toss_client_key_test</label>
                                <input type="text" wire:model="toss_client_key_test" class="form-control">
                            </div>
                            <div class="col-xl-6 mb-2">
                                <label class="form-label">toss_secret_key_test</label>
                                <input type="text" wire:model="toss_secret_key_test" class="form-control">
                            </div>
                            <div class="col-xl-6 mb-2">
                                <label class="form-label">toss_client_key</label>
                                <input type="text" wire:model="toss_client_key" class="form-control">
                            </div>
                            <div class="col-xl-6 mb-2">
                                <label class="form-label">toss_secret_key</label>
                                <input type="text" wire:model="toss_secret_key" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-12 mb-2">
                <div class="text-end mb-4 d-flex">
                    <button wire:click="update" wire:loading.attr="disabled" class="btn btn-primary mt-2 ms-auto">
                        업데이트
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@section('js')
@endsection
