<x-samsung-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
            <div>
                <div class="flexslider">
                    <ul class="slides">
                        @foreach ($product->images as $image)
                            <li data-thumb=" {{ Storage::url($image->url) }}">
                                <img src=" {{ Storage::url($image->url) }}" />
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <div>
                <h1 class="text-xl font-bold text-truegray-700">{{ $product->name }}</h1>
               
                <div class="flex">
                    <div class="text-sm font-bold text-blue-400">{{ $product->sku }}</div>
                    <p class="text-truegray-700 mx-6">5 <i class="fas fa-star text-sm text-yellow-400"></i></p>
                    <a class="text-red-500 hover:text-red-700 underline" href="">39 reseñas</a>
                </div>

                <p class="text-2xl font-semibold text-truegray-700 my-4 line-through"> S/ {{ $product->price_tachado }}
                </p>
                <p class="text-2xl font-semibold text-truegray-700 my-4 ">Precio Oferta S/ {{ $product->price }}</p>

                <div class="mt-6 text-gray-700">
                    <h2 class="font-bold text-lg">Descripción</h2>
                    {!! $product->description !!}
                </div>

                <div class="bg-white rounded-lg shadow-lg mb-6">
                    <div class="p-4 flex items-center">
                        <span class="flex items-center justify-center h-10 w-10 rounded-full bg-green-400 ">
                            <li class="fas fa-truck"></li>
                        </span>
                        <div class="ml-4">
                            <p class="text-lg font-semibold text-green-400">Se hace envíos a todo el Perú</p>
                            <p>Retiro en tiendas Fisicas</p>

                        </div>
                    </div>
                </div>
                @if ($product->subcategory->size)
                    @livewire('add-cart-item-size', ['product' => $product])
                @elseif ($product->subcategory->color)
                    @livewire('add-cart-item-color', ['product' => $product])
                @else
                    @livewire('add-cart-item', ['product' => $product])
                @endif

                <div class="mt-6">
                    <h2 class="font-bold text-lg">Disponibilidad de Stock</h2>

                    @php
                        $tablaAliases = [
                            'atocong' => 'Mi Store Open Plaza Atocongo',
                            'jockeypz' => 'Mi Store Tottus Jockey Plaza',
                            'megaplz' => 'Mi Store Tottus Mega Plaza',
                            'huaylas' => 'Mi Store Tottus Huaylas',
                            'puruchu' => 'Mi Store Tottus Puruchuco',
                        ];
                    @endphp

                    @foreach ($tablaAliases as $propiedad => $alias)
                        @if ($product->$propiedad > 0)
                            <div>
                                <p>{{ $alias }}</p>
                            </div>
                        @endif
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    @push('script')
        <script>
            $(document).ready(function() {
                $('.flexslider').flexslider({
                    animation: "slide",
                    controlNav: "thumbnails"
                });
            });
        </script>
    @endpush

</x-samsung-layout>
@livewire('footer')
