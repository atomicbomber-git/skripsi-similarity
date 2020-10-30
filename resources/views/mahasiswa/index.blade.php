@extends("layouts.app")

@section("content")
    <h1 class="feature-title">
        Mahasiswa
    </h1>

    @include('components.message')

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
                        <th> Kendali </th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($mahasiswas as $mahasiswa)
                        <tr>
                            <td> {{ $mahasiswas->firstItem() + $loop->index }} </td>
                            <td> {{ $mahasiswa->name }} </td>
                            <td> {{ $mahasiswa->username }} </td>
                            <td> </td>
                            <td>
                                <form action="{{ route("mahasiswa.destroy", $mahasiswa) }}"
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
