<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TICKET N° 000{{ $order->id }}</title>

    <!-- Incluimos la librería JsBarcode localmente -->
    <script src="{{ asset('js/JsBarcode.all.min.js') }}"></script>

    <!-- Incluimos la librería qrious desde CDN -->
    <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/browser/qrious.min.js"></script>

    <style>
        /* Agregamos estilos para mejorar la presentación */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            /* Agregamos margen cero para evitar espacios no deseados alrededor del cuerpo */
        }

        .center-content {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .blue-box {
            width: 100%;
            max-width: 600px;
            /* Ajusta el ancho máximo según tus necesidades */
            margin: 0 auto;
            /* Agregamos margen automático en los lados para centrar el recuadro */
            background-color: #ffffff;
            /* Color de fondo blanco */
            border: 2px solid #0b77ec;
            /* Contorno de color azul */
            border-radius: 10px;
            overflow: hidden;
            color: #000000;
            /* Color de texto negro para contrastar con el fondo */
        }

        .table {
            width: 100%;
            font-size: 12px;
            /* Tamaño de letra más pequeño para todas las tablas */
        }

        .table th,
        .table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #0b77ec;
        }

        .table th {
            background-color: #0b77ec;
            /* Puedes cambiar el color del encabezado según tus necesidades */
            color: #ffffff;
        }

        .table tbody tr:nth-child(even) {
            background-color: #0b77ec;
            /* Puedes cambiar el color de fondo de las filas pares según tus necesidades */
        }

        .table tbody tr:hover {
            background-color: #0b77ec;
            /* Puedes cambiar el color de fondo al pasar el ratón por encima según tus necesidades */
        }

        /* Tamaño de letra más pequeño para las tablas internas */
        .table-small {
            font-size: 10px;
        }

        /* Estilos para el código QR */
        .qrcode-container {
            margin-top: 20px;
            text-align: center;
            /* Centra el código QR dentro del contenedor */
        }

        /* Estilo para el código de barras */
        .barcode {
            display: block;
            margin: 20px auto;
        }
    </style>
</head>

<body>
    <div class="center-content">
        <!-- Blue box wrapper -->
        <div class="blue-box">
            <table class="table">
                <thead>
                    <tr>
                        <th style="text-align: center;">EMPRESA DE TRANSPORTE</th>
                        <th style="text-align: center;">NÚMERO DE PEDIDO</th>
                        <th style="text-align: center;">TRACKING NUMBER</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td id="courrier" style="text-align: center;">{{ $order->courrier }}</td>

                        <td id="barcode2" style="text-align: center;">
                            {!! DNS1D::getBarcodeHTML("$order->id", 'C128') !!} X-0000{{ $order->id }}
                        </td>

                        <td id="barcode1" style="text-align: center;">
                            {!! DNS1D::getBarcodeHTML("0" . str_replace('-', '', $order->tracking_number), 'C128') !!} {{ $order->tracking_number }}
                        </td>
                    </tr>
                </tbody>

            </table>

            <table class="table table-small">
                <tbody>
                    <tr>
                        <td>
                            <p>REMITENTE: SACO PERU</p>
                            <p>DIRECCIÓN: Av. Los Forestales 1296, Int C-09</p>
                            <p>LOCALIDAD: LIMA - LIMA - VILLA EL SALVADOR</p>
                            <p>UBIGEO: 15842</p>
                            <p>CONTACTO: Maria Quispe</p>
                            <p>CELULAR: 902977730</p>
                        </td>
                        <td class="table-small" style="text-align: right;">
                            <img class="navbar-brand-full app-header-logo"
                                src="{{ asset('img/Nueva carpeta/logo1.png') }}" width="150" alt="Infyom Logo">
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="table table-small">
                <tbody>
                    <tr>
                        <td>
                            <p>DESTINATARIO: {{ $order->name_order }}</p>
                            <p>DNI: {{ $order->dni_order }}</p>
                            <p>CELULAR: {{ $order->phone_order }}</p>
                            <p>DIRECCIÓN: {{ $order->address }}</p>
                            <p>LOCALIDAD:</p>
                            <p>UBIGEO:{{ $order->district_id }}</p>
                        </td>
                        <td class="table-small" style="text-align: right;">
                            <img class="navbar-brand-full app-header-logo"
                                src="{{ asset('img/Nueva carpeta/logoreseller.png') }}" width="150"
                                alt="Infyom Logo">
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="table">
                <tbody>
                    <tr>
                        <td>
                            <p>BULTO NUMERO: <sup>{{ $order->observacion }}</sup>__DE____</p>
                            <p>PESO: {{ $order->peso_paquete }} Kg</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>
