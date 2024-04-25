<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>COTIZACION N° 003-{{ $order->id }}</title>
    <style>
        .table {  /* 0b77ec */
            width: 100%;
            border: 0.5px solid #000000;
        }

        .table td {
            border: 0.5px solid #000000;
            padding: 4px;
        }

        .table th {
            background-color: #ffffff;
            color: #030303;
        }

        .text-center {
            text-align: center;
        }


        .grid {
            display: grid;
        }

        .grid-cols-2 {
            grid-template-columns: 1fr 1fr;
            /* 0b77ec */
            gap: 0.5rem;
            /* Espacio entre las columnas */
        }

        .text-right {
            text-align: right;
            /* Alinea el texto a la derecha */
        }

        .container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            /* Two equal-width columns */
            gap: 20px;
            /* Gap between columns */
        }

        .small-text {
            font-size: 12px;
            /* Set smaller font size for the text in the right div */
        }

        /* Additional styling for better appearance */
        .left {
            padding-right: 10px;
            /* Add some spacing between the two divs */
        }

        .right {
            padding-left: 10px;
            /* Add some spacing between the two divs */
        }

        .centrado2 {
            text-align: center;
        }

        .saco-header {
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .saco-logo {
            width: 150px;
            padding-right: 1rem;
        }

        .saco-info {
            text-align: right;
            font-size: 0.75rem;
            line-height: 1.25rem;
        }

        .small-text {
            font-size: 12px;
            /* Cambiar el tamaño del texto según sea necesario */
        }
    </style>
    <?php
    $total_sin_igv = $order->total / 1.18;
    $igv = $total_sin_igv * 0.18;
    ?>
</head>

<body>
    <table class="table">
        <tr>
            <td>
                <img class="saco-logo centrado2" src="{{ asset('img/Nueva carpeta/logo1.png') }}" alt="SACO Logo">
            </td>
            <td style="text-align: left;">
                <p class="text-right small-text">
                    Smartphones & Comunicaciones del Peru S.R.L. <br>
                    Villa El Salvador, Lima-Perú <br>
                    Avenida Los Forestales 1296, Almacén C-09 <br>
                    www.saco-communications.com <br>
                    Teléfono: +51 914250735
                </p>
            </td>
        </tr>
    </table>

    <div class="max-w-4xl mx-auto px-6 py-10">
        <div class="bg-white rounded-lg shadow-lg px-6 py-2 mb-6">
            <h2 class="text-gray-800 uppercase text-center text-4xl font-extrabold">COTIZACION N°
                003-{{ $order->id }}</h2>
        </div>

        <div class="bg-white rounded-lg shadow-lg p-2 text-gray-800 mb-6">
            <table class="table">
                <thead>
                    <tr>
                        <th>Datos Cliente</th>
                        <th>Condiciones Generales</th>
                    </tr>
                </thead>

                <tbody class="small-text">
                    <tr>
                        <td>
                            <table class="table">
                                <thead>
                                </thead>

                                <tbody>
                                        <tr>
                                            <td>Ruc : {{ $order->dni ? $order->dni : $order->ruc }}</td>
                                            <td>Nombre : {{ $order->name ? $order->name : $order->razon_social }}</td>
                                        </tr>
                                        <tr>
                                            <td>Direccion : {{ $order->direccion_fiscal }}</td>
                                            <td>Contacto : {{ $order->contacto_ruc }}</td>
                                        </tr>
                                        <tr>
                                            <td>Celular : {{ $order->phone_ruc ? $order->phone_ruc : $order->phone }}</td>
                                            <td>Email : {{ $order->email_ruc ? $order->email_ruc : $order->email }}</td>
                                        </tr>
                                </tbody>

                            </table>
                        </td>

                        <td>
                            <table class="table">
                                <thead>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Fecha de Emision: <br>{{ $order->created_at->format('d-m-Y') }}</td>
                                        <td>Fecha de Vencimiento:<br> {{ $order->created_at->addDays(2)->format('d-m-Y') }}</td>
                                        <td>Validez: Hasta Agotar Stock</td>
                                    </tr>
                                    <tr>
                                        <td>Forma de Pago: Contado PEN</td>
                                        <td>Plazo de Entrega: Inmediato</td>
                                    </tr>
                                    <tr>
                                        <td>Moneda: Soles</td>
                                        <td>Precios: INCLUYE I.G.V 18%</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>

        <div class="bg-white rounded-lg shadow-lg p-2 text-gray-800 mb-6">
            <div class="text-lg font-semibold mb-4 p-6 text-center">PERSONA AUTORIZADA PARA LA RECEPCIÓN DE MERCADERÍA</div>
            <table class="table small-text">
                <thead>
                </thead>

                <tbody>
                        <tr>
                            <td>Persona de Contacto : {{ $order->name_order }}</td>
                            <td>Celular: {{$order->phone_order}}</td>
                            <td>DNI: {{$order->dni_order}}</td>
                        </tr>

                        <tr>
                            <td>Direccion : {{ $order->address }}</td>
                            <td>Referencia: {{$order->references}}</td>
                            <td>Ubigeo: {{ $order->district_id }}</td>
                        </tr>

                        <tr>
                            <td>{{ $order->department->name }} - {{ $order->city->name }} - {{ $order->district->name }}</td>
                        </tr>

                </tbody>
            </table>
        </div>
        <br>

        <div class="bg-white rounded-lg shadow-lg p-6 text-gray-700 mb-6">

                <div class="text-lg font-semibold mb-4 p-6 text-center">DETALLES DEL PEDIDO</div>
                <table class="table small-text">
                    <thead>
                        <tr>
                            <th>Sku</th>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 text-center small-text">
                        @foreach ($items as $item)
                            <tr>
                                <td>{{ $item->options->sku }}</td>
                                <td>{{ $item->name }}</td>
                                <td>S/ {{ $item->price }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>S/ {{ $item->price * $item->qty }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

        </div>
        <br>

        <div class="bg-white rounded-lg shadow-lg p-6 text-gray-700 mb-6">
            <table class="table">
                <thead>
                </thead>

                <tbody class="small-text">
                    <tr>
                        <td align="right">Afecto: S/ {{ number_format($total_sin_igv, 2) }}</td>
                    </tr>
                    <tr>
                        <td align="right">IGV: S/ {{ number_format($igv, 2) }}</td>
                    </tr>
                    <tr class=" font-bold">
                        <td align="right">Total: S/ {{ number_format($order->total, 2) }}</td>
                    </tr>
                </tbody>

            </table>

        </div>
        <br>

        <div class="bg-white rounded-lg shadow-lg p-6 text-gray-700 mb-6">
            <div class="small-text font-semibold mb-4 p-6 text-center">Titular: Smartphones & Comunicaciones del Perú S.R.L.</div>
            <table class="table">
                <thead class="small-text">
                </thead>

                <tbody class="small-text">
                        <tr>
                             <td>Banco: BCP Cuenta Soles:<br>
                                193-2513652-0-23<br>
                                CCI Soles:<br>
                                002-193-002513652023-13 </td>

                             <td>Banco: BBVA Cuenta Soles:<br>
                                0011-0347-01-00056728<br>
                                CCI Soles:<br>
                                011-347-000100056728-27</td>

                            <td>Banco: SCOTIABANK Cuenta Soles:<br>
                                000-8842680<br>
                                CCI Soles:<br>
                                009-170-000008842680-24</td>
                        </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6 text-gray-700 mb-6 small-text">
        <p class=" text-right">{{ strtoupper($order->user->name) }} <br>------------<br>Vendedor</p>

    </div>
</body>

</html>
