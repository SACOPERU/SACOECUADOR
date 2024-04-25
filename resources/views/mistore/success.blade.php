

@extends('layouts.flex') <!-- Ajusta según tu estructura de plantillas -->

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Success</div>

                    <div class="card-body">
                        <p>Cliente registrado exitosamente.!</p>
                        <!-- Puedes agregar más contenido según sea necesario -->
                        <a href="{{ route('mistore.create-client') }}">Volver al formulario</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
