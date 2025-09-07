@extends('layouts.error')
@section('css')
@endsection
@section('content')
    <div class="page page-center">
        <div class="container-tight py-4">
            <div class="empty">
                <div class="empty-header">500</div>
                <p class="empty-title">Server Error</p>
                <p class="empty-subtitle text-secondary">Sorry, an internal error occurred on the server.</p>
                <div class="empty-action">
                    <a href="{{url('/')}}" class="btn btn-primary btn-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-2">
                            <path d="M5 12l14 0" />
                            <path d="M5 12l6 6" />
                            <path d="M5 12l6 -6" />
                        </svg>
                        Go to Home
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
@endsection
