<x-admin-layout>

    <div class="container py-12">
        @livewire('admin.icover-imagen')
    </div>

    @push('script')
        <script>
            Livewire.on('deleteIcover', icoverSlug => {

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

                        Livewire.emitTo('admin.icover-imagen', 'delete', icoverSlug)
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
