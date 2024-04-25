
<x-partner-layout>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <div class="bg-white rounded-lg shadow-lg px-6 py-8 mb-6 flex items-center">
            <div class="relative">
                <div
                    class="{{ $order->status >= 2 && $order->status != 5 ? 'bg-blue-400' : ' bg-gray-400 ' }} rounded-full h-12 w-12 flex items-center justify-center">
                    <i class="fas fa-wallet text-white"></i>
                </div>

                <div class="absolute -left-1.5 mt-0.5">
                    <p>Pagado</p>
                </div>
            </div>

            <div
                class="h-1 flex-1 {{ $order->status >= 3 && $order->status != 6 ? 'bg-blue-400' : ' bg-gray-400 ' }} mx-2">
            </div>

            <div class="relative">
                <div
                    class="{{ $order->status >= 3 && $order->status != 6 ? 'bg-blue-400' : ' bg-gray-400 ' }} rounded-full h-12 w-12 flex items-center justify-center">
                    <i class="fas fa-check text-white"></i>
                </div>

                <div class="absolute -left-1.5 mt-0.5">
                    <p>Aprobado</p>
                </div>
            </div>

            <div
                class="h-1 flex-1 {{ $order->status >= 4 && $order->status != 6 ? 'bg-blue-400' : ' bg-gray-400 ' }} mx-2">
            </div>

            <div class="relative">
                <div
                    class="rounded-full h-12 w-12 {{ $order->status >= 5 && $order->status != 6 ? 'bg-blue-400' : ' bg-gray-400 ' }} flex items-center justify-center">
                    <i class="fas fa-truck text-white"></i>
                </div>
                <div class="absolute -left-3.5 mt-0.5">
                    <p>Despachado</p>
                </div>
            </div>

            <div
                class="h-1 flex-1 {{ $order->status >= 6 && $order->status != 6 ? 'bg-blue-400' : ' bg-gray-400 ' }} mx-2">
            </div>

            <div class="relative">
                <div
                    class="rounded-full h-12 w-12 {{ $order->status >= 4 && $order->status != 5 ? 'bg-blue-400' : ' bg-gray-400 ' }} flex items-center justify-center">
                    <i class="fas fa-school text-white"></i>
                </div>
                <div class="absolute -left-3.5 mt-0.5">
                    <p>Entregado</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-lg px-6 py-4 mb-6 flex items-center">

            <p class="text-gray-700 uppercase"> <span class="font-semibold">Numero de orden :</span>
                Orden - 003-{{ $order->id }}</p>

            @if ($order->status == 1)
                <x-button-enlace class=" ml-auto" href="{{ route('orderpartners.payment', $order) }}">
                    Ir a Pagar
                </x-button-enlace>
            @endif

        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="grid grid-cols-2 gap-6 text-gray-700">
                <div>
                    @if ($order->envio_type == 1)
                        <p class="text-lg fond-semibold uppercase">RECOJO EN :</p>
                        @php
                            $selectedStoreMapping = [
                                '03-LIM-HUAYLAS-MISTR' => 'Mi Store Tottus Huaylas',
                                '03-LIM-PURUCHU-MISTR' => 'Mi Store Tottus Puruchuco',
                                '03-LIM-MEGAPLZ-MISTR' => 'Mi Store Tottus Mega Plaza',
                                '03-LIM-ATOCONG-MISTR' => 'Mi Store Open Plaza Atocongo',
                                '03-LIM-JOCKEYPZ-MIST' => 'Mi Store Tottus Jockey Plaza',
                            ];
                        @endphp
                        <p class="text-sm font-mobile font-bold">
                            {{ $selectedStoreMapping[$order->selected_store] ?? $order->selected_store }}
                        </p>
                    @else
                        <p class="text-lg fond-semibold uppercase">Envio</p>
                        <p class="text-sm font-mobile font-bold">Los productos serán enviados a:</p>
                        <p class="text-sm font-mobile font-bold">{{ $order->address }}</p>
                        <p>{{ $order->department->name }} - {{ $order->city->name }} -
                            {{ $order->district->name }}
                        </p>
                        <p class="text-sm font-mobile font-bold">Ubigeo: {{ $order->district_id }}</p>
                    @endif
                </div>

                <div>
                    <p class="text-lg fond-semibold uppercase">Datos de Contacto</p>

                    <p class="text-sm font-mobile font-bold">Persona que recibira el pedido:
                        {{ $order->name_order }}</p>
                    <p class="text-sm font-mobile font-bold">Telefono de contacto: {{ $order->phone_order }}</p>
                    <p class="text-sm font-mobile font-bold">Documento de Identidad: {{ $order->dni_order }}</p>
                </div>

            </div>

        </div>

        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <div class="grid grid-cols-2 gap-6 text-gray-700">
                <div>
                    @if ($order->tipo_doc == 2)
                        <p class="text-lg fond-semibold uppercase">Factura</p>
                    @else
                        <p class="text-lg fond-semibold uppercase">Boleta</p>
                    @endif


                    @if ($order->tipo_doc == 1)
                        <p>DNI :{{ $order->dni }}</p>
                        <p>NOMBRE :{{ $order->name }}</p>
                    @else
                        <p>RUC :{{ $order->ruc }}</p>
                        <p>RAZON SOCIAL :{{ $order->razon_social }}</p>
                        <p>DIRECCION FISCAL :{{ $order->direccion_fiscal }}</p>
                    @endif
                </div>

            </div>

        </div>

        <div class="bg-white order-1 lg:order-2 lg:col-span-1 xl:col-span-2">
            <p class="text-lg font-semibold mb-2 p-6">Resumen</p>

            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead>
                        <tr>
                            <th></th>
                            <th class="font-mobile">Precio</th>
                            <th class="font-mobile">Cantidad</th>
                            <th class="font-mobile">Total</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @foreach ($items as $item)
                            @if (!in_array($item->name, ['ZONA 1', 'ZONA 2', 'ZONA 3']))
                                <tr>
                                    <td>
                                        <div class="flex">
                                            <img class="h-15 w-20 object-cover mr-4"
                                                src="{{ $item->options->image }}" alt="">
                                            <article>
                                                <h1 class="font-bold font-mobile">{{ $item->name }}</h1>
                                                <!-- Aplicando estilo de fuente móvil aquí -->
                                                <h1 class="font-bold text-blue-400 font-mobile">
                                                    {{ $item->options->sku }}</h1>
                                                <!-- Aplicando estilo de fuente móvil aquí -->
                                            </article>
                                        </div>
                                    </td>
                                    <td class="text-center font-mobile">
                                        S/{{ $item->price }}
                                    </td>
                                    <td class="text-center font-mobile">
                                        {{ $item->qty }}
                                    </td>
                                    <td class="text-center font-mobile">
                                        S/{{ $item->price * $item->qty }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-partner-layout>

<style>
    /* Estilos para dispositivos móviles */
    @media only screen and (max-width: 640px) {
        .font-mobile {
            font-size: 12px;
            /* Tamaño de fuente más pequeño para dispositivos móviles */
            padding: 4px;
        }
    }
</style>
