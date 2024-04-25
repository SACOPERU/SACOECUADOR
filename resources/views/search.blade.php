<x-app-layout>
    <div class="container py-8">
        <ul>
            @forelse ($products as $product)
                <x-products-list :product="$product"/>
            @empty
                <li class="bg-white rounded-lg shadow-2x1">
                    <div class="p-4">
                        <h2 class="text-lg text-gray-700 font-bold">Lo sentimos</h2>
                        <p class="text-lg text-gray-700 p-3">Te recomendamos verificar la ortografía o intentar con otra palabra.</p>
                        <div class="w-full">
                            <img class="container navbar-brand-full app-header-logo w-10 h-10 sm:w-8 sm:h-8 md:w-6 md:h-6" src="{{ asset('img/busqueda.png') }}" width="150">
                        </div>
                    </div>
                </li>
            @endforelse
        </ul>
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>
</x-app-layout>
