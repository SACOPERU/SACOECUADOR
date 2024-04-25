@props(['product'])

<li class="bg-white rounded-lg shadow mb-4">
    <article class="md:flex">

        <a href="{{ route('products.show', $product) }}">
            <figure class="mb-4">
                <img class="w-full sm:h-80 md:h-60 lg:h-80 xl:h-80 object-cover object-center"
                    src="{{ Storage::url($product->images->first()->url) }}" alt="">
            </figure>
        </a>


        <div class="flex-1 py-4 px-6 flex flex-col">
            <div class="lg:flex justify-between">
                <div>
                    <h1 class="text-lg font-semibold text-gray-800">{{ $product->name }}</h1>
                    <p class="font-semibold text-xs text-blue-400 ">{{ $product->sku }}</p>
                    <p class="font-semibold text-gray-800 ">S/{{ $product->price }}</p>
                </div>
                <div class="flex items-center">
                    <ul>
                        <li class="fas fa-star text-yellow-400 mr-1"></li>
                        <li class="fas fa-star text-yellow-400 mr-1"></li>
                        <li class="fas fa-star text-yellow-400 mr-1"></li>
                        <li class="fas fa-star text-yellow-400 mr-1"></li>
                        <li class="fas fa-star text-yellow-400 mr-1"></li>
                    </ul>
                    <span class="text-gray-700 text-sm  ">(24)</span>
                </div>

            </div>
            <div class="mt-4 md:mt-auto mb-4">
                <x-danger-enlace href="{{ route('products.show', $product) }}">
                    Más Informacion
                </x-danger-enlace>
            </div>
        </div>
    </article>
</li>
