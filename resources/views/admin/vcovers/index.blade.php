<x-admin-layout>

    <div class="container py-12">
        @livewire('admin.vcover-imagen')
    </div>

    @push('script')
        <script>
            Livewire.on('deleteVcover', vcoverSlug => {

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

                        Livewire.emitTo('admin.vcover-imagen', 'delete', vcoverSlug)
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
