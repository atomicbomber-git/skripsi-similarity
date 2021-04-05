@extends("layouts.app")

@section("content")
    <h1 class="feature-title">
        Mahasiswa
    </h1>

    @include('components.message')

    <div class="d-flex justify-content-center py-3">
        <div class="flex-fill">
            <form>
                <div class="input-group">
                    <label for="search" class="sr-only">
                        Cari
                    </label>

                    <div class="input-group-prepend">
                        <span class="input-group-text" id="searchPrefix"> Pencarian </span>
                    </div>
                    <input type="text"
                           name="search"
                           id="search"
                           class="form-control"
                           placeholder="Cari..."
                           value="{{ request('search') }}"
                           aria-describedby="searchPrefix"
                    >
                </div>
            </form>
        </div>

        <div class="ml-3">
            <a href="{{ route("mahasiswa.create") }}"
               class="btn btn-primary"
            >
                Tambah
            </a>
        </div>
    </div>

    <div>
        @if($mahasiswas->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover">
                    <thead>
                    <tr>
                        <th> # </th>
                        <th> Nama </th>
                        <th> Nama Pengguna </th>
                        <th> Skripsi </th>
                        <th class="text-center"> Kendali </th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($mahasiswas as $mahasiswa)
                        <tr>
                            <td> {{ $mahasiswas->firstItem() + $loop->index }} </td>
                            <td> {{ $mahasiswa->name }} </td>
                            <td> {{ $mahasiswa->username }} </td>
                            <td>
                                @if($mahasiswa->skripsi !== null)
                                    <a href="{{ route("mahasiswa.download-skripsi", $mahasiswa) }}">
                                        {{ $mahasiswa->skripsi->judul }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="text-center">
                                <a
                                        class="btn btn-primary btn-sm"
                                        href="{{ route("mahasiswa.edit", $mahasiswa) }}"
                                >
                                    Ubah
                                </a>


                                <form
                                        x-data="{}"
                                        @submit.prevent="confirmDialog().then(res => res.isConfirmed && $event.target.submit())"
                                        class="d-inline-block"
                                        action="{{ route("mahasiswa.destroy", $mahasiswa) }}"
                                        method="POST"
                                >
                                    @csrf
                                    @method("DELETE")

                                    <button class="btn btn-danger btn-sm">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $mahasiswas->links() }}
            </div>

        @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                {{ __("messages.errors.no_data") }}
            </div>
        @endif
    </div>
@endsection
