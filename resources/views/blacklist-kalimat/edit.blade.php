@extends("layouts.app")

@section("content")
    <h1 class="feature-title">
        <a href="{{ route("blacklist-kalimat.index") }}">
            @lang("application.blacklist-sentences")
        </a>

        /

        @lang("application.edit")
    </h1>

    @include("components.message")

    <div class="card">
        <div class="card-body">
            <form action="{{ route("blacklist-kalimat.update", $kalimat) }}"
                  method="POST"
            >
                @csrf
                @method("PUT")

                <div class="form-group">
                    <label for="teks"> @lang("application.text") </label>
                    <textarea
                            id="teks"
                            type="text"
                            placeholder="Teks"
                            class="form-control @error("teks") is-invalid @enderror"
                            name="teks"
                    >{{ old("teks", $kalimat->teks) }}</textarea>
                    @error("teks")
                    <span class="invalid-feedback">
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary">
                        @lang("application.update")
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
