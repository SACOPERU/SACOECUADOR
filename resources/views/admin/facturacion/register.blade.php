


<!-- resources/views/register.blade.php -->

<form method="post" action="{{ route('admin.facturacion.register')}}">
    @csrf
    <input type="text" name="name" placeholder="Nombre"><br><br>
    <input type="email" name="email" placeholder="Email"><br><br>
    <input type="password" name="password" placeholder="Contraseña"><br><br>
    <input type="password" name="password_confirmation" placeholder="Confirmar contraseña"><br><br>
    <button type="submit">Enviar</button>
</form>
