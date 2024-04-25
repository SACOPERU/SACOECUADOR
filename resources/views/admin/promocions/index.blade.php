<x-admin-layout>

    <div class="container py-12">
        @livewire('admin.promocion-imagen')
    </div>

    @push('script')
    <script>

      Livewire.on('deletePromocion', promocionSlug => {

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

            Livewire.emitTo('admin.promocion-imagen', 'delete', promocionSlug)
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
