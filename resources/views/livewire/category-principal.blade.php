<div wire:init="loadPosts">
    @if (count($products))
        <section class="glider-contain">
            <ul class="glider-{{ $category->id }}">
                @foreach ($products as $product)
                    <li
                        class="bg-white border border-blue-500 rounded-lg shadow {{ $loop->last ? '' : 'sm:mr-4' }} mb-4 overflow-hidden">
                        <article>
                            <figure>
                                <a href="{{ route('products.show', $product) }}">
                                    <img src="{{ Storage::url($product->images->first()->url) }}"
                                        alt="Colocar Imagen al Producto">
                                </a>
                            </figure>

                            <div class="py-3 px-6">
                                <h1 class=" text-sm font-semibold">
                                    <a href="{{ route('products.show', $product) }}"
                                        onmouseover="this.style.color='#3b82f6'" onmouseout="this.style.color=''">
                                        {{ Str::limit($product->name, 50) }}
                                    </a>

                                </h1>
                                <section class="flex flex-col justify-end items-end">
                                    <p class="font-bold text-truegray-700 line-through">S/ {{ $product->price_tachado }}
                                    </p>
                                    <p class="font-bold text-blue-600 text-lg">S/ {{ $product->price }}</p>

                                    <div style="text-align: center; font-size: 80%;">
                                        <p style="display: inline;">@livewire('add-cart-item-saco', ['product' => $product])</p>
                                    </div>

                                </section>

                            </div>
                        </article>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
