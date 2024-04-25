@extends('layouts.flex')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Datos Creados</div>

                <div class="panel-body">
                    @if (count($datos) > 0)
                        <ul>
                            @foreach ($datos as $dato)
                                <li>{{ $dato->nombre }} - {{ $dato->email }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p>No hay datos disponibles.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
