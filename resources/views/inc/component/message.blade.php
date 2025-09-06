@if (session()->has('message'))
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false"
            data-bs-toggle="toast">
            <div class="toast-header bg-dark text-light">
                <div class="alert-icon">
                    <i class="ti ti-info-circle"></i>
                </div>
                <strong class="me-auto">
                    <h4 class="alert-heading">메시지</h4>
                </strong>
                <button type="button" class="ms-2 btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body text-dark">{{ session('message') }}</div>
        </div>
    </div>
@endif
@if (session()->has('success'))
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false"
            data-bs-toggle="toast">
            <div class="toast-header bg-primary text-white">
                <div class="alert-icon">
                    <i class="ti ti-check"></i>
                </div>
                <strong class="me-auto">
                    <h4 class="alert-heading">성공</h4>
                </strong>
                <button type="button" class="ms-2 btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body text-dark">{{ session('success') }}</div>
        </div>
    </div>
@endif
@if (session()->has('error'))
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100;">
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" data-bs-autohide="false"
            data-bs-toggle="toast">
            <div class="toast-header bg-danger text-white">
                <div class="alert-icon">
                    <i class="ti ti-alert-circle"></i>
                </div>
                <strong class="me-auto">
                    <h4 class="alert-heading">오류</h4>
                </strong>
                <button type="button" class="ms-2 btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body text-dark">{{ session('error') }}</div>
        </div>
    </div>
@endif
