<header class="bg-gray-100 sticky top-0 z-50 border-b-2 border-black" x-data="dropdown()">
    <div class="container flex items-center h-16 justify-between md:justify-start space-x-8">

        <a href="/" class="mx-6">
            <img class=" navbar-brand-full app-header-logo" src="{{ asset('img/Nueva carpeta/Infinix.png') }}" width="130"
                alt="Infyom Logo">
        </a>

        <div class="md:hidden">
            <a>
                @livewire('dropdown-cart')
            </a>
        </div>

        <div class="md:hidden">
            <a :class="{ 'bg-opacity-100 text-black : open' }" x-on:click="show()"
                class="flex flex-col items-center justify-center order-last md:order-first px-6 md:px-4 bg-gray-100 bg-opacity-2 text-black cursor-pointer font-semibold h-full">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path : class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <span class=" text-sm hidden md:block">Categorias</span>
            </a>
        </div>

        <div x-data="{ activeCategory: null, closeTimer: null, isWindowWideEnough: window.innerWidth >= 1300 && window.innerHeight >= 600 }" x-init="() => {
            window.addEventListener('resize', () => {
                isWindowWideEnough = window.innerWidth >= 1300 && window.innerHeight >= 600;
            });
            }" class="space-x-8 sm:-my-px sm:ml-11 sm:flex hidden md:flex"
            x-show="isWindowWideEnough">

            @foreach ($categories as $category)
                <div class="relative inline-block group"
                    x-on:mouseover="activeCategory = '{{ $category->id }}'; clearTimeout(closeTimer)"
                    x-on:mouseleave="closeTimer = setTimeout(() => activeCategory = null, 500)">
                    <x-jet-nav-link :class="'hover:border-black-400 hover:text-black'" href="{{ route('categories.show', $category) }}"
                        :active="''">
                        {{ $category->name }}
                    </x-jet-nav-link>

                    <div x-show="activeCategory == '{{ $category->id }}'" class="fixed top-0 left-0 z-0 mt-16">
                        <div class="flex flex-row">
                            <div class="w-screen bg-gray-100 p-10 border border-black">
                                <ul class="flex justify-center space-x-36">
                                    @foreach ($category->subcategories as $subcategory)
                                        <li>
                                            <a x-on:click="activeCategory = null">
                                                <span>{{ $subcategory->name }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                </div>
            @endforeach
        </div>


        <div class="flex-1 hidden md:block">
            @livewire('search')
        </div>

        <div class="hidden md:block">
            <a>
                @livewire('dropdown-cart')
            </a>
        </div>

        <div class="mx-6 relative hidden md:block">
            @auth
                <x-jet-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <button
                                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                <img class="h-8 w-8 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                                    alt="{{ Auth::user()->name }}" />
                            </button>

                            <!-- eliminar si es que hay problemas con el logo de usuario ok -_- -->
                        @else
                            <span class="inline-flex rounded-md">
                                <button type="button"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                    {{ Auth::user()->name }}

                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                        fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </span>
                        @endif

                        <!-- -_- -->
                    </x-slot>

                    <x-slot name="content">
                        <!-- Account Management -->
                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('Manage Account') }}
                        </div>

                        <x-jet-dropdown-link href="{{ route('profile.show') }}">
                            {{ __('Profile') }}
                        </x-jet-dropdown-link>

                        <x-jet-dropdown-link href="{{ route('orders.index') }}">
                            Mis Pedidos
                        </x-jet-dropdown-link>

                        @role('admin')
                            <x-jet-dropdown-link href="{{ route('admin.index') }}">
                                Administrador
                            </x-jet-dropdown-link>
                        @endrole



                        @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                            <x-jet-dropdown-link href="{{ route('api-tokens.index') }}">
                                {{ __('API Tokens') }}
                            </x-jet-dropdown-link>
                        @endif

                        <div class="border-t border-gray-100"></div>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}" x-data>
                            @csrf

                            <x-jet-dropdown-link href="{{ route('logout') }}" @click.prevent="$root.submit();">
                                {{ __('Log Out') }}
                            </x-jet-dropdown-link>
                        </form>
                    </x-slot>
                </x-jet-dropdown>
            @else
                <!-- Icono de Usuario-->
                <x-jet-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <i class="fas fa-user-circle  text-5xl cursor-pointer"></i>
                    </x-slot>

                    <x-slot name="content">

                        <x-jet-dropdown-link href="{{ route('login') }}">
                            {{ __('Login') }}
                        </x-jet-dropdown-link>


                        <x-jet-dropdown-link href="{{ route('register') }}">
                            {{ __('Register') }}
                        </x-jet-dropdown-link>

                    </x-slot>
                </x-jet-dropdown>
            @endauth
        </div>

    </div>

    <nav id="navigation-menu"
        :class="{ 'block': open, 'hidden': !open }"
        x-show="open"
        class="bg-gray-100 bg-opacity-25 w-full absolute hidden lg:hidden xl:hidden md:hidden sm:hidden xs:block"
        x-bind:class="{ 'xs:block': window.innerWidth <= 1300 && window.innerHeight <= 600 }">


        {{-- menu mobil --}}
        <div class="bg-white h-full overflow-y-auto">

            <div class="container bg-gray-200 py-3 mb-2">
                @livewire('search')
            </div>

            <ul>
                @foreach ($categories as $category)
                    <li class="text-trueGray-500 hover:bg-black hover:text-white">
                        <a href="{{ route('categories.show', $category) }}"
                            class="py-2 px-4 text-sm flex items-center">

                            <span class="flex justify-center w-9">
                                {!! $category->icon !!}
                            </span>

                            {{ $category->name }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <p class="text-trueGray-500 px-6 my-2">USUARIOS</p>

            @auth
                <a href="{{ route('profile.show') }}"
                    class="py-2 px-4 text-sm flex items-center text-trueGray-500 hover:bg-black hover:text-white">

                    <span class="flex justify-center w-9">
                        <i class="far fa-address-card"></i>
                    </span>

                    Perfil
                </a>

                <a href=""
                    onclick="event.preventDefault();
                    document.getElementById('logout-form').submit() "
                    class="py-2 px-4 text-sm flex items-center text-trueGray-500 hover:bg-black hover:text-white">

                    <span class="flex justify-center w-9">
                        <i class="fas fa-sign-out-alt"></i>
                    </span>

                    Cerrar sesión
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                    @csrf
                </form>
            @else
                <a href="{{ route('login') }}"
                    class="py-2 px-4 text-sm flex items-center text-trueGray-500 hover:bg-black hover:text-white">

                    <span class="flex justify-center w-9">
                        <i class="fas fa-user-circle"></i>
                    </span>

                    Iniciar sesión
                </a>

                <a href="{{ route('register') }}"
                    class="py-2 px-4 text-sm flex items-center text-trueGray-500 hover:bg-black hover:text-white">

                    <span class="flex justify-center w-9">
                        <i class="fas fa-fingerprint"></i>
                    </span>

                    registrate
                </a>
            @endauth
        </div>

    </nav>

</header>
