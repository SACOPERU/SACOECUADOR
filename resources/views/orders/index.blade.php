<x-app-layout>

    <div class="container py-12">

        <section class="flex flex-wrap justify-around gap-6 text-white">

            <a href="{{ route('orders.index') . "?status=1"}}" class="order-card bg-red-500 bg-opacity-75 rounded-lg">
                <p class="text-center text-2xl">
                    {{$reservado}}
                </p>
                <p class="uppercase text-center">Reservado</p>
                <p class="text-center text-2xl mt-2">
                    <i class="fas fa-business-time"></i>
                </p>
            </a>

            <a href="{{ route('orders.index') . "?status=2"}}" class="order-card bg-gray-500 bg-opacity-75 rounded-lg">
                <p class="text-center text-2xl">
                    {{$pagado}}
                </p>
                <p class="uppercase text-center">Pagado</p>
                <p class="text-center text-2xl mt-2">
                    <i class="fas fa-credit-card"></i>
                </p>
            </a>

            <a href="{{ route('orders.index') . "?status=3"}}" class="order-card bg-yellow-500 bg-opacity-75 rounded-lg">
                <p class="text-center text-2xl">
                    {{$despachado}}
                </p>
                <p class="uppercase text-center">Despachado</p>
                <p class="text-center text-2xl mt-2">
                    <i class="fas fa-truck"></i>
                </p>
            </a>

            <a href="{{ route('orders.index') . "?status=4"}}" class="order-card bg-pink-500 bg-opacity-75 rounded-lg">
                <p class="text-center text-2xl">
                    {{$entregado}}
                </p>
                <p class="uppercase text-center">Entregado</p>
                <p class="text-center text-2xl mt-2">
                    <i class="fas fa-check-circle"></i>
                </p>
            </a>

            <a href="{{ route('orders.index') . "?status=5"}}" class="order-card bg-green-500 bg-opacity-75 rounded-lg">
                <p class="text-center text-2xl">
                    {{$anulado}}
                </p>
                <p class="uppercase text-center">Anulado</p>
                <p class="text-center text-2xl mt-2">
                    <i class="fas fa-times-circle"></i>
                </p>
            </a>

        </section>

        @if ($orders->count())
            <section class="bg-white shadow-lg rounded-lg px-12 py-8 mt-12 text-gray-700">
            <h1 class="text-2xl mb-4">Pedidos Recientes</h1>

            <ul>
                @foreach ($orders as $order )
                    <li>
                        <a href="{{route('orders.show',$order)}}" class="flex items-center py-2 px-4 hover:bg-gray-100">
                            <span class="w-12 text-center">
                                @switch($order->status)
                                    @case(1)
                                        <i class="fas fa-business-time text-red-500 ocapacity-50"></i>
                                        @break
                                    @case(2)
                                        <i class="fas fa-credit-card text-gray-500 ocapacity-50"></i>
                                        @break
                                    @case(3)
                                        <i class="fas fa-truck text-yellow-500 ocapacity-50"></i>
                                        @break
                                    @case(4)
                                        <i class="fas fa-check-circle text-pink-500 ocapacity-50"></i>
                                        @break
                                    @case(5)
                                        <i class="fas fa-times-circle text-green-500 ocapacity-50"></i>
                                        @break

                                    @default

                                @endswitch
                            </span>

                            <span>
                                Order:{{$order->id}}
                                <br>
                                {{$order->created_at->format('d/m/Y')}}
                            </span>

                            <div class="ml-auto text-center">
                                <span class=" font-bold">
                                    @switch($order->status)
                                        @case(1)
                                            Reservado
                                            @break
                                        @case(2)
                                            Pagado
                                            @break
                                        @case(3)
                                            Despachado
                                            @break
                                        @case(4)
                                            Entregado
                                            @break
                                        @case(5)
                                            Anulado
                                            @break

                                        @default

                                    @endswitch
                                </span>
                                    <br>
                                <span class="text-sm">
                                    S/{{$order->total}}
                                </span>
                            </div>

                            <span>
                                <i class="fas fa-angle-right ml-6"></i>
                            </span>

                        </a>
                    </li>
                @endforeach
            </ul>
            </section>
        @else
            <div class="bg-white shadow-lg rounded-lg px-12 py-8 mt-12 text-gray-700">
                <span class="font-bold text-lg">
                No existe registro de Ordenes
                </span>
            </div>
        @endif

    </div>

    <style>
        /* Estilos para dispositivos móviles */
        .order-card {
            width: calc(50% - 1rem); /* 2 elementos en una fila */
            margin-bottom: 1rem; /* Agregar espacio entre los elementos */
            border-radius: 10px; /* Bordes redondeados */
            padding: 1rem; /* Espaciado interno */
            text-align: center; /* Texto centrado */
        }

        /* Estilos para pantallas más grandes (a partir de md) */
        @media (min-width: 768px) {
            .order-card {
                width: calc(20% - 1.5rem); /* 5 elementos en una fila */
            }
        }
    </style>
</x-app-layout>
@livewire('footer')
