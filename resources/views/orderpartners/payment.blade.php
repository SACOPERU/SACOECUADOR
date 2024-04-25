<x-partner-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.2/min/dropzone.min.css">
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

    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-5 gap-6 container py-8">

        <div class="xl:col-span-3 ">
            <div class="bg-white rounded-lg shadow-lg px-6 py-4 mb-6">

                <p class="text-gray-700 uppercase"> <span class="font-semibold">Numero de orden :</span> Orden -
                    000{{ $order->id }}</p>

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
                            <p class="text-sm font-mobile">
                                {{ $selectedStoreMapping[$order->selected_store] ?? $order->selected_store }}
                            </p>
                        @else
                            <p class="text-lg fond-semibold uppercase">Envio</p>
                            <p class="text-sm font-mobile font-bold">Los productos serán enviados a:</p>
                            <p class="text-sm font-mobile font-bold">{{ $order->address }}</p>
                            <p class="text-sm font-mobile font-bold">{{ $order->department->name }} -
                                {{ $order->city->name }} -
                                {{ $order->district->name }}
                            </p>
                            <p class="text-sm font-mobile font-bold">Ubigeo: {{ $order->district_id }}</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-lg fond-semibold uppercase">Datos de Contacto</p>

                        <p class="text-sm font-mobile font-bold ">Persona que recibira el pedido:
                            {{ $order->name_order }}</p>
                        <p class="text-sm font-mobile font-bold">Telefono de contacto: {{ $order->phone_order }}</p>
                        <p class="text-sm font-mobile font-bold">Documento de Identidad: {{ $order->dni_order }}</p>
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

                        <tbody class="divide-y divide-gray-200 ">
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

        <div class="xl:col-span-2 ">

            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex justify-between items-center">
                    <div class="text-gray-700 ">

                        <p class="text-sm font-semibold">
                            Subtotal: S/ {{ $order->total - $order->shipping_cost }}
                        </p>

                        <p class="text-sm font-semibold">
                            Envio: S/ {{ $order->shipping_cost }}
                        </p>

                        <p class="text-lg font-semibold uppercase text-red-600">
                            Total: S/ {{ $order->total }}
                        </p>

                    </div>
                </div>

            </div>

            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="mb-4" wire:ignore id="dropzone-container"
                    @if ($order->images->count() > 0) style="display:none;" @endif>
                    <form action="{{ route('orderpartners.files', $order) }}" method="POST" class="dropzone"
                        id="my-awesome-dropzone">
                        @csrf
                    </form>
                </div>

                @if ($order->images->count())
                    <section class="bg-white shadow-xl rounded-lg p-6 mb-4">
                        <h1 class="text-2xl text-center font-semibold mb-2">Constancia de Pago</h1>
                        <ul class="flex flex-wrap">
                            @foreach ($order->images as $image)
                                <li class="relative" wire:key="image-{{ $image->id }}">
                                    <img class="w-32 h-20 object-cover" src="{{ Storage::url($image->url) }}"
                                        alt="">
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif
                @if ($order->status == 1)
                    <form action="{{ route('orderpartners.show', $order) }}" method="POST">
                        @csrf
                        @method('PUT') <!-- Agrega este campo oculto para indicar que es un método PUT -->
                        <x-button-partner class="mt-6 mb-4 text-xs" type="submit">
                            Confirmar Pago
                        </x-button-partner>
                    </form>
                @endif

                @if ($order->status == 2 && auth()->user()->email == 'jose.chirinos@saco-communications.com')
                    <form action="{{ route('admin.orderpartners.index', $order) }}" method="POST">
                        @csrf
                        @method('PUT') <!-- Agrega este campo oculto para indicar que es un método PUT -->
                        <x-button-partner class="mt-6 mb-4 text-xs" type="submit">
                            Aprobar Pago
                        </x-button-partner>
                    </form>
                @endif

            </div>

        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.2/min/dropzone.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.2/min/dropzone.min.css">
    <script>
        var myDropzone = new Dropzone("#my-awesome-dropzone", {
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dictDefaultMessage: "Arrastre una imagen al recuadro",
            acceptedFiles: 'image/*',
            paramName: "file",
            maxFilesize: 2,
            complete: function(file) {
                this.disable();
                document.getElementById("dropzone-container").style.display =
                    "none"; // Oculta el contenedor del formulario Dropzone
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "La constancia de pago ha sido cargada correctamente",
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    // Reload the page
                    window.location.reload();
                });
            },
            accept: function(file, done) {
                if (this.files.length > 1) {
                    this.removeFile(file);
                    alert("Solo se permite cargar una imagen.");
                } else {
                    done();
                }
            }
        });
    </script>


</x-partner-layout>
