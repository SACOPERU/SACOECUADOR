<div>
    <x-slot name="header">
        <h2 class=" container font-bold text-xl text-gray-900 leading-tight">
            PRODUCTOS DISPONIBLES
        </h2>

    <div class="container py-12">

        <x-table-responsive>

        <div class="px-6 py-2">
            <x-jet-input type="text"
                wire:model="search"
                class="w-full"
                placeholder="Ingrese el nombre del producto"/>
        </div>

        @if ($products->count())

            <div class="bg-white">
             <div class="mx-auto max-w-2xl px-4 py-16 sm:px-6 sm:py-24 lg:max-w-7xl lg:px-8">

              <div class="grid grid-cols-2 gap-x-7 gap-y-10 sm:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 xl:gap-x-8">
                @foreach ($products as $product)
                <a href="#" class="group py-4">
                    <div class="aspect-h-1 aspect-w-1 w-full overflow-hidden rounded-lg bg-gray-200 xl:aspect-h-8 xl:aspect-w-7">
                        <img class="w-full h-80 object-cover object-center" src="{{Storage::url($product->images->first()->url)}}" alt="">
                    </div>
                    <h3 class="mt-4 text-sm text-gray-700"> {{$product->name}}</h3>
                    <p class="mt-1 text-lg font-medium text-gray-900"> S/ {{$product->price_partner}}</p>

                    <div class=" border-x-slate-50">
                        @livewire('partner.add-cart-item-partner', ['product'=>$product])
                    </div>


                </a>
                @endforeach
              </div>
                <!-- More products... -->
             </div>
            </div>

        @else
            <div class="px-6 py-4">
                No hay Registro
            </div>
        @endif
            @if ($products->hasPages())
            <div class="px-6 py-4">
            {{$products->links() }}
            </div>
            @endif
        </x-table-responsive>

    </div>

</div>
