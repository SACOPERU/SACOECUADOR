@extends('layouts.flex')

@section('content')
    <style>
        .table {
            width: 95%;
            border: 5px solid #3855e8;
        }

        .table2 {

            border: 5px solid #3855e8;
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
        <h1>Consulta de Precio</h1>

        <form method="POST" action="{{ route('livewire.admin.consulta-precio') }}">
            @csrf
            <div class="form-group">
                <label for="empresa">Id Empresa:</label>
                <input type="text" name="empresa" class="form-control" value="{{ old('empresa') }}" required>
            </div>
            <div class="form-group">
                <label for="listaprecio">ID Lista de Precios:</label>
                <input type="text" name="listaprecio" class="form-control" value="{{ old('listaprecio') }}">
            </div>

            <button type="submit" class="btn btn-primary">Consultar</button>
        </form>


        <div class=" text-right">
            <p>Lista de Precios Para Websaco</p>

            <li>Id = 131</li>

        </div>

        @if (isset($responseData))
            <h2>Productos Consultados :</h2>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Código de Producto</th>
                            <th>Descripción</th>
                            <th>Precio de Lista</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($responseData->PRODUCTO as $producto)
                            <tr>
                                <td>{{ $producto->PRODUCTO }}</td>
                                <td>{{ $producto->GLOSA }}</td>
                                <td>{{ number_format(floatval($producto->PRECIOLISTA), 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
