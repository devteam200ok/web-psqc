@if ($showVerificationModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">도메인 소유권 인증</h5>
                    <button type="button" wire:click="closeVerificationModal" class="btn-close"></button>
                </div>
                <div class="modal-body">
                    @if ($currentVerificationDomain)
                        <div class="mb-3">
                            <strong>도메인:</strong>
                            {{ $currentVerificationDomain['domain_only'] }}<br>
                            <strong>상태:</strong>
                            <span class="{{ $currentVerificationDomain['verification_status_class'] }}">
                                {{ $currentVerificationDomain['verification_status'] }}
                            </span>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">TXT 레코드 방식</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="small text-muted">DNS 설정에서 TXT 레코드를 추가하세요.</p>
                                        <div class="mb-2">
                                            <strong>이름:</strong><br>
                                            <code>{{ $currentVerificationDomain['domain_only'] }}</code>
                                        </div>
                                        <div class="mb-3">
                                            <strong>값:</strong><br>
                                            <code class="small">{{ $currentVerificationDomain['txt_record_value'] }}</code>
                                        </div>
                                        <button wire:click="verifyDomainByTxt" class="btn btn-primary btn-sm w-100">
                                            TXT 레코드 확인
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">파일 업로드 방식</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="small text-muted">웹사이트 루트 디렉토리에 파일을 업로드하세요.</p>
                                        <div class="mb-2">
                                            <strong>파일명:</strong><br>
                                            <code class="small">{{ $currentVerificationDomain['verification_file_name'] }}</code>
                                        </div>
                                        <div class="mb-3">
                                            <strong>파일 내용:</strong><br>
                                            <textarea class="form-control small" rows="4" readonly>{{ $currentVerificationDomain['verification_file_content'] }}</textarea>
                                        </div>
                                        <button wire:click="verifyDomainByFile" class="btn btn-primary btn-sm w-100">
                                            파일 업로드 확인
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if ($verificationMessage)
                            <div class="alert alert-{{ $verificationMessageType }} mt-3">
                                {{ $verificationMessage }}
                            </div>
                        @endif
                    @endif
                </div>
                <div class="modal-footer">
                    <button wire:click="refreshVerificationToken" class="btn btn-secondary">
                        새 토큰 생성
                    </button>
                    <button wire:click="closeVerificationModal" class="btn btn-outline-secondary">
                        닫기
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif