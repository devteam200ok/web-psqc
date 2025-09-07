@if ($showVerificationModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Domain Ownership Verification</h5>
                    <button type="button" wire:click="closeVerificationModal" class="btn-close"></button>
                </div>
                <div class="modal-body">
                    @if ($currentVerificationDomain)
                        <div class="mb-3">
                            <strong>Domain:</strong>
                            {{ $currentVerificationDomain['domain_only'] }}<br>
                            <strong>Status:</strong>
                            <span class="{{ $currentVerificationDomain['verification_status_class'] }}">
                                {{ $currentVerificationDomain['verification_status'] }}
                            </span>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">TXT Record Method</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="small text-muted">Add a TXT record in your DNS settings.</p>
                                        <div class="mb-2">
                                            <strong>Name:</strong><br>
                                            <code>{{ $currentVerificationDomain['domain_only'] }}</code>
                                        </div>
                                        <div class="mb-3">
                                            <strong>Value:</strong><br>
                                            <code class="small">{{ $currentVerificationDomain['txt_record_value'] }}</code>
                                        </div>
                                        <button wire:click="verifyDomainByTxt" class="btn btn-primary btn-sm w-100">
                                            Verify TXT Record
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">File Upload Method</h5>
                                    </div>
                                    <div class="card-body">
                                        <p class="small text-muted">Upload a file to your website's root directory.</p>
                                        <div class="mb-2">
                                            <strong>File Name:</strong><br>
                                            <code class="small">{{ $currentVerificationDomain['verification_file_name'] }}</code>
                                        </div>
                                        <div class="mb-3">
                                            <strong>File Content:</strong><br>
                                            <textarea class="form-control small" rows="4" readonly>{{ $currentVerificationDomain['verification_file_content'] }}</textarea>
                                        </div>
                                        <button wire:click="verifyDomainByFile" class="btn btn-primary btn-sm w-100">
                                            Verify File Upload
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
                        Generate New Token
                    </button>
                    <button wire:click="closeVerificationModal" class="btn btn-outline-secondary">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif