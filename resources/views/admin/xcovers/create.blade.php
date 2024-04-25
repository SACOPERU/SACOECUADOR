<x-admin-layout>

    <form action="{{ route('admin.xcovers.create') }}" method="POST">
        @csrf

        <figure class="relative">
            <div class="absolute top-8 right-8">
                <label class="flex items-center px-4 py-2 rounded-lg bg-blue-500 cursor-pointer">
                    <i class="fas fa-camera mr-2"></i>
                    Actualizar imagen
                    <!-- Llama a la función previewImage desde el evento onchange -->
                    <input type="file" accept="image/*" name="image" id="imageInput">
                </label>
            </div>

            <img src="{{ asset('img/Nueva carpeta/banner.png') }}" alt="Portada"
                class="w-full aspect-[3/1] object-cover object-center" id="imgPreview">
        </figure>
    </form>



@push('js')
    <script>
        // Define la función previewImage antes de usarla
        function previewImage(event) {
            // Recuperamos el input que desencadenó la acción
            const input = event.target;

            // Recuperamos la etiqueta img donde cargaremos la imagen
            const imgPreview = document.getElementById('imgPreview');

            // Verificamos si existe una imagen seleccionada
            if (!input.files || !input.files[0]) return;

            // Recuperamos el archivo subido
            const file = input.files[0];

            // Creamos la URL para la previsualización
            const objectURL = URL.createObjectURL(file);

            // Modificamos el atributo src de la etiqueta img
            imgPreview.src = objectURL;
        }

        // Agrega un evento de escucha al input file
        document.getElementById('imageInput').addEventListener('change', previewImage);
    </script>
@endpush
  </x-admin-layout>
