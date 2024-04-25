<x-admin-layout>
    <div class="container py-6 md:py-12">

        <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 text-white">

            <a href="{{ route('admin.orderpartners.index') . '?status=2' }}"
                class="bg-blue-400 bg-opacity-75 rounded-lg px-8 py-6 md:py-8 text-center">
                <p class="text-2xl">
                    {{ $pagado }}
                </p>
                <p class="uppercase mt-2">
                    Pagado
                </p>
                <p class="text-2xl mt-2">
                    <i class="fas fa-credit-card"></i>
                </p>
            </a>

            <a href="{{ route('admin.orderpartners.index') . '?status=3' }}"
                class="bg-green-500 bg-opacity-75 rounded-lg px-8 py-6 md:py-8 text-center">
                <p class="text-2xl">
                    {{ $aprobado }}
                </p>
                <p class="uppercase mt-2">
                    Aprobado
                </p>
                <p class="text-2xl mt-2">
                    <i class="fas fa-check-circle"></i>
                </p>
            </a>

            <a href="{{ route('admin.orderpartners.index') . '?status=4' }}"
                class="bg-yellow-500 bg-opacity-75 rounded-lg px-8 py-6 md:py-8 text-center">
                <p class="text-2xl">
                    {{ $despachado }}
                </p>
                <p class="uppercase mt-2">
                    Despachado
                </p>
                <p class="text-2xl mt-2">
                    <i class="fas fa-truck"></i>
                </p>
            </a>

            <a href="{{ route('admin.orderpartners.index') . '?status=5' }}"
                class="bg-pink-500 bg-opacity-75 rounded-lg px-8 py-6 md:py-8 text-center">
                <p class="text-2xl">
                    {{ $entregado }}
                </p>
                <p class="uppercase mt-2">
                    Entregado
                </p>
                <p class="text-2xl mt-2">
                    <i class="fas fa-school"></i>
                </p>
            </a>

        </section>

        @if ($orders->count())

            <section
                class="bg-white shadow-lg rounded-lg px-4 md:px-8 py-4 md:py-8 mt-12 text-gray-700 overflow-x-auto mx-auto">
                <h1 class="text-2xl mb-4">Pedidos Recientes</h1>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse mb-6 md:mb-0">
                        <thead>
                            <tr>

                                <th class="text-left py-2 px-4 md:px-6">Codigo Pedido</th>
                                <th class="text-left py-2 px-4 md:px-6">Fecha</th>
                                <th class="text-left py-2 px-4 md:px-6">Cliente</th>
                                <th class="text-left py-2 px-4 md:px-6">Total</th>
                                <th class="text-left py-2 px-4 md:px-6">Estado</th>
                                <th class="text-left py-2 px-4 md:px-6">Acciones</th>
                                <th class="text-left py-2 px-4 md:px-6"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                                <tr>

                                    <td class="py-2 px-4 md:px-6">003-{{ $order->id }}</td>
                                    <td class="py-2 px-4 md:px-6">{{ $order->created_at->format('d/m/Y') }}</td>
                                    <td class="py-2 px-4 md:px-6">
                                        {{ $order->name ? $order->name : $order->razon_social }}</td>
                                    <td class="py-2 px-4 md:px-6">S/{{ $order->total }}</td>
                                    <td class="py-2 px-4 md:px-6 font-bold">
                                        @switch($order->status)
                                            @case(1)
                                                Reservado
                                            @break

                                            @case(2)
                                                Pagado
                                            @break

                                            @case(3)
                                                Aprobado
                                            @break

                                            @case(4)
                                                Despachado
                                            @break

                                            @case(5)
                                                Entregado
                                            @break

                                            @case(6)
                                                Anulado
                                            @break

                                            @default
                                        @endswitch
                                    </td>
                                    <td class="py-2 px-4 md:px-6">
                                        @if ($order->status == 1)
                                            <a href="{{ route('orderpartners.payment', ['order' => $order]) }}"
                                                class="text-indigo-600 hover:text-indigo-900">Pagar</a>
                                        @endif

                                        @if ($order->status > 1)
                                            <a href="{{ route('orderpartners.show', ['order' => $order]) }}"
                                                class="text-indigo-600 hover:text-indigo-900">Ver</a>
                                        @endif

                                        @if ($order->status > 1)
                                            <a href="{{ route('admin.orderpartners.show', ['order' => $order]) }}"
                                                class="text-indigo-600 hover:text-indigo-900">editar</a>
                                        @endif

                                    </td>


                                    <td>
                                        <x-button-enlace
                                            href="{{ route('orderpartners.pdf_cotizacion', ['order' => $order]) }}"
                                            class="text-xs p-1">
                                            COTIZACION
                                        </x-button-enlace>

                                        <x-button-enlace
                                            href="{{ route('orderpartners.pdf_order', ['order' => $order]) }}"
                                            class="text-xs p-1">
                                            PEDIDO
                                        </x-button-enlace>
                                    </td>

                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @else
            <div class="bg-white shadow-lg rounded-lg px-4 md:px-8 py-4 md:py-8 mt-4 md:mt-12 text-gray-700">
                <span class="font-bold text-lg">
                    No existe registro de Ordenes
                </span>
            </div>
        @endif
    </div>
</x-admin-layout>
