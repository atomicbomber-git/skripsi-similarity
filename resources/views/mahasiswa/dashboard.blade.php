@extends("layouts.app")

@section("content")
    @include("components.message")

    <div class="card mb-3">
        <div class="card-body">
            @if($mahasiswa->skripsi !== null)
                <dl>
                    <dt> Judul Skripsi </dt>
                    <dd>
                        {{ $mahasiswa->skripsi->judul }}
                    </dd>
                </dl>

                <form action="{{ route("mahasiswa.delete-skripsi", $mahasiswa) }}"
                      method="POST"
                >
                    @csrf
                    @method("DELETE")

                    <div class="d-flex justify-content-end">
                        <button class="btn btn-danger btn-sm">
                            Hapus Berkas Skripsi
                        </button>
                    </div>
                </form>
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
                        <label for="skripsis"> Berkas Skripsi (Dokumen Word / *.docx, Boleh > 1): </label>
                        <input
                                multiple
                                id="skripsi"
                                type="file"
                                accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                                placeholder="Berkas Skripsi"
                                class="form-control @error("skripsi") is-invalid @enderror"
                                name="skripsis[]"
                                value="{{ old("skripsis") }}"
                        />
                        @error("skripsis")
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

    <div class="row">
        <div class="col-md-7">
            <h3> Kalimat Termirip pada Skripsi-Skripsi Lain </h3>
            <div 
                    id="most-similar-sentences-index"
                    data-data-url="{{ route("skripsi.similaritas-kalimat.index", $mahasiswa->skripsi) }}"
            ></div>
        </div>

        <div class="col-md-5">
            <h3> Skripsi Termirip </h3>
            <div
                id="most-similar-skripsis-index"
                data-data-url="{{ route("skripsi.similaritas-skripsi.index", $mahasiswa->skripsi) }}"
            >
            </div>
        </div>
    </div>
@endsection
