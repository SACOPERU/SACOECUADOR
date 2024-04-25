@php
    $links = [
        [
            'icon' => 'fa-solid fa-gauge',
            'name' => 'Dashboard',
            'route' => '/admin',
            'active' => request()->routeIs('/admin'),
        ],
        [
            'icon' => 'fa-solid fa-user-large',
            'name' => 'Usuarios',
            'route' => route('admin.users.index'),
            'active' => request()->routeIs('admin.users.index'),
        ],
        [
            'icon' => 'fa-brands fa-product-hunt',
            'name' => 'Productos',
            'route' => route('admin.index'),
            'active' => request()->routeIs('admin.index'),
        ],
        [
            'icon' => 'fa-solid fa-paste',
            'name' => 'Pedidos',
            'route' => route('admin.orders.index'),
            'active' => request()->routeIs('admin.orders.index'),
        ],
        [
            'icon' => 'fa-solid fa-paste',
            'name' => 'Pedidos Partner',
            'route' => route('admin.orderpartners.index'),
            'active' => request()->routeIs('admin.orderpartners.index'),
        ],
        [
            'icon' => 'fa-solid fa-layer-group',
            'name' => 'Categorias',
            'route' => route('admin.categories.index'),
            'active' => request()->routeIs('admin.categories.index'),
        ],
        [
            'icon' => 'fa-solid fa-tag',
            'name' => 'Marca',
            'route' => route('admin.brands.index'),
            'active' => request()->routeIs('admin.brands.index'),
        ],
        [
            'icon' => 'fa-solid fa-images',
            'name' => 'Banners',
            'route' => route('admin.banners.index'),
            'active' => request()->routeIs('admin.banners.index'),
        ],
        [
            'icon' => 'fa-solid fa-photo-film',
            'name' => 'Promocion Banners',
            'route' => route('admin.promocions.index'),
            'active' => request()->routeIs('admin.promocions.index'),
        ],
        [
            'icon' => 'fa-solid fa-images',
            'name' => 'Banner Xiaomi',
            'route' => route('admin.xcovers.index'),
            'active' => request()->routeIs('admin.xcovers.index'),
        ],
        [
            'icon' => 'fa-solid fa-images',
            'name' => 'Banner Samsung',
            'route' => route('admin.scovers.index'),
            'active' => request()->routeIs('admin.scovers.index'),
        ],
        [
            'icon' => 'fa-solid fa-images',
            'name' => 'Banner Vivo',
            'route' => route('admin.vcovers.index'),
            'active' => request()->routeIs('admin.vcovers.index'),
        ],
        [
            'icon' => 'fa-solid fa-images',
            'name' => 'Banner Infinix',
            'route' => route('admin.icovers.index'),
            'active' => request()->routeIs('admin.icovers.index'),
        ],
    ];
@endphp

<aside id="logo-sidebar"
    class="fixed top-0 left-0 z-40 w-64 h-[100dvh]  pt-20 transition-transform -translate-x-full bg-white border-r border-gray-200 sm:translate-x-0 dark:bg-gray-800 dark:border-gray-700"
    :class="{
        'translate-x-0 ease-out': sidebarOpen,
        '-translate-x-full ease-in': !sidebarOpen
    }"
    aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-gray-800">
        <ul class="space-y-2 font-medium">
            @foreach ($links as $link)
                <li>
                    <a href=" {{ $link['route'] }} "
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ $link['active'] ? 'bg-green-500' : '' }}">
                        <span class="inline-flex w-6 h-6 justify-center items-center"><i
                                class="{{ $link['icon'] }}"></i></span>
                        <span class="ms-2">{{ $link['name'] }}</span>
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</aside>
