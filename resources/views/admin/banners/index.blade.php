<x-admin-layout>

    <div class="container py-12">
        @livewire('admin.banner-imagen')
    </div>

    @push('script')
    <script>

      Livewire.on('deleteBanner', bannerSlug => {

        Swal.fire({
        title: '¿Está seguro?',
        text: "¡No podrás revertir esto!",
        icon: 'advertencia',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: '¡Sí, bórralo!'
        }).then((result) => {
        if (result.isConfirmed) {

            Livewire.emitTo('admin.banner-imagen', 'delete', bannerSlug)
''
          Swal.fire(
            '¡Borrado!',
            'Su archivo ha sido eliminado.',
            'éxito'
          )
        }
        })

       });
    </script>
    @endpush

</x-admin-layout>
