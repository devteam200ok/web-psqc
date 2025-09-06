<!--  BEGIN FOOTER  -->
<footer class="footer footer-transparent d-print-none py-3">
    <div class="container-xl">
        <div class="row">
            <div class="col-sm-8 text-sm-start text-center mt-2">
                {{ App\Models\Setting::first()->footer }}
            </div>
            <div class="col-sm-4 text-sm-end text-center mt-2">
                <a href="{{ url('/') }}/theme/changelog.html" class="link-secondary" rel="noopener">
                    v{{ App\Models\Setting::first()->version }}
                </a>
            </div>
        </div>
    </div>
</footer>
