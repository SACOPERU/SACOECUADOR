
@extends('layouts.flex')
<form action="{{ route('livewire.admin.consulta-productos') }}" method="POST">
    @csrf
    <!-- Otros campos del formulario -->
    <label for="empresa">Empresa:</label>
    <input type="text" name="empresa" value="{{ old('empresa') }}" /><br>
    <label for="bodega">Bodega:</label>
    <input type="text" name="bodega" value="{{ old('bodega') }}" /><br>

    <button type="submit">Consultar</button>
</form>
