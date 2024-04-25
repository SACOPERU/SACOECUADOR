<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>PEDIDO N° 003-{{ $order->id }}</title>
    <style>

        .table {
            width: 95%;
            border: 5px solid #0b77ec;
        }

        .table2 {

            border: 5px solid #0b77ec;
        }

        .centrado {
            text-align: center;
            padding: 8px;
        }

        .centrado2 {
        margin-left: auto;
        margin-right: 0;
        }

        .bg-white {
            background-color: #fff;
        }

        .rounded-lg {
            border-radius: 8px;
        }

        .shadow-lg {
            box-shadow: 0 4px 6px rgba(62, 96, 247, 0.861);
        }

        .px-6 {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .py-4 {
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .mb-6 {
            margin-bottom: 1.5rem;
        }

        .text-gray-700 {
            color: #000000;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .font-extrabold {
            font-weight: 800;
        }

        .text-5xl {
            font-size: 3rem;
        }

        .text-sm {
            font-size: 0.875rem;
        }

        .font-semibold {
            font-weight: 600;
        }

        .grid {
            display: grid;
        }

        .grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .gap-4 {
            gap: 1rem;
        }

        .text-lg {
            font-size: 1.125rem;
        }

        .text-gray-800 {
            color: #000000;
        }

        .divide-y {
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .text-center {
            text-align: center;
        }

        .table td,
        .table th {
            border: 1px solid #0b77ec;
            padding: 8px;
        }

        .table th {
            background-color: #0b77ec;
            color: #fff;
        }
        .center-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh; /* Ajusta la altura para centrar verticalmente */
        }

        /* Estilo para centrar el primer cuadro */
        .first-box {
            margin: 0 auto; /* Esto centrará el primer cuadro horizontalmente */
        }

    </style>
</head>
<body>
    <div class="center-content">
        <div class="table max-w-5xl mx-auto px-6 py-10 first-box">
            <div class="bg-white rounded-lg shadow-lg px-6 py-2 mb-6">
                <div>
                    <img class="navbar-brand-full p-4 app-header-logo"
                                src="{{ asset('img/Nueva carpeta/logo1.png') }}" width="150" alt="Infyom Logo">
                </div>
                <div class="text-gray-800 uppercase centrado text-5xl font-bold">PEDIDO N° 003-{{ $order->id }}</div>
                <div class="centrado">Fecha de Emisión: {{ $order->created_at->format('d-m-Y') }}</div>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6 text-gray-700 mb-6">
                <div class="text-lg font-semibold mb-4 centrado">DATOS DE ENVIO</div>
                <div class="grid grid-cols-2 gap-4 text-gray-800">
                    <!-- Add a new table for "Envío" -->
                <table class="table">
                    <thead>
                        <tr>
                            <th class="text-left">Campo</th>
                        </tr>
                                </thead>
                    <tbody class="divide-y divide-gray-200 centrado2">
                        <tr>
                                @if ($order->envio_type == 1)
                                <td class="font-semibold">RECOJO EN :</td>

                                <td class="text-sm">{{$order->selected_store}}</td>
                                @else
                                    <p class="text-sm font-semibold">Los productos serán enviados a:</p>
                                    <p class="text-sm">Direccion : {{ $order->address }}</p>
                                    <p class="text-sm">Referencia: {{$order->references}}</p>
                                   <p>{{ $order->department->name }} - {{ $order->city->name }} - {{ $order->district->name }}</p>
                                    <p class="text-sm">Ubigeo: {{ $order->district_id }}</p>
                                @endif
                        </tr>
                    </tbody>
                </table>

                <div class="bg-white rounded-lg shadow-lg p-6 text-gray-800 mb-6">
                    <div class="text-lg font-semibold mb-4 centrado">DATOS DE CONTACTO</div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="text-left">Campo</th>
                                <th class="text-left">Información</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 centrado2">
                            <tr>
                                <td class="font-semibold">Persona que recibirá el pedido:</td>
                                <td>{{ $order->name_order }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">Teléfono de contacto:</td>
                                <td>{{ $order->phone_order }}</td>
                            </tr>
                            <tr>
                                <td class="font-semibold">Documento de Identidad:</td>
                                <td>{{ $order->dni_order }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6 text-gray-800 mb-6">
                <div class="text-lg font-semibold mb-4 centrado">DETALLES DEL PEDIDO</div>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Sku</th>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 centrado2">
                        @foreach ($items as $item)
                            <tr>
                                <td>
                                    <div class="flex">
                                        <article>
                                            <div class="flex text-xs">{{ $item->options->sku}}</div>
                                        </article>
                                    </div>
                                </td>
                                <td>
                                    <div class="flex">
                                        <article>
                                            <div class="flex text-xs">{{ $item->name }}</div>
                                        </article>
                                    </div>
                                </td>
                                <td class="text-center">S/ {{ $item->price }}</td>
                                <td class="text-center">{{ $item->qty }}</td>
                                <td class="text-center">S/ {{ $item->price * $item->qty }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <br>
                <table class="divide-y centrado2 table2">
                    <thead>
                        <tr>
                            <th>Envío</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $order->shipping_cost }}</td>
                            <td>S/ {{ $order->total }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>










