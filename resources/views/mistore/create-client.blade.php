@extends('layouts.flex') <!-- Ajusta según tu estructura de plantillas -->

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-lg">
                    <div class="card-header bg-dark text-white">
                        <h2 class="mb-0">Registro de Punto de Venta</h2>
                    </div>

                    <div class="card-body">
                        <form method="post" action="{{ route('mistore.store-client') }}">
                            @csrf

                            <label for="doc_identidad">DNI/RUC:</label>
                            <input type="text" name="doc_identidad" value="{{ old('doc_identidad') }}" required>
                            @error('doc_identidad')
                                <div style="color: red;">{{ $message }}</div>
                            @enderror
                            <br>

                            <label for="name">Nombre/Razon Social:</label>
                            <input type="text" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div style="color: red;">{{ $message }}</div>
                            @enderror
                            <br>

                            <label for="direccion">Direccion:</label>
                            <input type="text" name="direccion" value="{{ old('direccion') }}" required>
                            @error('direccion')
                                <div style="color: red;">{{ $message }}</div>
                            @enderror
                            <br>

                            <label for="tipo_persona">Tipo Persona:</label>
                            <select name="tipo_persona" required>
                                <option value="01" {{ old('tipo_persona') == '01' ? 'selected' : '' }}>Persona Natural</option>
                                <option value="02" {{ old('tipo_persona') == '02' ? 'selected' : '' }}>Persona Juridica</option>
                            </select>
                            @error('tipo_persona')
                                <div style="color: red;">{{ $message }}</div>
                            @enderror
                            <br>

                            <label for="tipo_identidad">Tipo Identidad:</label>
                            <select name="tipo_identidad" required>
                                <option value="1" {{ old('tipo_identidad') == '1' ? 'selected' : '' }}>Documento nacional de identidad</option>
                                <option value="4" {{ old('tipo_identidad') == '4' ? 'selected' : '' }}>Carnet de Extranjeria</option>
                                <option value="6" {{ old('tipo_identidad') == '6' ? 'selected' : '' }}>R.U.C</option>
                                <option value="7" {{ old('tipo_identidad') == '7' ? 'selected' : '' }}>Pasaporte</option>
                            </select>
                            @error('tipo_identidad')
                                <div style="color: red;">{{ $message }}</div>
                            @enderror
                            <br>

                            <label for="canal">Canal:</label>
                            <input type="text" name="canal" value="{{ old('canal') }}" required>
                            @error('canal')
                                <div style="color: red;">{{ $message }}</div>
                            @enderror
                            <br>

                            <label for="subcanal">Subcanal:</label>
                            <input type="text" name="subcanal" value="{{ old('subcanal') }}" required>
                            @error('subcanal')
                                <div style="color: red;">{{ $message }}</div>
                            @enderror
                            <br>

                            <label for="vendedor">Vendedor:</label>
                            <input type="text" name="vendedor" value="{{ old('vendedor') }}" required>
                            @error('vendedor')
                                <div style="color: red;">{{ $message }}</div>
                            @enderror
                            <br>

                            <label for="number">Number:</label>
                            <input type="number" name="number" value="{{ old('number') }}" required>
                            @error('number')
                                <div style="color: red;">{{ $message }}</div>
                            @enderror
                            <br>

                            <button type="submit" class="btn btn-primary">Crear Clientez</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
