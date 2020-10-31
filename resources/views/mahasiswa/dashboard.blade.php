@extends("layouts.app")

@section("content")
    <h1 class="feature-title">
        Dashboard | {{ $mahasiswa->name }}
    </h1>

    @include("components.message")

    <div class="card">
        <div class="card-body">
            @if($mahasiswa->skripsi !== null)
                <dl>
                    <dt> Judul Skripsi </dt>
                    <dd> {{ $mahasiswa->skripsi->judul }} </dd>

                    <dt>  </dt>
                </dl>
            @else
                <div class="alert alert-warning">
                    Anda belum mengunggah berkas skripsi Anda. Silahkan unggah dengan fitur di bawah.
                </div>

                <form action="{{ route("mahasiswa.upload-skripsi", $mahasiswa) }}"
                      enctype="multipart/form-data"
                      method="POST"
                >
                    @csrf
                    @method("POST")

                    <div class="form-group">
                        <label for="judul"> Judul Skripsi: </label>
                        <input
                                id="judul"
                                type="text"
                                placeholder="Judul Skripsi"
                                class="form-control @error("judul") is-invalid @enderror"
                                name="judul"
                                value="{{ old("judul") }}"
                        />
                        @error("judul")
                        <span class="invalid-feedback">
                        {{ $message }}
                    </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="skripsi"> Berkas Skripsi (PDF): </label>
                        <input
                                id="skripsi"
                                type="file"
                                accept="application/pdf"
                                placeholder="Berkas Skripsi"
                                class="form-control @error("skripsi") is-invalid @enderror"
                                name="skripsi"
                                value="{{ old("skripsi") }}"
                        />
                        @error("skripsi")
                        <span class="invalid-feedback">
                    {{ $message }}
                </span>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary">
                            Unggah
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection
