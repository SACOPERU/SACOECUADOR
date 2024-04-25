<div>

    <x-slot name="header">
        <div class="flex items-center container">
            <h2 class="font-semibold text-xl text-gray-700 leading-tigth">
                Lista de productos
            </h2>

            <x-button-enlace class="ml-auto" href="{{ route('admin.products.create') }}">
                Agregar Producto
            </x-button-enlace>

        </div>
    </x-slot>

    <!-- This example requires Tailwind CSS v2.0+ -->
    <div class="container py-12">

        <x-table-responsive>

            <x-jet-button wire:loading.attr="disabled" wire:click="exportToExcel">
                Exportar a Excel
            </x-jet-button>

            <div class="px-6 py-4">

                <x-jet-input type="text" wire:model="search" class="w-full"
                    placeholder="Ingrese el nombre del producto" />

            </div>

            @if ($products->count())

                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th
                                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                Nombre
                            </th>
                            <th
                                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                SKU
                            </th>
                            <th
                                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                Categoria
                            </th>
                            <th
                                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                Estado
                            </th>
                            <th
                                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                Stock
                            </th>
                            <th
                                class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                Precio
                            </th>
                            <th class="px-6 py-3 bg-gray-50"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">

                        @foreach ($products as $product)
                            <tr v-for="(person, i) in persons" :key="i">
                                <td class="px-6 py-4 whitespace-no-wrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 w-10 h-10">
                                            @if ($product->images->count())
                                                <img class="w-10 h-10 rounded-full object-cover"
                                                    src="{{ Storage::url($product->images->first()->url) }}"
                                                    alt="">
                                            @else
                                                <img class="w-10 h-10 rounded-full object-cover"
                                                    src="https://images.pexels.com/photos/3747139/pexels-photo-3747139.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1"
                                                    alt="">
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium leading-5 text-gray-900">
                                                {{ $product->name }}
                                            </div>

                                        </div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-no-wrap">
                                    <div class="text-sm leading-5 text-gray-900">
                                        {{ $product->sku }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-no-wrap">
                                    <div class="text-sm leading-5 text-gray-900">
                                        {{ $product->subcategory->category->name }}
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-no-wrap">
                                    @switch($product->status)
                                        @case(1)
                                            <span
                                                class="inline-flex px-2 text-xs font-semibold leading-5 text-red-500 bg-green-100 rounded-full">
                                                Borrador
                                            </span>
                                        @break

                                        @case(2)
                                            <span
                                                class="inline-flex px-2 text-xs font-semibold leading-5 text-green-800 bg-green-100 rounded-full">
                                                Publicado
                                            </span>
                                        @break

                                        @default
                                    @endswitch
                                </td>
                                <td class="px-6 py-4 text-sm leading-5 text-gray-500 whitespace-no-wrap">
                                    {{ $product->quantity }}
                                </td>
                                <td class="px-6 py-4 text-sm leading-5 text-gray-500 whitespace-no-wrap">
                                    S/{{ $product->price }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium leading-5 text-right whitespace-no-wrap">
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                        class="text-indigo-600 hover:text-indigo-900">Editar</a>
                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            @else
                <div class="px-6 py-4">
                    No hay registro
                </div>

            @endif

            @if ($products->hasPages())
                <div class="px-6 py-4">
                    {{ $products->links() }}

                </div>
            @endif

        </x-table-responsive>

    </div>
</div>
