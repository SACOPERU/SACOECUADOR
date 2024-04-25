<div class="relative inline-block">
    <button id="dropdownTrigger" class="ml-4 flow-root lg:ml-6 group -m-2 items-center p-2">
        <div class="flex items-center">
            <svg class="h-6 w-6 flex-shrink-0 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
            </svg>
            @if (Cart::count())
                <span class="ml-2 text-sm font-medium text-gray-700 group-hover:text-red-600">{{ Cart::count() }}</span>
            @else
                <span class="ml-2 inline-block w-2 h-2 bg-red-600 rounded-full"></span>
            @endif
        </div>
    </button>

    <div id="dropdown" class="hidden absolute right-0 z-10 mt-2 w-screen max-w-md bg-white rounded-md shadow-lg">
        <div class="relative z-10" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                        <div class="pointer-events-auto w-screen max-w-md">
                            <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl">
                                <div class="flex-1 overflow-y-auto px-4 py-6 sm:px-6">
                                    <div class="flex items-start justify-between">
                                        <h2 class="text-lg font-medium text-gray-900" id="slide-over-title">Carrito de
                                            Compras</h2>
                                        <div class="ml-3 flex h-7 items-center">
                                            <button id="closeButton" type="button"
                                                class="relative -m-2 p-2 text-gray-400 hover:text-gray-500">
                                                <span class="absolute -inset-0.5"></span>
                                                <span class="sr-only">Close panel</span>
                                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                                                    stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="mt-8">
                                        <div class="flow-root">
                                            <ul>
                                                @forelse (Cart::content() as $item)
                                                    <li class="flex p-2 border-b border-blue-500">
                                                        <img class="h-15 w-20 object-cover mr-4"
                                                            src="{{ $item->options->image }}" alt="">
                                                        <article class="flex-1">
                                                            <h1 class="font-bold text-gray-700">{{ $item->name }}</h1>
                                                            <h1 class="font-bold text-xs text-green-400">
                                                                {{ $item->options->sku }}</h1>
                                                            <div class="flex">
                                                                <p>Cant: {{ $item->qty }}</p>
                                                            </div>
                                                            <p>S/ {{ $item->price }}</p>
                                                        </article>

                                                        <div class="text-sm text-blue-500">
                                                            <a class="ml-6 cursor-pointer hover:text-red-600"
                                                                wire:click.stop="delete('{{ $item->rowId }}')"
                                                                wire:loading.class="text-red-600 opacity-25"
                                                                wire:target="delete('{{ $item->rowId }}')">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </li>

                                                @empty
                                                    <li class="py-6 px-4">
                                                        <p class="text-center text-gray-700">No tiene agregado ningún
                                                            item en el carrito</p>
                                                    </li>
                                                @endforelse
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="border-t border-gray-200 px-4 py-6 sm:px-6">
                                    <div class="flex justify-between text-base font-medium text-gray-900">
                                        @if (Cart::count())
                                            <div class="py-2 px-3 text-right">
                                                <p class="text-lg text-gray-700 mt-2 mb-3"><span
                                                        class="font-bold">Total:</span> S/ {{ Cart::subtotal() }}</p>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mt-6">
                                        <a href="{{ route('create-order-partner') }}"
                                            class="flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-6 py-3 text-base font-medium text-white shadow-sm hover:bg-indigo-700">Ir
                                            a Pagar</a>
                                    </div>
                                    <div class="mt-6 flex justify-center text-center text-sm text-gray-500">
                                        <p>
                                            o
                                            <button id="continueShoppingButton" type="button"
                                                class="font-medium text-indigo-600 hover:text-indigo-500">
                                                Continuar Comprando
                                                <span aria-hidden="true"> &rarr;</span>
                                            </button>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const panel = document.getElementById('dropdown');
    const closeButton = document.getElementById('closeButton');
    const continueShoppingButton = document.getElementById('continueShoppingButton');
    const dropdownTrigger = document.getElementById('dropdownTrigger');

    function hidePanel() {
        panel.classList.add('hidden');
    }

    function showPanel() {
        panel.classList.remove('hidden');
    }

    closeButton.addEventListener('click', hidePanel);
    continueShoppingButton.addEventListener('click', hidePanel);
    dropdownTrigger.addEventListener('click', showPanel);
</script>
