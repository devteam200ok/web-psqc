@section('css')
@endsection
<div class="page-body px-xl-3">
    <div class="container-xl">
        @include('inc.component.message')
        <div class="row">
            <div class="col-xl-8 mb-2">
                <div class="card">
                    <div wire:ignore class="card-body">
                        <div class="mb-3">
                            <label class="form-label">서비스 이용약관</label>
                            <textarea wire:model="terms" id="hugerte-mytextarea">{{ $terms }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-2 mb-3">
                    <button id="updateTerms" wire:loading.attr="disabled" type="button" class="btn btn-primary">
                        업데이트
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@section('js')
    <script src="{{ url('/') }}/theme/libs/hugerte/hugerte.min.js?1744816591"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            let options = {
                selector: '#hugerte-mytextarea',
                height: 600,
                menubar: false,
                statusbar: false,
                license_key: 'gpl',
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
                    'preview', 'anchor', 'searchreplace', 'visualblocks',
                    'code', 'fullscreen', 'insertdatetime', 'media', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | formatselect | ' +
                    'bold italic backcolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat',
                content_style: 'body { font-family: Nunito Sans, sans-serif; font-size:14px; }'
            };

            if (localStorage.getItem("tablerTheme") === 'dark') {
                options.skin = 'oxide-dark';
                options.content_css = 'dark';
            }

            hugeRTE.init(options);
        });

        document.getElementById('updateTerms').addEventListener('click', function() {
            const editor = hugeRTE.get('hugerte-mytextarea');
            if (!editor) {
                console.error('Editor instance not found');
                return;
            }
            const terms = editor.getContent();
            Livewire.dispatch('update', {
                terms
            });
        });
    </script>
@endsection
