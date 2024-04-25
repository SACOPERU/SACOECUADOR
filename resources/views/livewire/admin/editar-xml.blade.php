@extends('layouts.flex')

@section('content')
<div class="container">
    <h1>Editar XML</h1>

    <form method="POST" action="{{ route('livewire.admin.enviar-xml') }}">
        @csrf
        <div class="form-group">
            <label for="Numero">Numero :</label>
            <input type="text" name="Numero" id="Numero">
        </div>
        <div class="form-group">
            <label for="Cliente">Cliente :</label>
            <input type="text" name="Cliente" id="Cliente">
        </div>
        <div class="form-group">
            <label for="Fecha">Fecha :</label>
            <input type="text" name="Fecha" id="Fecha">
        </div>
        <!-- Agrega más campos según sea necesario -->

        <button type="submit" class="btn btn-primary">Enviar XML</button>
    </form>
</div>
@endsection
