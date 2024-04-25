<div wire:init="loadPosts">

    @if (count($products))

    <div class="glider-contain">
        <ul class="glider-{{$category->id}}">
       @foreach ($products as $product)

        <li class="bg-white rounded-lg shadow {{ $loop->last ? '' : 'sm:mr-4' }}">
               <article>
                <figure>
                    <a href="{{ route('products.show', $product) }}">
                    <img  src="{{ Storage::url($product->images->first()->url) }}" alt="">
                    </a>
                </figure>

                    <div class="py-3 px-6">
                        <h1 class="text-sm font-semibold">
                            <a href="{{route('products.show', $product)}}">
                             {{-- Activar para mostrar el nombre completo de los productos {{$product->name}} --}}
                             {{Str::limit($product->name, 50)}}
                            </a>
                        </h1>

                        <p class="font-bold text-truegray-700 line-through ">S/ {{$product->price_tachado}}</p>
                        <p class="font-bold text-truegray-700 text-lg">S/ {{$product->price}}</p>


                    </div>
               </article>

        </li>

       @endforeach

        </ul>

        <button aria-label="Previous" class="glider-prev">«</button>
        <button aria-label="Next" class="glider-next">»</button>
        <div role="tablist" class="dots"></div>

    </div>

    @else

    <div class="mb-4 h-48 flex justify-center items-center bg-white shadow-xl border border-gray-100 rounded-lg">
        <div class="rounded animate-spin ease duration-300 w-10 h-10 border-2 border-blue-400"></div>
    </div>

    @endif

</div>
