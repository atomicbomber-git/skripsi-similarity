@extends("layouts.app")

@section("content")
    <div>
        @if($mahasiswas->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover">
                    <thead>
                    <tr>
                        <th> # </th>
                        <th> @lang("application.name") </th>
                        <th> @lang("application.thesis") </th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($mahasiswas as $mahasiswa)
                        <tr>
                            <td> {{ $mahasiswas->firstItem() + $loop->index }} </td>
                            <td> {{ $mahasiswa->name }} </td>
                            <td>
                                <a href="{{ route("mahasiswa.download-skripsi", $mahasiswa) }}">
                                    {{ $mahasiswa->skripsi->judul ?? '-' }}
                                </a>
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