@extends("layouts.app")

@section("content")
    <h1 class="title">
        <a href="{{ route("mahasiswa.index") }}">
            Mahasiswa
        </a>

        /

        Ubah
    </h1>

    @include("components.message")

    <div class="card">
        <div class="card-body">
            <form action="{{ route("mahasiswa.update", $mahasiswa) }}"
                  method="POST"
            >
                @csrf
                @method("PUT")

                <div class="form-group">
                    <label for="name"> Nama: </label>
                    <input
                            id="name"
                            type="text"
                            placeholder="Nama"
                            class="form-control @error("name") is-invalid @enderror"
                            name="name"
                            value="{{ old("name", $mahasiswa->name) }}"
                    />
                    @error("name")
                    <span class="invalid-feedback">
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="username"> Username: </label>
                    <input
                            id="username"
                            type="text"
                            placeholder="Username"
                            class="form-control @error("username") is-invalid @enderror"
                            name="username"
                            value="{{ old("username", $mahasiswa->username) }}"
                    />
                    @error("username")
                    <span class="invalid-feedback">
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <div class="alert alert-warning">
                    Kosongkan kolom kata sandi jika Anda tidak ingin mengubah kata sandi.
                </div>

                <div class="form-group">
                    <label for="password"> Kata Sandi: </label>
                    <input
                            id="password"
                            type="password"
                            placeholder="Kata Sandi"
                            class="form-control @error("password") is-invalid @enderror"
                            name="password"
                            value="{{ old("password") }}"
                    />
                    @error("password")
                    <span class="invalid-feedback">
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation"> Ulangi Sandi: </label>
                    <input
                            id="password_confirmation"
                            type="password"
                            placeholder="Ulangi Sandi"
                            class="form-control @error("password_confirmation") is-invalid @enderror"
                            name="password_confirmation"
                            value="{{ old("password_confirmation") }}"
                    />
                    @error("password_confirmation")
                    <span class="invalid-feedback">
                        {{ $message }}
                    </span>
                    @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary">
                        Ubah
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
