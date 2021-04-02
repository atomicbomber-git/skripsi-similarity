@extends("layouts.app")

@section("content")
    <h1 class="feature-title">
        Dashboard | {{ $mahasiswa->name }}
    </h1>

    @include("components.message")

    <div class="card mb-3">
        <div class="card-body">
            @if($mahasiswa->skripsi !== null)
                <dl>
                    <dt> Judul Skripsi </dt>
                    <dd>
                        {{ $mahasiswa->skripsi->judul }}
                        <a href="{{ route("mahasiswa.download-skripsi", $mahasiswa) }}">
                            
                        </a>
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
                        <label for="skripsi"> Berkas Skripsi (Dokumen Word / *.docx): </label>
                        <input
                                id="skripsi"
                                type="file"
                                accept="application/vnd.openxmlformats-officedocument.wordprocessingml.document"
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

    <h2>
        Perbandingan Similaritas Dokumen Skripsi
    </h2>

    <div class="row">
        <div class="col-md-12">
            @if($skripsiSimilarityRecords->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover">
                        <thead>
                        <tr>
                            <th> </th>
                            <th> Nama</th>
                            <th> Skripsi</th>
                            <th> Dice Similarity </th>
                            <th> Chebyshev Distance </th>
                            <th> Kalimat Termirip </th>
                        </tr>
                        </thead>

                        <tbody>
                        @foreach ($skripsiSimilarityRecords as $skripsiSimilarityRecord)
                            <tr>
                                <td> {{ $loop->iteration }} </td>
                                <td> {{ $skripsiSimilarityRecord->skripsi->mahasiswa->name  }} </td>
                                <td> {{ $skripsiSimilarityRecord->skripsi->judul  }} </td>
                                <td> {{ $skripsiSimilarityRecord->diceSimilarityAverage * 100 }}% </td>
                                <td> {{ $skripsiSimilarityRecord->chebyshevDistanceAverage }} </td>
                                <td>
                                    @foreach ($skripsiSimilarityRecord->mostSimilarKalimats as $mostSimilarKalimat)
                                        <div class="row">
                                            <div class="col">
                                                {{ $targetSkripsi->kalimatSkripsis->where("id", $mostSimilarKalimat->kalimatAId)->first()->teks }}
                                            </div>
                                            <div class="col">
                                                {{ $skripsiSimilarityRecord->skripsi->kalimatSkripsis->where("id", $mostSimilarKalimat->kalimatBId)->first()->teks }}
                                            </div>
                                        </div>
                                        <hr/>
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

            @else
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ __("messages.errors.no_data") }}
                </div>
            @endif
        </div>
    </div>
@endsection
