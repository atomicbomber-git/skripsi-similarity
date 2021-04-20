@extends("layouts.app")

@section("content")
    <h1 class="feature-title">
        Blacklist Kalimat
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
            <a href="{{ route("blacklist-kalimat.create") }}"
               class="btn btn-primary"
            >
                @lang("application.create")
            </a>
        </div>
    </div>

    <div>
        @if($kalimats->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-sm table-striped table-hover">
                    <thead>
                    <tr>
                        <th> @lang("application.number_symbol") </th>
                        <th> @lang("application.text") </th>
                        <th class="text-center"> @lang("application.controls") </th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach ($kalimats as $kalimat)
                        <tr>
                            <td> {{ $kalimats->firstItem() + $loop->index }} </td>
                            <td> {{ $kalimat->teks }} </td>
                            <td class="text-center" style="width: 200px">
                                <a
                                        class="btn btn-primary btn-sm"
                                        href="{{ route("blacklist-kalimat.edit", $kalimat) }}"
                                >
                                    Ubah
                                </a>


                                <form
                                        x-data="{}"
                                        @submit.prevent="confirmDialog().then(res => res.isConfirmed && $event.target.submit())"
                                        class="d-inline-block"
                                        action="{{ route("blacklist-kalimat.destroy", $kalimat) }}"
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
                {{ $kalimats->links() }}
            </div>

        @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                {{ __("messages.errors.no_data") }}
            </div>
        @endif
    </div>
@endsection
