@extends('layouts.flex')

@section('content')

    <style>
        .table {
            width: 95%;
            border: 5px solid #0c48e0;
        }

        .table2 {

            border: 5px solid #0c48e0;
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
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
            color: #4a4a4a;
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
            color: #333;
        }

        .divide-y {
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .text-center {
            text-align: center;
        }

        .table td,
        .table th {
            border: 1px solid #161510;
            padding: 8px;
        }

        .table th {
            background-color: #161510;
            color: #fff;
        }

        .center-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            /* Ajusta la altura para centrar verticalmente */
        }

        /* Estilo para centrar el primer cuadro */
        .first-box {
            margin: 0 auto;
            /* Esto centrará el primer cuadro horizontalmente */
        }
    </style>


    <div class="container">
        <h1>Consulta de Productos</h1>

        <form method="post" action="{{ route('livewire.admin.consulta-productos') }}">
            @csrf
            <div class="form-group">
                <label for="empresa">Empresa:</label>
                <input type="text" name="empresa" id="empresa" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="bodega">Bodega:</label>
                <input type="text" name="bodega" id="bodega" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Consultar</button>
        </form>

        <div class=" text-right">
            <p>Bodegas</p>

            <li>03-LIM-JOCKEYPZ-MIST</li>
            <li>03-LIM-ATOCONG-MISTR</li>
            <li>03-LIM-HUAYLAS-MISTR</li>
            <li>03-LIM-MEGAPLZ-MISTR</li>
            <li>03-LIM-PURUCHU-MISTR</li>
            <li>03-LIM-BSF-WEBSACO</li>
        </div>

        @if ($responseData)
            <h2>Productos Consultados</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Nombre</th>
                        <th>Bodega</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($responseData->Producto as $product)
                        @if (
                            $product->Bodega->Cantidad > 0 &&
                                (   str_starts_with($product->CodProducto, 'XIA')||
                                    str_starts_with($product->CodProducto, 'SM') ||
                                    str_starts_with($product->CodProducto, 'INF') ||
                                    str_starts_with($product->CodProducto, 'TMG')))
                            <tr>
                                <td>{{ $product->CodProducto }}</td>
                                <td>{{ $product->Descripcion }}</td>
                                <td>{{ $product->Bodega->Descripcion }}</td>
                                <td>{{ intval($product->Bodega->Cantidad) }}</td>
                            </tr>
                        @endif
                    @endforeach

                </tbody>
            </table>
        @else
            <p>No se encontraron productos consultados.</p>
        @endif
    </div>
@endsection
