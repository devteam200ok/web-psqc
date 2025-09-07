@php
    $setting = App\Models\Setting::first();
@endphp
<footer class="footer footer-transparent d-print-none">
    <div class="container-xl">
        <div class="row mb-2">
            <div class="col-12 text-muted small d-none d-md-block text-start" style="line-height: 1.6;">
                <div>
                    <strong>Company:</strong> {{$setting->company}} |
                    <strong>Business Registration:</strong> {{$setting->business_number}} |
                    <strong>Business Address:</strong> {{$setting->company_address}}
                </div>
            </div>

            <div class="col-12 text-muted small d-block d-md-none text-start" style="line-height: 1.6;">
                <div><strong>Company:</strong> {{$setting->company}}</div>
                <div><strong>Business Registration:</strong> {{$setting->business_number}}</div>
                <div><strong>Business Address:</strong> {{$setting->company_address}}</div>
            </div>
        </div>

        <hr class="mt-0 mb-2">

        <div class="row text-start align-items-center flex-row-reverse">
            <div class="col-lg-auto ms-lg-auto">
                <ul class="list-inline list-inline-dots mb-0">
                    <li class="list-inline-item">
                        <a href="{{ url('/') }}/privacy" target="_blank" class="link-secondary">Privacy Policy</a>
                    </li>
                    <li class="list-inline-item">
                        <a href="{{ url('/') }}/terms" target="_blank" class="link-secondary">Terms of Service</a>
                    </li>
                </ul>
            </div>
            <div class="col-12 col-lg-auto mt-3 mt-lg-0">
                <ul class="list-inline list-inline-dots mb-0">
                    <li class="list-inline-item">
                        {{$setting->footer}}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>
