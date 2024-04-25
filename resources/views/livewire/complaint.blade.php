<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Libro de Reclamaciones</title>
    <style>
        /* Estilos para el contenedor principal del formulario */
        .complaint-form {
            max-width: 500px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        /* Estilos para los campos de etiqueta */
        .complaint-form label {
            display: block;
            margin-bottom: 5px;
        }

        /* Estilos para los campos de entrada */
        .complaint-form input[type="text"],
        .complaint-form select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        /* Estilos para los mensajes de error */
        .complaint-form span {
            color: red;
            font-size: 12px;
        }

        /* Estilos para el botón de enviar */
        .complaint-form button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: none;
            border-radius: 5px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
        }

        /* Estilos para el botón de enviar al pasar el mouse */
        .complaint-form button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="complaint-form">
    <h2>Libro de Reclamaciones</h2>

    @if (session()->has('message'))
        <div>{{ session('message') }}</div>
    @endif

    <form wire:submit.prevent="submit">
        <div>
            <label for="documento_id">Tipo de Documento:</label>
           <select wire:model="documento_id">
    		<option value="">Seleccione</option>
    		<option value="DNI">DNI</option>
    		<option value="RUC">RUC</option>
    		<option value="CE">CE</option>
    		<option value="PASAPORTE">PASAPORTE</option>
			</select>
			@error('documento_id') <span>{{ $message }}</span> @enderror

        </div>

        <div>
            <label for="numero_documento">Número de Documento:</label>
            <input type="text" wire:model="numero_documento">
            @error('numero_documento') <span>{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="primer_nombre">Primer Nombre:</label>
            <input type="text" wire:model="primer_nombre">
            @error('primer_nombre') <span>{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="segundo_nombre">Segundo Nombre:</label>
            <input type="text" wire:model="segundo_nombre">
            @error('segundo_nombre') <span>{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="apellido_paterno">Apellido Paterno:</label>
            <input type="text" wire:model="apellido_paterno">
            @error('apellido_paterno') <span>{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="apellido_materno">Apellido Materno:</label>
            <input type="text" wire:model="apellido_materno">
            @error('apellido_materno') <span>{{ $message }}</span> @enderror
        </div>

       <div>
            <label for="correo_electronico">Correo Electrónico:</label>
            <input type="text" wire:model="correo_electronico">
            @error('correo_electronico') <span>{{ $message }}</span> @enderror
        </div>

       <div>
            <label for="telefono">Teléfono:</label>
            <input type="text" wire:model="telefono">
            @error('telefono') <span>{{ $message }}</span> @enderror
        </div>

       <div>
            <label for="direccion">Dirección:</label>
            <input type="text" wire:model="direccion">
            @error('direccion') <span>{{ $message }}</span> @enderror
        </div>

        <div>
            <label for="coment">Comentarios:</label>
            <input type="text" wire:model="direccion">
            @error('coment') <span>{{ $message }}</span> @enderror
        </div>



        <button type="submit">Enviar Reclamo</button>
    </form>
</div>

</body>
</html>
