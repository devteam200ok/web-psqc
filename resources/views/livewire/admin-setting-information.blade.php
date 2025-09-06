@section('css')
@endsection
<div class="page-body px-xl-3">
    <div class="container-xl">
        <div class="row">
            @include('inc.component.message')
            <div class="col-xl-6 mb-2">
                    <div class="card mb-3">
                        <div class="card-header">회사 정보</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="company" class="form-label">상호명</label>
                                <input wire:model.defer="company" type="text" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="company_phone" class="form-label">전화번호</label>
                                <input wire:model.defer="company_phone" type="text" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="company_address" class="form-label">회사 주소</label>
                                <input wire:model.defer="company_address" type="text" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="company_ceo" class="form-label">CEO</label>
                                <input wire:model.defer="company_ceo" type="text" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="company_cpo" class="form-label">CPO</label>
                                <input wire:model.defer="company_cpo" type="text" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="business_number" class="form-label">사업자 등록번호</label>
                                <input wire:model.defer="business_number" type="text" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="footer" class="form-label">라이선스 정보</label>
                                <input wire:model.defer="footer" type="text" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-header">애플리케이션 정보</div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="version" class="form-label">버전</label>
                                <input wire:model.defer="version" type="text" class="form-control">
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
