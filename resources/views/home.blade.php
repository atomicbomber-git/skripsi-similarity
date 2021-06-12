@extends("layouts.app")

@section("base-content")
    @include('components.message')

    <div
            style="
                    height: calc(100vh - 69px);
                    background-size: cover;
                    background-position: left;
                    background-image: url('{{ asset('home.png') }}')
                    "
    >
@endsection
