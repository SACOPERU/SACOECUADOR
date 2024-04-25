<div>
    <!-- ... (header and other content) ... -->
    <div class="container py-12">
        <x-table-responsive>
            <table class="min-w-full divide-y divide-gray-200">
                <!-- Table headers -->
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
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </x-table-responsive>

        <form method="POST" action="{{ route('livewire.save-products') }}">
            @csrf
            <!-- You can include a loop here to generate form fields for each product -->
            @foreach($responseData->Producto as $product)
                <input type="hidden" name="products[{{$loop->index}}][name]" value="{{$product->Descripcion}}">
                <input type="hidden" name="products[{{$loop->index}}][sku]" value="{{$product->CodProducto}}">
                <!-- Other input fields for each product -->
            @endforeach

            <button type="submit">Save Products</button>
        </form>
    </div>
</div>
