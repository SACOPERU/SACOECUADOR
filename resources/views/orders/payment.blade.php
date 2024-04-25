<x-app-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

    @push('head')
        <script src="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/stable/kr-payment-form.min.js"
            kr-public-key="{{ config('services.izipay.public_key') }}" kr-post-url-success="{{ route('paid.izipay') }}">
        </script>

        <link rel="stylesheet" href="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/ext/classic-reset.css">
        <script src="https://static.micuentaweb.pe/static/js/krypton-client/V4.0/ext/classic.js"></script>
    @endpush

    @push('head')
        <!-- Scripts de OpenPay para obtener el token -->
        <script src="https://js.openpay.pe/openpay.v1.min.js"></script>
        <script src="https://js.openpay.pe/openpay-data.v1.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

        <script type="text/javascript">
            // Configurar las credenciales de OpenPay
            var client_id = "{{ config('services.openpay.client_id') }}";
            var public_key = "{{ config('services.openpay.public_key') }}";
            var sandbox = "{{ config('services.openpay.sandbox') }}";
            OpenPay.setId(client_id);
            OpenPay.setApiKey(public_key);
            OpenPay.setSandboxMode(sandbox);

            $(document).ready(function() {
                // Configurar el ID del dispositivo y obtenerlo
                var deviceSessionId = OpenPay.deviceData.setup("payment-form", "deviceIdHiddenFieldName");
                console.log("ID del dispositivo:", deviceSessionId);

                // Manejar el clic del botón de pago
                $('#pay-button').on('click', function(event) {
                    event.preventDefault();
                    $("#pay-button").prop("disabled", true);
                    // Extraer los datos del formulario y crear el token
                    OpenPay.token.extractFormAndCreate('payment-form', success_callback, error_callback);
                });
            });

            // Función de éxito al obtener el token
            var success_callback = function(response) {
                var token_id = response.data.id;
                $('#token_id').val(token_id);
                $('#payment-form').submit();
                console.log("Token ID obtenido con éxito:", token_id);
            };

            // Función de error al obtener el token
            // Función de error al obtener el token
            var error_callback = function(response) {
                var mensaje = response.description || response.message || 'Error al obtener el token.';
                console.error("Error al obtener el token:", mensaje);

                // Mostrar el mensaje de error usando SweetAlert2
                Swal.fire({
                    icon: 'error',
                    title: 'Error al procesar su pago',
                    text: 'Intentarlo nuevamente con los datos correctos'
                }).then((result) => {
                    $('#boton_pago').prop('disabled', false);
                    window.location.reload(); // Actualizar la página después de hacer clic en OK
                });
            };
        </script>
    @endpush

    @if (Session::has('openpay_error'))
        <script>
            // Mostrar el mensaje de error usando SweetAlert2
            Swal.fire({
                icon: 'error',
                title: 'Error al procesar su pago',
                text: '{{ Session::get('openpay_error') }}'
            });
        </script>
    @endif

    <script>
        function toggleVisibility(inputId) {
            var input = document.getElementById(inputId);
            var eyeIcon = document.getElementById('eyeIcon');

            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.textContent = '🔒';
            } else {
                input.type = 'password';
                eyeIcon.textContent = '👁️';
            }
        }
    </script>


    <script>
        function validateMonth(value, event) {
            // Obtener el valor actual del campo de entrada
            var inputValue = event.key === '0' ? '0' : value + event.key;

            // Convertir el valor a un número entero
            var month = parseInt(inputValue);

            // Verificar si el valor está dentro del rango de 1 a 12
            if (month >= 1 && month <= 12) {
                return true; // Permitir la entrada
            }
        }
    </script>


    @push('head')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @endpush

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
                            <p class="text-sm font-mobile">Los productos serán enviados a:</p>
                            <p class="text-sm font-mobile">{{ $order->address }}</p>
                            <p>{{ $order->department->name }} - {{ $order->city->name }} -
                                {{ $order->district->name }}
                            </p>
                            <p class="text-sm font-mobile">Ubigeo: {{ $order->district_id }}</p>
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

        <div class="xl:col-span-2 ">

            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <div class="flex justify-between items-center">
                    <img class="h-8 hidden md:block" src="{{ asset('img/pago.jpg') }}" alt="">
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
                        <div class="px-4 sm:px-0">
                            <p class="mb-4">Selecciona el metodo de pago:</p>
                            <ul>
                                <li x-data="{ open: false }">
                                    <button x-on:click="open = !open; $refs.form1.open = false"
                                        class="flex justify-center bg-red-500 py-2 w-full rounded-lg shadow">
                                        <h2 class="text-center text-lg font-bold"></h2>
                                        <img class="h-8"
                                            src="https://www.openpay.pe/_nuxt/img/openpay-color.77b290c.webp"
                                            alt="">
                                    </button>

                                    <div class="pt-2 pb-2 flex justify-center" x-show='open' style="display: none"
                                        x-ref="form1">
                                        <!-- Adaptación del formulario existente -->
                                        <form class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 w-full max-w-md"
                                            action="{{ route('orders.pagoexitoso', $order) }}" method="POST"
                                            id="payment-form">
                                            @csrf
                                            <input type="hidden" name="token_id" id="token_id">
                                            <input type="hidden" name="deviceIdHiddenFieldName"
                                                id="deviceIdHiddenFieldName">
                                            <div class="pymnt-itm card active">
                                                <h2 class="text-lg font-semibold mb-4">Tarjeta de crédito o débito</h2>
                                                <div class="pymnt-cntnt">
                                                    <div class="card-expl">

                                                    </div>
                                                    <div class="sctn-row">
                                                        <div class="sctn-col">
                                                            <label
                                                                class="block text-sm font-medium text-gray-700">Nombre
                                                                del titular</label>
                                                            <input type="text"
                                                                placeholder="Como aparece en la tarjeta"
                                                                autocomplete="off" data-openpay-card="holder_name"
                                                                name="name"
                                                                class="mt-1 p-2 block w-full border rounded-md shadow-sm focus:outline-none focus:border-blue-500"
                                                                oninput="this.value = this.value.toUpperCase()">

                                                        </div>
                                                        <div class="sctn-col">
                                                            <label
                                                                class="block text-sm font-medium text-gray-700">Número
                                                                de tarjeta</label>
                                                            <input type="text" autocomplete="off"
                                                                data-openpay-card="card_number"
                                                                class="mt-1 p-2 block w-full border rounded-md shadow-sm focus:outline-none focus:border-blue-500"
                                                                onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                                        </div>
                                                    </div>
                                                    <div class="sctn-row">
                                                        <div class="sctn-col">
                                                            <label class="block text-sm font-medium text-gray-700">Fecha
                                                                de expiración</label>
                                                            <div class="flex">
                                                                <input type="text" placeholder="Mes" maxlength="2"
                                                                    data-openpay-card="expiration_month"
                                                                    class="mt-1 p-2 mr-2 block w-1/2 border rounded-md shadow-sm focus:outline-none focus:border-blue-500"
                                                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57 && validateMonth(this.value, event)">

                                                                <input type="text" placeholder="Año"
                                                                    data-openpay-card="expiration_year"
                                                                    class="mt-1 p-2 block w-1/2 border rounded-md shadow-sm focus:outline-none focus:border-blue-500">
                                                            </div>
                                                        </div>
                                                        <div class="sctn-col cvv relative">
                                                            <label
                                                                class="block text-sm font-medium text-gray-700">Código
                                                                de seguridad</label>
                                                            <input type="password" placeholder="3 dígitos"
                                                                autocomplete="off" data-openpay-card="cvv2"
                                                                class="mt-1 p-2 pr-10 block w-full border rounded-md shadow-sm focus:outline-none focus:border-blue-500"
                                                                id="cvvInput">
                                                            <button type="button"
                                                                class="absolute inset-y-0 right-0 px-3 py-2"
                                                                onclick="toggleVisibility('cvvInput')">
                                                                <span id="eyeIcon">&#128065;</span>
                                                            </button>

                                                            <div id="error-message"></div>
                                                        </div>

                                                    </div>
                                                    <div class="openpay mt-4">
                                                        <div class="shield">Tus pagos se realizan de forma segura</div>
                                                    </div>
                                                    <div class="sctn-row mt-4">
                                                        <button onclick="console.error()"
                                                            class="bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-400"
                                                            type="submit" id="pay-button" value="Pagar">
                                                            PAGAR S/ {{ number_format($order->total, 2) }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                </li>

                                <li x-data="{ open: false }" class="py-2">

                                    <button x-on:click="open = !open; $refs.form2.open = false"
                                        class="flex justify-center bg-red-500 py-2 w-full rounded-lg shadow">
                                        <h2 class=" text-center text-lg font-bold"></h2>
                                        <img class="h-8 "src="https://secure.micuentaweb.pe/doc/assets/nuxt/white_labels/procesos/logo.png"
                                            alt="">
                                    </button>

                                    <div class="pt-6 pb-4 flex justify-center" x-show='open' style="display: none">
                                        <!-- payment form -->
                                        <div class="kr-embedded" kr-form-token="{{ $formToken }}">
                                            <div class="kr-pan"></div>
                                            <div class="kr-expiry"></div>
                                            <div class="kr-security-code"></div>
                                            <button class="kr-payment-button"></button>
                                            <div class="kr-form-error"></div>
                                        </div>
                                    </div>
                                </li>
                            </ul>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</x-app-layout>
