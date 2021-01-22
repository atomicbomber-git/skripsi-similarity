<nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
    <div class="container">
        <a class="navbar-brand"
           href="{{ url('/') }}"
        >
            {{ config('app.name', 'Laravel') }}
        </a>
        <button class="navbar-toggler"
                type="button"
                data-toggle="collapse"
                data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent"
                aria-expanded="false"
                aria-label="{{ __('Toggle navigation') }}"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse"
             id="navbarSupportedContent"
        >
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
                @auth
                    @if(auth()->user()->level === \App\Models\User::LEVEL_ADMIN)

                    @elseif(auth()->user()->level === \App\Models\User::LEVEL_MAHASISWA)
                        <li class="nav-item {{ \Illuminate\Support\Facades\Route::is("mahasiswa.dashboard") ? "active" : ""  }}">
                            <a class="nav-link"
                               href="{{ route('mahasiswa.dashboard', auth()->user()) }}"
                            >
                                @lang("application.dashboard")
                            </a>
                        </li>

                        <li class="nav-item {{ \Illuminate\Support\Facades\Route::is("bank-skripsi-mahasiswa") ? "active" : ""  }}">
                            <a class="nav-link"
                               href="{{ route('bank-skripsi-mahasiswa', auth()->user()) }}"
                            >
                                @lang("application.bank-skripsi-mahasiswa")
                            </a>
                        </li>
                    @endif
                @endauth
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link"
                           href="{{ route('login') }}"
                        >{{ __('Login') }}</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link"
                               href="{{ route('register') }}"
                            >{{ __('') }}</a>
                        </li>
                    @endif
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown"
                           class="nav-link dropdown-toggle"
                           href="#"
                           role="button"
                           data-toggle="dropdown"
                           aria-haspopup="true"
                           aria-expanded="false"
                           v-pre
                        >
                            {{ Auth::user()->name }}
                        </a>

                        <div class="dropdown-menu dropdown-menu-right"
                             aria-labelledby="navbarDropdown"
                        >
                            <a class="dropdown-item"
                               href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();"
                            >
                                {{ __('Logout') }}
                            </a>

                            <form id="logout-form"
                                  action="{{ route('logout') }}"
                                  method="POST"
                                  class="d-none"
                            >
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
