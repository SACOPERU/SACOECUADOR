<div class="container py-12">
    <x-table-responsive>
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                        Nombre
                    </th>
                    <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                        SKU
                    </th>
                    <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                        BODEGA
                    </th>
                    <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                        STOCK FLEX
                    </th>
                    <th class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                        Acciones
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach ($responseData->Producto as $product)
                <tr>
                    <td class="px-6 py-4 whitespace-no-wrap">
                        <div class="flex items-center">
                            <div class="ml-4">
                                <div class="text-sm font-medium leading-5 text-gray-900">
                                    {{ $product->Descripcion }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap">
                        <div class="text-sm leading-5 text-gray-900">
                            {{ $product->CodProducto }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap">
                        <div class="text-sm leading-5 text-gray-900">
                            {{ $product->Bodega->Descripcion }}
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm leading-5 text-gray-500 whitespace-no-wrap">
                        @if ($product->Bodega->Cantidad <= 0)
                        sin stock
                        @else
                        {{ $product->Bodega->Cantidad }}
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-no-wrap">
                        <a href="{{ route('livewire.edit-product', ['id' => $product->ID]) }}" class="btn btn-primary">Editar</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </x-table-responsive>
</div>
