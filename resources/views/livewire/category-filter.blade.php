<div>
    <div class="bg-white rounded-lg shadow-lg mb-6">
        <div class="px-6 py-2 flex justify-between items-center">
            <h1 class="font-semibold text-gray-700 uppercase">{{ $category->name }}</h1>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6">
        <aside>
            <!-- {{ $subcategoria }} -_- -->

            <h2 class="font-semibold text-center mb-2">Subcategorias</h2>
            <ul class="divide-y divide-red-700">
                @foreach ($category->subcategories as $subcategory)
                    <li class="py-2 text-sm">
                        <a class="cursor-pointer hover:text-red-500 capitalize {{ $subcategoria == $subcategory->slug ? 'text-red-700 font-semibold' : '' }}"
                            wire:click="$set('subcategoria','{{ $subcategory->slug }}')">{{ $subcategory->name }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <x-jet-button class="mt-4 mb-6" wire:click="limpiar">
                Eliminar Filtros
            </x-jet-button>

            <h2 class="font-semibold text-center mb-2">Ordenar Precio de:</h2>
            <div class="md:flex items-center border border-gray-200 divide-x divide-gray-200 text-black rounded-md shadow-sm">
                <select wire:model="orderByPrice" class="w-full px-3 py-2 cursor-pointer bg-white rounded-r-md appearance-none">
                    <option value="asc">Bajo a más Alto ↑</option>
                    <option value="desc">Alto a más Bajo ↓</option>
                </select>
            </div>



        </aside>

        <div class="md:grid-cols-2 lg:col-span-4">
            @if ($view == 'grid')

                <ul class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @forelse ($products as $product)
                        <li class="bg-white rounded-lg shadow">
                            <article>
                                <figure class="mb-4">
                                    <a href="{{ route('products.show', $product) }}">
                                        <img class="w-full sm:h-64 md:h-48 lg:h-64 xl:h-64 object-cover object-center"
                                            src="{{ Storage::url($product->images->first()->url) }}" alt="">
                                    </a>
                                </figure>

                                <div class="py-3 px-6">
                                    <h1 class="text-lg font-semibold">
                                        <a href="{{ route('products.show', $product) }}">
                                            {{-- Activar para mostrar el nombre completo de los productos {{$product->name}} --}}
                                            {{ Str::limit($product->name, 20) }}
                                        </a>
                                        <p class="font-bold text-xs text-blue-400">{{ $product->sku }}</p>
                                    </h1>

                                    <p class="font-bold text-truegray-700">S/ {{ $product->price }}</p>
                                </div>
                            </article>
                        </li>

                    @empty

                        <li class="md:col-span-2 lg:col-span-4">
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                                role="alert">
                                <strong class="font-bold">Upss.!!</strong>
                                <span class="block sm:inline">No hay registro con ese filtro</span>
                            </div>
                        </li>
                    @endforelse
                </ul>
            @else
                <ul>
                    @forelse ($products as $product)
                        <x-products-list :product="$product" />

                    @empty

                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                            role="alert">
                            <strong class="font-bold">Upss.!!</strong>
                            <span class="block sm:inline">No hay registro con ese filtro</span>
                        </div>
                    @endforelse
                </ul>

            @endif

            <div class="mt-4">
                {{ $products->links() }}
            </div>

        </div>

    </div>
</div>
